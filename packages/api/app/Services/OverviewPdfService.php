<?php

namespace App\Services;

use App\Enums\AnswerType;
use App\Enums\FormType;
use App\Enums\OverviewPdfField;
use App\Enums\PropertyType;
use App\Enums\StepType;
use App\Models\Property;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class OverviewPdfService
{
    public Property $property;

    public $providedAnswers;

    public $mortgageProvidedAnswers;

    public function __construct(Property $property)
    {
        $this->property = $property;

        $this->providedAnswers = $property
            ->providedAnswers()
            ->whereHas('answer', function ($query) {
                $query->whereHas('form', function ($query) {
                    $query->where('forms.ta_form_template', FormType::GettingStarted);
                });
            })
            ->get();

        $this->mortgageProvidedAnswers = $property
            ->providedAnswers()
            ->whereHas('answer', function ($query) {
                $query->whereHas('form', function ($query) {
                    $query->where('forms.ta_form_template', FormType::GettingStartedMortgages);
                });
            })
            ->get();
    }

    public function getPdfAttributes()
    {
        return [
            'overview' => $this->getOverviewAttributes(),
            'overview_details' => $this->getOverviewDetailsAttributes(),
            'owners' => $this->getOwnersAttributes(),
            'buyers' => $this->getBuyersAttributes(),
            'people' => $this->getPeopleAttributes(),
            'sellers' => $this->getSellersAttributes(),
            'client_bank_details' => $this->getBankDetailsAttributes(),
            'sdlt_general' => $this->getSdltGeneralAttributes(),
            'related_transactions' => $this->getRelatedTransactionAttributes(),
            'sdlt_client_declaration' => $this->getSdltClientDeclarationAttributes(),
            'further_information' => $this->getFurtherInformationAttributes(),
            'mortgages_charges_loans' => $this->getMortgagesChargesLoansAttributes(),
            'purchase_funds' => $this->getPurchaseFundsAttributes(),
            'current_ownership' => $this->getCurrentOwnershipAttributes(),
        ];
    }

    public function getValues(EloquentCollection $answers, string $searchByType, string|array $searchValue)
    {
        $values = collect();

        $answers = $answers->where(fn ($pa) => ! is_null($pa->value));
        $answers = match ($searchByType) {
            'pdf' => $answers->where(
                fn ($pa) => $pa->answer->type === AnswerType::File
                    ? $pa->answer->getDetails($this->property)?->pdfFormField === $searchValue
                    : $pa->answer->getDetails($this->property)?->pdfFormFieldName === $searchValue
            ),
            'label' => $answers->where(fn ($pa) => $pa->answer->getDetails($this->property)?->label === $searchValue),
            'option' => $answers->where(fn ($pa) => in_array($searchValue, collect($pa->answer->getDetails($this)?->options ?? [])->pluck('value')->toArray())),
            'question' => $answers->where(fn ($pa) => Str::contains($pa->answer->step->question, $searchValue)),
            'step_type' => $answers->where(
                fn ($pa) => is_array($searchValue)
                    ? in_array($pa->answer->step->type, $searchValue)
                    : $pa->answer->step->type === $searchValue
            ),
            default => $answers,
        };

        $answers
            ->pluck('value')
            ->each(function ($answer) use (&$values) {
                if (is_array($answer)) {
                    if (array_key_exists('postcode', $answer)) {
                        $values->push(
                            collect([
                                Arr::get($answer, 'line_1'),
                                Arr::get($answer, 'line_2'),
                                Arr::get($answer, 'city'),
                                Arr::get($answer, 'postcode'),
                            ])->filter()->join(', ')
                        );
                    } else {
                        foreach ($answer as $a) {
                            $values->push($a);
                        }
                    }
                } else {
                    $values->push($answer);
                }
            });

        return $values;
    }

    public function formatAsAddress($address)
    {
        if (is_null($address)) {
            return null;
        }

        if (! is_array($address)) {
            return $address;
        }

        return collect([
            Arr::get($address, 'line_1'),
            Arr::get($address, 'line_2'),
            Arr::get($address, 'city'),
            Arr::get($address, 'postcode'),
        ])->filter()->join(', ');
    }

    private function getOverviewAttributes()
    {
        $overviewAttributes = [
            'address' => $this->property->address->single_line,
            'tenure' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::Tenure)->first(),
        ];

        if ($this->property->type === PropertyType::Purchase || $this->property->type === PropertyType::Remortgage) {
            $overviewAttributes = [
                ...$overviewAttributes,
                'price' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::Price)->first(),
                'property_type' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::PropertyType)->first(),
                'property_sub_type' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::PropertySubType)->first(),
                'current_use' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::CurrentUse)->first(),
                'intended_use' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::IntendedUse)->first(),
                'dependent_on_sale' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::DependentOnSale)->first(),
            ];
        }

        if ($this->property->type === PropertyType::Purchase) {
            $overviewAttributes = [
                ...$overviewAttributes,
                'shared_ownership_percentage' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::SharedOwnershipPercentage)->first(),
                'relationship_to_seller' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::RelationshipToSeller)->first(),
            ];
        }

        return $overviewAttributes;
    }

    private function getOverviewDetailsAttributes()
    {
        return [
            'sale_status' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::SaleStatus)->first(),
            'sale_type' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::PurchaseThroughEstateAgent)->first(),
            'deposit_paid' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::DepositPaid)->first(),
            'deposit_paid_amount' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::DepositPaidAmount)->first(),
            'legal_representation_name' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::LegalRepresentationName)->first(),
            'legal_representation_address' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::LegalRepresentationAddress)->first(),
            'legal_representation_phone' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::LegalRepresentationPhone)->first(),
            'legal_representation_email' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::LegalRepresentationEmail)->first(),
        ];
    }

    private function getOwnersAttributes()
    {
        switch ($this->property->type) {
            case PropertyType::Sale:
                $owners = $this->providedAnswers
                    ->firstWhere('answer.step.type', StepType::OwnerName)
                    ?->answer
                    ?->step
                    ?->getCompiledAnswer($this->property)
                    ?? [];
                $propertyReps = $this->property->getRepresentatives();

                if (count($owners) > 0 && count($propertyReps) > 0) {
                    for ($i = 0; $i < count($owners); $i++) {
                        $owners[$i] = array_merge($owners[$i], $propertyReps[$i]);
                    }
                }

                return $owners;
            case PropertyType::Purchase:
            case PropertyType::Remortgage:
                return [];
        }
    }

    private function getBuyersAttributes()
    {
        $attributes = [
            'buyer_capacity' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::BuyerCapacity)->first(),
            'trust_deed' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::TrustDeed)->first(),
            'trust_deed_details' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::TrustDeedDetails)->first(),
        ];

        switch ($this->property->type) {
            case PropertyType::Sale:
                $buyers = $this->providedAnswers
                    ->firstWhere('answer.step.type', StepType::Buyer)?->answer?->step
                    ?->getCompiledAnswer($this->property) ?? [];

                break;
            case PropertyType::Purchase:
                $buyers = $this->providedAnswers
                    ->firstWhere('answer.step.type', StepType::BuyerExpanded)?->answer?->step
                    ?->getCompiledAnswer($this->property) ?? [];

                $buyerCapacity = Arr::get($attributes, 'buyer_capacity');

                if ($buyerCapacity === 'Tenants in common in unequal shares') {
                    $sharePercentages = $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::SharedOwnershipPercentageValue);
                    $sharePercentages = $sharePercentages->map(fn ($share) => is_null($share) ? 'N/A' : $share);
                } elseif ($buyerCapacity === 'Tenants in common in equal shares') {
                    $sharePercentages = array_fill(0, count($buyers), 'Equal');
                } else {
                    $sharePercentages = array_fill(0, count($buyers), null);
                }

                for ($i = 0; $i < count($buyers); $i++) {
                    $buyers[$i] = $buyers[$i] + [
                        'share' => $sharePercentages[$i],
                    ];
                }

                break;
            case PropertyType::Remortgage:
                $buyers = $this->providedAnswers
                    ->firstWhere('answer.step.type', StepType::Mortgager)?->answer?->step
                    ?->getCompiledAnswer($this->property) ?? [];

                break;
        }

        return [
            ...$attributes,
            'buyers' => $buyers,
        ];
    }

    private function getPeopleAttributes()
    {
        switch ($this->property->type) {
            case PropertyType::Sale:
                $peopleStepAnswers = $this->providedAnswers->where('answer.step.type', StepType::OwnerName);
                break;
            case PropertyType::Purchase:
                $peopleStepAnswers = $this->providedAnswers->where('answer.step.type', StepType::BuyerExpanded);
                break;
            case PropertyType::Remortgage:
                $peopleStepAnswers = $this->providedAnswers->where('answer.step.type', StepType::Mortgager);
                break;
        }

        $personForms = $this->property
            ->activeForms()
            ->whereIn('forms.ta_form_template', [FormType::Individual, FormType::Company])
            ->get();

        $personFormProvidedAnswers = $this->property
            ->providedAnswers()
            ->whereHas('answer', function ($query) {
                $query->whereHas('form', function ($query) {
                    $query->whereIn('forms.ta_form_template', [FormType::Individual, FormType::Company]);
                });
            })
            ->get();

        $people = array_fill(0, count($personForms), []);

        $personForms->each(function ($form, $index) use (&$people, $personFormProvidedAnswers, $peopleStepAnswers) {
            $personProvidedAnswers = (clone $personFormProvidedAnswers)->where('active_form_id', $form->pivot->id);

            $personAttributesService = new OverviewPdfPersonService(
                $this,
                $form,
                $personProvidedAnswers,
                $peopleStepAnswers,
                $index,
            );

            $people[$index] = [
                'type' => $this->getValues((clone $peopleStepAnswers), 'pdf', OverviewPdfField::Type)->get($index),
                'overview' => $personAttributesService->getOverviewAttributes(),
                'contact_details' => $personAttributesService->getContactDetailsAttributes(),
                'representation' => $personAttributesService->getRepresentationAttributes(),
            ];
        });

        return $people;
    }

    private function getSellersAttributes()
    {
        if ($this->property->type === PropertyType::Purchase) {
            $sellers = (clone $this->providedAnswers)->firstWhere(fn ($pa) => $pa->answer->step->type === StepType::Seller)
                ?->answer?->step?->getCompiledAnswer($this->property) ?? [];

            return [
                'solicitor' => [
                    'name' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::SellerCompanyName)->first(),
                    'phone' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::SellerCompanyPhoneNumber)->first(),
                ],
                'sellers' => $sellers,
            ];
        }
    }

    private function getBankDetailsAttributes()
    {
        switch ($this->property->type) {
            case PropertyType::Remortgage:
            case PropertyType::Purchase:
                return [
                    'account_name' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::BuyerAccountName)->first(),
                    'account_number' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::BuyerAccountNumber)->first(),
                    'sort_code' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::BuyerSortCode)->first(),
                ];
        }
    }

    private function getSdltGeneralAttributes()
    {
        switch ($this->property->type) {
            case PropertyType::Purchase:
                return [
                    'property_moveable' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::IsThePropertyMoveable)->first(),
                    'mixture_of_residential_and_non_residential' => $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::MixtureResidentialAndNonResidential)->first(),
                ];
        }
    }

    private function getSdltClientDeclarationAttributes()
    {
        switch ($this->property->type) {
            case PropertyType::Purchase:
                $buyers = (clone $this->providedAnswers)->firstWhere(fn ($pa) => $pa->answer->step->type === StepType::BuyerExpanded)?->answer?->step?->getCompiledAnswer($this->property) ?? [];
                $sdlt = (clone $this->providedAnswers)->firstWhere(fn ($pa) => $pa->answer->step->type === StepType::SDLT)?->answer?->step?->getCompiledAnswer($this->property) ?? [];

                return [
                    'client_declaration' => $sdlt,
                    'buyers' => $buyers,
                ];
        }
    }

    private function getRelatedTransactionAttributes()
    {
        // Sale
        $relatedTransactionNames = $this->getValues((clone $this->mortgageProvidedAnswers), 'pdf', OverviewPdfField::SellerRelatedTransactions);
        $relatedTransactionAddresses = $this->getValues((clone $this->mortgageProvidedAnswers), 'pdf', OverviewPdfField::SellerRelatedTransactionAddresses);

        // Purchase & Remortgage
        $purchaseRepresentation = [];
        $purchaseRepresentation['name'] = $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::LegalRepresentationName)->first();
        $purchaseRepresentation['email'] = $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::LegalRepresentationEmail)->first();
        $purchaseRepresentation['address'] = $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::LegalRepresentationAddress)->first();
        $purchaseRepresentation['phone'] = $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::LegalRepresentationPhone)->first();

        $dependentAddress = $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::DependentOnSaleAddress)->first();

        $transactionData = [];
        if (count($relatedTransactionAddresses) === count($relatedTransactionNames)) {
            for ($index = 0; $index < count($relatedTransactionNames); $index++) {
                $transactionData[] = [
                    'names' => $relatedTransactionNames[$index],
                    'address' => $relatedTransactionAddresses[$index],
                ];
            }
        }

        return [
            'transactions' => $transactionData,
            'purchase_representation' => $purchaseRepresentation,
            'dependant_address' => $dependentAddress,
        ];
    }

    private function getFurtherInformationAttributes()
    {
        if ($this->property->type !== PropertyType::Purchase) {
            return null;
        }

        return $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::FurtherInformation)->first();
    }

    private function getMortgagesChargesLoansAttributes()
    {
        $mortgagesChargesLoans = [];
        $title = '';

        switch ($this->property->type) {
            case PropertyType::Sale:
                $title = 'Mortgages and Related Transactions';
                $mortgagesChargesLoans = (clone $this->mortgageProvidedAnswers)
                    ->firstWhere(fn ($pa) => $pa->answer->step->type === StepType::Charges)
                    ?->answer?->step?->getCompiledAnswer($this->property) ?? [];
                break;
            case PropertyType::Remortgage:
                $title = 'Outstanding mortgages, charges and loans';
                $mortgagesChargesLoans = (clone $this->providedAnswers)
                    ->firstWhere(fn ($pa) => $pa->answer->step->type === StepType::MortgageChargeLoan)
                    ?->answer?->step?->getCompiledAnswer($this->property) ?? [];
                break;
        }

        return [
            'title' => $title,
            'propertyTypes' => PropertyType::asArray(),
            'mortgages' => collect($mortgagesChargesLoans)->filter(fn ($item) => $item['type'] === 'Mortgage')->toArray(),
            'charges' => collect($mortgagesChargesLoans)->filter(fn ($item) => $item['type'] === 'Charge')->toArray(),
            'loans' => collect($mortgagesChargesLoans)->filter(fn ($item) => $item['type'] === 'Loan')->toArray(),
        ];
    }

    private function getPurchaseFundsAttributes()
    {
        switch ($this->property->type) {
            case PropertyType::Purchase:
                $giftors = (clone $this->providedAnswers)->firstWhere(fn ($pa) => $pa->answer->step->type === StepType::BuyerGiftor)?->answer?->step?->getCompiledAnswer($this->property);
                $mortgageLender = $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::MortgageLender)->first();
                $mortgageAmount = $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::MortgageAmount)->first();
                $lenders = (clone $this->providedAnswers)->firstWhere(fn ($pa) => $pa->answer->step->type === StepType::Loaner)?->answer?->step?->getCompiledAnswer($this->property);
                $savings = (clone $this->providedAnswers)->firstWhere(fn ($pa) => $pa->answer->step->type === StepType::SavingsAmount)?->answer?->step?->getCompiledAnswer($this->property);
                $buyers = $this->providedAnswers->firstWhere('answer.step.type', StepType::BuyerExpanded)?->answer?->step?->getCompiledAnswer($this->property) ?? [];
                break;
            case PropertyType::Remortgage:
                $giftors = (clone $this->providedAnswers)->firstWhere(fn ($pa) => $pa->answer->step->type === StepType::RemortgageGiftor)?->answer?->step?->getCompiledAnswer($this->property);
                $mortgageLender = $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::MortgageLender)->first();
                $mortgageAmount = $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::Price)->first();
                $lenders = (clone $this->providedAnswers)->firstWhere(fn ($pa) => $pa->answer->step->type === StepType::Loaner)?->answer?->step?->getCompiledAnswer($this->property);
                $savings = (clone $this->providedAnswers)->firstWhere(fn ($pa) => $pa->answer->step->type === StepType::SavingsAmount)?->answer?->step?->getCompiledAnswer($this->property);
                $buyers = $this->providedAnswers->firstWhere('answer.step.type', StepType::Mortgager)?->answer?->step?->getCompiledAnswer($this->property) ?? [];
                break;
        }

        $other = $this->getValues((clone $this->providedAnswers), 'pdf', OverviewPdfField::PurchaseFundsOther)->first() ?? [];

        $buyerSavings = [];

        $length = max(count($savings), count($buyers));

        for ($index = 0; $index < $length; $index++) {
            $buyer = $buyers[$index] ?? null;
            $saving = $savings[$index] ?? null;

            $buyerSavings[] = [
                'buyer' => $buyer,
                'saving' => $saving,
            ];
        }

        return [
            'mortgage_lender' => $mortgageLender,
            'mortgage_amount' => $mortgageAmount,
            'buyerSavings' => $buyerSavings,
            'loans' => $lenders,
            'giftors' => $giftors,
            'other' => $other,
        ];
    }

    private function getCurrentOwnershipAttributes()
    {
        $owners[] = [];

        switch ($this->property->type) {
            case PropertyType::Remortgage:
                $owners = $this->providedAnswers->firstWhere('answer.step.type', StepType::Owner)->answer->step->getCompiledAnswer($this->property) ?? [];
                break;
            default:
                return null;
                break;
        }

        return [
            'owners' => $owners,
        ];
    }
}
