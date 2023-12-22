<?php

namespace App\GraphQL\Mutations\Form;

use App\Enums\AnswerType;
use App\Enums\ConditionType;
use App\Enums\DocumentType;
use App\Enums\FormGroup;
use App\Enums\FormType;
use App\Enums\PropertyType;
use App\Enums\PropertyUserRole;
use App\Enums\StepType;
use App\Enums\UserRole;
use App\GraphQL\Queries\Property\MyProgress;
use App\Jobs\GenerateSaleOverviewPdf;
use App\Models\Answer;
use App\Models\Form;
use App\Models\Property;
use App\Models\ProvidedAnswer;
use App\Models\Section;
use App\Models\Step;
use App\Models\User;
use App\Notifications\InviteClient;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class SaveProvidedAnswer
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        // Start a transaction so we call rollback if needed
        DB::beginTransaction();

        // Get all steps and add extra answers to array if missing
        $activeFormId = collect($args['provided_answers'])->pluck('active_form_id')->first();
        $answerIds = collect($args['provided_answers'])->pluck('answer_id');
        $propertyIds = collect($args['provided_answers'])
            ->pluck('property_id')
            ->merge([Arr::get($args, 'current_property_id', null)]);

        $steps = Step::query()
            ->with('answers')
            ->whereHas('answers', function ($query) use ($answerIds) {
                $query->whereIn('id', $answerIds);
            })
            ->orWhere('id', Arr::get($args, 'current_step_id', null))
            ->get();

        $properties = Property::query()
            ->whereIn('id', $propertyIds)
            ->get();

        $repeatableCount = Step::whereIn('repeatable_answer_id', $answerIds)->exists()
            ? Arr::get(collect($args['provided_answers'])
                ->whereIn('answer_id', $steps->pluck('repeatable_answer_id'))
                ->first(), 'value', null)
            : ProvidedAnswer::query()
                ->where('active_form_id', $activeFormId)
                ->whereIn('property_id', $propertyIds)
                ->whereHas('answer', function ($query) use ($steps) {
                    $query->whereHas('repeatableStep', function ($query) use ($steps) {
                        $query->whereIn('id', $steps->pluck('id'));
                    });
                })
                ->first()
                ?->value;

        $isNotRelatedTransaction = true;

        // This is a type of custom step where the user clicks 'add' for new items, different from repeatables
        if (count($steps->where('type', StepType::MortgageRelatedTransactions)) > 0) {
            $isNotRelatedTransaction = false;
            $repeatableCount = count(collect($args['provided_answers'])->pluck('value')->first());
        }

        $repeatableCount = $repeatableCount ? intval($repeatableCount) : null;

        if (is_null($repeatableCount) && Answer::query()
            ->whereIn('id', $answerIds)
            ->whereHas('step', function ($query) {
                $query->where('type', StepType::MortgageRelatedTransactions);
            })
            ->exists()
        ) {
            $relatedTransactionAnswer = Answer::query()
                ->whereIn('id', $answerIds)
                ->whereHas('step', function ($query) {
                    $query->where('type', StepType::MortgageRelatedTransactions);
                })
                ->first();

            // Only check the submitted items for validation
            $repeatableCount = count(collect($args['provided_answers'])
                ->firstWhere('answer_id', $relatedTransactionAnswer->id)['value']
            );

            $repeatableCount = is_int($repeatableCount) ? intval($repeatableCount) : null;
        }

        // Add missing answers. This adds in "fake" answers for questions that are not
        // present in the request body. This is so we can validate them.
        // It ensures that there is a provided answer for every property id that was
        // provided by the original answers and it fetches all answers for all steps.
        $properties->each(function ($property) use (&$args, $steps, $repeatableCount, $activeFormId) {
            $steps->each(function ($step) use (&$args, $property, $repeatableCount, $activeFormId) {
                $step->answers()->get()->each(function ($answer) use (&$args, $property, $repeatableCount, $activeFormId, $step) {
                    $combined = collect($args['provided_answers'])->map(function ($arg) {
                        return [$arg['answer_id'], $arg['property_id']];
                    });

                    if (! $combined->contains([$answer->id, $property->id])) {
                        $defaultValue = $answer->type !== AnswerType::Address
                            ? null
                            : [
                                'line_1' => null,
                                'line_2' => null,
                                'city' => null,
                                'postcode' => null,
                            ];

                        $args['provided_answers'][] = [
                            'active_form_id' => $activeFormId,
                            'answer_id' => $answer->id,
                            'property_id' => $property->id,
                            'value' => is_null($step->repeatable_answer_id)
                                ? $defaultValue
                                : array_fill(0, $repeatableCount, $defaultValue),
                        ];
                    }
                });
            });
        });

        $user = Auth::user();

        // Get the provided answers and set a default if it doesn't exist
        foreach ($args['provided_answers'] as $index => $providedAnswerValues) {
            /** @var ProvidedAnswer */
            $providedAnswer = ProvidedAnswer::with('answer.conditions')
                ->where('active_form_id', $providedAnswerValues['active_form_id'])
                ->where('answer_id', $providedAnswerValues['answer_id'])
                ->where('property_id', $providedAnswerValues['property_id'])
                ->first();

            if ($user->role === UserRole::Conveyancer) {
                $user = User::find($providedAnswer->user_id);
            }

            $args['provided_answers'][$index]['provided_answer'] = $providedAnswer ?? new ProvidedAnswer([
                'active_form_id' => $activeFormId,
                'property_id' => $providedAnswerValues['property_id'],
                'answer_id' => $providedAnswerValues['answer_id'],
                'user_id' => $user->id,
            ]);
        }

        $validationMessages = collect();

        foreach ($args['provided_answers'] as $index => $providedAnswerValues) {
            $providedAnswer = $providedAnswerValues['provided_answer'];

            if (is_null($providedAnswer->answer->step->repeatable_answer_id) && $isNotRelatedTransaction) {
                // Compile a list of validators for this answer
                /** @var \Illuminate\Support\Collection<string> */
                $validators = $providedAnswer->answer->validationRules->pluck('rule');

                // Check if this answer is conditional, and check it's condition trigger is set.
                // If the condition is not met (the provided value doesn't match the condition) remove the `required`
                // validation rule as the value is not required.
                if ($providedAnswer->answer->conditions->count() > 0
                    && ! $providedAnswer->answer->conditions->reduce(function ($carry, $condition) use ($args) {
                        $answerValue = Arr::get(
                            collect($args['provided_answers'])->firstWhere('answer_id', $condition['answer_id']),
                            'value',
                        );

                        return match ($condition->type) {
                            ConditionType::AND => ($carry ?? true) && $answerValue === $condition['selected_value'],
                            ConditionType::OR => ($carry ?? false) || $answerValue === $condition['selected_value'],
                        };
                    }, null)
                ) {
                    $validators = $validators->filter(function ($validator) {
                        return $validator !== 'required';
                    });
                } else {
                    // Get any additional validators from the answer type
                    // Only if the answer is not conditional or if the condition has been met.
                    $validators = $validators->merge($providedAnswer->answer->getTypeValidationRules($providedAnswer->property));
                }

                // Split and flatten the validators
                $validators = $validators->reduce(function ($carry, $item) {
                    if (! $carry->has('value')) {
                        $carry['value'] = collect();
                    }

                    if (is_array($item)) {
                        foreach ($item as $key => $subItem) {
                            $carry["value.$key"] = $subItem;
                        }
                    } else {
                        $carry['value']->add($item);
                    }

                    return $carry;
                }, collect());
            } else {
                $validators = collect(array_fill(0, $repeatableCount, null))->map(function ($_, $i) use ($args, $providedAnswer) {
                    // Compile a list of validators for this answer
                    /** @var \Illuminate\Support\Collection<string> */
                    $validators = $providedAnswer->answer->validationRules->pluck('rule');

                    // Check if this answer is conditional, and check it's condition trigger is set.
                    // If the condition is not met (the provided value doesn't match the condition) remove the `required`
                    // validation rule as the value is not required.
                    if ($providedAnswer->answer->conditions->count() > 0
                        && ! $providedAnswer->answer->conditions->reduce(function ($carry, $condition) use ($args, $i) {
                            $answerValue = Arr::get(
                                collect($args['provided_answers'])->firstWhere('answer_id', $condition['answer_id']),
                                'value.'.$i,
                            );

                            return match ($condition->type) {
                                ConditionType::AND => ($carry ?? true) && $answerValue === $condition['selected_value'],
                                ConditionType::OR => ($carry ?? false) || $answerValue === $condition['selected_value'],
                            };
                        }, null)
                    ) {
                        $validators = $validators->filter(function ($validator) {
                            return $validator !== 'required';
                        });
                    } else {
                        // Get any additional validators from the answer type
                        // Only if the answer is not conditional or if the condition has been met.
                        $validators = $validators->merge($providedAnswer->answer->getTypeValidationRules($providedAnswer->property));
                    }

                    // Split and flatten the validators
                    $validators = $validators->reduce(function ($carry, $item) {
                        if (! $carry->has('value')) {
                            $carry['value'] = collect();
                        }

                        if (is_array($item)) {
                            foreach ($item as $key => $subItem) {
                                $carry["value.$key"] = $subItem;
                            }
                        } else {
                            $carry['value']->add($item);
                        }

                        return $carry;
                    }, collect());

                    return $validators;
                });

                $validators = $validators->reduce(function ($carry, $item, $index) {
                    $item->each(function ($subItem, $key) use (&$carry, $index, &$item) {
                        $formattedKey = collect([
                            'value',
                            $index,
                            Str::replaceFirst('.', '', (Str::replace('value', '', $key))),
                        ])
                            ->filter(function ($item) {
                                return $item !== '';
                            })
                            ->join('.');

                        $carry[$formattedKey] = $subItem;
                    });

                    return $carry;
                }, collect());
            }

            // Trigger validation
            $validator = Validator::make(
                $providedAnswerValues,
                $validators->toArray(),
                [
                    'value.required' => 'This field is required.',
                    'value.line_1.required' => 'This field is required.',
                    'value.city.required' => 'This field is required.',
                    'value.postcode.required' => 'This field is required.',
                ]
            );
            if ($validator->fails()) {
                $validationMessages[$providedAnswerValues['answer_id']] = $validator->errors()->toArray();
            }
        }

        // Throw any validation errors if they exist
        if (count($validationMessages)) {
            throw ValidationException::withMessages(
                $validationMessages->reduce(function ($carry, $item, $key) {
                    foreach ($item as $subKey => $message) {
                        $carry["input.provided_answers.$key.$subKey"] = $message;
                    }

                    return $carry;
                }, [])
            );
        }

        // Save the answers
        foreach ($args['provided_answers'] as $providedAnswerValues) {
            $providedAnswer = $providedAnswerValues['provided_answer'];

            if (is_null($providedAnswer->answer->step->repeatable_answer_id) && $isNotRelatedTransaction) {
                if ($providedAnswer->answer->type === AnswerType::File && is_array($providedAnswerValues['value'])) {
                    if ($providedAnswer->file_value) {
                        $providedAnswer->file_value->delete();
                    }

                    $docType = match ($providedAnswer->answer->step->section->form->group) {
                        FormGroup::GettingStarted => DocumentType::EvidenceGettingStarted,
                        FormGroup::Protocol => DocumentType::EvidenceProtocol,
                        FormGroup::Enquiry => DocumentType::EvidenceEnquiry,
                    };

                    $providedAnswerValues['value'] = $providedAnswer
                        ->addMediaFromDisk($providedAnswerValues['value']['key'])
                        ->usingFileName(explode('/', $providedAnswerValues['value']['key'])[1].'.'.$providedAnswerValues['value']['extension'])
                        ->withCustomProperties([
                            'type' => $docType,
                        ])
                        ->usingName($providedAnswerValues['value']['name'])
                        ->toMediaCollection('file_value')
                        ->id;
                }
            } else {
                if ($providedAnswer->answer->type === AnswerType::File && is_array($providedAnswerValues['value'])) {
                    for ($i = 0; $i < $repeatableCount; $i++) {
                        $providedAnswerValue = Arr::get($providedAnswerValues, "value.$i");
                        $shouldDeleteMedia = is_array($providedAnswerValue)
                            || $providedAnswerValue === 'Not applicable'
                            || $providedAnswerValue === 'Add later';

                        if ($shouldDeleteMedia) {
                            $mediaToDelete = $providedAnswer->getFirstMedia('file_value', function (Media $media) use ($i) {
                                return $media->getCustomProperty('repeatable_index') === $i;
                            });
                            if ($mediaToDelete) {
                                $mediaToDelete->delete();
                            }
                        }

                        if (is_array(Arr::get($providedAnswerValues, "value.$i"))) {
                            $docType = match ($providedAnswer->answer->step->section->form->group) {
                                FormGroup::GettingStarted => DocumentType::EvidenceGettingStarted,
                                FormGroup::Protocol => DocumentType::EvidenceProtocol,
                                FormGroup::Enquiry => DocumentType::EvidenceEnquiry,
                            };

                            $providedAnswerValues['value'][$i] = $providedAnswer
                                ->addMediaFromDisk($providedAnswerValues['value'][$i]['key'])
                                ->usingFileName(explode('/', $providedAnswerValues['value'][$i]['key'])[1].'.'.$providedAnswerValues['value'][$i]['extension'])
                                ->withCustomProperties([
                                    'type' => $docType,
                                    'repeatable_index' => $i,
                                ])
                                ->usingName($providedAnswerValues['value'][$i]['name'])
                                ->toMediaCollection('file_value')
                                ->id;
                        }
                    }
                }
            }

            $providedAnswer->value = $providedAnswerValues['value'];
            $providedAnswer->save();
        }

        // Check for any conditionals
        foreach ($args['provided_answers'] as $providedAnswerValues) {
            $providedAnswer = $providedAnswerValues['provided_answer'];

            $providedAnswerQuery = ProvidedAnswer::query()
                ->where('property_id', $providedAnswer->property_id)
                ->where('user_id', $user->id);

            // For each conditional
            $providedAnswer->answer->conditionTriggers->each(function ($conditionTrigger) use ($providedAnswerValues, $providedAnswer, $providedAnswerQuery, $user) {
                $conditionable = $conditionTrigger->conditionable;

                // If the value is null, delete any existing provided answers
                if (is_null($providedAnswerValues['value'])) {
                    $this->deleteExistingAnswers($conditionable, $providedAnswer);
                }

                $valueMatches = $conditionable->conditions->reduce(function ($carry, $condition) use ($providedAnswerQuery) {
                    $answerValue = (clone $providedAnswerQuery)
                        ->where('answer_id', $condition['answer_id'])
                        ->first()?->value;

                    $isConditionMet = is_array($answerValue)
                        ? in_array($condition['selected_value'], $answerValue)
                        : $answerValue === $condition['selected_value'];

                    return match ($condition->type) {
                        ConditionType::AND => ($carry ?? true) && $isConditionMet,
                        ConditionType::OR => ($carry ?? false) || $isConditionMet,
                    };
                }, null);

                // If the value matches, create any provided answers
                if ($valueMatches) {
                    if (is_a($conditionable, Answer::class)) {
                        ProvidedAnswer::updateOrCreate([
                            'active_form_id' => $providedAnswer->active_form_id,
                            'property_id' => $providedAnswer->property_id,
                            'answer_id' => $conditionable->id,
                            'user_id' => $user->id,
                        ], [
                            'active_form_id' => $providedAnswer->active_form_id,
                            'property_id' => $providedAnswer->property_id,
                            'answer_id' => $conditionable->id,
                            'user_id' => $user->id,
                        ]);
                    } elseif (is_a($conditionable, Step::class)) {
                        $conditionable->answers->each(function ($answer) use ($providedAnswer, $user) {
                            ProvidedAnswer::updateOrCreate([
                                'active_form_id' => $providedAnswer->active_form_id,
                                'property_id' => $providedAnswer->property_id,
                                'answer_id' => $answer->id,
                                'user_id' => $user->id,
                            ], [
                                'active_form_id' => $providedAnswer->active_form_id,
                                'property_id' => $providedAnswer->property_id,
                                'answer_id' => $answer->id,
                                'user_id' => $user->id,
                            ]);
                        });
                    } elseif (is_a($conditionable, Form::class)) {
                        $conditionable->properties()->syncWithoutDetaching($providedAnswer->property_id);

                        $activeForm = $conditionable
                            ->properties()
                            ->firstWhere('property_id', $providedAnswer->property_id)
                            ->pivot;

                        $conditionable->answers->each(function ($answer) use ($providedAnswer, $activeForm, $user) {
                            ProvidedAnswer::updateOrCreate([
                                'active_form_id' => $activeForm->id,
                                'property_id' => $providedAnswer->property_id,
                                'answer_id' => $answer->id,
                                'user_id' => $user->id,
                            ], [
                                'active_form_id' => $activeForm->id,
                                'property_id' => $providedAnswer->property_id,
                                'answer_id' => $answer->id,
                                'user_id' => $user->id,
                            ]);
                        });
                    }
                } else {
                    $this->deleteExistingAnswers($conditionable, $providedAnswer);
                }
            });

            // Check if any steps repeat based on this answer
            Step::with('answers')
                ->where('repeatable_answer_id', $providedAnswer->answer->id)
                ->get()
                ->each(function ($step) use ($providedAnswer, $args, $user) {
                    $step->answers
                        ->filter(fn ($a) => empty($a->conditions))
                        ->map(function ($answer) use ($providedAnswer, $args, $user) {
                            ProvidedAnswer::updateOrCreate([
                                'active_form_id' => $providedAnswer->active_form_id,
                                'property_id' => $providedAnswer->property_id,
                                'answer_id' => $answer->id,
                                'user_id' => $user->id,
                            ], [
                                'active_form_id' => $providedAnswer->active_form_id,
                                'property_id' => $providedAnswer->property_id,
                                'answer_id' => $answer->id,
                                'user_id' => $user->id,
                                'value' => collect($args['provided_answers'])->firstWhere('answer_id', $answer->id)?->value,
                            ]);
                        });
                });

            // Check if any forms repeat based on this answer
            Form::with('sections.steps.answers')
                ->where('repeatable_answer_id', $providedAnswer->answer->id)
                ->get()
                ->each(function ($form) use ($providedAnswer, $args, $user) {
                    $individualForm = Form::query()
                        ->where('ta_form_template', FormType::Individual)
                        ->where('type', $providedAnswer->property->type)
                        ->first();
                    $companyForm = Form::query()
                        ->where('ta_form_template', FormType::Company)
                        ->where('type', $providedAnswer->property->type)
                        ->first();

                    if (DB::table('active_forms')
                        ->whereIn('form_id', [$individualForm->id, $companyForm->id])
                        ->where('property_id', $providedAnswer->property_id)
                        ->count()
                        !== count($providedAnswer->value)
                    ) {
                        // Delete old active forms as there is a new amount required.
                        DB::table('active_forms')
                            ->whereIn('form_id', [$individualForm->id, $companyForm->id])
                            ->where('property_id', $providedAnswer->property_id)
                            ->delete();

                        // Answer Values as array
                        $answerValues = collect($args['provided_answers'])
                            ->filter(function ($value) {
                                return in_array(
                                    $value['provided_answer']->answer['details']->label,
                                    ['Company name', 'Title', 'First name', 'Middle name(s)', 'Surname'],
                                );
                            })
                            ->pluck('value');

                        // Add active forms
                        for ($i = 0; $i < count($providedAnswer->value); $i++) {
                            $formId = $providedAnswer->value[$i] === 'Individual'
                                ? $individualForm->id
                                : $companyForm->id;

                            $ownerName = $answerValues->pluck($i)->filter()->join(' ');

                            $providedAnswer->property->activeForms()->attach(
                                $formId,
                                ['title' => "Getting Started: $ownerName"],
                            );

                            $activeForm = $providedAnswer
                                ->property
                                ->activeForms()
                                ->wherePivot('form_id', $formId)
                                ->first()
                                ->pivot;

                            $form->sections->each(function ($section) use ($providedAnswer, $args, $activeForm, $user) {
                                $section->steps->each(function ($step) use ($providedAnswer, $args, $activeForm, $user) {
                                    $step->answers
                                        ->filter(fn ($a) => empty($a->conditions))
                                        ->map(function ($answer) use ($providedAnswer, $args, $activeForm, $user) {
                                            ProvidedAnswer::updateOrCreate([
                                                'active_form_id' => $activeForm->id,
                                                'property_id' => $providedAnswer->property_id,
                                                'answer_id' => $answer->id,
                                                'user_id' => $user->id,
                                            ], [
                                                'active_form_id' => $activeForm->id,
                                                'property_id' => $providedAnswer->property_id,
                                                'answer_id' => $answer->id,
                                                'user_id' => $user->id,
                                                'value' => collect($args['provided_answers'])->firstWhere('answer_id', $answer->id)?->value,
                                            ]);
                                        });
                                });
                            });
                        }
                    }
                });
        }

        // Commit changes
        DB::commit();

        // Get all provided answers for this property
        $allProvidedAnswers = ProvidedAnswer::with('answer.step.section.form')
            ->where('active_form_id', $providedAnswerValues['active_form_id'])
            ->where('property_id', $providedAnswerValues['property_id'])
            ->where('user_id', $user->id)
            ->get();

        // Get a list of all forms for this property
        $allForms = $providedAnswer = ProvidedAnswer::with('answer.conditions', 'answer.step.section.form')
            ->where('active_form_id', $providedAnswerValues['active_form_id'])
            ->where('property_id', $providedAnswerValues['property_id'])
            ->where('user_id', $user->id)
            ->get()
            ->pluck('answer.step.section.form')
            ->unique();

        // Filter down to just forms which are completed
        $completedForms = $allForms
            ->filter(function ($form) use ($allProvidedAnswers) {
                $formAnswers = $allProvidedAnswers->filter(function ($providedAnswer) use ($form) {
                    return $providedAnswer->answer->step->section->form->id === $form->id;
                });

                $completedQuestions = $form->sections->reduce(function ($carry, $section) use ($formAnswers) {
                    return $carry + $section->steps->reduce(function ($carry, $step) use ($formAnswers) {
                        return $carry + $formAnswers->filter(function ($providedAnswer) use ($step) {
                            return $step->answers->pluck('id')->contains($providedAnswer->answer->id) && $providedAnswer->value !== null;
                        })->count() ? 1 : 0;
                    }, 0);
                }, 0);

                $totalQuestions = $form->sections->reduce(function ($carry, $section) {
                    return $carry + $section->steps->count();
                }, 0);

                return $completedQuestions === $totalQuestions;
            });

        $formToNotifyAbout = $allForms
            ->filter(function ($form) use ($completedForms) {
                return ! $completedForms->pluck('id')->contains($form->id);
            })
            ->sortBy('order_number')
            ->first();

        // @TODO: Uncomment this when we have a notification to send for the new forms
        // if (isset($formToNotifyAbout)) {
        //     NotificationHelper::sendNotification($formToNotifyAbout);
        // }

        // Generate Sale Overview PDF if all forms completed
        if ($completedForms->count() === $allForms->count()) {
            if (isset($providedAnswer->property)) {
                dispatch(new GenerateSaleOverviewPdf($providedAnswer->property));
            }
        }

        // Check if any specific logic should happen based on the steps
        if ($steps->whereIn('type', [StepType::BuyerGiftor, StepType::RemortgageGiftor])->count() > 0) {
            $giftorProvidedAnswers = $allProvidedAnswers->filter(function ($providedAnswer) {
                return $providedAnswer->property->type === PropertyType::Purchase
                    ? $providedAnswer->answer->step->type === StepType::BuyerGiftor
                    : $providedAnswer->answer->step->type === StepType::RemortgageGiftor;
            });

            $giftors = [];

            $giftorProvidedAnswers->each(function ($providedAnswer) use (&$giftors, $repeatableCount, &$propertyId) {
                for ($i = 0; $i < $repeatableCount; $i++) {
                    switch ($providedAnswer->answer->details->label) {
                        case 'Title':
                            $giftors[$i]['title'] = $providedAnswer->value[$i];
                            break;
                        case 'First name':
                            $giftors[$i]['first_name'] = $providedAnswer->value[$i];
                            break;
                        case 'Middle name(s)':
                            $giftors[$i]['middle_name'] = $providedAnswer->value[$i];
                            break;
                        case 'Surname':
                            $giftors[$i]['surname'] = $providedAnswer->value[$i];
                            break;
                        case 'Address':
                            $giftors[$i]['address'] = $providedAnswer->value[$i];
                            break;
                        case 'Phone number':
                            $giftors[$i]['phone'] = $providedAnswer->value[$i];
                            break;
                        case 'Email address':
                            $giftors[$i]['email'] = $providedAnswer->value[$i];
                            break;
                    }

                    $propertyId = $providedAnswer->property_id;
                }
            });

            foreach ($giftors as $giftor) {
                // Creates a Giftor as a User
                $authUser = Auth::user();
                $property = Property::find($propertyId);
                $conveyancer = $property->conveyancer;

                $giftorUser = User::firstOrCreate([
                    'email' => Arr::get($giftor, 'email'),
                ], [
                    'role' => UserRole::Client,
                    'title' => Arr::get($giftor, 'title'),
                    'email' => Arr::get($giftor, 'email'),
                    'first_name' => Arr::get($giftor, 'first_name'),
                    'middle_name' => Arr::get($giftor, 'middle_name'),
                    'last_name' => Arr::get($giftor, 'surname'),
                    'phone' => Arr::get($giftor, 'phone'),
                    'invite_code' => Str::random(32),
                ]);

                // Attaches the giftor to an address
                $giftorUser->address()->updateOrCreate([
                    'line_1' => Arr::get($giftor, 'address.line_1'),
                    'line_2' => Arr::get($giftor, 'address.line_2'),
                    'city' => Arr::get($giftor, 'address.city'),
                    'postcode' => Arr::get($giftor, 'address.postcode'),
                ], [
                    'line_1' => Arr::get($giftor, 'address.line_1'),
                    'line_2' => Arr::get($giftor, 'address.line_2'),
                    'city' => Arr::get($giftor, 'address.city'),
                    'postcode' => Arr::get($giftor, 'address.postcode'),
                ]);

                // Attaches the giftor to the Form Property
                $property->users()->syncWithPivotValues(
                    $giftorUser,
                    ['role' => PropertyUserRole::Giftor],
                    false,
                );

                if ($giftorUser->email_verified_at === null) {
                    $giftorUser->notify(new InviteClient($giftorUser, $authUser, $property->address, $conveyancer));
                }
            }
        }

        // Return data
        $property = $properties[0];

        return (new MyProgress)($property, [], $context, $resolveInfo);
    }

    protected function deleteExistingAnswers(Answer|Step|Form|Section $conditionable, ProvidedAnswer $providedAnswer)
    {
        if (is_a($conditionable, Answer::class)) {
            ProvidedAnswer::query()
                ->where('active_form_id', $providedAnswer->active_form_id)
                ->where('property_id', $providedAnswer->property_id)
                ->where('answer_id', $conditionable->id)
                ->delete();
        } elseif (is_a($conditionable, Step::class)) {
            ProvidedAnswer::query()
                ->where('active_form_id', $providedAnswer->active_form_id)
                ->where('property_id', $providedAnswer->property_id)
                ->whereIn('answer_id', $conditionable->answers->pluck('id'))
                ->delete();
        } elseif (is_a($conditionable, Form::class)) {
            ProvidedAnswer::query()
                ->where('active_form_id', $providedAnswer->active_form_id)
                ->where('property_id', $providedAnswer->property_id)
                ->whereIn('answer_id', $conditionable->answers->pluck('id'))
                ->delete();

            $providedAnswer->property->activeForms()->detach($conditionable->id);
        } elseif (is_a($conditionable, Section::class)) {
            $answers = $conditionable->steps->map(fn ($step) => $step->answers->pluck('id'))->flatten();

            ProvidedAnswer::query()
                ->where('active_form_id', $providedAnswer->active_form_id)
                ->where('property_id', $providedAnswer->property_id)
                ->whereIn('answer_id', $answers)
                ->delete();
        }
    }
}
