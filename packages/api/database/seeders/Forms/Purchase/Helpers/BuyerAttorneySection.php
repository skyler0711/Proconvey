<?php

namespace Database\Seeders\Forms\Purchase\Helpers;

use App\Enums\AnswerType;
use App\Enums\ConditionType;
use App\Enums\OverviewPdfField;
use App\Enums\StepType;
use App\Models\Section;
use App\Services\StepAnswerGeneration\StepAnswerGeneration;

class BuyerAttorneySection
{
    private $section;

    private string $number;

    public function __construct(Section $section, string $number)
    {
        $this->section = $section;
        $this->number = $number;
    }

    public function createSteps()
    {
        // 1.1 Start of attorney quantity section
        $attorneyQuantityStep = $this->section->steps()->create([
            'question' => 'Please confirm the number of attorneys acting for the above company',
            'help_text' => 'Please indicate the correct count of attorneys listed on the Power of Attorney, even if you are the sole individual handling the property purchase. Your cooperation in providing this information is essential for the proper processing and legal compliance of the transaction.',
        ]);

        $attorneyQuantityAnswer = $attorneyQuantityStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => '1'], // Skip to 1.2b
                    ['value' => '2'], // Continue to 1.2
                    ['value' => '3'], // Continue to 1.2
                    ['value' => '4'], // Continue to 1.2
                    ['value' => '5'], // Continue to 1.2
                ],
            ],
        ]);

        $attorneyQuantityAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 1.2 Attorney Team Status section
        $attorneyTeamStatusStep = $this->section->steps()->create([
            'question' => 'Will the attorneys be required to act jointly or severally?',
            'help_text' => '<p>For the attorneys listed on the Power of Attorney, please specify whether they will be required to act jointly or severally. Here\'s what each option means:</p><p><strong>JOINTLY:</strong></p>Select this option if all attorneys must be involved in making any decision regarding the property. If you choose this, please provide the names of all the attorneys. All attorneys will then be invited to ProConvey.</p><p><strong>SEVERALLY:</strong></p>Choose this option if an attorney can act alone in making decisions without the involvement of other attorneys. If you select this, you do not need to enter the names of the other attorneys.</p><p>By selecting the appropriate option, you will help us ensure an accurate transaction process.</p>',
        ]);

        $attorneyTeamStatusStep->conditions()->create([
            'answer_id' => $attorneyQuantityAnswer->id,
            'selected_value' => '2',
            'type' => ConditionType::OR,
        ]);

        $attorneyTeamStatusStep->conditions()->create([
            'answer_id' => $attorneyQuantityAnswer->id,
            'selected_value' => '3',
            'type' => ConditionType::OR,
        ]);

        $attorneyTeamStatusStep->conditions()->create([
            'answer_id' => $attorneyQuantityAnswer->id,
            'selected_value' => '4',
            'type' => ConditionType::OR,
        ]);

        $attorneyTeamStatusStep->conditions()->create([
            'answer_id' => $attorneyQuantityAnswer->id,
            'selected_value' => '5',
            'type' => ConditionType::OR,
        ]);

        $attorneyTeamStatusAnswer = $attorneyTeamStatusStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Jointly'], // Continue to 1.2a
                    ['value' => 'Severally'], // Continue to 1.2b
                ],
                'pdfFormFieldName' => OverviewPdfField::RepresentativeAuthority.$this->number,
            ],
        ]);

        $attorneyTeamStatusAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 1.2a Attorney Details (Severally) (REPEATABLE, based on 1.1) section
        $attorneyDetailsSeverallyStep = $this->section->steps()->create([
            'type' => StepType::Attorney,
            'question' => 'Please enter the details of each attorney for the above company representative:',
            'help_text' => 'Please provide details and contact information for all attorneys actively involved in the transaction. All attorneys will then be invited to ProConvey. This information is necessary for your conveyancer or solicitor to effectively communicate and coordinate with the attorneys throughout the process. By providing the details of the attorneys, including their names and contact information, your conveyancer or solicitor can ensure smooth collaboration and accurate documentation.',
            'repeatable_answer_id' => $attorneyQuantityAnswer->id,
        ]);

        StepAnswerGeneration::personInformation(
            $attorneyDetailsSeverallyStep,
            text: 'attorney',
            canSelectPreviousPerson: true,
            canSelectSaleAddress: true,
            pdfField: OverviewPdfField::RepresentativeRepresentatives.$this->number,
            useSaleAddressPdfField: OverviewPdfField::RepresentativeRepresentativesUseSaleAddress.$this->number,
        );

        $attorneyDetailsSeverallyStep->conditions()->create([
            'answer_id' => $attorneyTeamStatusAnswer->id,
            'selected_value' => 'Severally',
        ]);
        // End ---

        // 1.2b Attorney Details (Jointly) section
        $attorneyDetailsJointlyStep = $this->section->steps()->create([
            'type' => StepType::Attorney,
            'question' => 'Please enter the details of the attorney who will be completing the sale on behalf of the above company representative:',
            'help_text' => 'Please provide the full current name(s) of the attorney who will be handling the sale on behalf of the buyer. This information is essential for your conveyancer or solicitor to accurately identify and communicate with the designated attorney throughout the transaction. By providing the full name(s) of the attorney, your conveyancer or solicitor can ensure proper documentation and effective collaboration.',
        ]);

        StepAnswerGeneration::personInformation(
            $attorneyDetailsJointlyStep,
            text: 'attorney',
            canSelectPreviousPerson: true,
            canSelectSaleAddress: true,
            pdfField: OverviewPdfField::RepresentativeRepresentatives.$this->number,
            useSaleAddressPdfField: OverviewPdfField::RepresentativeRepresentativesUseSaleAddress.$this->number,
        );

        $attorneyDetailsJointlyStep->conditions()->create([
            'answer_id' => $attorneyTeamStatusAnswer->id,
            'selected_value' => 'Jointly',
            'type' => ConditionType::OR,
        ]);

        $attorneyDetailsJointlyStep->conditions()->create([
            'answer_id' => $attorneyQuantityAnswer->id,
            'selected_value' => '1',
            'type' => ConditionType::OR,
        ]);
        // End ---

        // 1.3 Present power of attorney section
        $presentPowerOfAttorneyStep = $this->section->steps()->create([
            'question' => 'Is there a Power of Attorney already in place for the above company representative?',
            'help_text' => "In most cases, obtaining a Power of Attorney is necessary to act as an attorney for someone's estate. Your response will help us ensure that the appropriate legal arrangements are in order. If you have an ongoing application for a Power of Attorney, you can select 'Add later' for now and provide the document at a later stage. This will allow us to proceed with the necessary steps while accommodating your ongoing application process.",
        ]);

        $presentPowerOfAttorneyDocumentsAnswer = $presentPowerOfAttorneyStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'],
                    ['value' => 'Ongoing Application'],
                ],
                'pdfFormFieldName' => OverviewPdfField::RepresentativeApplication.$this->number,
            ],
        ]);

        $presentPowerOfAttorneyDocumentsAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $presentPowerOfAttorneyDocumentsUploadAnswer = $presentPowerOfAttorneyStep->answers()->create([
            'type' => AnswerType::File,
            'details' => [
                'label' => 'Upload Document',
            ],
        ]);

        $presentPowerOfAttorneyDocumentsUploadAnswer->conditions()->create([
            'answer_id' => $presentPowerOfAttorneyDocumentsAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $presentPowerOfAttorneyDocumentsUploadAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 1.4 Attorney name(s) incorrect section
        $incorrectAttorneyNamesStep = $this->section->steps()->create([
            'question' => "Are any of the attorneys' current names different to how they appear (or will appear) on the Power of Attorney?",
            'help_text' => "Please let us know if any of the attorneys' names have changed since the issuance of the Power of Attorney. This could be due to events such as marriage, divorce, or other circumstances. If any of the attorneys' names are not exactly as they appear on the Power of Attorney, please click 'Yes'. Your response will help us ensure accurate documentation for the property transaction.",
        ]);

        $incorrectAttorneyNamesAnswer = $incorrectAttorneyNamesStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'], // Continue to 1.4a
                    ['value' => 'No'], // Skip to end of form
                ],
                'pdfFormFieldName' => OverviewPdfField::RepresentativeRepresentativesNameChange.$this->number,
            ],
        ]);

        $incorrectAttorneyNamesAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 1.4a, 1.4b Attorney name(s) confirmation section (REPEATABLE, based on 1.1)
        $attorneyNameSwapReasonStep = $this->section->steps()->create([
            'question' => 'Please confirm which names are (or will be) different on the Power of Attorney:',
            'help_text' => 'Please indicate which names on the Power of Attorney differ from the current names of the individuals involved. If there are any differences or if you anticipate any changes, kindly provide this information. Please also specify the reason for the name change and provide proof of the name change. If you are unable to upload the document at this time, no worries! You have the option to select "Add later". It is important to provide this information and documentation for accurate record-keeping and legal compliance.',
            'repeatable_answer_id' => $attorneyQuantityAnswer->id,
            'type' => StepType::RepeatableNameChangeAttorney,
        ]);

        $attorneyNameSwapReasonStep->conditions()->create([
            'answer_id' => $incorrectAttorneyNamesAnswer->id,
            'selected_value' => 'Yes',
        ]);

        StepAnswerGeneration::nameChange(
            step: $attorneyNameSwapReasonStep,
            number: $this->number,
        );
    }
}
