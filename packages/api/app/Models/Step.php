<?php

namespace App\Models;

use App\Enums\AnswerType;
use App\Enums\StepType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Step extends Model implements Sortable, HasMedia
{
    use HasFactory, SortableTrait, InteractsWithMedia;

    public $sortable = [
        'order_column_name' => 'sort_order',
        'sort_when_creating' => true,
        'sort_on_has_many' => true,
    ];

    /**
     * Get the image
     */
    public function getImageAttribute(): ?Media
    {
        return $this->getFirstMedia('image');
    }

    /**
     * Register the media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
    }

    /**
     * Section relationship
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Answers relationship
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * Repeatable answer relationship
     */
    public function repeatableAnswer()
    {
        return $this->belongsTo(Answer::class, 'repeatable_answer_id');
    }

    /**
     * Conditions relationship
     */
    public function conditions()
    {
        return $this->morphMany(Condition::class, 'conditionable');
    }

    /**
     * Get Compiled Answer
     *
     * @return ?string | ?array
     */
    public function getCompiledAnswer(Property $property)
    {
        switch ($this->type) {
            case StepType::Owner:
                $typeAnswerId = $this->answers->firstWhere('details.label', 'Title')->id;
                $owners = [];

                $answers = $property
                    ->providedAnswers()
                    ->whereHas('answer', function ($query) {
                        $query->where('step_id', $this->id);
                    });

                foreach ($property->providedAnswers->where('answer.id', $typeAnswerId)->values() as $index => $providedAnswer) {
                    $fullName = '';

                    $titles = $this->getValues(clone $answers, 'Title', $property);
                    $firstNames = $this->getValues(clone $answers, 'First name', $property);
                    $middleNames = $this->getValues(clone $answers, 'Middle name(s)', $property);
                    $surnames = $this->getValues(clone $answers, 'Surname', $property);
                    $emails = $this->getValues(clone $answers, 'Email', $property);
                    $phoneNumbers = $this->getValues(clone $answers, 'Main contact number', $property);

                    $length = count($titles);

                    for ($index = 0; $index < $length; $index++) {
                        $fullName = collect([
                            Arr::get($titles, $index),
                            Arr::get($firstNames, $index),
                            Arr::get($middleNames, $index),
                            Arr::get($surnames, $index),
                        ])->filter()->implode(' ');

                        $owners[] = [
                            'full_name' => $fullName,
                            'title' => Arr::get($titles, $index),
                            'first_name' => Arr::get($firstNames, $index),
                            'middle_name' => Arr::get($middleNames, $index),
                            'surname' => Arr::get($surnames, $index),
                            'email' => Arr::get($emails, $index),
                            'phone' => Arr::get($phoneNumbers, $index),
                        ];
                    }
                }

                return $owners;
                break;
            case StepType::OwnerName:
                $typeAnswerId = $this->answers->firstWhere('details.label', 'Owner type')->id;
                $owners = [];

                foreach ($property->providedAnswers->where('answer.id', $typeAnswerId)->values() as $index => $providedAnswer) {
                    $name = '';

                    $answers = $property
                        ->providedAnswers()
                        ->whereHas('answer', function ($query) {
                            $query->where('step_id', $this->id);
                        });

                    $ownerType = $this->getValues(clone $answers, 'Owner type', $property);
                    $titles = $this->getValues(clone $answers, 'Title', $property);
                    $firstNames = $this->getValues(clone $answers, 'First name', $property);
                    $middleNames = $this->getValues(clone $answers, 'Middle name(s)', $property);
                    $surnames = $this->getValues(clone $answers, 'Surname', $property);

                    $companyNames = $this->getValues(clone $answers, 'Company name', $property);

                    $emails = $this->getValues(clone $answers, 'Email address', $property);
                    $phoneNumber = $this->getValues(clone $answers, 'Main contact number', $property);

                    $addresses = $property
                        ->providedAnswers()
                        ->whereHas('answer', function ($query) {
                            $query
                                ->where('step_id', $this->id)
                                ->where('type', AnswerType::Address);
                        })
                        ->first()?->value;

                    $length = max($titles->count(), $companyNames->count());

                    for ($index = 0; $index < $length; $index++) {
                        $name = collect([
                            Arr::get($titles, $index),
                            Arr::get($firstNames, $index),
                            Arr::get($middleNames, $index),
                            Arr::get($surnames, $index),
                            Arr::get($companyNames, $index),
                        ])->filter()->implode(' ');

                        $address = collect([
                            Arr::get($addresses, "$index.line_1"),
                            Arr::get($addresses, "$index.line_2"),
                            Arr::get($addresses, "$index.city"),
                            Arr::get($addresses, "$index.postcode"),
                        ])->filter()->implode(', ');

                        if ($name !== '') {
                            $owners[] = [
                                'name' => $name,
                                'email' => Arr::get($emails, $index),
                                'phone' => Arr::get($phoneNumber, $index),
                                'type' => Arr::get($ownerType, $index),
                                'address' => $address,
                            ];
                        }
                    }
                }

                return $owners;
            case StepType::BuyerGiftor:
            case StepType::RemortgageGiftor:
                $typeAnswerId = $this->answers->firstWhere('details.label', 'Title')->id;
                $giftors = [];

                foreach ($property->providedAnswers->where('answer.id', $typeAnswerId)->values() as $index => $providedAnswer) {
                    $name = '';

                    $answers = $property
                        ->providedAnswers()
                        ->whereHas('answer', function ($query) {
                            $query->where('step_id', $this->id);
                        });

                    $titles = $this->getValues(clone $answers, 'Title', $property);
                    $firstNames = $this->getValues(clone $answers, 'First name', $property);
                    $middleNames = $this->getValues(clone $answers, 'Middle name(s)', $property);
                    $surnames = $this->getValues(clone $answers, 'Surname', $property);
                    $emails = $this->getValues(clone $answers, 'Email address', $property);
                    $phoneNumber = $this->getValues(clone $answers, 'Phone number', $property);
                    $amountBeingLoaned = $this->getValues(clone $answers, 'Amount being loaned', $property);

                    $addresses = $property
                        ->providedAnswers()
                        ->whereHas('answer', function ($query) {
                            $query
                                ->where('step_id', $this->id)
                                ->where('type', AnswerType::Address);
                        })
                        ->first()?->value;

                    $length = $titles->count();

                    for ($index = 0; $index < $length; $index++) {
                        $name = collect([
                            Arr::get($titles, $index),
                            Arr::get($firstNames, $index),
                            Arr::get($middleNames, $index),
                            Arr::get($surnames, $index),
                        ])->filter()->implode(' ');

                        $address = collect([
                            Arr::get($addresses, "$index.line_1"),
                            Arr::get($addresses, "$index.line_2"),
                            Arr::get($addresses, "$index.city"),
                            Arr::get($addresses, "$index.postcode"),
                        ])->filter()->implode(', ');

                        if ($name !== '') {
                            $giftors[] = [
                                'index' => $index,
                                'name' => $name,
                                'email' => Arr::get($emails, $index),
                                'phone' => Arr::get($phoneNumber, $index),
                                'amount_being_loaned' => Arr::get($amountBeingLoaned, $index),
                                'address' => $address,
                                'step_id' => $this->id,
                                'active_form_id' => $providedAnswer->active_form_id,
                            ];
                        }
                    }
                }

                return $giftors;
            case StepType::Buyer:
                $typeAnswerId = $this->answers->firstWhere('details.label', 'Buyer type')->id;
                $buyers = [];

                foreach ($property->providedAnswers->where('answer.id', $typeAnswerId)->values() as $index => $providedAnswer) {
                    $name = '';

                    $answers = $property
                        ->providedAnswers()
                        ->whereHas('answer', function ($query) {
                            $query->where('step_id', $this->id);
                        });

                    $titles = $this->getValues(clone $answers, 'Title', $property);
                    $firstNames = $this->getValues(clone $answers, 'First name', $property);
                    $middleNames = $this->getValues(clone $answers, 'Middle name(s)', $property);
                    $surnames = $this->getValues(clone $answers, 'Surname', $property);
                    $companyNames = $this->getValues(clone $answers, 'Company name', $property);

                    $emails = $this->getValues(clone $answers, 'Email', $property);
                    $phones = $this->getValues(clone $answers, 'Main contact number', $property);

                    $length = max($titles->count(), $companyNames->count());

                    for ($index = 0; $index < $length; $index++) {
                        $name = collect([
                            Arr::get($titles, $index),
                            Arr::get($firstNames, $index),
                            Arr::get($middleNames, $index),
                            Arr::get($surnames, $index),
                            Arr::get($companyNames, $index),
                        ])->filter()->implode(' ');

                        if ($name !== '') {
                            $buyers[] = [
                                'name' => $name,
                                'email' => Arr::get($emails, $index),
                                'phone' => Arr::get($phones, $index),
                            ];
                        }
                    }
                }

                return $buyers;
            case StepType::BuyerExpanded:
                $typeAnswerId = $this->answers->firstWhere('details.label', 'Buyer type')->id;
                $buyers = [];

                foreach ($property->providedAnswers->where('answer.id', $typeAnswerId)->values() as $index => $providedAnswer) {
                    $name = '';

                    $answers = $property
                        ->providedAnswers()
                        ->whereHas('answer', function ($query) {
                            $query->where('step_id', $this->id);
                        });

                    $types = $this->getValues(clone $answers, 'Buyer type', $property);

                    $titles = $this->getValues(clone $answers, 'Title', $property);
                    $firstNames = $this->getValues(clone $answers, 'First name', $property);
                    $middleNames = $this->getValues(clone $answers, 'Middle name(s)', $property);
                    $surnames = $this->getValues(clone $answers, 'Surname', $property);
                    $companyNames = $this->getValues(clone $answers, 'Company name', $property);

                    $addresses = $property
                        ->providedAnswers()
                        ->whereHas('answer', function ($query) {
                            $query
                                ->where('step_id', $this->id)
                                ->where('type', AnswerType::Address);
                        })
                        ->first()?->value;

                    $emails = $this->getValues(clone $answers, 'Email', $property);
                    $phones = $this->getValues(clone $answers, 'Main contact number', $property);

                    $length = max($titles->count(), $companyNames->count());

                    for ($index = 0; $index < $length; $index++) {
                        $name = collect([
                            Arr::get($titles, $index),
                            Arr::get($firstNames, $index),
                            Arr::get($middleNames, $index),
                            Arr::get($surnames, $index),
                            Arr::get($companyNames, $index),
                        ])->filter()->implode(' ');

                        $address = collect([
                            Arr::get($addresses, "$index.line_1"),
                            Arr::get($addresses, "$index.line_2"),
                            Arr::get($addresses, "$index.city"),
                            Arr::get($addresses, "$index.postcode"),
                        ])->filter()->implode(', ');

                        if ($name !== '') {
                            $buyers[] = [
                                'name' => $name,
                                'email' => Arr::get($emails, $index),
                                'phone' => Arr::get($phones, $index),
                                'type' => Arr::get($types, $index),
                                'address' => $address,
                            ];
                        }
                    }
                }

                return $buyers;

            case StepType::Mortgager:
                $typeAnswerId = $this->answers->firstWhere('details.label', 'Remortgager type')->id;
                $buyers = [];

                foreach ($property->providedAnswers->where('answer.id', $typeAnswerId)->values() as $index => $providedAnswer) {
                    $name = '';

                    $answers = $property
                        ->providedAnswers()
                        ->whereHas('answer', function ($query) {
                            $query->where('step_id', $this->id);
                        });

                    $types = $this->getValues(clone $answers, 'Remortgager type', $property);

                    $titles = $this->getValues(clone $answers, 'Title', $property);
                    $firstNames = $this->getValues(clone $answers, 'First name', $property);
                    $middleNames = $this->getValues(clone $answers, 'Middle name(s)', $property);
                    $surnames = $this->getValues(clone $answers, 'Surname', $property);
                    $companyNames = $this->getValues(clone $answers, 'Company name', $property);

                    $addresses = $property
                        ->providedAnswers()
                        ->whereHas('answer', function ($query) {
                            $query
                                ->where('step_id', $this->id)
                                ->where('type', AnswerType::Address);
                        })
                        ->first()?->value;

                    $emails = $this->getValues(clone $answers, 'Email', $property);
                    $phones = $this->getValues(clone $answers, 'Main contact number', $property);

                    $length = max($titles->count(), $companyNames->count());

                    for ($index = 0; $index < $length; $index++) {
                        $name = collect([
                            Arr::get($titles, $index),
                            Arr::get($firstNames, $index),
                            Arr::get($middleNames, $index),
                            Arr::get($surnames, $index),
                            Arr::get($companyNames, $index),
                        ])->filter()->implode(' ');

                        $address = collect([
                            Arr::get($addresses, "$index.line_1"),
                            Arr::get($addresses, "$index.line_2"),
                            Arr::get($addresses, "$index.city"),
                            Arr::get($addresses, "$index.postcode"),
                        ])->filter()->implode(', ');

                        if ($name !== '') {
                            $buyers[] = [
                                'name' => $name,
                                'email' => Arr::get($emails, $index),
                                'phone' => Arr::get($phones, $index),
                                'type' => Arr::get($types, $index),
                                'address' => $address,
                            ];
                        }
                    }
                }

                return $buyers;

            case StepType::EstateAgent:
                $address = $property
                    ->providedAnswers
                    ->firstWhere(fn ($pa) => $pa->answer->getDetails($property)?->label === 'Correspondence address' && $pa->answer->step->id === $this->id)
                    ?->value;

                return [
                    'name' => $property
                        ->providedAnswers
                        ->firstWhere(fn ($pa) => $pa->answer->getDetails($property)?->label === 'Estate Agency name' && $pa->answer->step->id === $this->id)
                        ?->value,
                    'phone' => $property
                        ->providedAnswers
                        ->firstWhere(fn ($pa) => $pa->answer->getDetails($property)?->label === 'Estate Agency name' && $pa->answer->step->id === $this->id)
                        ?->value,
                    'email' => $property
                        ->providedAnswers
                        ->firstWhere(fn ($pa) => $pa->answer->getDetails($property)?->label === 'Email address' && $pa->answer->step->id === $this->id)
                        ?->value,
                    'address' => $address,
                    'address_single_line' => $address
                        // ? collect(array_values(json_decode($address, true)))->filter()->implode(', ')
                        ? null
                        : null,
                ];

            case StepType::BuyersSolicitor:
                $notKnownAnswerId = $this->answers->firstWhere('details.label', 'Not known')->id;
                $notKnownAnswer = $property->providedAnswers->firstWhere('answer.id', $notKnownAnswerId)?->value;

                if (is_null($notKnownAnswer)) {
                    return null;
                }

                if ($notKnownAnswer === 1) {
                    return 'Not known';
                }

                return $property
                    ->providedAnswers
                    ->firstWhere(fn ($pa) => $pa->answer->getDetails($property)?->label === 'Company name' && $pa->answer->step->id === $this->id)
                    ?->value;

            case StepType::CompanyFormPowerOfAttorneyRepresentative:
                $solicitors = [];

                $answers = ProvidedAnswer::query()
                    ->where('property_id', $property->id)
                    ->where('user_id', Auth::id())
                    ->whereDoesntHave('answer', function ($query) {
                        $query
                            ->whereNotIn('type', AnswerType::getGeneratedTypes())
                            ->orWhere('step_id', $this->id);
                    });

                $titles = $this->getValues(clone $answers, 'Title', $property);
                $firstNames = $this->getValues(clone $answers, 'First name', $property);
                $middleNames = $this->getValues(clone $answers, 'Middle name(s)', $property);
                $surnames = $this->getValues(clone $answers, 'Surname', $property);

                for ($index = 0; $index < $titles->count(); $index++) {
                    $value = collect([
                        Arr::get($titles, $index),
                        Arr::get($firstNames, $index),
                        Arr::get($middleNames, $index),
                        Arr::get($surnames, $index),
                    ])->filter()->implode(' ');

                    if ($value !== '') {
                        $solicitors[] = ['value' => $value];
                    }
                }

                return $solicitors;

            case StepType::OwnerFormPowerOfAttorney:
                $attorneys = [];

                $answers = ProvidedAnswer::query()
                    ->where('property_id', $property->id)
                    ->where('user_id', Auth::id())
                    ->whereDoesntHave('answer', function ($query) {
                        $query
                            ->where('type', AnswerType::OwnerDropdown)
                            ->orWhere('step_id', $this->id);
                    });

                $titles = $this->getValues(clone $answers, 'Title', $property);
                $firstNames = $this->getValues(clone $answers, 'First name', $property);
                $middleNames = $this->getValues(clone $answers, 'Middle name(s)', $property);
                $surnames = $this->getValues(clone $answers, 'Surname', $property);

                $companyNames = $this->getValues(clone $answers, 'Company name', $property);

                $length = max($titles->count(), $companyNames->count());

                for ($index = 0; $index < $length; $index++) {
                    $value = collect([
                        Arr::get($titles, $index),
                        Arr::get($firstNames, $index),
                        Arr::get($middleNames, $index),
                        Arr::get($surnames, $index),
                        Arr::get($companyNames, $index),
                    ])->filter()->implode(' ');

                    if ($value !== '') {
                        $attorneys[] = ['value' => $value];
                    }
                }

                return $attorneys;
            case StepType::SellersSolicitorSelectable:
                $solicitors = [];

                $answers = ProvidedAnswer::query()
                    ->where('property_id', $property->id)
                    ->where('user_id', Auth::id())
                    ->whereDoesntHave('answer', function ($query) {
                        $query
                            ->whereIn('type', AnswerType::getGeneratedTypes())
                            ->orWhere('step_id', $this->id);
                    });

                $titles = $this->getValues(clone $answers, 'Title', $property);
                $firstNames = $this->getValues(clone $answers, 'First name', $property);
                $middleNames = $this->getValues(clone $answers, 'Middle name(s)', $property);
                $surnames = $this->getValues(clone $answers, 'Surname', $property);

                for ($index = 0; $index < $titles->count(); $index++) {
                    $value = collect([
                        Arr::get($titles, $index),
                        Arr::get($firstNames, $index),
                        Arr::get($middleNames, $index),
                        Arr::get($surnames, $index),
                    ])->filter()->implode(' ');

                    if ($value !== '') {
                        $solicitors[] = ['value' => $value];
                    }
                }

                return $solicitors;

            case StepType::Loaner:
                $typeAnswerId = $this->answers->firstWhere('details.label', 'Type')->id;
                $lenders = [];

                foreach ($property->providedAnswers->where('answer.id', $typeAnswerId)->values() as $index => $providedAnswer) {
                    $name = '';

                    $answers = $property
                        ->providedAnswers()
                        ->whereHas('answer', function ($query) {
                            $query->where('step_id', $this->id);
                        });

                    $ownerType = $this->getValues(clone $answers, 'Owner type', $property);
                    $titles = $this->getValues(clone $answers, 'Title', $property);
                    $firstNames = $this->getValues(clone $answers, 'First name', $property);
                    $middleNames = $this->getValues(clone $answers, 'Middle name(s)', $property);
                    $surnames = $this->getValues(clone $answers, 'Surname', $property);

                    $companyNames = $this->getValues(clone $answers, 'Company name', $property);

                    $amountBeingLoaned = $this->getValues(clone $answers, 'Amount being loaned', $property);

                    $emails = $this->getValues(clone $answers, 'Email address', $property);
                    $phoneNumber = $this->getValues(clone $answers, 'Phone number', $property);

                    $length = max($titles->count(), $companyNames->count());

                    for ($index = 0; $index < $length; $index++) {
                        $name = collect([
                            Arr::get($titles, $index),
                            Arr::get($firstNames, $index),
                            Arr::get($middleNames, $index),
                            Arr::get($surnames, $index),
                            Arr::get($companyNames, $index),
                        ])->filter()->implode(' ');

                        if ($name !== '') {
                            $lenders[] = [
                                'name' => $name,
                                'email' => Arr::get($emails, $index),
                                'phone' => Arr::get($phoneNumber, $index),
                                'loan_amount' => Arr::get($amountBeingLoaned, $index),
                            ];
                        }
                    }
                }

                return $lenders;

            case StepType::CompanyRepresentative:
                $companyRepresentatives = [];

                $answers = ProvidedAnswer::query()
                    ->where('property_id', $property->id)
                    ->where('user_id', Auth::id())
                    ->whereDoesntHave('answer', function ($query) {
                        $query
                            ->where('type', AnswerType::OwnerDropdown)
                            ->orWhere('step_id', $this->id);
                    });

                $titles = $this->getValues(clone $answers, 'Title', $property);
                $firstNames = $this->getValues(clone $answers, 'First name', $property);
                $middleNames = $this->getValues(clone $answers, 'Middle name(s)', $property);
                $surnames = $this->getValues(clone $answers, 'Surname', $property);

                for ($index = 0; $index < $titles->count(); $index++) {
                    $value = collect([
                        Arr::get($titles, $index),
                        Arr::get($firstNames, $index),
                        Arr::get($middleNames, $index),
                        Arr::get($surnames, $index),
                    ])->filter()->implode(' ');

                    if ($value !== '') {
                        $companyRepresentatives[] = ['value' => $value];
                    }
                }

                return $companyRepresentatives;
            case StepType::MortgageChargeLoan:
                $typeIds = $this->answers->first()->id;
                $charges = [];

                foreach ($property->providedAnswers->where('answer.id', $typeIds)->values() as $index => $providedAnswer) {
                    $types = $property
                        ->providedAnswers
                        ->where(fn ($pa) => $pa->answer->getDetails($property)?->label === 'Type' && $pa->answer->step->id === $providedAnswer->answer->step->id)
                        ->pluck('value');

                    $mortgageLender = $property
                        ->providedAnswers
                        ->where(fn ($pa) => $pa->answer->getDetails($property)?->label === 'Mortgage lender' && $pa->answer->step->id === $providedAnswer->answer->step->id)
                        ->pluck('value');

                    $mortgageAcctNo = $property
                        ->providedAnswers
                        ->where(fn ($pa) => $pa->answer->getDetails($property)?->label === 'Mortgage account number' && $pa->answer->step->id === $providedAnswer->answer->step->id)
                        ->pluck('value');

                    $mortgageOutstanding = $property
                        ->providedAnswers
                        ->where(fn ($pa) => $pa->answer->getDetails($property)?->label === 'Approximate amount outstanding on mortgage' && $pa->answer->step->id === $providedAnswer->answer->step->id)
                        ->pluck('value');

                    $mortgageEarlyRepayment = $property
                        ->providedAnswers
                        ->where(fn ($pa) => $pa->answer->getDetails($property)?->label === 'Early repayment charge' && $pa->answer->step->id === $providedAnswer->answer->step->id)
                        ->pluck('value');

                    $mortgageEarlyRepaymentAmount = $property
                        ->providedAnswers
                        ->where(fn ($pa) => $pa->answer->getDetails($property)?->label === 'Early repayment charge amount' && $pa->answer->step->id === $providedAnswer->answer->step->id)
                        ->pluck('value');

                    $chargeLoaneeName = $property
                        ->providedAnswers
                        ->where(fn ($pa) => $pa->answer->getDetails($property)?->label === 'Chargee/loanee name' && $pa->answer->step->id === $providedAnswer->answer->step->id)
                        ->pluck('value');

                    $approxChargeLoanAmount = $property
                        ->providedAnswers
                        ->where(fn ($pa) => $pa->answer->getDetails($property)?->label === 'Approximate charge/loan amount' && $pa->answer->step->id === $providedAnswer->answer->step->id)
                        ->pluck('value');

                    $length = count($types[$index]);

                    for ($jindex = 0; $jindex < $length; $jindex++) {
                        $charges[] = [
                            'index' => strval($jindex),
                            'active_form_id' => strval($providedAnswer->active_form_id),
                            'step_id' => $providedAnswer->answer->step->id,
                            'type' => $types[$index][$jindex],
                            'chargee' => match ($types[$index][$jindex]) {
                                'Mortgage' => Arr::get($mortgageLender, "$index.$jindex"),
                                'Loan' => Arr::get($chargeLoaneeName, "$index.$jindex"),
                                'Charge' => Arr::get($chargeLoaneeName, "$index.$jindex"),
                                default => null,
                            },
                            'account_number' => $types[$index][$jindex] === 'Mortgage' ? $mortgageAcctNo[$index][$jindex] : null,
                            'amount_outstanding' => match ($types[$index][$jindex]) {
                                'Mortgage' => $mortgageOutstanding[$index][$jindex],
                                'Loan' => Arr::get($approxChargeLoanAmount, "$index.$jindex"),
                                'Charge' => Arr::get($approxChargeLoanAmount, "$index.$jindex"),
                                default => null,
                            },
                            'early_repayment_charge' => $types[$index][$jindex] === 'Mortgage' ? Arr::get($mortgageEarlyRepayment, "$index.$jindex") : null,
                            'approx_repayment_charge' => $types[$index][$jindex] === 'Mortgage' ? Arr::get($mortgageEarlyRepaymentAmount, "$index.$jindex") : null,
                        ];
                    }
                }

                return $charges;
            case StepType::Charges:
                $chargeTypesId = $this->answers->first()->id;
                $charges = [];

                foreach ($property->providedAnswers->where('answer.id', $chargeTypesId)->values() as $index => $providedAnswer) {
                    $types = $property
                        ->providedAnswers
                        ->where(fn ($pa) => $pa->answer->getDetails($property)?->label === 'Mortgage, charge or loan' && $pa->answer->step->id === $providedAnswer->answer->step->id)
                        ->pluck('value');

                    $mortgageLenders = $property
                        ->providedAnswers
                        ->where(fn ($pa) => $pa->answer->getDetails($property)?->label === 'What is the name of the mortgage lender?' && $pa->answer->step->id === $providedAnswer->answer->step->id)
                        ->pluck('value');

                    $mortgageAcctNo = $property
                        ->providedAnswers
                        ->where(fn ($pa) => $pa->answer->getDetails($property)?->label === 'Enter the mortgage account number:' && $pa->answer->step->id === $providedAnswer->answer->step->id)
                        ->pluck('value');

                    $mortgageOutstanding = $property
                        ->providedAnswers
                        ->where(fn ($pa) => $pa->answer->getDetails($property)?->label === 'Approximate amount outstanding on mortgage:' && $pa->answer->step->id === $providedAnswer->answer->step->id)
                        ->pluck('value');

                    $mortgageEarlyRepayment = $property
                        ->providedAnswers
                        ->where(fn ($pa) => $pa->answer->getDetails($property)?->label === 'Is there an early repayment charge payable?' && $pa->answer->step->id === $providedAnswer->answer->step->id)
                        ->pluck('value');

                    $mortgageEarlyRepaymentAmount = $property
                        ->providedAnswers
                        ->where(fn ($pa) => $pa->answer->getDetails($property)?->label === 'Approximate amount of early repayment charge:' && $pa->answer->step->id === $providedAnswer->answer->step->id)
                        ->pluck('value');

                    $beneficiary = $property
                        ->providedAnswers
                        ->where(fn ($pa) => $pa->answer->getDetails($property)?->label === 'What is the name of the loanee?' && $pa->answer->step->id === $providedAnswer->answer->step->id)
                        ->pluck('value');

                    $outstanding = $property
                        ->providedAnswers
                        ->where(fn ($pa) => $pa->answer->getDetails($property)?->label === 'Approximate amount outstanding on loan:' && $pa->answer->step->id === $providedAnswer->answer->step->id)
                        ->pluck('value');

                    $nameOfChargee = $property
                        ->providedAnswers
                        ->where(fn ($pa) => $pa->answer->getDetails($property)?->label === 'What is the name of the chargee?' && $pa->answer->step->id === $providedAnswer->answer->step->id)
                        ->pluck('value');

                    $outstandingAmountLoan = $property
                        ->providedAnswers
                        ->where(fn ($pa) => $pa->answer->getDetails($property)?->label === 'Approximate amount outstanding on charge:' && $pa->answer->step->id === $providedAnswer->answer->step->id)
                        ->pluck('value');

                    $length = count($types[$index]);

                    for ($jindex = 0; $jindex < $length; $jindex++) {
                        $charges[] = [
                            'index' => strval($jindex),
                            'active_form_id' => strval($providedAnswer->active_form_id),
                            'step_id' => $providedAnswer->answer->step->id,
                            'type' => $types[$index][$jindex],
                            'chargee' => match ($types[$index][$jindex]) {
                                'Mortgage' => Arr::get($mortgageLenders, "$index.$jindex"),
                                'Loan' => Arr::get($beneficiary, "$index.$jindex"),
                                'Charge' => Arr::get($nameOfChargee, "$index.$jindex"),
                                default => null,
                            },
                            'account_number' => $types[$index][$jindex] === 'Mortgage' ? $mortgageAcctNo[$index][$jindex] : null,
                            'amount_outstanding' => match ($types[$index][$jindex]) {
                                'Mortgage' => $mortgageOutstanding[$index][$jindex],
                                'Loan' => $outstanding[$index][$jindex],
                                'Charge' => Arr::get($outstandingAmountLoan, "$index.$jindex"),
                                default => null,
                            },
                            'early_repayment_charge' => $types[$index][$jindex] === 'Mortgage' ? Arr::get($mortgageEarlyRepayment, "$index.$jindex") : null,
                            'approx_repayment_charge' => $types[$index][$jindex] === 'Mortgage' ? Arr::get($mortgageEarlyRepaymentAmount, "$index.$jindex") : null,
                        ];
                    }
                }

                return $charges;

            case StepType::MortgageRelatedTransactions:
                $owners = [];

                $answers = ProvidedAnswer::query()
                    ->where('property_id', $property->id)
                    ->where('user_id', Auth::id())
                    ->whereHas('answer', function ($query) {
                        $query
                            ->where(function ($query) {
                                $query
                                    ->whereNotIn('type', AnswerType::getGeneratedTypes())
                                    ->orWhere('step_id', '!=', $this->id);
                            })
                            ->whereHas('step', function ($query) {
                                $query->where('type', StepType::OwnerName);
                            });
                    });

                $titles = $this->getValues(clone $answers, 'Title', $property);
                $firstNames = $this->getValues(clone $answers, 'First name', $property);
                $middleNames = $this->getValues(clone $answers, 'Middle name(s)', $property);
                $surnames = $this->getValues(clone $answers, 'Surname', $property);

                $companyNames = $this->getValues(clone $answers, 'Company name', $property);

                $length = max(
                    $titles->count(),
                    $firstNames->count(),
                    $middleNames->count(),
                    $surnames->count(),
                    $companyNames->count(),
                );

                for ($index = 0; $index < $length; $index++) {
                    $value = collect([
                        Arr::get($titles, $index),
                        Arr::get($firstNames, $index),
                        Arr::get($middleNames, $index),
                        Arr::get($surnames, $index),
                        Arr::get($companyNames, $index),
                    ])->filter()->implode(' ');

                    if ($value !== '') {
                        $owners[] = ['value' => $value];
                    }
                }

                return $owners;

            case StepType::Seller:
                $sellers = [];

                $answers = ProvidedAnswer::query()
                    ->where('property_id', $property->id)
                    ->where('user_id', Auth::id())
                    ->whereHas('answer', function ($query) {
                        $query
                            ->where(function ($query) {
                                $query
                                    ->where('step_id', $this->id);
                            });
                    });

                $titles = $this->getValues(clone $answers, 'Title', $property);
                $firstNames = $this->getValues(clone $answers, 'First name', $property);
                $middleNames = $this->getValues(clone $answers, 'Middle name(s)', $property);
                $surnames = $this->getValues(clone $answers, 'Surname', $property);

                $companyNames = $this->getValues(clone $answers, 'Company name', $property);

                $companyNumbers = $this->getValues(clone $answers, 'Company number', $property);

                $length = max(
                    $titles->count(),
                    $firstNames->count(),
                    $middleNames->count(),
                    $surnames->count(),
                    $companyNames->count(),
                    $companyNumbers->count(),
                );

                for ($index = 0; $index < $length; $index++) {
                    $name = collect([
                        Arr::get($titles, $index),
                        Arr::get($firstNames, $index),
                        Arr::get($middleNames, $index),
                        Arr::get($surnames, $index),
                        Arr::get($companyNames, $index),
                    ])->filter()->implode(' ');

                    if ($name !== '') {
                        $sellers[] = [
                            'name' => $name,
                            'company_number' => Arr::get($companyNumbers, $index),
                        ];
                    }
                }

                return $sellers;

            case StepType::DirectorDetails:
            case StepType::TA9Secretary:
            case StepType::TA9ManagingAgent:
                $people = [];

                $answers = ProvidedAnswer::query()
                    ->where('property_id', $property->id)
                    ->whereHas('answer', function ($query) {
                        $query->whereHas('step', function ($query) {
                            $query->where('id', $this->id);
                        });
                    });

                $people = $this->getValues(clone $answers, 'Select from previously added person', $property);
                $titles = $this->getValues(clone $answers, 'Title', $property);
                $firstNames = $this->getValues(clone $answers, 'First name', $property);
                $middleNames = $this->getValues(clone $answers, 'Middle name(s)', $property);
                $surnames = $this->getValues(clone $answers, 'Surname', $property);
                $emails = $this->getValues(clone $answers, 'Email address', $property);
                $phones = $this->getValues(clone $answers, 'Phone number', $property);
                $addresses = $this->getValues(clone $answers, 'Correspondence address', $property);
                $sameAsPropertyAddresses = $this->getValues(clone $answers, 'Same as the sale address', $property);

                $length = max(
                    $people->count(),
                    $titles->count(),
                    $firstNames->count(),
                    $middleNames->count(),
                    $surnames->count(),
                );

                for ($index = 0; $index < $length; $index++) {
                    $value = collect([
                        Arr::get($people, $index),
                        Arr::get($titles, $index),
                        Arr::get($firstNames, $index),
                        Arr::get($middleNames, $index),
                        Arr::get($surnames, $index),
                    ])->filter()->implode(' ');

                    $address = collect([
                        Arr::get($addresses, "$index.line_1"),
                        Arr::get($addresses, "$index.line_2"),
                        Arr::get($addresses, "$index.city"),
                        Arr::get($addresses, "$index.postcode"),
                    ])->filter()->implode(', ');

                    $sameAsPropertyAddress = boolval(Arr::get($sameAsPropertyAddresses, $index));

                    if ($value !== '') {
                        $people[] = implode(' - ', [
                            $value,
                            Arr::get($emails, $index),
                            Arr::get($phones, $index),
                            $sameAsPropertyAddress
                                ? '(Address: same as sale address)'
                                : "(Address: $address)",
                        ]);
                    }
                }

                return is_array($people) ? $people : $people->toArray();
            case StepType::SDLT:
                $sdlt = [];
                $answers = ProvidedAnswer::query()
                    ->where('property_id', $property->id)
                    ->whereHas('answer', function ($query) {
                        $query->whereHas('step', function ($query) {
                            $query->where('id', $this->id);
                        });
                    });

                $firstTimeBuyer = $this->getValues(clone $answers, 'Whether bought, gifted or inherited, has this buyer ever owned any residential property or land anywhere in the world?', $property);

                $higherOrLower = $this->getValues(clone $answers, 'After this purchase has completed will the buyer, and their spouses or civil partners, own more than one property worth more than £40,000?', $property);

                $firstTimeBuyerRelief = $this->getValues(clone $answers, 'After this purchase has completed will the buyer, and their spouses or civil partners, own more than one property worth more than £40,000?', $property);

                $length = max(
                    $firstTimeBuyer->count(),
                    $higherOrLower->count(),
                    $firstTimeBuyerRelief->count(),
                );

                for ($index = 0; $index < $length; $index++) {
                    $value = collect([
                        Arr::get($firstTimeBuyer, $index),
                        Arr::get($higherOrLower, $index),
                        Arr::get($firstTimeBuyerRelief, $index),
                    ]);

                    if ($value !== '') {
                        $sdlt[] = [
                            'first_time_buyer' => Arr::get($firstTimeBuyer, $index),
                            'higher_or_lower' => Arr::get($higherOrLower, $index),
                            'first_time_buyer_relief' => Arr::get($firstTimeBuyerRelief, $index),
                        ];
                    }
                }

                return $sdlt;
            case StepType::SavingsAmount:
                $savingsAmount = [];

                $answers = ProvidedAnswer::query()
                    ->where('property_id', $property->id)
                    ->whereHas('answer', function ($query) {
                        $query->whereHas('step', function ($query) {
                            $query->where('id', $this->id);
                        });
                    });

                $savingsAmount = $this->getValues(clone $answers, 'Savings amount', $property);

                $length = $savingsAmount->count();

                for ($index = 0; $index < $length; $index++) {
                    $value = collect([
                        Arr::get($savingsAmount, $index),
                    ]);
                }

                return $savingsAmount;

            default:
                return null;
        }
    }

    public function getPdfAnswer(Property $property)
    {
        switch ($this->type) {
            case StepType::MortgageRelatedTransactions:
                $answers = ProvidedAnswer::query()
                    ->where('property_id', $property->id)
                    ->whereHas('answer', function ($query) {
                        $query->whereHas('step', function ($query) {
                            $query->where('type', StepType::MortgageRelatedTransactions);
                        });
                    });

                $names = $this->getValues(clone $answers, 'Related Transaction', $property);

                $addresses = $this->getValues(clone $answers, 'Address', $property);

                $length = max($names->count(), $addresses->count());

                $relatedTransactions = [];

                for ($index = 0; $index < $length; $index++) {
                    $name = collect(Arr::get($names, $index))->filter()->join(', ');
                    $address = collect([
                        Arr::get($addresses, "$index.line_1"),
                        Arr::get($addresses, "$index.line_2"),
                        Arr::get($addresses, "$index.city"),
                        Arr::get($addresses, "$index.postcode"),
                    ])->filter()->join(', ');

                    $relatedTransactions[] = [
                        'name' => $name,
                        'address' => $address,
                    ];
                }

                return $relatedTransactions;
            default:
                return null;
        }
    }

    private function getValues(
        $answers,
        string $label,
        Property $property,
    ) {
        $values = collect();

        $answers
            ->get()
            ->where(fn ($pa) => $pa->answer->getDetails($property)?->label === $label && ! is_null($pa->value))
            ->pluck('value')
            ->each(function ($answer) use (&$values) {
                if (is_array($answer) && ! isset($answer['line_1'])) {
                    foreach ($answer as $a) {
                        $values->push($a);
                    }
                } else {
                    $values->push($answer);
                }
            });

        return $values;
    }
}
