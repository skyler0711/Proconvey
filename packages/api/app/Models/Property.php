<?php

namespace App\Models;

use App\Enums\AnswerType;
use App\Enums\FormType;
use App\Enums\OverviewPdfField;
use App\Enums\StepType;
use App\Services\OverviewPdfService;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class Property extends Model implements HasMedia
{
    use HasFactory, HasRelationships, InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'conveyancer_id',
        'type',
        'case_reference',
        'letters_required',
        'id_check_required',
        'payment_required',
        'payment_amount',
        'sale_price',
        'conveyancing_fee',
        'fee_earner_id',
        'payment_id',
        'billed_for_at',
        'pack_completed_at',
        'sof_check_required',
        'payment_on_account_amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'letters_required' => 'boolean',
        'id_check_required' => 'boolean',
        'payment_required' => 'boolean',
        'billed_for_at' => 'datetime',
    ];

    /**
     * Register the media collections
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('documents');
    }

    /**
     * Get the documents
     */
    public function getDocumentsAttribute(): ?Collection
    {
        return $this->getMedia('documents');
    }

    /**
     * Conveyancer relationship
     */
    public function conveyancer(): BelongsTo
    {
        return $this->belongsTo(Conveyancer::class);
    }

    /**
     * Address relationship
     */
    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    /**
     * Users relationship
     */
    public function users(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class)
            ->withPivot(
                'role',
                'is_primary_user',
                'onboarding_forms_completed_at',
                'payment_on_account_completed_at',
                'sof_completed_at',
                'representation',
            )
            ->using(PropertyUser::class);
    }

    /**
     * Users relationship
     */
    public function feeEarner(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'fee_earner_id');
    }

    /**
     * Provided answers relationship
     */
    public function providedAnswers(): HasMany
    {
        return $this->hasMany(ProvidedAnswer::class);
    }

    /**
     * Get the forms for this property
     */
    public function forms(): HasManyDeep
    {
        return $this
            ->hasManyDeep(
                Form::class,
                [ProvidedAnswer::class, Answer::class, Step::class, Section::class],
                [null, 'id', 'id', 'id', 'id'],
                [null, 'answer_id', 'step_id', 'section_id', 'form_id']
            )
            ->groupBy('forms.id')
            ->groupBy('provided_answers.property_id');
    }

    /**
     * Get the signed forms for this property
     */
    public function signedForms()
    {
        return $this->belongsToMany(Form::class, 'form_property')
            ->withPivot('letters_envelope_id', 'letters_envelope_token');
    }

    /**
     * Active forms relationship
     */
    public function activeForms(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Form::class,
                'active_forms',
                'property_id',
                'form_id',
            )
            ->withPivot(
                'id',
                'title',
            );
    }

    public function getOverviewPdfDetails(): array
    {
        $overviewPdfService = new OverviewPdfService($this);

        return $overviewPdfService->getPdfAttributes();
    }

    /**
     * Get the representatives for this property
     */
    public function getRepresentatives(): array
    {
        $providedAnswers = ProvidedAnswer::query()
            ->whereHas('answer', function ($query) {
                $query->whereHas('form', function ($query) {
                    $query
                        ->whereHas('properties', function ($query) {
                            $query->where('properties.id', $this->id);
                        })
                        ->whereIn('forms.ta_form_template', [FormType::Individual, FormType::Company]);
                });
            });

        $activeForms = $providedAnswers->orderBy('active_form_id')->get()->groupBy('active_form_id');

        $postMap = $activeForms->map(function ($providedAnswers, int $key) {
            $representation = $this->getValues((clone $providedAnswers), 'pdf', OverviewPdfField::Representation, $this);

            if ($providedAnswers->first()->answer->form->ta_form_template === FormType::Individual) {
                return [
                    'representation' => $representation->first(),
                    'application_status' => $this->getValues((clone $providedAnswers), 'pdf', OverviewPdfField::Application, $this)->first() === 'Ongoing application'
                        ? 'Ongoing application'
                        : 'Complete',
                    'authority' => $this->getValues((clone $providedAnswers), 'pdf', OverviewPdfField::Authority, $this)->first() ?? 'N/A',
                    'name_change' => $this->getValues((clone $providedAnswers), 'pdf', OverviewPdfField::NameChange, $this)->first() ?? 'N/A',
                    'name_change_reason' => $this->getValues((clone $providedAnswers), 'pdf', OverviewPdfField::NameChangeReason, $this)->first() ?? 'N/A',
                    'name_change_proof' => $this->getValues((clone $providedAnswers), 'pdf', OverviewPdfField::NameChangeProof, $this)->first() ?? 'N/A',
                    'representatives' => (clone $providedAnswers)
                        ->where(fn ($pa) => ! is_null($pa->value))
                        ->where(fn ($pa) => in_array($pa->answer->step->type, [StepType::OwnerFormPowerOfAttorney, StepType::DeputyDropdown]))
                        ->reduce(function ($carry, $pa) {
                            collect($pa->value)->map(function ($value, $index) use ($pa, &$carry) {
                                switch ($pa->answer->getDetails($this)?->label) {
                                    case 'Title':
                                    case 'First name':
                                    case 'Middle names(s)':
                                    case 'Surname':
                                        $carry[$index]['name'] = ! is_null($value) ? implode(' ', [Arr::get($carry, "$index.name"), $value]) : $carry[$index]['name'];
                                        break;
                                    case 'Email address':
                                        $carry[$index]['email'] = $value;
                                        break;
                                    case 'Phone number':
                                        $carry[$index]['phone'] = $value;
                                        break;
                                    case 'Correspondence address':
                                        $carry[$index]['address'] = collect([
                                            Arr::get($value, 'line_1'),
                                            Arr::get($value, 'line_2'),
                                            Arr::get($value, 'city'),
                                            Arr::get($value, 'postcode'),
                                        ])->filter()->join(', ');
                                        break;
                                    case 'Same as the sale address':
                                        if ($value === '1') {
                                            $carry[$index]['address'] = 'Same as the sale address';
                                        }
                                        break;
                                }
                            });

                            return $carry;
                        }, []),
                ];
            } else {
                $companyRepresentatives = [];

                $reps = (clone $providedAnswers)
                    ->where(fn ($pa) => ! is_null($pa->value))
                    ->where(fn ($pa) => $pa->answer->step->type === StepType::CompanyRepresentative)
                    ->groupBy('answer.step_id')
                    ->values();

                $individualRepresentatives = (clone $providedAnswers)
                    ->where(fn ($pa) => in_array($pa->answer->step->type, [
                        StepType::CompanyFormPowerOfAttorneyRepresentative,
                        StepType::CompanyFormDeputyshipOrderRepresentative,
                        StepType::CompanyFormGrantOfProbateRepresentative,
                    ]));

                $reps->each(function ($rep, $index) use (&$companyRepresentatives, $providedAnswers, $individualRepresentatives) {
                    $addressValue = $rep->firstWhere('answer.type', AnswerType::Address)?->value;

                    $representativeNameChange = $this->getValues((clone $providedAnswers), 'pdf', OverviewPdfField::RepresentativeRepresentativesNameChange.$index, $this);
                    $representativeNameChangeReason = $this->getValues((clone $providedAnswers), 'pdf', OverviewPdfField::RepresentativeRepresentativesNameChangeReason.$index, $this);
                    $representativeNameChangeProof = $this->getValues((clone $providedAnswers), 'pdf', OverviewPdfField::RepresentativeRepresentativesNameChangeProof.$index, $this);

                    $companyRepresentatives[$index] = [
                        'name' => collect([
                            $this->getValues((clone $rep), 'label', 'Title', $this)->first(),
                            $this->getValues((clone $rep), 'label', 'First name', $this)->first(),
                            $this->getValues((clone $rep), 'label', 'Middle name(s)', $this)->first(),
                            $this->getValues((clone $rep), 'label', 'Surname', $this)->first(),
                        ])->filter()->join(' '),
                        'email' => $this->getValues((clone $rep), 'label', 'Email address', $this)->first(),
                        'phone' => $this->getValues((clone $rep), 'label', 'Phone number', $this)->first(),
                        'address' => collect([
                            Arr::get($addressValue, 'line_1'),
                            Arr::get($addressValue, 'line_2'),
                            Arr::get($addressValue, 'city'),
                            Arr::get($addressValue, 'postcode'),
                        ])->filter()->join(', '),
                        'representation' => $this->getValues((clone $providedAnswers), 'pdf', OverviewPdfField::RepresentativeRepresentation.$index, $this)->first(),
                        'application_status' => $this->getValues((clone $providedAnswers), 'pdf', OverviewPdfField::RepresentativeApplication.$index, $this)->first() === 'Ongoing application'
                            ? 'Ongoing application'
                            : 'Complete',
                        'authority' => $this->getValues((clone $providedAnswers), 'pdf', OverviewPdfField::RepresentativeAuthority.$index, $this)->first() ?? 'N/A',
                        'representatives' => $individualRepresentatives
                            ->where(fn ($pa) => $pa->answer->getDetails($this)?->pdfFormFieldName === OverviewPdfField::RepresentativeRepresentatives.$index)
                            ->reduce(function ($carry, $pa) use ($representativeNameChange, $representativeNameChangeReason, $representativeNameChangeProof) {
                                collect($pa->value)->map(function ($value, $index) use ($pa, &$carry, $representativeNameChange, $representativeNameChangeReason, $representativeNameChangeProof) {
                                    switch ($pa->answer->getDetails($this)?->label) {
                                        case 'Title':
                                        case 'First name':
                                        case 'Middle names(s)':
                                        case 'Surname':
                                            $carry[$index]['name'] = ! is_null($value) ? implode(' ', [Arr::get($carry, "$index.name"), $value]) : $carry[$index]['name'];
                                            break;
                                        case 'Email address':
                                            $carry[$index]['email'] = $value;
                                            break;
                                        case 'Phone number':
                                            $carry[$index]['phone'] = $value;
                                            break;
                                        case 'Correspondence address':
                                            $carry[$index]['address'] = collect([
                                                Arr::get($value, 'line_1'),
                                                Arr::get($value, 'line_2'),
                                                Arr::get($value, 'city'),
                                                Arr::get($value, 'postcode'),
                                            ])->filter()->join(', ');
                                            break;
                                        case 'Same as the sale address':
                                            if ($value === '1') {
                                                $carry[$index]['address'] = 'Same as the sale address';
                                            }
                                            break;
                                    }

                                    $carry[$index]['name_change'] = $representativeNameChangeReason->get($index) === 'Not applicable'
                                        ? 'N/A'
                                        : $representativeNameChange->first() ?? 'N/A';
                                    $carry[$index]['name_change_reason'] = $representativeNameChangeReason->get($index);
                                    $carry[$index]['name_change_proof'] = $representativeNameChangeProof->get($index);
                                });

                                return $carry;
                            }, []),
                    ];
                });

                $addressItems = $this->getValues((clone $providedAnswers), 'pdf', OverviewPdfField::Address, $this)->first();

                return [
                    'representation' => $representation->first(),
                    'company_number' => $this->getValues((clone $providedAnswers), 'pdf', OverviewPdfField::CompanyNumber, $this)->first(),
                    'address' => collect([
                        Arr::get($addressItems, 'line_1'),
                        Arr::get($addressItems, 'line_2'),
                        Arr::get($addressItems, 'city'),
                        Arr::get($addressItems, 'postcode'),
                    ])->filter()->join(', '),
                    'email' => $this->getValues((clone $providedAnswers), 'pdf', OverviewPdfField::Email, $this)->first(),
                    'phone' => $this->getValues((clone $providedAnswers), 'pdf', OverviewPdfField::Phone, $this)->first(),
                    'name_change' => $this->getValues((clone $providedAnswers), 'pdf', OverviewPdfField::NameChange, $this)->first() ?? 'N/A',
                    'name_change_proof' => $this->getValues((clone $providedAnswers), 'pdf', OverviewPdfField::NameChangeProof, $this)->first() ?? 'N/A',
                    'vat_status' => $this->getValues((clone $providedAnswers), 'pdf', OverviewPdfField::VatRegistered, $this)->first() ?? 'N/A',
                    'vat_number' => $this->getValues((clone $providedAnswers), 'pdf', OverviewPdfField::VatNumber, $this)->first() ?? 'N/A',
                    'representatives' => $companyRepresentatives,
                ];
            }
        });

        $returnVal = array_values($postMap->toArray());

        return $returnVal;
    }

    private function getValues(
        EloquentCollection $answers,
        string $searchByType,
        string|array $searchValue,
        Property $property,
    ) {
        $values = collect();

        $answers = $answers->where(fn ($pa) => ! is_null($pa->value));
        $answers = match ($searchByType) {
            'pdf' => $answers->where(fn ($pa) => $pa->answer->type === AnswerType::File
                ? $pa->answer->getDetails($property)?->pdfFormField === $searchValue
                : $pa->answer->getDetails($property)?->pdfFormFieldName === $searchValue
            ),
            'label' => $answers->where(fn ($pa) => $pa->answer->getDetails($property)?->label === $searchValue),
            'option' => $answers->where(fn ($pa) => in_array($searchValue, collect($pa->answer->getDetails($this)?->options ?? [])->pluck('value')->toArray())),
            'question' => $answers->where(fn ($pa) => Str::contains($pa->answer->step->question, $searchValue)),
            'step_type' => $answers->where(fn ($pa) => is_array($searchValue)
                ? in_array($pa->answer->step->type, $searchValue)
                : $pa->answer->step->type === $searchValue
            ),
            default => $answers,
        };

        $answers
            ->pluck('value')
            ->each(function ($answer) use (&$values) {
                if (is_array($answer)) {
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
