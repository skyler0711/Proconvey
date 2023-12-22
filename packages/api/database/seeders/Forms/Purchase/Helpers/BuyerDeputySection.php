<?php

namespace Database\Seeders\Forms\Purchase\Helpers;

use App\Enums\AnswerType;
use App\Enums\ConditionType;
use App\Enums\OverviewPdfField;
use App\Enums\StepType;
use App\Models\Section;
use App\Services\StepAnswerGeneration\StepAnswerGeneration;

class BuyerDeputySection
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
        // 1.1 Start of deputy quantity section
        $deputyQuantityStep = $this->section->steps()->create([
            'question' => 'Please confirm the number of deputies acting for the above company representative',
            'help_text' => 'Please indicate the correct count of deputies listed on the Deputyship Order, even if you are the sole individual handling the property purchase. Your cooperation in providing this information is essential for the proper processing and legal compliance of the transaction.',
        ]);

        $deputyQuantityAnswer = $deputyQuantityStep->answers()->create([
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

        $deputyQuantityAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 1.2 Deputy Team Status section
        $deputyTeamStatusStep = $this->section->steps()->create([
            'question' => 'Will the deputies be required to act jointly or severally?',
            'help_text' => '<p>For the deputies listed on the Deputyship Order, please specify whether they will be required to act jointly or severally. Here\'s what each option means:</p><p><strong>JOINTLY:</strong>Select this option if all deputies must be involved in making any decision regarding the property. If you choose this, please provide the names of all the deputies. All deputies will then be invited to ProConvey.</p><p><strong>SEVERALLY:</strong>Choose this option if a deputy can act alone in making decisions without the involvement of other deputies. If you select this, you do not need to enter the names of the other attorneys.</p><p>By selecting the appropriate option, you will help us ensure accurate processing of the forms and align with the deputies\' roles.</p>',
        ]);

        $deputyTeamStatusStep->conditions()->create([
            'answer_id' => $deputyQuantityAnswer->id,
            'selected_value' => '2',
            'type' => ConditionType::OR,
        ]);

        $deputyTeamStatusStep->conditions()->create([
            'answer_id' => $deputyQuantityAnswer->id,
            'selected_value' => '3',
            'type' => ConditionType::OR,
        ]);

        $deputyTeamStatusStep->conditions()->create([
            'answer_id' => $deputyQuantityAnswer->id,
            'selected_value' => '4',
            'type' => ConditionType::OR,
        ]);

        $deputyTeamStatusStep->conditions()->create([
            'answer_id' => $deputyQuantityAnswer->id,
            'selected_value' => '5',
            'type' => ConditionType::OR,
        ]);

        $deputyTeamStatusAnswer = $deputyTeamStatusStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Jointly'], // Continue to 1.2a
                    ['value' => 'Severally'], // Continue to 1.2b
                ],
                'pdfFormFieldName' => OverviewPdfField::RepresentativeAuthority.$this->number,
            ],
        ]);

        $deputyTeamStatusAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 1.2a Deputy Details (Severally) (REPEATABLE, based on 1.1) section
        $deputyDetailsSeverallyStep = $this->section->steps()->create([
            'type' => StepType::Deputy,
            'question' => 'Please enter the details of each deputy acting for the above company representative:',
            'help_text' => 'Please provide details and contact information for all deputies actively involved in the transaction. All deputies will then be invited to ProConvey. This information is necessary for your conveyancer or solicitor to effectively communicate and coordinate with the deputies throughout the process. By providing the details of the deputies, including their names and contact information, your conveyancer or solicitor can ensure smooth collaboration and accurate documentation.',
            'repeatable_answer_id' => $deputyQuantityAnswer->id,
        ]);

        StepAnswerGeneration::personInformation(
            $deputyDetailsSeverallyStep,
            text: 'deputy',
            canSelectPreviousPerson: true,
            canSelectSaleAddress: true,
            pdfField: OverviewPdfField::RepresentativeRepresentatives.$this->number,
            useSaleAddressPdfField: OverviewPdfField::RepresentativeRepresentativesUseSaleAddress.$this->number,
        );

        $deputyDetailsSeverallyStep->conditions()->create([
            'answer_id' => $deputyTeamStatusAnswer->id,
            'selected_value' => 'Severally',
        ]);
        // End ---

        // 1.2b Deputy Details (Jointly) section
        $deputyDetailsJointlyStep = $this->section->steps()->create([
            'type' => StepType::Deputy,
            'question' => 'Please enter the details of the deputy who will be completing the Purchase on behalf of the above company representative:',
            'help_text' => 'Please provide the full current name(s) of the deputy who will be handling the sale on behalf of the buyer. This information is essential for your conveyancer or solicitor to accurately identify and communicate with the designated deputy throughout the transaction. By providing the full name(s) of the deputy, your lawyer can ensure proper documentation and effective collaboration.',
        ]);

        StepAnswerGeneration::personInformation(
            $deputyDetailsJointlyStep,
            text: 'deputy',
            canSelectPreviousPerson: true,
            canSelectSaleAddress: true,
            pdfField: OverviewPdfField::RepresentativeRepresentatives.$this->number,
            useSaleAddressPdfField: OverviewPdfField::RepresentativeRepresentativesUseSaleAddress.$this->number,
        );

        $deputyDetailsJointlyStep->conditions()->create([
            'answer_id' => $deputyTeamStatusAnswer->id,
            'selected_value' => 'Jointly',
            'type' => ConditionType::OR,
        ]);

        $deputyDetailsJointlyStep->conditions()->create([
            'answer_id' => $deputyQuantityAnswer->id,
            'selected_value' => '1',
            'type' => ConditionType::OR,
        ]);
        // End ---

        // 1.3 Present power of deputy section
        $presentPowerOfDeputyStep = $this->section->steps()->create([
            'question' => 'Is there a Deputyship Order already in place for the above company representative?',
            'help_text' => "In most cases, obtaining a Deputyship Order is necessary to act as a deputy for someone's estate. Your response will help us ensure that the appropriate legal arrangements are in order. If you have an ongoing application for a Deputyship Order, you can select 'Add later' for now and provide the document at a later stage. This will allow us to proceed with the necessary steps while accommodating your ongoing application process.",
        ]);

        $presentPowerOfDeputyDocumentsAnswer = $presentPowerOfDeputyStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'],
                    ['value' => 'Ongoing Application'],
                ],
                'pdfFormFieldName' => OverviewPdfField::RepresentativeApplication.$this->number,
            ],
        ]);

        $presentPowerOfDeputyDocumentsAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $presentPowerOfDeputyDocumentsUploadAnswer = $presentPowerOfDeputyStep->answers()->create([
            'type' => AnswerType::File,
            'details' => [
                'label' => 'Upload Document',
            ],
        ]);

        $presentPowerOfDeputyDocumentsUploadAnswer->conditions()->create([
            'answer_id' => $presentPowerOfDeputyDocumentsAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $presentPowerOfDeputyDocumentsUploadAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 1.4 Deputy name(s) incorrect section
        $incorrectDeputyNamesStep = $this->section->steps()->create([
            'question' => "Are any of the deputies' current names different to how they appear (or will appear) on the Deputyship Order?",
            'help_text' => "Please let us know if any of the deputies' names have changed since the issuance of the Deputyship Order. This could be due to events such as marriage, divorce, or other circumstances. If any of the deputies' names are not exactly as they appear on the Deputyship Order, please click 'Yes'. Your response will help us ensure accurate documentation for the property transaction.",
        ]);

        $incorrectDeputyNamesAnswer = $incorrectDeputyNamesStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'], // Continue to 1.4a
                    ['value' => 'No'], // Skip to end of form
                ],
                'pdfFormFieldName' => OverviewPdfField::RepresentativeRepresentativesNameChange.$this->number,
            ],
        ]);

        $incorrectDeputyNamesAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 1.4a, 1.4b Deputy name(s) confirmation section (REPEATABLE, based on 1.1)
        $deputyNameSwapReasonStep = $this->section->steps()->create([
            'question' => 'Please enter the details of the deputies whose names are different to how they appear (or will appear) on the Deputyship Order:',
            'help_text' => 'Please indicate which names on the Deputyship Order differ from the current names of the individuals involved. If there are any differences or if you anticipate any changes, kindly provide this information. 
            Please also specify the reason for the name change and provide proof of the name change. If you are unable to upload the document at this time, no worries! You have the option to select "Add later". It is important to provide this information and documentation for accurate record-keeping and legal compliance.',
            'repeatable_answer_id' => $deputyQuantityAnswer->id,
            'type' => StepType::RepeatableNameChangeDeputy,
        ]);

        $deputyNameSwapReasonStep->conditions()->create([
            'answer_id' => $incorrectDeputyNamesAnswer->id,
            'selected_value' => 'Yes',
        ]);

        StepAnswerGeneration::nameChange(
            step: $deputyNameSwapReasonStep,
            number: $this->number,
        );
    }
}
