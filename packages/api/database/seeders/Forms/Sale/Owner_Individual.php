<?php

namespace Database\Seeders\Forms\Sale;

use App\Enums\AnswerType;
use App\Enums\ConditionType;
use App\Enums\FileTextAnswerTypes;
use App\Enums\FormGroup;
use App\Enums\FormType;
use App\Enums\OverviewPdfField;
use App\Enums\PropertyType;
use App\Enums\StepType;
use App\Models\Answer;
use App\Models\Condition;
use App\Models\Form;
use App\Models\Section;
use App\Models\Step;
use App\Models\ValidationRule;
use App\Services\StepAnswerGeneration\StepAnswerGeneration;
use Illuminate\Database\Seeder;

class Owner_Individual extends Seeder
{
    private Answer $answerOwnerStatus;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stepOwnerName = Step::firstWhere('type', StepType::OwnerName);

        // Form
        $form = Form::factory()
            ->state([
                'name' => 'Getting started: The Owners - Individual',
                'group' => FormGroup::GettingStarted,
                'description' => 'Details about the owners of the property',
                'template_id' => $stepOwnerName->id,
                'repeatable_answer_id' => $stepOwnerName->answers->firstWhere('details.label', 'Owner type')->id,
                'ta_form_template' => FormType::Individual,
                'order_number' => 2,
                'type' => PropertyType::Sale,
            ])
            ->create();

        $this->ownerDetails($form);
        $this->powerOfAttorney($form);
        $this->deputyshipOrder($form);
        $this->grantOfProbate($form);
    }

    private function ownerDetails(Form $form)
    {
        // Section
        $sectionOwnerDetails = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Owner Details',
                ])
                ->make()
                ->toArray()
        );

        // Owner name different step
        $stepOwnerNameDifferent = $sectionOwnerDetails->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is this owner\'s current name different to how it appears on the Property Title Deeds?',
                    'help_text' => 'Some names may have changed since the Title Deeds were issued (e.g. marriage, divorce etc.). Please click \'Yes\' if any of the owners\' names are not exactly as they are shown on the Title Deeds.',
                ])
                ->make()
                ->toArray()
        );

        $answerOwnerNameDifferent = $stepOwnerNameDifferent->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'No'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::NameChange,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerOwnerNameDifferent->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Why owner name different step
        $stepOwnerNameDifferentWhy = $sectionOwnerDetails->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Why has this owner\'s name changed?',
                    'help_text' => 'Please provide proof of the name change. If you are unable to upload the document, please provide it to your conveyancer/solicitor in due course.',
                    'type' => StepType::NameChange,
                ])
                ->make()
                ->toArray()
        );

        $stepOwnerNameDifferentWhy->answers->each(function ($answer) {
            if ($answer->type === AnswerType::SingleSelect) {
                $answer->update([
                    'details' => [
                        'options' => [
                            ['value' => 'Marriage/Civil Partnership'],
                            ['value' => 'Divorce/Dissolved Civil Partnership'],
                            ['value' => 'Change of name deed'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::NameChangeReason,
                    ],
                ]);
            } elseif ($answer->type === AnswerType::File) {
                $answer->update([
                    'details' => [
                        'pdfFormField' => OverviewPdfField::NameChangeProof,
                        'textAnswers' => [
                            FileTextAnswerTypes::Enclosed => 'Enclosed',
                            FileTextAnswerTypes::AddLater => 'To follow',
                            FileTextAnswerTypes::NotApplicable => 'N/A',
                        ],
                    ],
                ]);
            }
        });

        $stepOwnerNameDifferentWhy->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $stepOwnerNameDifferentWhy->id,
            'answer_id' => $answerOwnerNameDifferent->id,
            'selected_value' => 'Yes',
        ]);

        // Owner status step
        $stepOwnerStatus = $sectionOwnerDetails->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'What is the status of this owner?',
                    'help_text' => '<p>Owners may be selling the property as themselves or in certain circumstances have someone selling on their behalf.</p>'
                    .'<p>If the owner is selling the property themselves, select "Acting for themselves"</p>'
                    .'<p>If the owner has an attorney dealing with the sale, select "Selling via attorney"</p>'
                    .'<p>If the owner has an deputy dealing with the sale, select "Selling via deputy"</p>'
                    .'<p>If the owner is deceased AND has an executor dealing with the sale, select "Selling via executor"</p>',
                ])
                ->make()
                ->toArray()
        );

        $this->answerOwnerStatus = $stepOwnerStatus->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Acting for themselves'],
                            ['value' => 'Selling via attorney'],
                            ['value' => 'Selling via deputy'],
                            ['value' => 'Selling via executor (deceased)'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::Representation,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $this->answerOwnerStatus->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
    }

    private function powerOfAttorney(Form $form)
    {
        // Section
        $sectionPowerOfAttorney = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Power of Attorney',
                ])
                ->make()
                ->toArray()
        );

        $sectionPowerOfAttorney->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'section',
                    'conditionable_id' => $sectionPowerOfAttorney->id,
                    'answer_id' => $this->answerOwnerStatus->id,
                    'selected_value' => 'Selling via attorney',
                ])
                ->make()
                ->toArray()
        );

        // Number of attorneys step
        $stepNumberOfAttorneys = $sectionPowerOfAttorney->steps()->create(
            Step::factory()
            ->state([
                'question' => 'Please confirm the number of attorneys acting for this owner?',
                'help_text' => 'Please make sure you select the number of attorneys listed on the Power of Attorney, even if you are the only one dealing with the sale of the property.',
            ])
                ->make()
                ->toArray()
        );

        $numberOfAttorneys = $stepNumberOfAttorneys->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => '1'],
                            ['value' => '2'],
                            ['value' => '3'],
                            ['value' => '4'],
                            ['value' => '5'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $numberOfAttorneys->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Jointly or severally step
        $stepJointlyOrSeverally = $sectionPowerOfAttorney->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Will the attorneys be required to act jointly or severally?',
                    'help_text' => '<p>JOINTLY: All Attorneys are required for any decision. If Jointly is selected we will require the names of all the Attorneys. All Attorneys will then need to be invited to ProConvey to confirm your answers to the following enquiries.</p>'
                        .'<p>SEVERALLY: An Attorney can act alone in making decisions. If Severally is selected you can complete the forms alone and do not need to enter the names of the other Attorneys.</p>',
                ])
                ->make()
                ->toArray()
        );

        $stepJointlyOrSeverally->conditions()->create([
            'answer_id' => $numberOfAttorneys->id,
            'selected_value' => '2',
            'type' => ConditionType::OR,
        ]);
        $stepJointlyOrSeverally->conditions()->create([
            'answer_id' => $numberOfAttorneys->id,
            'selected_value' => '3',
            'type' => ConditionType::OR,
        ]);
        $stepJointlyOrSeverally->conditions()->create([
            'answer_id' => $numberOfAttorneys->id,
            'selected_value' => '4',
            'type' => ConditionType::OR,
        ]);
        $stepJointlyOrSeverally->conditions()->create([
            'answer_id' => $numberOfAttorneys->id,
            'selected_value' => '5',
            'type' => ConditionType::OR,
        ]);

        $jointlyOrSeverally = $stepJointlyOrSeverally->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Jointly'],
                            ['value' => 'Severally'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::Authority,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $detailsOfTheAttorney = $sectionPowerOfAttorney->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the details of each attorney:',
                    'help_text' => 'Please enter the current full names of all owners. If any of these names have changed, the other owners will need to provide proof of their name change.',
                    'type' => StepType::OwnerFormPowerOfAttorney,
                    'repeatable_answer_id' => $numberOfAttorneys->id,
                ])
                ->make()
                ->toArray()
        );

        $detailsOfTheAttorney->conditions()->create([
            'answer_id' => $jointlyOrSeverally->id,
            'selected_value' => 'Jointly',
        ]);

        // Attorney details step
        $detailsOfTheAttorneyOnBehalf = $sectionPowerOfAttorney->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the details of the attorney who will be completing the sale on behalf of this owner:',
                    'help_text' => 'Please enter the full current names and contact details of all attorneys. The conveyancer/solicitor will require at least a phone number or email address for each attorney.',
                    'type' => StepType::OwnerFormPowerOfAttorney,
                ])
                ->make()
                ->toArray()
        );

        $detailsOfTheAttorneyOnBehalf->conditions()->create([
            'answer_id' => $jointlyOrSeverally->id,
            'selected_value' => 'Severally',
            'type' => ConditionType::OR,
        ]);
        $detailsOfTheAttorneyOnBehalf->conditions()->create([
            'answer_id' => $numberOfAttorneys->id,
            'selected_value' => '1',
            'type' => ConditionType::OR,
        ]);

        // Power of attorney in place step
        $stepPowerOfAttorneyInPlace = $sectionPowerOfAttorney->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there a Power of Attorney already in place for this owner?',
                    'help_text' => 'In the vast majority of cases, you\'ll need to obtain a Power of Attorney to act as the attorney of someone\'s estate.',
                ])
                ->make()
                ->toArray()
        );

        $answerPowerOfAttorneyInPlace = $stepPowerOfAttorneyInPlace->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'Ongoing application'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::Application,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerPowerOfAttorneyInPlace->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerPowerOfAttorneyInPlaceDoc = $stepPowerOfAttorneyInPlace->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $answerPowerOfAttorneyInPlaceDoc->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerPowerOfAttorneyInPlaceDoc->conditions()->create([
            'answer_id' => $answerPowerOfAttorneyInPlace->id,
            'selected_value' => 'Yes',
        ]);

        $nameDifference = $sectionPowerOfAttorney->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are any of the attorneys\' current names different to how they appear (or will appear) on the Power of Attorney?',
                    'help_text' => 'Some names may have changed since the Power of Attorney was issued (e.g. marriage, divorce etc.). Please click \'Yes\' if any of the attorneys\' names doesn\'t appear exactly like it\'s shown on the Power of Attorney.',
                ])
                ->make()
                ->toArray()
        );

        $answerNameDifference = $nameDifference->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'No'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerNameDifference->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Attorney Name Change step
        $uploadForTheNameChange = $sectionPowerOfAttorney->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm which names are (or will be) different on the Power of Attorney:',
                    'help_text' => '<p>Please indicate which names on the Power of Attorney differ from the current names of the individuals involved. If there are any differences or if you anticipate any changes, kindly provide this information.</p> 
                    <p>Please also specify the reason for the name change and provide proof of the name change. If you are unable to upload the document at this time, no worries! You have the option to select "Add later". It is important to provide this information and documentation for accurate record-keeping and legal compliance.</p>',
                    'repeatable_answer_id' => $numberOfAttorneys->id,
                    'type' => StepType::RepeatableNameChangeAttorney,
                ])
                ->make()
                ->toArray()
        );

        $uploadForTheNameChange->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'section',
                    'conditionable_id' => $uploadForTheNameChange->id,
                    'answer_id' => $answerNameDifference->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        StepAnswerGeneration::nameChange(
            step: $uploadForTheNameChange
        );
    }

    private function deputyshipOrder(Form $form)
    {
        $sectionDeputyshipOrder = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Deputyship Order',
                ])
                ->make()
                ->toArray()
        );

        $sectionDeputyshipOrder->conditions()->create([
            'conditionable_type' => 'section',
            'conditionable_id' => $sectionDeputyshipOrder->id,
            'answer_id' => $this->answerOwnerStatus->id,
            'selected_value' => 'Selling via deputy',
        ]);

        // Number of Deputies
        $numberOfDeputies = $sectionDeputyshipOrder->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm the number of deputies acting for the owner:',
                    'help_text' => 'Please make sure you select the number of deputies listed on the Deputyship Order, even if you are the only one dealing with the sale of the property.',
                ])
                ->make()
                ->toArray()
        );

        $answerNumberOfDeputies = $numberOfDeputies->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => '1'],
                            ['value' => '2'],
                            ['value' => '3'],
                            ['value' => '4'],
                            ['value' => '5'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerNumberOfDeputies->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Number of Deputies

        // Deputies jointly or severally
        $deputiesJointlyOrSeverally = $sectionDeputyshipOrder->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are the deputies acting jointly or severally?',
                    'help_text' => '<p>JOINTLY: all deputies are required for any decision. If jointly is selected we will require the names of all the deputies. All deputies will then be invited to PreConvey to confirm your replies to the forms.</p>'
                        .'<p>SEVERALLY: a deputy can act alone in making decisions. If severally is selected you can complete the forms alone and do not need to enter the names of the other deputies.</p>',
                ])
                ->make()
                ->toArray()
        );

        $deputiesJointlyOrSeverally->conditions()->create([
            'answer_id' => $answerNumberOfDeputies->id,
            'selected_value' => '2',
            'type' => ConditionType::OR,
        ]);
        $deputiesJointlyOrSeverally->conditions()->create([
            'answer_id' => $answerNumberOfDeputies->id,
            'selected_value' => '3',
            'type' => ConditionType::OR,
        ]);
        $deputiesJointlyOrSeverally->conditions()->create([
            'answer_id' => $answerNumberOfDeputies->id,
            'selected_value' => '4',
            'type' => ConditionType::OR,
        ]);
        $deputiesJointlyOrSeverally->conditions()->create([
            'answer_id' => $answerNumberOfDeputies->id,
            'selected_value' => '5',
            'type' => ConditionType::OR,
        ]);

        $answerDeputiesJointlyOrSeverally = $deputiesJointlyOrSeverally->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Jointly'],
                            ['value' => 'Severally'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::Authority,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerDeputiesJointlyOrSeverally->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Deputies jointly or severally

        // Details of the deputy
        $provideDetailsOfDeputyShipOrder = $sectionDeputyshipOrder->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the details of each deputy for the owner:',
                    'help_text' => 'Please enter the full current names and contact details of all deputies. The conveyancer/solicitor will require at least a phone number or email address for each deputy.',
                    'type' => StepType::Deputy,
                    'repeatable_answer_id' => $answerNumberOfDeputies->id,
                ])
                ->make()
                ->toArray()
        );

        $provideDetailsOfDeputyShipOrder->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $provideDetailsOfDeputyShipOrder->id,
            'answer_id' => $answerDeputiesJointlyOrSeverally->id,
            'selected_value' => 'Jointly',
        ]);

        $behalfOfOwner = $sectionDeputyshipOrder->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the details of the deputy who will be completing the sale on behalf of the owner:',
                    'help_text' => 'Please enter the full current names of the deputy who will be dealing with the sale on behalf of the owner.',
                    'type' => StepType::Deputy,
                ])
                ->make()
                ->toArray()
        );

        $behalfOfOwner->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $behalfOfOwner->id,
            'answer_id' => $answerDeputiesJointlyOrSeverally->id,
            'selected_value' => 'Severally',
        ]);

        $deputyshipOrderAlreadyInPlace = $sectionDeputyshipOrder->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there a Deputyship Order already in place for the owner?',
                    'help_text' => 'In the vast majority of cases, you\'ll need to obtain a Deputyship Order to act as the attorney of someone\'s estate.',
                ])
                ->make()
                ->toArray()
        );

        $answerDeputyOrderAlreadyInPlace = $deputyshipOrderAlreadyInPlace->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'Ongoing application'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::Application,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $fileUploadDeputyOrderAlreadyInPlace = $deputyshipOrderAlreadyInPlace->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $fileUploadDeputyOrderAlreadyInPlace->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray(),
        );

        $fileUploadDeputyOrderAlreadyInPlace->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $fileUploadDeputyOrderAlreadyInPlace->id,
            'answer_id' => $answerDeputyOrderAlreadyInPlace->id,
            'selected_value' => 'Yes',
        ]);

        // Confirm weather any names are different
        $anyDeputiesWithDifferentNames = $sectionDeputyshipOrder->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are any of the deputies\' current names different to how they appear (or will appear) on the Deputyship Order?',
                    'help_text' => 'Please let us know if any of the deputies\' names have changed since the issuance of the Deputyship Order. This could be due to events such as marriage, divorce, or other circumstances. If any of the deputies\' names are not exactly as they appear on the Deputyship Order, please click \'Yes\'. Your response will help us ensure accurate documentation for the property transaction.',
                ])
                ->make()
                ->toArray()
        );

        $answerAnyDeputiesWithDifferentNames = $anyDeputiesWithDifferentNames->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'No'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerAnyDeputiesWithDifferentNames->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray(),
        );
        // End Confirm weather any names are different

        // File Upload for the name change
        $uploadForTheNameChange = $sectionDeputyshipOrder->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm which names are (or will be) different on the Deputyship Order:',
                    'help_text' => 'Please provide proof of the name change. If you are unable to upload the document, please provide it to your conveyancer/solicitor in due course.',
                    'repeatable_answer_id' => $answerNumberOfDeputies->id,
                    'type' => StepType::RepeatableNameChangeDeputy,
                ])
                ->make()
                ->toArray()
        );

        StepAnswerGeneration::nameChange(
            step: $uploadForTheNameChange
        );

        $uploadForTheNameChange->conditions()->create([
            'answer_id' => $answerAnyDeputiesWithDifferentNames->id,
            'selected_value' => 'Yes',
        ]);
    }

    private function grantOfProbate(Form $form)
    {
        $sectionGrantOfProbate = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Grant of Probate',
                ])
                ->make()
                ->toArray()
        );

        $sectionGrantOfProbate->conditions()->create([
            'conditionable_type' => 'section',
            'conditionable_id' => $sectionGrantOfProbate->id,
            'answer_id' => $this->answerOwnerStatus->id,
            'selected_value' => 'Selling via executor (deceased)',
        ]);

        // Death Certificate
        $sectionDeathCertificate = $sectionGrantOfProbate->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the death certificate of the owner',
                    'help_text' => 'A death certificate is a legal document which states when a person had died. We will require a death certificate for each owner named on the Title Deeds that you have marked as deceased. If you are unable to upload the document, please provide it to your conveyancer/solicitor in due course.',
                ])
                ->make()
                ->toArray()
        );

        $uploadDeathCertificate = $sectionDeathCertificate->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $uploadDeathCertificate->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray(),
        );
        //End of Death Certificate

        // Represented by an executor
        $representedByExecutor = $sectionGrantOfProbate->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Will the owner be represented by an executor via a Grant of Probate?',
                    'help_text' => 'In most cases, obtaining a Grant of Probate is necessary to act as an executor for someone\'s estate. Your response will help us ensure that the appropriate legal arrangements are in order. ',
                ])
                ->make()
                ->toArray()
        );

        $answerRepresentedByExecutor = $representedByExecutor->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'Not required'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerRepresentedByExecutor->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray(),
        );
        // End of Represented by an executor

        // Grant in place for the owner
        $grantInPlaceForOwner = $sectionGrantOfProbate->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there a Grant of Probate already in place for the owner',
                    'help_text' => 'In most cases, obtaining a Grant of Probate is necessary to act as an executor for someone\'s estate. Your response will help us ensure that the appropriate legal arrangements are in order. If you have an ongoing application for a Grant of Probate, you can select \'Add later\' for now and provide the document at a later stage. This will allow us to proceed with the necessary steps while accommodating your ongoing application process.',
                ])
                ->make()
                ->toArray()
        );

        $grantInPlaceForOwner->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $grantInPlaceForOwner->id,
            'answer_id' => $answerRepresentedByExecutor->id,
            'selected_value' => 'Yes',
        ]);

        $answerGrantInPlaceForOwner = $grantInPlaceForOwner->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'Ongoing application'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::Application,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $uploadGrantInPlaceForOwner = $grantInPlaceForOwner->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $uploadGrantInPlaceForOwner->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $uploadGrantInPlaceForOwner->id,
            'answer_id' => $answerGrantInPlaceForOwner->id,
            'selected_value' => 'Yes',
        ]);

        $uploadGrantInPlaceForOwner->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray(),
        );
        // End of Grant in place for the owner

        // Numbers of Executors
        $stepNumberOfExecutors = $sectionGrantOfProbate->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm the number of executors on the Grant of Probate:',
                    'help_text' => 'Please indicate the correct count of executors listed on the Grant of Probate, even if you are the sole individual handling the property sale. Your cooperation in providing this information is essential for the proper processing and legal compliance of the transaction.',
                ])
                ->make()
                ->toArray()
        );

        $stepNumberOfExecutors->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $stepNumberOfExecutors->id,
            'answer_id' => $answerRepresentedByExecutor->id,
            'selected_value' => 'Yes',
        ]);

        $stepNumberOfExecutors->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $stepNumberOfExecutors->id,
            'answer_id' => $answerRepresentedByExecutor->id,
            'selected_value' => 'Yes',
        ]);

        $answerNumberOfExecutors = $stepNumberOfExecutors->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => '1'],
                            ['value' => '2'],
                            ['value' => '3'],
                            ['value' => '4'],
                            ['value' => '5'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerNumberOfExecutors->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray(),
        );
        // End of Numbers of Executors

        // Details for the executors
        $stepDetailsOfExecutors = $sectionGrantOfProbate->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the details of the executor who will be completing the sale on behalf of the owner',
                    'help_text' => 'Please enter the full current names of the executor. The conveyancer/solicitor will require at least a phone number or email address for the executor.',
                    'type' => StepType::DeputyDropdown,
                ])
                ->make()
                ->toArray()
        );

        $stepDetailsOfExecutors->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $stepDetailsOfExecutors->id,
            'answer_id' => $answerRepresentedByExecutor->id,
            'selected_value' => 'Yes',
        ]);

        $stepDetailsOfExecutors->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $stepDetailsOfExecutors->id,
            'answer_id' => $answerNumberOfExecutors->id,
            'selected_value' => '1',
        ]);
        // End of Details for the executors

        // Details for the executors
        $stepDetailsOfExecutors = $sectionGrantOfProbate->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the details of each executor acting for the owner',
                    'help_text' => 'Please enter the full current names of all executors. The conveyancer/solicitor will require at least a phone number or email address for each executor.',
                    'repeatable_answer_id' => $answerNumberOfExecutors->id,
                    'type' => StepType::DeputyDropdown,
                ])
                ->make()
                ->toArray()
        );

        $stepDetailsOfExecutors->conditions()->create([
            'answer_id' => $answerNumberOfExecutors->id,
            'selected_value' => '2',
            'type' => ConditionType::OR,
        ]);

        $stepDetailsOfExecutors->conditions()->create([
            'answer_id' => $answerNumberOfExecutors->id,
            'selected_value' => '3',
            'type' => ConditionType::OR,
        ]);

        $stepDetailsOfExecutors->conditions()->create([
            'answer_id' => $answerNumberOfExecutors->id,
            'selected_value' => '4',
            'type' => ConditionType::OR,
        ]);

        $stepDetailsOfExecutors->conditions()->create([
            'answer_id' => $answerNumberOfExecutors->id,
            'selected_value' => '5',
            'type' => ConditionType::OR,
        ]);

        $stepDetailsOfExecutors->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $stepDetailsOfExecutors->id,
            'answer_id' => $answerRepresentedByExecutor->id,
            'selected_value' => 'Yes',
        ]);
        // End of Details for the executors

        // Any of the names different to the grant of probate
        $stepAnyOfTheNamesDifferentToTheGrantOfProbate = $sectionGrantOfProbate->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are any of the executors\' current names different to how they appear (or will appear) on the Grant of Probate?',
                    'help_text' => 'Some names may have changed since the Grant of Probate was issued (e.g. marriage, divorce etc.). Please click \'Yes\' if any of the executors\' names doesn\'t appear exactly like it\'s shown on the Grant of Probate. They will also need to provide proof of this change.',
                ])
                ->make()
                ->toArray()
        );

        $stepAnyOfTheNamesDifferentToTheGrantOfProbate->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $stepAnyOfTheNamesDifferentToTheGrantOfProbate->id,
            'answer_id' => $answerRepresentedByExecutor->id,
            'selected_value' => 'Yes',
        ]);

        $answerAnyOfTheNameDifferentToTheGrantOfProbate = $stepAnyOfTheNamesDifferentToTheGrantOfProbate->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'No'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerAnyOfTheNameDifferentToTheGrantOfProbate->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray(),
        );
        // End of Any of the names different to the grant of probate

        // Reason for name change evidence
        $stepReasonForNameChangeEvidence = $sectionGrantOfProbate->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm which names are (or will be) different on the Grant of Probate:',
                    'help_text' => 'Please provide the reason for the name change and the evidence you have to support this:',
                    'repeatable_answer_id' => $answerNumberOfExecutors->id,
                    'type' => StepType::RepeatableNameChangeExecutor,
                ])
                ->make()
                ->toArray()
        );

        StepAnswerGeneration::nameChange(
            step: $stepReasonForNameChangeEvidence
        );

        $stepReasonForNameChangeEvidence->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $stepReasonForNameChangeEvidence->id,
            'answer_id' => $answerRepresentedByExecutor->id,
            'selected_value' => 'Yes',
        ]);

        $stepReasonForNameChangeEvidence->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $stepReasonForNameChangeEvidence->id,
            'answer_id' => $answerAnyOfTheNameDifferentToTheGrantOfProbate->id,
            'selected_value' => 'Yes',
        ]);
    }
}
