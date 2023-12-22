<?php

namespace App\Observers;

use App\Enums\AnswerType;
use App\Enums\ConditionType;
use App\Enums\OverviewPdfField;
use App\Enums\StepType;
use App\Enums\ValidationRules;
use App\Models\Step;
use App\Services\StepAnswerGeneration\StepAnswerGeneration;

class StepObserver
{
    public function created(Step $step)
    {
        // Completely custom, we don't need to create anything
        if ($step->type === StepType::Custom) {
            return;
        }

        switch ($step->type) {
            case StepType::OwnerName:
                $this->createdOwnerName($step);
                break;
            case StepType::Address:
                $this->createdAddress($step);
                break;
            case StepType::Tenure:
                $this->createdTenure($step);
                break;
            case StepType::EstateAgent:
                $this->createdEstateAgent($step);
                break;
            case StepType::SoldStatus:
                $this->createdSoldStatus($step);
                break;
            case StepType::Seller:
                $this->createdSeller($step);
                break;
            case StepType::Buyer:
                $this->createdBuyer($step);
                break;
            case StepType::BuyerExpanded:
                $this->createdBuyerExpanded($step);
                break;
            case StepType::SalePrice:
                $this->createdSalePrice($step);
                break;
            case StepType::BuyersSolicitor:
                $this->createdBuyersSolicitor($step);
                break;
            case StepType::Charges:
                $this->createdCharges($step);
                break;
            case StepType::Loaner:
                $this->createdLoaner($step);
                break;
            case StepType::RemortgageGiftor:
                $this->createdRemortgageGiftor($step);
                break;
            case StepType::BuyerGiftor:
                $this->createdBuyerGiftor($step);
                break;
            case StepType::SDLT:
                $this->createdSDLT($step);
                break;
            case StepType::NameChange:
                $this->createdNameChange($step);
                break;
            case StepType::DeputyDropdown:
                $this->createdDeputyDropdown($step);
                break;
            case StepType::SellersSolicitorSelectable:
                $this->createdSellersSolicitorSelectable($step);
                break;
            case StepType::DirectorDetails:
                StepAnswerGeneration::personInformation(
                    $step,
                    text: 'director',
                    canSelectPreviousPerson: true,
                    canSelectSaleAddress: true,
                    pdfField: '1.2a_directors_of_the_association',
                );
                break;
            case StepType::TA9Secretary:
                StepAnswerGeneration::personInformation(
                    $step,
                    text: 'attorney',
                    canSelectPreviousPerson: true,
                    canSelectSaleAddress: true,
                    pdfField: '1.2b_secretary_of_the_association',
                );
                break;
            case StepType::TA9ManagingAgent:
                StepAnswerGeneration::personInformation(
                    $step,
                    text: 'attorney',
                    canSelectPreviousPerson: true,
                    canSelectSaleAddress: true,
                    pdfField: '1.2c_managing_agent'
                );
                break;
            case StepType::Mortgager:
                $this->createdMortgager($step);
                break;
            case StepType::Owner:
                $this->createdOwner($step);
                break;
            case StepType::MortgageChargeLoan:
                $this->createdMortgageChargeLoan($step);
                break;
            case StepType::SavingsAmount:
                $this->createdSavingsAmount($step);
                break;
            case StepType::MortgageBroker:
                $this->createdMortgageBroker($step);
                break;
            case StepType::OwnerFormPowerOfAttorney:
                StepAnswerGeneration::personInformation(
                    $step,
                    text: 'attorney',
                    canSelectPreviousPerson: true,
                    canSelectSaleAddress: true,
                    pdfField: OverviewPdfField::RepresentativeRepresentatives,
                    useSaleAddressPdfField: OverviewPdfField::RepresentativeRepresentativesUseSaleAddress,
                );
                break;
            case StepType::Attorney:
                StepAnswerGeneration::personInformation(
                    $step,
                    text: 'attorney',
                    canSelectPreviousPerson: true,
                    canSelectSaleAddress: true,
                    pdfField: OverviewPdfField::RepresentativeRepresentatives,
                    useSaleAddressPdfField: OverviewPdfField::RepresentativeRepresentativesUseSaleAddress,
                );
                break;
            case StepType::Deputy:
                StepAnswerGeneration::personInformation(
                    $step,
                    text: 'Deputy',
                    canSelectSaleAddress: true,
                    pdfField: OverviewPdfField::RepresentativeRepresentatives,
                    useSaleAddressPdfField: OverviewPdfField::RepresentativeRepresentativesUseSaleAddress,
                );
            case StepType::CompanyRepresentative:
            case StepType::CompanyFormPowerOfAttorneyRepresentative:
            case StepType::CompanyFormDeputyshipOrderRepresentative:
            case StepType::CompanyFormGrantOfProbateRepresentative:
            case StepType::RepeatableNameChangeAttorney:
            case StepType::RepeatableNameChangeDeputy:
            case StepType::RepeatableNameChangeExecutor:
                // None of these require observed generation as they are made separately
                break;
        }
    }

    /**
     * Type
     * Mortgage lender
     * Mortgage account number
     * Approximate amount outstanding
     * Early repayment charge
     * Approximate amount of early repayment charge
     * Name of chargee/loanee
     * Approximate amount outstanding
     */
    private function createdMortgageChargeLoan(Step $step)
    {
        // Type
        $type = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Type',
                'options' => [
                    ['value' => 'Mortgage'],
                    ['value' => 'Charge'],
                    ['value' => 'Loan'],
                ],
            ],
        ]);

        // Mortgage lender name
        $mortgageLender = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Mortgage Amount',
            ],
        ]);

        $mortgageLender->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Mortgage',
        ]);

        $mortgageLender->validationRules()->create([
            'rule' => 'required',
        ]);

        // Mortgage account number
        $mortgageAccountNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Mortgage account number',
            ],
        ]);

        $mortgageAccountNumber->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Mortgage',
        ]);

        $mortgageAccountNumber->validationRules()->create([
            'rule' => 'required',
        ]);

        // Approximate amount outstanding
        $outstandingMortgage = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Approximate amount outstanding on mortgage',
            ],
        ]);

        $outstandingMortgage->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Mortgage',
        ]);

        $outstandingMortgage->validationRules()->create([
            'rule' => 'required',
        ]);

        // Early repayment charge
        $earlyRepaymentCharge = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Early repayment charge',
                'options' => [
                    ['value' => 'Yes'],
                    ['value' => 'No'],
                    ['value' => 'Not Known'],
                ],
            ],
        ]);

        $earlyRepaymentCharge->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Mortgage',
        ]);

        $earlyRepaymentCharge->validationRules()->create([
            'rule' => 'required',
        ]);

        // Early repayment charge amount
        $outstandingMortgage = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Early repayment charge amount',
            ],
        ]);

        $outstandingMortgage->conditions()->create([
            'answer_id' => $earlyRepaymentCharge->id,
            'selected_value' => 'Yes',
        ]);

        $outstandingMortgage->validationRules()->create([
            'rule' => 'required',
        ]);

        // Name of chargee / loanee
        // TODO Add OR statement to only show this field if type is CHARGE or LOAN
        $nameOfRecipient = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Chargee/loanee name',
            ],
        ]);

        $nameOfRecipient->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Charge',
            'type' => ConditionType::OR,
        ]);

        $nameOfRecipient->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Loan',
            'type' => ConditionType::OR,
        ]);

        $nameOfRecipient->validationRules()->create([
            'rule' => 'required',
        ]);

        // Approximate amount outstanding
        // TODO Add OR statement to only show this field if type is CHARGE or LOAN
        $approximateOutstandingFees = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Approximate charge/loan amount',
            ],
        ]);

        $approximateOutstandingFees->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Charge',
            'type' => ConditionType::OR,
        ]);

        $approximateOutstandingFees->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Loan',
            'type' => ConditionType::OR,
        ]);

        $approximateOutstandingFees->validationRules()->create([
            'rule' => 'required',
        ]);
    }

    /**
     * Title
     * First name
     * Middle name(s)
     * Surname
     * Email address
     * Main contact number
     */
    private function createdOwner(Step $step)
    {
        // Title
        $title = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Title',
                'options' => [
                    ['value' => 'Mr'],
                    ['value' => 'Mrs'],
                    ['value' => 'Miss'],
                    ['value' => 'Ms'],
                ],
            ],
        ]);

        $title->validationRules()->create([
            'rule' => 'required',
        ]);

        // First name
        $firstName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'First name',
            ],
        ]);

        $firstName->validationRules()->create([
            'rule' => 'required',
        ]);

        // Middle name (optional)
        $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Middle name(s)',
            ],
        ]);

        // Surname
        $surname = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Surname',
            ],
        ]);

        $surname->validationRules()->create([
            'rule' => 'required',
        ]);

        // Email
        $email = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Email',
            ],
        ]);

        $email->validationRules()->create([
            'rule' => 'required',
        ]);

        // Contact number
        $contactNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Main contact number',
            ],
        ]);

        $contactNumber->validationRules()->create([
            'rule' => 'required',
        ]);
    }

    /**
     * Title
     * First name
     * Middle name(s)
     * Surname
     * Email
     * Phone number
     * Address
     */
    private function createdAttorney(Step $step)
    {
        // TODO
        // Person dropdown to pick a previously added person

        // Attorney title
        $attorneyTitle = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Title',
                'options' => [
                    ['value' => 'Mr'],
                    ['value' => 'Mrs'],
                    ['value' => 'Miss'],
                    ['value' => 'Ms'],
                ],
            ],
        ]);

        $attorneyTitle->validationRules()->create([
            'rule' => 'required',
        ]);

        // Attorney first name
        $attorneyFirstName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'First name',
            ],
        ]);

        $attorneyFirstName->validationRules()->create([
            'rule' => 'required',
        ]);

        // Attorney middle name
        $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Middle name(s)',
            ],
        ]);

        // Attorney surname
        $attorneySurname = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Surname',
            ],
        ]);

        $attorneySurname->validationRules()->create([
            'rule' => 'required',
        ]);

        // Attorney email address
        $attorneyEmail = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Email address',
            ],
        ]);

        $attorneyEmail->validationRules()->create([
            'rule' => 'required',
        ]);

        // Attorney phone number
        $attorneyPhoneNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Phone number',
            ],
        ]);

        $attorneyPhoneNumber->validationRules()->create([
            'rule' => 'required',
        ]);

        // Attorney address
        $attorneyPhoneNumber = $step->answers()->create([
            'type' => AnswerType::Address,
            'details' => [
                'label' => 'Correspondence address',
            ],
        ]);
    }

    /**
     * Title
     * First name
     * Middle name(s)
     * Surname
     * Address
     * Phone number
     * Email
     * Amount
     */
    private function createdRemortgageGiftor(Step $step)
    {
        // Giftor title
        $answerGiftorTitle = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Title',
                'options' => [
                    ['value' => 'Mr'],
                    ['value' => 'Mrs'],
                    ['value' => 'Miss'],
                    ['value' => 'Ms'],
                ],
            ],
        ]);

        $answerGiftorTitle->validationRules()->create([
            'rule' => 'required',
        ]);

        // Giftor first name
        $answerGiftorFirstName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'First name',
            ],
        ]);

        $answerGiftorFirstName->validationRules()->create([
            'rule' => 'required',
        ]);

        // Giftor middle name
        $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Middle name(s)',
            ],
        ]);

        // Giftor surname
        $answerGiftorSurname = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Surname',
            ],
        ]);

        $answerGiftorSurname->validationRules()->create([
            'rule' => 'required',
        ]);

        // Giftor address
        $answerGiftorPhoneNumber = $step->answers()->create([
            'type' => AnswerType::Address,
            'details' => [
                'label' => 'Address',
            ],
        ]);

        // Giftor phone number
        $answerGiftorPhoneNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Phone number',
            ],
        ]);

        $answerGiftorPhoneNumber->validationRules()->create([
            'rule' => 'required',
        ]);

        // loaner email address
        $answerGiftorEmail = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Email address',
            ],
        ]);

        $answerGiftorEmail->validationRules()->create([
            'rule' => 'required',
        ]);

        // Giftor amount
        $answerGiftorAmount = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Amount being gifted',
            ],
        ]);

        $answerGiftorAmount->validationRules()->create([
            'rule' => 'required',
        ]);
    }

    /**
     * Title
     * First name
     * Middle name(s)
     * Surname
     * Address
     * Phone number
     * Email
     * Amount
     */
    private function createdBuyerGiftor(Step $step)
    {
        // Giftor title
        $answerGiftorTitle = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Title',
                'options' => [
                    ['value' => 'Mr'],
                    ['value' => 'Mrs'],
                    ['value' => 'Miss'],
                    ['value' => 'Ms'],
                ],
            ],
        ]);

        $answerGiftorTitle->validationRules()->create([
            'rule' => 'required',
        ]);

        // Giftor first name
        $answerGiftorFirstName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'First name',
            ],
        ]);

        $answerGiftorFirstName->validationRules()->create([
            'rule' => 'required',
        ]);

        // Giftor middle name
        $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Middle name(s)',
            ],
        ]);

        // Giftor surname
        $answerGiftorSurname = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Surname',
            ],
        ]);

        $answerGiftorSurname->validationRules()->create([
            'rule' => 'required',
        ]);

        // Giftor address
        $answerGiftorPhoneNumber = $step->answers()->create([
            'type' => AnswerType::Address,
            'details' => [
                'label' => 'Address',
            ],
        ]);

        // Giftor phone number
        $answerGiftorPhoneNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Phone number',
            ],
        ]);

        $answerGiftorPhoneNumber->validationRules()->create([
            'rule' => 'required',
        ]);

        // loaner email address
        $answerGiftorEmail = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Email address',
            ],
        ]);

        $answerGiftorEmail->validationRules()->create([
            'rule' => 'required',
        ]);

        // Giftor amount
        $answerGiftorAmount = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Amount being gifted',
            ],
        ]);

        $answerGiftorAmount->validationRules()->create([
            'rule' => 'required',
        ]);
    }

    /**
     * Type
     * Title
     * First name
     * Middle name(s)
     * Surname
     * Phone number
     * Email
     * Company number
     * Company email
     * Loaner amount
     */
    private function createdLoaner(Step $step)
    {
        // Loaner type
        $answerLoanerType = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Type',
                'options' => [
                    ['value' => 'Individual'],
                    ['value' => 'Company'],
                ],
            ],
        ]);

        $answerLoanerType->validationRules()->create([
            'rule' => 'required',
        ]);

        // Loaner title
        $answerLoanerTitle = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Title',
                'options' => [
                    ['value' => 'Mr'],
                    ['value' => 'Mrs'],
                    ['value' => 'Miss'],
                    ['value' => 'Ms'],
                ],
            ],
        ]);

        $answerLoanerTitle->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerLoanerTitle->conditions()->create([
            'answer_id' => $answerLoanerType->id,
            'selected_value' => 'Individual',
        ]);

        // Loaner first name
        $answerLoanerFirstName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'First name',
            ],
        ]);

        $answerLoanerFirstName->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerLoanerFirstName->conditions()->create([
            'answer_id' => $answerLoanerType->id,
            'selected_value' => 'Individual',
        ]);

        // Loaner middle name
        $answerLoanerMiddleName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Middle name(s)',
            ],
        ]);

        $answerLoanerMiddleName->conditions()->create([
            'answer_id' => $answerLoanerType->id,
            'selected_value' => 'Individual',
        ]);

        // Loaner surname
        $answerLoanerSurname = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Surname',
            ],
        ]);

        $answerLoanerSurname->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerLoanerSurname->conditions()->create([
            'answer_id' => $answerLoanerType->id,
            'selected_value' => 'Individual',
        ]);

        // Loaner phone number
        $answerLoanerPhoneNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Phone number',
            ],
        ]);

        $answerLoanerPhoneNumber->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerLoanerPhoneNumber->conditions()->create([
            'answer_id' => $answerLoanerType->id,
            'selected_value' => 'Individual',
        ]);

        // loaner email address
        $answerLoanerEmail = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Email address',
            ],
        ]);

        $answerLoanerEmail->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerLoanerEmail->conditions()->create([
            'answer_id' => $answerLoanerType->id,
            'selected_value' => 'Individual',
        ]);
        // Company name
        $answerLoanerCompanyName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Company name',
            ],
        ]);

        $answerLoanerCompanyName->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerLoanerCompanyName->conditions()->create([
            'answer_id' => $answerLoanerType->id,
            'selected_value' => 'Company',
        ]);

        // Loaner company number
        $answerLoanerCompanyNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Company phone number',
            ],
        ]);

        $answerLoanerCompanyNumber->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerLoanerCompanyNumber->conditions()->create([
            'answer_id' => $answerLoanerType->id,
            'selected_value' => 'Company',
        ]);

        // Loaner company email
        $answerLoanerCompanyNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Company email address',
            ],
        ]);

        $answerLoanerCompanyNumber->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerLoanerCompanyNumber->conditions()->create([
            'answer_id' => $answerLoanerType->id,
            'selected_value' => 'Company',
        ]);

        // Loaner amount
        $answerLoanerAmount = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Amount being loaned',
            ],
        ]);

        $answerLoanerAmount->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerLoanerAmount->conditions()->create([
            'answer_id' => $answerLoanerType->id,
            'selected_value' => 'Company',
        ]);
    }

    private function createdSellersSolicitorSelectable(Step $step)
    {
        StepAnswerGeneration::personInformation(
            $step,
            text: 'attorney',
            canSelectPreviousPerson: true,
        );
    }

    private function createdCharges(Step $step)
    {
        $mortgageTypeAnswer = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Mortgage, charge or loan',
                'placeholder' => 'Select type',
                'options' => [
                    ['value' => 'Mortgage'],
                    ['value' => 'Charge'],
                    ['value' => 'Loan'],
                ],
            ],
        ]);

        $mortgageTypeAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        // Mortgage
        $lenderNameAnswer = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'What is the name of the mortgage lender?',
            ],
        ]);

        $lenderNameAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $lenderNameAnswer->id,
            'answer_id' => $mortgageTypeAnswer->id,
            'selected_value' => 'Mortgage',
        ]);

        $lenderNameAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $mortgageAccountAnswer = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Enter the mortgage account number:',
            ],
        ]);

        $mortgageAccountAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $mortgageAccountAnswer->id,
            'answer_id' => $mortgageTypeAnswer->id,
            'selected_value' => 'Mortgage',
        ]);

        $mortgageAccountAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $mortgageAmountAnswer = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Approximate amount outstanding on mortgage:',
            ],
        ]);

        $mortgageAmountAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $mortgageAmountAnswer->id,
            'answer_id' => $mortgageTypeAnswer->id,
            'selected_value' => 'Mortgage',
        ]);

        $mortgageAmountAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $mortgageEarlyRepaymentAnswer = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Is there an early repayment charge payable?',
                'options' => [
                    ['value' => 'Yes'],
                    ['value' => 'No'],
                ],
            ],
        ]);

        $mortgageEarlyRepaymentAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $mortgageEarlyRepaymentAnswer->id,
            'answer_id' => $mortgageTypeAnswer->id,
            'selected_value' => 'Mortgage',
        ]);

        $mortgageEarlyRepaymentAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $mortgageEarlyRepaymentAmountAnswer = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Approximate amount of early repayment charge:',
            ],
        ]);

        $mortgageEarlyRepaymentAmountAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $mortgageEarlyRepaymentAmountAnswer->id,
            'answer_id' => $mortgageEarlyRepaymentAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $mortgageEarlyRepaymentAmountAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End of Mortgage

        // Loan
        $nameOfBeneficiaryAnswer = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'What is the name of the loanee?',
            ],
        ]);

        $nameOfBeneficiaryAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $nameOfBeneficiaryAnswer->id,
            'answer_id' => $mortgageTypeAnswer->id,
            'selected_value' => 'Loan',
        ]);

        $nameOfBeneficiaryAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $loanAmountAnswer = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Approximate amount outstanding on loan:',
            ],
        ]);

        $loanAmountAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $loanAmountAnswer->id,
            'answer_id' => $mortgageTypeAnswer->id,
            'selected_value' => 'Loan',
        ]);

        $loanAmountAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End of Loan

        // Charge
        $chargeBeneficiaryAnswer = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'What is the name of the chargee?',
            ],
        ]);

        $chargeBeneficiaryAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $chargeBeneficiaryAnswer->id,
            'answer_id' => $mortgageTypeAnswer->id,
            'selected_value' => 'Charge',
        ]);

        $chargeBeneficiaryAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $chargeAmountAnswer = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Approximate amount outstanding on charge:',
            ],
        ]);

        $chargeAmountAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $chargeAmountAnswer->id,
            'answer_id' => $mortgageTypeAnswer->id,
            'selected_value' => 'Charge',
        ]);

        $chargeAmountAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End of Charge
    }

    private function createdBuyersSolicitor(Step $step)
    {
        $answerConveyancerNotKnown = $step->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
            ],
        ]);

        $answerConveyancerName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Company name',
                'placeholder' => 'Enter company name',
            ],
        ]);

        $answerConveyancerName->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerConveyancerName->conditions()->create([
            'answer_id' => $answerConveyancerNotKnown->id,
            'selected_value' => '0',
        ]);

        $answerConveyancerPhoneNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Phone Number',
            ],
        ]);

        $answerConveyancerPhoneNumber->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerConveyancerPhoneNumber->conditions()->create([
            'answer_id' => $answerConveyancerNotKnown->id,
            'selected_value' => '0',
        ]);

        $answerConveyancerAddress = $step->answers()->create([
            'type' => AnswerType::Address,
        ]);

        $answerConveyancerAddress->conditions()->create([
            'answer_id' => $answerConveyancerNotKnown->id,
            'selected_value' => '0',
        ]);
    }

    /**
     * Type
     * Title
     * First name
     * Middle name(s)
     * Surname
     * Company name
     */
    private function createdOwnerName(Step $step)
    {
        // Owner type
        $ownerType = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Owner type',
                'options' => [
                    ['value' => 'Individual'],
                    ['value' => 'Company'],
                ],
                'pdfFormFieldName' => OverviewPdfField::Type,
            ],
        ]);

        $ownerType->validationRules()->create([
            'rule' => ValidationRules::Required,
        ]);

        // Title
        $title = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Title',
                'options' => [
                    ['value' => 'Mr'],
                    ['value' => 'Mrs'],
                    ['value' => 'Ms'],
                    ['value' => 'Miss'],
                ],
                'pdfFormFieldName' => OverviewPdfField::Title,
            ],
        ]);

        $title->conditions()->create([
            'answer_id' => $ownerType->id,
            'selected_value' => 'Individual',
        ]);

        $title->validationRules()->create([
            'rule' => ValidationRules::Required,
        ]);

        // First name
        $firstName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'First name',
                'placeholder' => 'Enter owner\'s first name',
                'pdfFormFieldName' => OverviewPdfField::FirstName,
            ],
        ]);

        $firstName->conditions()->create([
            'answer_id' => $ownerType->id,
            'selected_value' => 'Individual',
        ]);

        $firstName->validationRules()->create([
            'rule' => ValidationRules::Required,
        ]);

        // Middle name
        $middleName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Middle name(s)',
                'placeholder' => 'Enter owner\'s middle name(s)',
                'pdfFormFieldName' => OverviewPdfField::MiddleName,
            ],
        ]);

        $middleName->conditions()->create([
            'answer_id' => $ownerType->id,
            'selected_value' => 'Individual',
        ]);

        // Last name
        $surname = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Surname',
                'placeholder' => 'Enter owner\'s surname',
                'pdfFormFieldName' => OverviewPdfField::Surname,
            ],
        ]);

        $surname->conditions()->create([
            'answer_id' => $ownerType->id,
            'selected_value' => 'Individual',
        ]);

        $surname->validationRules()->create([
            'rule' => ValidationRules::Required,
        ]);

        // Date of Birth
        $dateOfBirth = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Date of birth',
                'placeholder' => 'DD-MM-YYYY',
                'pdfFormFieldName' => OverviewPdfField::DateOfBirth,
            ],
        ]);

        $dateOfBirth->conditions()->create([
            'answer_id' => $ownerType->id,
            'selected_value' => 'Individual',
        ]);

        $dateOfBirth->validationRules()->create([
            'rule' => ValidationRules::Required,
        ]);

        // Occupation
        $occupation = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Occupation',
                'placeholder' => 'Enter owner\'s occupation',
                'pdfFormFieldName' => OverviewPdfField::Occupation,
            ],
        ]);

        $occupation->conditions()->create([
            'answer_id' => $ownerType->id,
            'selected_value' => 'Individual',
        ]);

        $occupation->validationRules()->create([
            'rule' => ValidationRules::Required,
        ]);

        // Main Contact Number
        $mainContactNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Main contact number',
                'placeholder' => 'Enter owner\'s main contact number',
                'pdfFormFieldName' => OverviewPdfField::Phone,
            ],
        ]);

        $mainContactNumber->conditions()->create([
            'answer_id' => $ownerType->id,
            'selected_value' => 'Individual',
        ]);

        $mainContactNumber->validationRules()->create([
            'rule' => ValidationRules::Required,
        ]);

        // Alternative Contact Number
        $alternativeContactNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Alternative contact number',
                'placeholder' => 'Enter owner\'s alternative contact number',
                'pdfFormFieldName' => OverviewPdfField::AltPhone,
            ],
        ]);

        $alternativeContactNumber->conditions()->create([
            'answer_id' => $ownerType->id,
            'selected_value' => 'Individual',
        ]);

        $alternativeContactNumber->validationRules()->create([
            'rule' => ValidationRules::Required,
        ]);

        // Correspondence Address
        $correspondenceAddress = $step->answers()->create([
            'type' => AnswerType::Address,
            'details' => [
                'label' => 'Correspondence address',
            ],
        ]);

        $correspondenceAddress->conditions()->create([
            'answer_id' => $ownerType->id,
            'selected_value' => 'Individual',
        ]);

        $correspondenceAddress->validationRules()->create([
            'rule' => ValidationRules::Required,
        ]);

        // Email address
        $emailAddress = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Email address',
                'placeholder' => 'Enter owner\'s email address',
                'pdfFormFieldName' => OverviewPdfField::Email,
            ],
        ]);

        $emailAddress->conditions()->create([
            'answer_id' => $ownerType->id,
            'selected_value' => 'Individual',
        ]);

        $emailAddress->validationRules()->create([
            'rule' => ValidationRules::Required,
        ]);

        // Company name
        $companyName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Company name',
                'placeholder' => 'Enter company name',
                'pdfFormFieldName' => OverviewPdfField::CompanyName,
            ],
        ]);

        $companyName->conditions()->create([
            'answer_id' => $ownerType->id,
            'selected_value' => 'Company',
        ]);

        $companyName->validationRules()->create([
            'rule' => ValidationRules::Required,
        ]);

        // Company Number
        $companyNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Company number',
                'placeholder' => 'Enter company number',
                'pdfFormFieldName' => OverviewPdfField::CompanyNumber,
            ],
        ]);

        $companyNumber->conditions()->create([
            'answer_id' => $ownerType->id,
            'selected_value' => 'Company',
        ]);

        $companyNumber->validationRules()->create([
            'rule' => ValidationRules::Required,
        ]);
    }

    private function createdAddress(Step $step)
    {
        $answerAddress = $step->answers()->create([
            'type' => AnswerType::Address,
        ]);

        $answerAddress->validationRules()->create([
            'rule' => 'required',
        ]);
    }

    private function createdTenure(Step $step)
    {
        $answerPropertyType = $step->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Freehold'],
                    ['value' => 'Leasehold'],
                    ['value' => 'Commonhold'],
                    ['value' => 'Shared ownership'],
                ],
                'pdfFormFieldName' => OverviewPdfField::Tenure,
            ],
        ]);

        $answerPropertyType->validationRules()->create([
            'rule' => 'required',
        ]);
    }

    /**
     * Estate Agency name
     * Email address
     * Phone number
     * Correspondence address
     */
    private function createdEstateAgent(Step $step)
    {
        $answerEstateAgentName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Estate Agency name',
                'placeholder' => 'Enter representative\'s full name',
            ],
        ]);

        $answerEstateAgentName->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerEstateAgentEmail = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Email address',
                'placeholder' => 'Enter representative\'s email address',
            ],
        ]);

        $answerEstateAgentEmail->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerEstateAgentPhone = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Phone number',
                'placeholder' => '+44 ---- -- -- --',
            ],
        ]);

        $answerEstateAgentPhone->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerEstateAgentAddress = $step->answers()->create([
            'type' => AnswerType::Address,
            'details' => [
                'label' => 'Correspondence address',
            ],
        ]);

        $answerEstateAgentAddress->validationRules()->create([
            'rule' => 'required',
        ]);
    }

    private function createdSoldStatus(Step $step)
    {
        $answerPropertySold = $step->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'],
                    ['value' => 'No'],
                ],
                'pdfFormFieldName' => OverviewPdfField::SaleStatus,
            ],
        ]);

        $answerPropertySold->validationRules()->create([
            'rule' => 'required',
        ]);
    }

    private function createdSalePrice(Step $step)
    {
        $answerPropertySalePrice = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Property sale price',
                'placeholder' => 'e.g. £320000',
            ],
        ]);

        $answerPropertySalePrice->validationRules()->create([
            'rule' => 'required',
        ]);
    }

    /**
     * Type
     * Title
     * First name
     * Middle name(s)
     * Surname
     * Company title
     * Company number
     */
    private function createdBuyer(Step $step)
    {
        $answerBuyerType = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Buyer type',
                'options' => [
                    ['value' => 'Individual'],
                    ['value' => 'Company'],
                ],
            ],
        ]);

        $answerBuyerType->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyerTitle = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Title',
                'options' => [
                    ['value' => 'Mr'],
                    ['value' => 'Mrs'],
                    ['value' => 'Miss'],
                    ['value' => 'Ms'],
                ],
            ],
        ]);

        $answerBuyerTitle->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyerTitle->conditions()->create([
            'answer_id' => $answerBuyerType->id,
            'selected_value' => 'Individual',
        ]);

        $answerBuyerFirstName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'First name',
                'placeholder' => 'Enter owner\'s first name',
            ],
        ]);

        $answerBuyerFirstName->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyerFirstName->conditions()->create([
            'answer_id' => $answerBuyerType->id,
            'selected_value' => 'Individual',
        ]);

        $answerBuyerMiddleName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Middle name(s)',
                'placeholder' => 'Enter owner\'s middle name(s)',
            ],
        ]);

        $answerBuyerMiddleName->conditions()->create([
            'answer_id' => $answerBuyerType->id,
            'selected_value' => 'Individual',
        ]);

        $answerBuyerSurname = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Surname',
                'placeholder' => 'Enter owner\'s surname',
            ],
        ]);

        $answerBuyerSurname->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyerSurname->conditions()->create([
            'answer_id' => $answerBuyerType->id,
            'selected_value' => 'Individual',
        ]);

        $answerBuyerCompanyName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Company name',
                'placeholder' => 'Enter company name',
            ],
        ]);

        $answerBuyerCompanyName->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyerCompanyName->conditions()->create([
            'answer_id' => $answerBuyerType->id,
            'selected_value' => 'Company',
        ]);

        $answerBuyerCompanyNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Company number',
                'placeholder' => 'Enter company number',
            ],
        ]);

        $answerBuyerCompanyNumber->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyerCompanyNumber->conditions()->create([
            'answer_id' => $answerBuyerType->id,
            'selected_value' => 'Company',
        ]);
    }

    private function createdNameChange(Step $step)
    {
        $answerNameChange = $step->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Marriage/Civil Partnership'],
                    ['value' => 'Divorce/Dissolved Civil Partnership'],
                    ['value' => 'Change of name deed'],
                ],
            ],
        ]);

        $answerNameChange->validationRules()->create([
            'rule' => 'required',
        ]);

        $uploadAnswerNameChange = $step->answers()->create([
            'type' => AnswerType::File,
        ]);

        $uploadAnswerNameChange->validationRules()->create([
            'rule' => 'required',
        ]);
    }

    private function createdDeputyDropdown(Step $step)
    {
        StepAnswerGeneration::personInformation(
            $step,
            text: 'Deputy',
            canSelectSaleAddress: true,
            pdfField: OverviewPdfField::RepresentativeRepresentatives,
            useSaleAddressPdfField: OverviewPdfField::RepresentativeRepresentativesUseSaleAddress,
        );
    }

    private function createdSeller(Step $step)
    {
        $answerSellerType = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Type',
                'options' => [
                    ['value' => 'Individual'],
                    ['value' => 'Company'],
                ],
            ],
        ]);

        $answerSellerType->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerSellerTitle = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Title',
                'options' => [
                    ['value' => 'Mr'],
                    ['value' => 'Mrs'],
                    ['value' => 'Miss'],
                    ['value' => 'Ms'],
                ],
            ],
        ]);

        $answerSellerTitle->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerSellerTitle->conditions()->create([
            'answer_id' => $answerSellerType->id,
            'selected_value' => 'Individual',
        ]);

        $answerSellerFirstName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'First name',
            ],
        ]);

        $answerSellerFirstName->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerSellerFirstName->conditions()->create([
            'answer_id' => $answerSellerType->id,
            'selected_value' => 'Individual',
        ]);

        $answerSellerMiddleName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Middle name(s)',
            ],
        ]);

        $answerSellerMiddleName->conditions()->create([
            'answer_id' => $answerSellerType->id,
            'selected_value' => 'Individual',
        ]);

        $answerSellerSurname = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Surname',
            ],
        ]);

        $answerSellerSurname->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerSellerSurname->conditions()->create([
            'answer_id' => $answerSellerType->id,
            'selected_value' => 'Individual',
        ]);

        $answerSellerCompanyName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Company name',
            ],
        ]);

        $answerSellerCompanyName->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerSellerCompanyName->conditions()->create([
            'answer_id' => $answerSellerType->id,
            'selected_value' => 'Company',
        ]);

        $answerSellerCompanyNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Company number',
            ],
        ]);

        $answerSellerCompanyNumber->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerSellerCompanyNumber->conditions()->create([
            'answer_id' => $answerSellerType->id,
            'selected_value' => 'Company',
        ]);
    }

    private function createdBuyerExpanded(Step $step)
    {
        $answerBuyerType = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Buyer type',
                'options' => [
                    ['value' => 'Individual'],
                    ['value' => 'Company'],
                ],
                'pdfFormFieldName' => OverviewPdfField::Type,
            ],
        ]);

        $answerBuyerType->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyerTitle = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Title',
                'options' => [
                    ['value' => 'Mr'],
                    ['value' => 'Mrs'],
                    ['value' => 'Miss'],
                    ['value' => 'Ms'],
                ],
                'pdfFormFieldName' => OverviewPdfField::Title,
            ],
        ]);

        $answerBuyerTitle->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyerTitle->conditions()->create([
            'answer_id' => $answerBuyerType->id,
            'selected_value' => 'Individual',
        ]);

        $answerBuyerFirstName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'First name',
                'placeholder' => 'Enter first name',
                'pdfFormFieldName' => OverviewPdfField::FirstName,
            ],
        ]);

        $answerBuyerFirstName->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyerFirstName->conditions()->create([
            'answer_id' => $answerBuyerType->id,
            'selected_value' => 'Individual',
        ]);

        $answerBuyerMiddleName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Middle name(s)',
                'placeholder' => 'Enter middle name(s)',
                'pdfFormFieldName' => OverviewPdfField::MiddleName,
            ],
        ]);

        $answerBuyerMiddleName->conditions()->create([
            'answer_id' => $answerBuyerType->id,
            'selected_value' => 'Individual',
        ]);

        $answerBuyerSurname = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Surname',
                'placeholder' => 'Enter surname',
                'pdfFormFieldName' => OverviewPdfField::Surname,
            ],
        ]);

        $answerBuyerSurname->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyerSurname->conditions()->create([
            'answer_id' => $answerBuyerType->id,
            'selected_value' => 'Individual',
        ]);

        $answerBuyerDateOfBirth = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Date of Birth',
                'pdfFormFieldName' => OverviewPdfField::DateOfBirth,
            ],
        ]);

        $answerBuyerDateOfBirth->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyerDateOfBirth->conditions()->create([
            'answer_id' => $answerBuyerType->id,
            'selected_value' => 'Individual',
        ]);

        $answerBuyerOccupation = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Occupation',
                'pdfFormFieldName' => OverviewPdfField::Occupation,
            ],
        ]);

        $answerBuyerOccupation->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyerOccupation->conditions()->create([
            'answer_id' => $answerBuyerType->id,
            'selected_value' => 'Individual',
        ]);

        $answerBuyerNIN = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'National Insurance Number',
                'pdfFormFieldName' => OverviewPdfField::NationalInsurance,
            ],
        ]);

        $answerBuyerNIN->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyerNIN->conditions()->create([
            'answer_id' => $answerBuyerType->id,
            'selected_value' => 'Individual',
        ]);

        $answerBuyerMainContactNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Main contact number',
                'pdfFormFieldName' => OverviewPdfField::Phone,
            ],
        ]);

        $answerBuyerMainContactNumber->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyerMainContactNumber->conditions()->create([
            'answer_id' => $answerBuyerType->id,
            'selected_value' => 'Individual',
        ]);

        $answerBuyerAltContactNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Alternative contact number',
                'pdfFormFieldName' => OverviewPdfField::AltPhone,
            ],
        ]);

        $answerBuyerAltContactNumber->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyerAltContactNumber->conditions()->create([
            'answer_id' => $answerBuyerType->id,
            'selected_value' => 'Individual',
        ]);

        $answerBuyerAltCorrespondenceAddress = $step->answers()->create([
            'type' => AnswerType::Address,
            'details' => [
                'label' => 'Correspondence Address',
                'pdfFormFieldName' => OverviewPdfField::Address,
            ],
        ]);

        $answerBuyerAltCorrespondenceAddress->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyerAltCorrespondenceAddress->conditions()->create([
            'answer_id' => $answerBuyerType->id,
            'selected_value' => 'Individual',
        ]);

        $answerBuyerEmail = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Email',
                'pdfFormFieldName' => OverviewPdfField::Email,
            ],
        ]);

        $nextCorrespondenceAddress = $step->answers()->create([
            'type' => AnswerType::Address,
            'details' => [
                'label' => 'Post completion correspondence address',
                'pdfFormFieldName' => OverviewPdfField::PostCompletionAddress,
            ],
        ]);

        $nextCorrespondenceAddress->validationRules()->create([
            'rule' => 'required',
        ]);

        $nextCorrespondenceAddress->conditions()->create([
            'answer_id' => $answerBuyerType->id,
            'selected_value' => 'Individual',
        ]);

        $answerBuyerEmail->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyerEmail->conditions()->create([
            'answer_id' => $answerBuyerType->id,
            'selected_value' => 'Individual',
        ]);

        $answerBuyerCompanyName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Company name',
                'placeholder' => 'Enter company name',
                'pdfFormFieldName' => OverviewPdfField::CompanyName,
            ],
        ]);

        $answerBuyerCompanyName->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyerCompanyName->conditions()->create([
            'answer_id' => $answerBuyerType->id,
            'selected_value' => 'Company',
        ]);

        $answerBuyerCompanyNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Company number',
                'placeholder' => 'Enter company number',
                'pdfFormFieldName' => OverviewPdfField::CompanyNumber,
            ],
        ]);

        $answerBuyerCompanyNumber->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyerCompanyNumber->conditions()->create([
            'answer_id' => $answerBuyerType->id,
            'selected_value' => 'Company',
        ]);
    }

    /**
     * Savings Amount
     */
    private function createdSavingsAmount(Step $step)
    {
        $savingsAmountAnswer = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Savings amount',
            ],
        ]);

        $savingsAmountAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
    }

    /**
     * Type
     * Title
     * First name
     * Middle name(s)
     * Surname
     * Date of Birth
     * Occupation
     * National insurance Number
     * Main contact number
     * Alternative contact number
     * Correspondence address
     * Email address
     * Post completion correspondence address
     * Company name
     * Company number
     */
    private function createdMortgager(Step $step)
    {
        // Type
        $type = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Remortgager type',
                'options' => [
                    ['value' => 'Individual'],
                    ['value' => 'Company'],
                ],
                'pdfFormFieldName' => OverviewPdfField::Type,
            ],
        ]);

        $type->validationRules()->create([
            'rule' => 'required',
        ]);

        // Title
        $title = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Title',
                'options' => [
                    ['value' => 'Mr'],
                    ['value' => 'Mrs'],
                    ['value' => 'Miss'],
                    ['value' => 'Ms'],
                ],
                'pdfFormFieldName' => OverviewPdfField::Title,
            ],
        ]);

        $title->validationRules()->create([
            'rule' => 'required',
        ]);

        $title->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Individual',
        ]);

        // First name
        $firstName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'First name',
                'pdfFormFieldName' => OverviewPdfField::FirstName,
            ],
        ]);

        $firstName->validationRules()->create([
            'rule' => 'required',
        ]);

        $firstName->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Individual',
        ]);

        // Middle name (optional)
        $middleName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Middle name(s)',
                'pdfFormFieldName' => OverviewPdfField::MiddleName,
            ],
        ]);

        $middleName->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Individual',
        ]);

        // Surname
        $surname = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Surname',
                'pdfFormFieldName' => OverviewPdfField::Surname,
            ],
        ]);

        $surname->validationRules()->create([
            'rule' => 'required',
        ]);

        $surname->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Individual',
        ]);

        // Date of birth
        $dateOfBirth = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Date of Birth',
                'pdfFormFieldName' => OverviewPdfField::DateOfBirth,
            ],
        ]);

        $dateOfBirth->validationRules()->create([
            'rule' => 'required',
        ]);

        $dateOfBirth->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Individual',
        ]);

        // Occupation
        $occupation = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Occupation',
                'pdfFormFieldName' => OverviewPdfField::Occupation,
            ],
        ]);

        $occupation->validationRules()->create([
            'rule' => 'required',
        ]);

        $occupation->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Individual',
        ]);

        // National insurance number
        $nationalInsuranceNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'National Insurance Number',
                'pdfFormFieldName' => OverviewPdfField::NationalInsurance,
            ],
        ]);

        $nationalInsuranceNumber->validationRules()->create([
            'rule' => 'required',
        ]);

        $nationalInsuranceNumber->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Individual',
        ]);

        // Contact number
        $contactNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Main contact number',
                'pdfFormFieldName' => OverviewPdfField::Phone,
            ],
        ]);

        $contactNumber->validationRules()->create([
            'rule' => 'required',
        ]);

        $contactNumber->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Individual',
        ]);

        // Alternate contact number
        $altContactNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Alternative contact number',
                'pdfFormFieldName' => OverviewPdfField::AltPhone,
            ],
        ]);

        $altContactNumber->validationRules()->create([
            'rule' => 'required',
        ]);

        $altContactNumber->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Individual',
        ]);

        // Correspondence address
        $correspondenceAddress = $step->answers()->create([
            'type' => AnswerType::Address,
            'details' => [
                'label' => 'Correspondence address',
                'pdfFormFieldName' => OverviewPdfField::Address,
            ],
        ]);

        $correspondenceAddress->validationRules()->create([
            'rule' => 'required',
        ]);

        $correspondenceAddress->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Individual',
        ]);

        // Email address
        $email = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Email',
                'pdfFormFieldName' => OverviewPdfField::Email,
            ],
        ]);

        $email->validationRules()->create([
            'rule' => 'required',
        ]);

        $email->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Individual',
        ]);

        // Post completion correspondence address
        $nextCorrespondenceAddress = $step->answers()->create([
            'type' => AnswerType::Address,
            'details' => [
                'label' => 'Post completion correspondence address',
                'pdfFormFieldName' => OverviewPdfField::PostCompletionAddress,
            ],
        ]);

        $nextCorrespondenceAddress->validationRules()->create([
            'rule' => 'required',
        ]);

        $nextCorrespondenceAddress->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Individual',
        ]);

        // Company name
        $companyName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Company name',
                'pdfFormFieldName' => OverviewPdfField::CompanyName,
            ],
        ]);

        $companyName->validationRules()->create([
            'rule' => 'required',
        ]);

        $companyName->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Company',
        ]);

        // Company number
        $companyNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Company number',
                'pdfFormFieldName' => OverviewPdfField::CompanyNumber,
            ],
        ]);

        $companyNumber->validationRules()->create([
            'rule' => 'required',
        ]);

        $companyNumber->conditions()->create([
            'answer_id' => $type->id,
            'selected_value' => 'Company',
        ]);
    }

    private function createdSDLT(Step $step)
    {
        $answerFirstTimeBuyer = $step->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'label' => 'Whether bought, gifted or inherited, has this buyer ever owned any residential property or land anywhere in the world?',
                'options' => [
                    ['value' => 'Yes'], // Not First Time Buyer
                    ['value' => 'No'], // First Time Buyer
                ],
            ],
        ]);

        $answerFirstTimeBuyer->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerSDLTRate = $step->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'label' => 'After this purchase has completed will the buyer, and their spouses or civil partners, own more than one property worth more than £40,000?',
                'options' => [
                    ['value' => 'Yes'], // Higher Rate
                    ['value' => 'No'], // Lower Rate
                ],
            ],
        ]);

        $answerSDLTRate->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerFirstTimeBuyerRelief = $step->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'label' => 'Will the property be the main residence for this buyer?',
                'options' => [
                    ['value' => 'Yes'], // First time buyer relief
                    ['value' => 'No'], // Not first time buyer relief
                ],
            ],
        ]);

        $answerFirstTimeBuyerRelief->validationRules()->create([
            'rule' => 'required',
        ]);
    }

    public function createdMortgageBroker(Step $step)
    {
        $mortgageBrokerName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Mortgage broker name',
            ],
        ]);

        $mortgageBrokerName->validationRules()->create([
            'rule' => 'required',
        ]);

        $mortgagePhoneNumber = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Phone number',
            ],
        ]);

        $mortgagePhoneNumber->validationRules()->create([
            'rule' => 'required',
        ]);

        $mortgageEmailAddress = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Email address',
            ],
        ]);

        $mortgageEmailAddress->validationRules()->create([
            'rule' => 'required',
        ]);

        $mortgageBrokerAddress = $step->answers()->create([
            'type' => AnswerType::Address,
            'details' => [
                'label' => 'Address',
            ],
        ]);

        $mortgageBrokerAddress->validationRules()->create([
            'rule' => 'required',
        ]);
    }
}
