<?php

namespace App\Services;

use App\Enums\AnswerType;
use App\Enums\FormType;
use App\Enums\OverviewPdfField;
use App\Enums\StepType;
use App\Models\Form;
use App\Models\ProvidedAnswer;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;

class OverviewPdfPersonService
{
    private OverviewPdfService $service;

    private Form $form;

    private EloquentCollection $personProvidedAnswers;

    private EloquentCollection $peopleStepAnswers;

    private int $index;

    public function __construct(
        OverviewPdfService $overviewPdfService,
        Form $form,
        EloquentCollection $personProvidedAnswers,
        EloquentCollection $peopleStepAnswers,
        int $index,
    ) {
        $this->service = $overviewPdfService;

        $this->form = $form;

        $this->personProvidedAnswers = $personProvidedAnswers;
        $this->peopleStepAnswers = $peopleStepAnswers;

        $this->index = $index;
    }

    public function getOverviewAttributes()
    {
        $overview = [
            'name' => collect([
                $this->service->getValues((clone $this->peopleStepAnswers), 'pdf', OverviewPdfField::Title)->get($this->index),
                $this->service->getValues((clone $this->peopleStepAnswers), 'pdf', OverviewPdfField::FirstName)->get($this->index),
                $this->service->getValues((clone $this->peopleStepAnswers), 'pdf', OverviewPdfField::MiddleName)->get($this->index),
                $this->service->getValues((clone $this->peopleStepAnswers), 'pdf', OverviewPdfField::Surname)->get($this->index),
                $this->service->getValues((clone $this->peopleStepAnswers), 'pdf', OverviewPdfField::CompanyName)->get($this->index),
            ])->filter()->join(' '),

            'name_change' => $this->service->getValues((clone $this->personProvidedAnswers), 'pdf', OverviewPdfField::NameChange)->first(),
            'name_change_reason' => $this->service->getValues((clone $this->personProvidedAnswers), 'pdf', OverviewPdfField::NameChangeReason)->first(),
            'name_change_proof' => $this->service->getValues((clone $this->personProvidedAnswers), 'pdf', OverviewPdfField::NameChangeProof)->first(),

            'status' => $this->service->getValues((clone $this->personProvidedAnswers), 'pdf', OverviewPdfField::Representation)->first(),

        ];

        if ($this->form->ta_form_template === FormType::Company) {
            $overview = [
                ...$overview,
                'company_number' => $this->service->getValues((clone $this->peopleStepAnswers), 'pdf', OverviewPdfField::CompanyNumber)->get($this->index),
                'vat_status' => $this->service->getValues((clone $this->personProvidedAnswers), 'pdf', OverviewPdfField::VatRegistered)->first(),
                'vat_number' => $this->service->getValues((clone $this->personProvidedAnswers), 'pdf', OverviewPdfField::VatNumber)->first(),
            ];
        }

        if ($this->form->ta_form_template === FormType::Individual) {
            $overview = [
                ...$overview,
                'national_insurance' => $this->service->getValues((clone $this->peopleStepAnswers), 'pdf', OverviewPdfField::NationalInsurance)->get($this->index),
                'occupation' => $this->service->getValues((clone $this->peopleStepAnswers), 'pdf', OverviewPdfField::Occupation)->get($this->index),
                'date_of_birth' => $this->service->getValues((clone $this->peopleStepAnswers), 'pdf', OverviewPdfField::DateOfBirth)->get($this->index),
            ];
        }

        return $overview;
    }

    public function getContactDetailsAttributes()
    {
        $contactDetails = [];

        if ($this->form->ta_form_template === FormType::Company) {
            $contactDetails = [
                'address' => $this->service->getValues((clone $this->personProvidedAnswers), 'pdf', OverviewPdfField::Address)->first(),
                'email' => $this->service->getValues((clone $this->personProvidedAnswers), 'pdf', OverviewPdfField::Email)->first(),
                'phone' => $this->service->getValues((clone $this->personProvidedAnswers), 'pdf', OverviewPdfField::Phone)->first(),
            ];
        }

        if ($this->form->ta_form_template === FormType::Individual) {
            $contactDetails = [
                'address' => $this->service->formatAsAddress(
                    $this->service->getValues((clone $this->peopleStepAnswers), 'pdf', OverviewPdfField::Address)->get($this->index)
                ),
                'email' => $this->service->getValues((clone $this->peopleStepAnswers), 'pdf', OverviewPdfField::Email)->get($this->index),
                'phone' => $this->service->getValues((clone $this->peopleStepAnswers), 'pdf', OverviewPdfField::Phone)->get($this->index),
                'phone_alt' => $this->service->getValues((clone $this->peopleStepAnswers), 'pdf', OverviewPdfField::AltPhone)->get($this->index),
                'post_address' => $this->service->formatAsAddress(
                    $this->service->getValues((clone $this->peopleStepAnswers), 'pdf', OverviewPdfField::PostCompletionAddress)->get($this->index),
                ),
            ];
        }

        return $contactDetails;
    }

    public function getRepresentationAttributes()
    {
        $representation = [
            'representation' => $this->service->getValues((clone $this->personProvidedAnswers), 'pdf', OverviewPdfField::Representation)->first(),
            'authority' => $this->service->getValues((clone $this->personProvidedAnswers), 'pdf', OverviewPdfField::Authority)->first(),
            'application_status' => $this->service->getValues((clone $this->personProvidedAnswers), 'pdf', OverviewPdfField::Application)->first() === 'Ongoing application'
                ? 'Ongoing application'
                : 'Complete',
        ];

        $representatives = [];

        if ($this->form->ta_form_template === FormType::Company) {
            $representatives = [];

            $companyRepresentatives = (clone $this->personProvidedAnswers)
                ->where(fn ($pa) => ! is_null($pa->value))
                ->where(fn ($pa) => $pa->answer->step->type === StepType::CompanyRepresentative)
                ->groupBy('answer.step_id')
                ->values();

            $companyRepresentatives->each(function ($companyRepresentative, $index) use (&$representatives) {
                $addressValue = $this->service->getValues((clone $companyRepresentative), 'pdf', OverviewPdfField::RepresentativeUseSaleAddress.$index)->first() === '1'
                    ? 'Same as sale address'
                    : $companyRepresentative->firstWhere('answer.type', AnswerType::Address)?->value;

                $representatives[$index] = [
                    'name' => collect([
                        $this->service->getValues((clone $companyRepresentative), 'label', 'Title')->first(),
                        $this->service->getValues((clone $companyRepresentative), 'label', 'First name')->first(),
                        $this->service->getValues((clone $companyRepresentative), 'label', 'Middle name(s)')->first(),
                        $this->service->getValues((clone $companyRepresentative), 'label', 'Surname')->first(),
                    ])->filter()->join(' '),
                    'email' => $this->service->getValues((clone $companyRepresentative), 'label', 'Email address')->first(),
                    'phone' => $this->service->getValues((clone $companyRepresentative), 'label', 'Phone number')->first(),
                    'address' => is_string($addressValue)
                        ? $addressValue
                        : collect([
                            Arr::get($addressValue, 'line_1'),
                            Arr::get($addressValue, 'line_2'),
                            Arr::get($addressValue, 'city'),
                            Arr::get($addressValue, 'postcode'),
                        ])->filter()->join(', '),
                    'representation' => $this->service->getValues((clone $this->personProvidedAnswers), 'pdf', OverviewPdfField::RepresentativeRepresentation.$index)->first(),
                    'application_status' => $this->service->getValues((clone $this->personProvidedAnswers), 'pdf', OverviewPdfField::RepresentativeApplication.$index)->first() === 'Ongoing application'
                        ? 'Ongoing application'
                        : 'Complete',
                    'authority' => $this->service->getValues((clone $this->personProvidedAnswers), 'pdf', OverviewPdfField::RepresentativeAuthority.$index)->first() ?? 'N/A',
                    'representatives' => $this->getRepresentativeRepresentation($index),
                ];
            });
        }

        if ($this->form->ta_form_template === FormType::Individual) {
            $individualRepresentatives = (clone $this->personProvidedAnswers)
                ->where(fn ($pa) => ! is_null($pa->value))
                ->where(fn ($pa) => in_array($pa->answer->step->type, [
                    StepType::OwnerFormPowerOfAttorney,
                    StepType::DeputyDropdown,
                    StepType::Attorney,
                    StepType::Deputy,
                ]));

            if (is_array($individualRepresentatives->first())) {
                $representatives = $individualRepresentatives->reduce(function ($carry, $pa) {
                    collect($pa->value)->map(function ($value, $index) use ($pa, &$carry) {
                        $this->getRepresentativeDetails($carry, $pa, $index, $value);
                    });

                    return $carry;
                }, []);
            } else {
                $representatives = $individualRepresentatives->reduce(function ($carry, $pa) {
                    $this->getRepresentativeDetails($carry, $pa, 0, $pa->value);

                    return $carry;
                }, []);
            }
        }

        return [
            ...$representation,
            'representatives' => $representatives,
        ];
    }

    private function getRepresentativeDetails(
        array &$carry,
        ProvidedAnswer $pa,
        int $index,
        $value,
    ) {
        switch ($pa->answer->getDetails($this->service->property)?->label) {
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

        return $carry;
    }

    private function getRepresentativeRepresentation(int $index)
    {
        $representativeNameChange = $this->service->getValues((clone $this->personProvidedAnswers), 'pdf', OverviewPdfField::RepresentativeRepresentativesNameChange.$index);
        $representativeNameChangeReason = $this->service->getValues((clone $this->personProvidedAnswers), 'pdf', OverviewPdfField::RepresentativeRepresentativesNameChangeReason.$index);
        $representativeNameChangeProof = $this->service->getValues((clone $this->personProvidedAnswers), 'pdf', OverviewPdfField::RepresentativeRepresentativesNameChangeProof.$index);

        $companyRepresentatives = (clone $this->personProvidedAnswers)
            ->where(fn ($pa) => in_array($pa->answer->step->type, [
                StepType::CompanyFormPowerOfAttorneyRepresentative,
                StepType::CompanyFormDeputyshipOrderRepresentative,
                StepType::CompanyFormGrantOfProbateRepresentative,
                StepType::Attorney,
                StepType::Deputy,
            ]))
            ->where(fn ($pa) => $pa->answer->getDetails($this->service->property)?->pdfFormFieldName === OverviewPdfField::RepresentativeRepresentatives.$index);

        if (is_array($companyRepresentatives->first()->value)) {
            $representatives = $companyRepresentatives->reduce(function ($carry, $pa) use ($representativeNameChange, $representativeNameChangeReason, $representativeNameChangeProof) {
                collect($pa->value)->map(function ($value, $index) use ($pa, &$carry, $representativeNameChange, $representativeNameChangeReason, $representativeNameChangeProof) {
                    $this->getRepresentativeDetails($carry, $pa, $index, $value);

                    $carry[$index]['name_change'] = $representativeNameChangeReason->get($index) === 'Not applicable'
                        ? 'N/A'
                        : $representativeNameChange->first() ?? 'N/A';
                    $carry[$index]['name_change_reason'] = $representativeNameChangeReason->get($index);
                    $carry[$index]['name_change_proof'] = $representativeNameChangeProof->get($index);
                });

                return $carry;
            }, []);
        } else {
            $representatives = $companyRepresentatives->reduce(function ($carry, $pa) use ($representativeNameChange, $representativeNameChangeReason, $representativeNameChangeProof) {
                $this->getRepresentativeDetails($carry, $pa, 0, $pa->value);

                $carry[0]['name_change'] = $representativeNameChangeReason->get(0) === 'Not applicable'
                ? 'N/A'
                : $representativeNameChange->first() ?? 'N/A';
                $carry[0]['name_change_reason'] = $representativeNameChangeReason->get(0);
                $carry[0]['name_change_proof'] = $representativeNameChangeProof->get(0);

                return $carry;
            }, []);
        }

        return $representatives ?? [];
    }
}
