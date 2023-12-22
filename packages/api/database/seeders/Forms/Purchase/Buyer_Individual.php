<?php

namespace Database\Seeders\Forms\Purchase;

use App\Enums\AnswerType;
use App\Enums\ConditionType;
use App\Enums\FormGroup;
use App\Enums\FormType;
use App\Enums\OverviewPdfField;
use App\Enums\PropertyType;
use App\Enums\StepType;
use App\Models\Form;
use App\Models\Step;
use App\Services\StepAnswerGeneration\StepAnswerGeneration;
use Illuminate\Database\Seeder;

class Buyer_Individual extends Seeder
{
    private $globalBuyerStatusAnswer;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stepBuyer = Step::firstWhere('type', StepType::BuyerExpanded);

        // Form
        $form = Form::factory()
            ->state([
                'name' => 'Buyer Form (Individual)',
                'group' => FormGroup::GettingStarted,
                'description' => 'This section aims to gather information about the buyer(s) and whether the property is being bought by them or on their behalf.',
                'repeatable_answer_id' => $stepBuyer->answers->firstWhere('details.label', 'Buyer type')->id,
                'ta_form_template' => FormType::Individual,
                'order_number' => 2,
                'type' => PropertyType::Purchase,
            ])
            ->create();

        $this->theBuyer($form);
        $this->thePowerOfAttorney($form);
        $this->theDeputyshipOrder($form);
    }

    public function theBuyer(Form $form)
    {
        // 1.0 Buyer Section
        $buyerSection = $form->sections()->create([
            'name' => 'Buyer Details',
        ]);

        // 1.1 Start of Buyer Status step
        $buyerStatusStep = $buyerSection->steps()->create([
            'question' => 'Please select the status of the above buyer:',
            'help_text' => ''
                .'<p>Buyers may be buying the property as themselves or in certain circumstances have someone buying on their behalf.</p>'
                .'<p>If the buyer is buying the property as themselves, select &quot;Buying as themselves&quot;</p>'
                .'<p>If the owner has an attorney dealing with the sale, select &#39;Selling via attorney&#39;</p>'
                .'<p>If the owner has an deputy dealing with the sale, select &#39;Selling via deputy&#39;</p>',
        ]);

        $buyerStatusAnswer = $buyerStatusStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Acting for themselves'], // End form
                    ['value' => 'Buying via attorney'], // Load section 2.1
                    ['value' => 'Buying via deputy'], // Load section 3.1
                ],
                'pdfFormFieldName' => OverviewPdfField::Representation,
            ],
        ]);

        $this->globalBuyerStatusAnswer = $buyerStatusAnswer;

        $buyerStatusAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---
    }

    public function thePowerOfAttorney(Form $form)
    {
        // 2.0 Power of Attorney section
        $powerOfAttorneySection = $form->sections()->create([
            'name' => 'Power of Attorney',
        ]);

        $powerOfAttorneySection->conditions()->create([
            'answer_id' => $this->globalBuyerStatusAnswer->id,
            'selected_value' => 'Buying via attorney',
        ]);

        // 2.1 Start of attorney quantity section
        $attorneyQuantityStep = $powerOfAttorneySection->steps()->create([
            'question' => 'Please confirm the number of attorneys acting for the above buyer:',
            'help_text' => 'Please make sure you select the number of attorneys listed on the Power of Attorney, even if you are the only one dealing with the sale of the property.',
        ]);

        $attorneyQuantityAnswer = $attorneyQuantityStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => '1'], // Skip to 2.2b
                    ['value' => '2'], // Continue to 2.2
                    ['value' => '3'], // Continue to 2.2
                    ['value' => '4'], // Continue to 2.2
                    ['value' => '5'], // Continue to 2.2
                ],
            ],
        ]);

        $attorneyQuantityAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 2.2 Attorney Team Status section
        $attorneyTeamStatusStep = $powerOfAttorneySection->steps()->create([
            'question' => 'Will the attorneys be required to act jointly or severally?',
            'help_text' => '<p>SEVERALLY: all attorneys are required for any decision. If jointly is selected we will require the names of all the attorneys. All attorneys will then be invited to PreConvey to confirm your replies to the forms.</p><p>JOINTLY: an attorney can act alone in making decisions. If severally is selected you can complete the forms alone and do not need to enter the names of the other attorneys.</p>',
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
                    ['value' => 'Jointly'], // Continue to 2.2a
                    ['value' => 'Severally'], // Continue to 2.2b
                ],
                'pdfFormFieldName' => OverviewPdfField::Authority,
            ],
        ]);

        $attorneyTeamStatusAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 2.2a Attorney Details (Severally) (REPEATABLE, based on 2.1) section
        $attorneyDetailsSeverallyStep = $powerOfAttorneySection->steps()->create([
            'type' => StepType::Attorney,
            'question' => 'Please enter the details of each attorney for the above buyer:',
            'help_text' => 'Please enter the full current names and contact details of all attorneys. The conveyancer/solicitor will require at least a phone number or email address for each attorney.',
            'repeatable_answer_id' => $attorneyQuantityAnswer->id,
        ]);

        $attorneyDetailsSeverallyStep->conditions()->create([
            'answer_id' => $attorneyTeamStatusAnswer->id,
            'selected_value' => 'Jointly',
        ]);
        // End ---

        // 2.2b Attorney Details (Jointly) section
        $attorneyDetailsJointlyStep = $powerOfAttorneySection->steps()->create([
            'type' => StepType::Attorney,
            'question' => 'Please enter the details of the attorney who will be completing the sale on behalf of the above buyer:',
            'help_text' => 'Please enter the full current names of the attorney who will be dealing with the sale on behalf of the owner.',
        ]);

        $attorneyDetailsJointlyStep->conditions()->create([
            'answer_id' => $attorneyTeamStatusAnswer->id,
            'selected_value' => 'Severally',
            'type' => ConditionType::OR,
        ]);

        $attorneyDetailsJointlyStep->conditions()->create([
            'answer_id' => $attorneyQuantityAnswer->id,
            'selected_value' => '1',
            'type' => ConditionType::OR,
        ]);
        // End ---

        // 2.3 Present power of attorney section
        $presentPowerOfAttorneyStep = $powerOfAttorneySection->steps()->create([
            'question' => 'Is there a Power of Attorney already in place for the above buyer?',
            'help_text' => "In the vast majority of cases, you'll need to obtain a Power of Attorney to act as the attorney of someone's estate.",
        ]);

        $presentPowerOfAttorneyDocumentsAnswer = $presentPowerOfAttorneyStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'],
                    ['value' => 'Ongoing Application'],
                ],
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

        // 2.4 Attorney name(s) incorrect section
        $incorrectAttorneyNamesStep = $powerOfAttorneySection->steps()->create([
            'question' => "Are any of the attorneys' current names different to how they appear (or will appear) on the Power of Attorney?",
            'help_text' => "Some names may have changed since the Power of Attorney was issued (e.g. marriage, divorce etc.). Please click 'Yes' if any of the attorneys' names doesn't appear exactly like it's shown on the Power of Attorney.",
        ]);

        $incorrectAttorneyNamesAnswer = $incorrectAttorneyNamesStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'], // Continue to 2.4a
                    ['value' => 'No'], // Skip to end of form
                ],
                'pdfFormFieldName' => OverviewPdfField::NameChange,
            ],
        ]);

        $incorrectAttorneyNamesAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 2.4a, 2.4b Attorney name(s) confirmation section (REPEATABLE, based on 2.1)
        $attorneyNameSwapReasonStep = $powerOfAttorneySection->steps()->create([
            'question' => 'Please confirm which names are (or will be) different on the Power of Attorney:',
            'help_text' => 'Please provide proof of the name change. If you are unable to upload the document, please provide it to your conveyancer/solicitor in due course.',
            'repeatable_answer_id' => $attorneyQuantityAnswer->id,
            'type' => StepType::RepeatableNameChangeAttorney,
        ]);

        $attorneyNameSwapReasonStep->conditions()->create([
            'answer_id' => $incorrectAttorneyNamesAnswer->id,
            'selected_value' => 'Yes',
        ]);

        StepAnswerGeneration::nameChange(
            step: $attorneyNameSwapReasonStep
        );
        // End ---
    }

    public function theDeputyshipOrder(Form $form)
    {
        // 3.0 Deputyship Order section
        $powerOfDeputySection = $form->sections()->create([
            'name' => 'Deputyship Order',
        ]);

        $powerOfDeputySection->conditions()->create([
            'answer_id' => $this->globalBuyerStatusAnswer->id,
            'selected_value' => 'Buying via deputy',
        ]);

        // 3.1 Start of deputy quantity section
        $deputyQuantityStep = $powerOfDeputySection->steps()->create([
            'question' => 'Please confirm the number of deputies acting for the above buyer:',
            'help_text' => 'Please make sure you select the number of deputies listed on the Deputyship Order, even if you are the only one dealing with the sale of the property.',
        ]);

        $deputyQuantityAnswer = $deputyQuantityStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => '1'], // Skip to 3.2b
                    ['value' => '2'], // Continue to 3.2
                    ['value' => '3'], // Continue to 3.2
                    ['value' => '4'], // Continue to 3.2
                    ['value' => '5'], // Continue to 3.2
                ],
            ],
        ]);

        $deputyQuantityAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 3.2 Deputy Team Status section
        $deputyTeamStatusStep = $powerOfDeputySection->steps()->create([
            'question' => 'Will the deputies be required to act jointly or severally?',
            'help_text' => '<p>SEVERALLY: all deputies are required for any decision. If jointly is selected we will require the names of all the deputies. All deputies will then be invited to PreConvey to confirm your replies to the forms.</p><p>JOINTLY: a deputy can act alone in making decisions. If severally is selected you can complete the forms alone and do not need to enter the names of the other deputies.</p>',
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
                    ['value' => 'Jointly'], // Continue to 3.2a
                    ['value' => 'Severally'], // Continue to 3.2b
                ],
            ],
        ]);

        $deputyTeamStatusAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 3.2a Deputy Details (Severally) (REPEATABLE, based on 3.1) section
        $deputyDetailsSeverallyStep = $powerOfDeputySection->steps()->create([
            'type' => StepType::Deputy,
            'question' => 'Please enter the details of each deputy for the above buyer:',
            'help_text' => 'Please enter the full current names and contact details of all deputies. The conveyancer/solicitor will require at least a phone number or email address for each deputy.',
            'repeatable_answer_id' => $deputyQuantityAnswer->id,
        ]);

        $deputyDetailsSeverallyStep->conditions()->create([
            'answer_id' => $deputyTeamStatusAnswer->id,
            'selected_value' => 'Jointly',
        ]);
        // End ---

        // 3.2b Deputy Details (Jointly) section
        $deputyDetailsJointlyStep = $powerOfDeputySection->steps()->create([
            'type' => StepType::Deputy,
            'question' => 'Please enter the details of the deputy who will be completing the Purchase on behalf of the above buyer:',
            'help_text' => 'Please enter the full current names of the deputy who will be dealing with the sale on behalf of the owner.',
        ]);

        $deputyDetailsJointlyStep->conditions()->create([
            'answer_id' => $deputyTeamStatusAnswer->id,
            'selected_value' => 'Severally',
            'type' => ConditionType::OR,
        ]);

        $deputyDetailsJointlyStep->conditions()->create([
            'answer_id' => $deputyQuantityAnswer->id,
            'selected_value' => '1',
            'type' => ConditionType::OR,
        ]);
        // End ---

        // 3.3 Present power of deputy section
        $presentPowerOfDeputyStep = $powerOfDeputySection->steps()->create([
            'question' => 'Is there a Deputyship Order already in place for the above buyer?',
            'help_text' => "In the vast majority of cases, you'll need to obtain a Deputyship Order to act as the attorney of someone's estate.",
        ]);

        $presentPowerOfDeputyDocumentsAnswer = $presentPowerOfDeputyStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'],
                    ['value' => 'Ongoing Application'],
                ],
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

        // 3.4 Deputy name(s) incorrect section
        $incorrectDeputyNamesStep = $powerOfDeputySection->steps()->create([
            'question' => "Are any of the deputies' current names different to how they appear (or will appear) on the Deputyship Order?",
            'help_text' => "Some names may have changed since the Deputyship Order was issued (e.g. marriage, divorce etc.). Please click 'Yes' if any of the deputies' names doesn't appear exactly like it's shown on the Deputyship Order.",
        ]);

        $incorrectDeputyNamesAnswer = $incorrectDeputyNamesStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'], // Continue to 3.4a
                    ['value' => 'No'], // Skip to end of form
                ],
            ],
        ]);

        $incorrectDeputyNamesAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 3.4a, 3.4b Deputy name(s) confirmation section (REPEATABLE, based on 3.1)
        $deputyNameSwapReasonStep = $powerOfDeputySection->steps()->create([
            'question' => 'Please confirm which names are (or will be) different on the Deputyship Order:',
            'help_text' => 'Please provide proof of the name change. If you are unable to upload the document, please provide it to your conveyancer/solicitor in due course.',
            'repeatable_answer_id' => $deputyQuantityAnswer->id,
            'type' => StepType::RepeatableNameChangeDeputy,
        ]);

        $deputyNameSwapReasonStep->conditions()->create([
            'answer_id' => $incorrectDeputyNamesAnswer->id,
            'selected_value' => 'Yes',
        ]);

        StepAnswerGeneration::nameChange(
            step: $deputyNameSwapReasonStep
        );
    }
}
