<?php

namespace Database\Seeders\Forms\Sale;

use App\Enums\AnswerType;
use App\Enums\FormGroup;
use App\Enums\FormType;
use App\Enums\PropertyType;
use App\Enums\StepType;
use App\Models\Answer;
use App\Models\Form;
use App\Models\Section;
use App\Models\Step;
use App\Models\ValidationRule;
use Illuminate\Database\Seeder;

class ProtocolForm_TA9_CommonholdInformation extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Form
        $form = Form::factory()
            ->state([
                'name' => 'TA9: Commonhold Information',
                'group' => FormGroup::Protocol,
                'description' => 'Information about the commonhold',
                'ta_form_template' => FormType::TA9CommonholdInformation,
                'order_number' => 7,
                'signature_coords' => [0.255, 0.452],
                'type' => PropertyType::Sale,
            ])
            ->create();

        $answerId = Answer::whereHas('step', function ($query) {
            $query->where('question', 'Is the property for sale a freehold or leasehold?');
        })->first()->id;

        $form->conditions()->create([
            'answer_id' => $answerId,
            'selected_value' => 'Commonhold',
        ]);

        $this->commonholdAssociation($form);
        $this->commonholdAssessmentsAndReserveFund($form);
        $this->notices($form);
        $this->commonParts($form);
        $this->insurance($form);
        $this->consents($form);
        $this->complaints($form);
        $this->rightsForTheDeveloper($form);
    }

    protected function commonholdAssociation(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Commonhold association',
                ])
                ->make()
                ->toArray()
        );

        // 1.1 Copy of the commonhold association's accounts
        $copyOfTheCommonholdAssociationsAccountsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide copies of the commonhold association\'s accounts for the last three years:',
                    'help_text' => 'The seller should provide copies of the commonhold associations accounts the last three years.',
                ])
                ->make()
                ->toArray()
        );

        $answerCopyOfTheCommonholdAssociationsAccountsStep1 = $copyOfTheCommonholdAssociationsAccountsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Checkbox,
                    'details' => [
                        'label' => 'Lost',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCopyOfTheCommonholdAssociationsAccountsStep = $copyOfTheCommonholdAssociationsAccountsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '1.1',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCopyOfTheCommonholdAssociationsAccountsStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerCopyOfTheCommonholdAssociationsAccountsStep->conditions()->create([
            'answer_id' => $answerCopyOfTheCommonholdAssociationsAccountsStep1->id,
            'selected_value' => '0',
        ]);


        // End of Copy of the commonhold association's accounts

        // 1.2 Number of directors of the association
        $numberOfDirectorsOfTheAssociationStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm the number of directors of the association:',
                    'help_text' => 'The seller should advise how many directors the association has.',
                ])
                ->make()
                ->toArray()
        );

        $answerNumberOfDirectorsOfTheAssociationStep = $numberOfDirectorsOfTheAssociationStep->answers()->create(
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

        $answerNumberOfDirectorsOfTheAssociationStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Number of directors of the association

        // 1.2a Details of the director of the association
        $directorDetailsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'type' => StepType::DirectorDetails,
                    'question' => 'Please enter the details of each director of the association:',
                    'help_text' => 'Please enter the full current names and contact details of all directors. The conveyancer/solicitor will require at least a phone number or email address for each director.',
                    'repeatable_answer_id' => $answerNumberOfDirectorsOfTheAssociationStep->id,
                ])
                ->make()
                ->toArray()
        );
        // End of the director of the association

        // 1.3 Secretary of the association
        $secretaryOfTheAssociationStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there a secretary of the association?',
                    'help_text' => 'The Secretary supports the Chair in ensuring the smooth functioning of the Management Committee.',
                ])
                ->make()
                ->toArray()
        );

        $answerSecretaryOfTheAssociationStep = $secretaryOfTheAssociationStep->answers()->create(
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

        $answerSecretaryOfTheAssociationStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Secretary of the association

        // 1.3a Details of Secretary of the association
        $stepDetailsOfSecretaryOfAssociation = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the details of the secretary of the association:',
                    'help_text' => 'Please enter the full current name and contact details of the secretary. The conveyancer/solicitor will require at least a phone number or email address for the secretary.',
                    'type' => StepType::TA9Secretary,
                ])
                ->make()
                ->toArray()
        );

        $stepDetailsOfSecretaryOfAssociation->conditions()->create([
            'answer_id' => $answerSecretaryOfTheAssociationStep->id,
            'selected_value' => 'Yes',
        ]);
        // End of Details of Secretary of the association

        // 1.4 Managing agent
        $managingAgentStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there a managing agent appointed by association?',
                    'help_text' => 'A managing agent may be employed by to collect the rent and / or manage the building.',
                ])
                ->make()
                ->toArray()
        );

        $answerManagingAgentStep = $managingAgentStep->answers()->create(
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

        $answerManagingAgentStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Managing agent

        // 1.4a Details of the managing agent appointed by the agent
        $stepDetailsOfManagingAgent = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the details of the managing agent appointed by the association:',
                    'help_text' => 'Please enter the details of the managing agent.',
                    'type' => StepType::TA9ManagingAgent,
                ])
                ->make()
                ->toArray()
        );

        $stepDetailsOfManagingAgent->conditions()->create([
            'answer_id' => $answerManagingAgentStep->id,
            'selected_value' => 'Yes',
        ]);
        // End of the managing agent appointed by the agent

        // 1.5 Seller know of any proposal to amend the terms of the commonhold
        $sellerKnowOfAnyProposalToAmendTheTermsOfTheCommonholdStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller know of any proposal to amend the terms of the commonhold community statement?',
                    'help_text' => 'The commonhold community statement defines the extent of the commonhold and the individual units, regulates the use and maintenance of the units and provides for the rights and duties of the unit-holders and of the commonhold association',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerKnowOfAnyProposalToAmendTheTermsOfTheCommonholdStep = $sellerKnowOfAnyProposalToAmendTheTermsOfTheCommonholdStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '1.3_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '1.3_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerKnowOfAnyProposalToAmendTheTermsOfTheCommonholdStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerKnowOfAnyProposal = $sellerKnowOfAnyProposalToAmendTheTermsOfTheCommonholdStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the commonhold association are due to amend the commonhold community statement to restrict access to certain areas',
                        'pdfFormFieldName' => '1.3_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerKnowOfAnyProposal->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerKnowOfAnyProposal->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerKnowOfAnyProposal->id,
            'answer_id' => $answerSellerKnowOfAnyProposalToAmendTheTermsOfTheCommonholdStep->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller know of any proposal to amend the terms of the commonhold

        // 1.6 Seller know of any proposal to enlarge the commonhold
        $sellerKnowOfAnyProposalToEnlargeTheCommonholdStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller know of any proposal to enlarge the commonhold?',
                    'help_text' => 'The seller should confirm whether they are aware of any proposals to enlarge the commonhold and if necessary, provide further details.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerKnowOfAnyProposalToEnlargeTheCommonholdStep = $sellerKnowOfAnyProposalToEnlargeTheCommonholdStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '1.4_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '1.4_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerKnowOfAnyProposalToEnlargeTheCommonholdStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerKnowOfAnyProposalToEnlargeTheCommonhold = $sellerKnowOfAnyProposalToEnlargeTheCommonholdStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the commonhold association propose to merge the commonhold with the land to the north',
                        'pdfFormFieldName' => '1.4_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerKnowOfAnyProposalToEnlargeTheCommonhold->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerKnowOfAnyProposalToEnlargeTheCommonhold->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerKnowOfAnyProposalToEnlargeTheCommonhold->id,
            'answer_id' => $answerSellerKnowOfAnyProposalToEnlargeTheCommonholdStep->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller know of any proposal to enlarge the commonhold

        // 1.7 Commonhold association a member of an approved ombudsment scheme
        $commonholdAssociationAMemberOfAnApprovedOmbudsmentSchemeStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the commonhold association a member of an approved ombudsman scheme?',
                    'help_text' => 'There may be a commonhold ombudsman who will be able to investigate and settle disputes.',
                ])
                ->make()
                ->toArray()
        );

        $answerCommonholdAssociationAMemberOfAnApprovedOmbudsmentSchemeStep = $commonholdAssociationAMemberOfAnApprovedOmbudsmentSchemeStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '1.5_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '1.5_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCommonholdAssociationAMemberOfAnApprovedOmbudsmentSchemeStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextCommonholdAssociationAMemberOfAnApprovedOmbudsmentScheme = $commonholdAssociationAMemberOfAnApprovedOmbudsmentSchemeStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. Housing Ombudsman Service, PO Box 152, Liverpool L33 7WQ',
                        'pdfFormFieldName' => '1.5_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextCommonholdAssociationAMemberOfAnApprovedOmbudsmentScheme->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextCommonholdAssociationAMemberOfAnApprovedOmbudsmentScheme->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextCommonholdAssociationAMemberOfAnApprovedOmbudsmentScheme->id,
            'answer_id' => $answerCommonholdAssociationAMemberOfAnApprovedOmbudsmentSchemeStep->id,
            'selected_value' => 'Yes',
        ]);
        // End of Commonhold association a member of an approved ombudsment scheme
    }

    protected function commonholdAssessmentsAndReserveFund(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Commonhold assessments and reserve fund levies',
                ])
                ->make()
                ->toArray()
        );

        // 2.1 Commonhold assessments
        $commonholdAssessmentsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the commonhold association made any commonhold assessments in respect of the seller\'s unit during the last three years?',
                    'help_text' => 'A commonhold assessment estimates the overall costs of managing, maintaining, repairing and insuring the building',
                ])
                ->make()
                ->toArray()
        );

        $answerCommonholdAssessmentsStep = $commonholdAssessmentsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '2.1_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '2.1_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCommonholdAssessmentsStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextCommonholdAssessments = $commonholdAssessmentsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. a commonhold assessment was carried out in 2020 and funds have been paid towards the maintenance',
                        'pdfFormFieldName' => '2.1_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextCommonholdAssessments->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextCommonholdAssessments->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextCommonholdAssessments->id,
            'answer_id' => $answerCommonholdAssessmentsStep->id,
            'selected_value' => 'Yes',
        ]);
        // End of Commonhold assessments

        // 2.2 Commonhold association established any reserve funds
        $commonholdAssociationEstablishedAnyReserveFundsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the commonhold association established any reserve funds (to pay for major expenditure on such items as outside painting, roof repairs, lift replacement)?',
                    'help_text' => 'In some commonhold\'s the unit-holders pay extra fees which are paid into a reserve fund and kept until they are needed for large, but infrequent, work projects.',
                ])
                ->make()
                ->toArray()
        );

        $answerCommonholdAssociationEstablishedAnyReserveFundsStep = $commonholdAssociationEstablishedAnyReserveFundsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '2.2_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '2.2_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCommonholdAssociationEstablishedAnyReserveFundsStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextCommonholdAssociationEstablishedAnyReserveFunds = $commonholdAssociationEstablishedAnyReserveFundsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the commonhold reserve fund is circa £20k',
                        'pdfFormFieldName' => '2.2_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextCommonholdAssociationEstablishedAnyReserveFunds->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextCommonholdAssociationEstablishedAnyReserveFunds->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextCommonholdAssociationEstablishedAnyReserveFunds->id,
            'answer_id' => $answerCommonholdAssociationEstablishedAnyReserveFundsStep->id,
            'selected_value' => 'Yes',
        ]);
        // End of Commonhold association established any reserve funds

        // 2.3 Association made levies
        $associationMadeLeviesStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the commonhold association made levies (demanded contributions) in respect of the seller\'s unit during the last three years?',
                    'help_text' => 'Some commonhold associations demand contributions towards the cost of repair or maintenance.',
                ])
                ->make()
                ->toArray()
        );

        $answerAssociationMadeLeviesStep = $associationMadeLeviesStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '2.3_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '2.3_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerAssociationMadeLeviesStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextAssociationMadeLevies = $associationMadeLeviesStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the commonhold association has demanded payment contributions to repair the fencing',
                        'pdfFormFieldName' => '2.3_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextAssociationMadeLevies->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextAssociationMadeLevies->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextAssociationMadeLevies->id,
            'answer_id' => $answerAssociationMadeLeviesStep->id,
            'selected_value' => 'Yes',
        ]);
        // End of Association made levies

        // 2.4 Seller know of any expense
        $sellerKnowOfAnyExpenseStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller know of any expense, which is not usually incurred every year and is not covered by a reserve fund (e.g. redecoration, repairing drives), which the commonhold association is likely to incur within the next three years?',
                    'help_text' => 'The seller should advise if they are aware of any upcoming costs not usually incurred annually.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerKnowOfAnyExpenseStep = $sellerKnowOfAnyExpenseStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '2.4_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '2.4_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerKnowOfAnyExpenseStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerKnowOfAnyExpense = $sellerKnowOfAnyExpenseStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'Please give details of f how much was payable to which fund',
                        'pdfFormFieldName' => '2.4_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerKnowOfAnyExpense->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerKnowOfAnyExpense->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerKnowOfAnyExpense->id,
            'answer_id' => $answerSellerKnowOfAnyExpenseStep->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller know of any expense

        // 2.5 Seller changed
        $sellerChangedStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the seller challenged, or does the seller know of any other unit-holder who has challenged, the amount of any commonhold assessment or reserve fund levy during the last three years?',
                    'help_text' => 'The seller should advise whether they are aware of any issues and, where necessary, provide further details.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerChangedStep = $sellerChangedStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '2.5_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '2.5_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerChangedStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerChanged = $sellerChangedStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the neighbour challenged the commonhold assessment as the owner failed to carry out their obligations under the lease',
                        'pdfFormFieldName' => '2.5_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerChanged->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerChanged->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerChanged->id,
            'answer_id' => $answerSellerChangedStep->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller changed

        // 2.6 Seller aware of any problems
        $sellerAwareOfAnyProblemsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller know of any problems in the last three years between unit-holders and the commonhold association about the payment of commhold assessments or reserve fund levies?',
                    'help_text' => 'The seller should advise whether they are aware of any issues and, where necessary, provide further details.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareOfAnyProblemsStep = $sellerAwareOfAnyProblemsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '2.6_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '2.6_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareOfAnyProblemsStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareOfAnyProblems = $sellerAwareOfAnyProblemsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the unit-holders challenged the commonhold assessment as the owner failed to carry out their obligations under the lease',
                        'pdfFormFieldName' => '2.6_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareOfAnyProblems->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareOfAnyProblems->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerAwareOfAnyProblems->id,
            'answer_id' => $answerSellerAwareOfAnyProblemsStep->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller aware of any problems

        // 2.7 Commonhold unit information certificate
        $commonholdUnitInformationCertificateStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please obtain a commonhold unit information certificate from the commonhold association and provide a copy:',
                    'help_text' => 'The commonhold unit information certificate sets out the debts the unit-holder owes to the association on the date of the assessment, relating to the commonhold assessment, the reserve fund and any interest on late payments.',
                ])
                ->make()
                ->toArray()
        );

        $answerCommonholdUnitInformationCertificateStep = $commonholdUnitInformationCertificateStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '2.7',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCommonholdUnitInformationCertificateStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Commonhold unit information certificate
    }

    protected function notices(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Notices',
                ])
                ->make()
                ->toArray()
        );

        // 3.1 Seller had any notice about the unit being sold or any other part of the commonhold
        $sellerHadAnyNoticeAboutTheUnitBeingSoldOrAnyOtherPartOfTheCommonholdStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the seller had any notice about the unit being sold or any other part of the commonhold, its use, its condition, or its repair and maintenance?',
                    'help_text' => 'A notice could be in a printed form or in the form of a letter. It could come from the commonhold association, another unit-holder, a neighbouring owner or an official body.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerHadAnyNoticeAboutTheUnitBeingSoldOrAnyOtherPartOfTheCommonholdStep = $sellerHadAnyNoticeAboutTheUnitBeingSoldOrAnyOtherPartOfTheCommonholdStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '3.1_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '3.1_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerHadAnyNoticeAboutTheUnitBeingSoldOrAnyOtherPartOfTheCommonholdStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerUploadSellerHadAnyNoticeAboutTheUnitEbingSoldOrAnyOtherPartOfTheCommonhold = $sellerHadAnyNoticeAboutTheUnitBeingSoldOrAnyOtherPartOfTheCommonholdStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '3.1',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerUploadSellerHadAnyNoticeAboutTheUnitEbingSoldOrAnyOtherPartOfTheCommonhold->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerUploadSellerHadAnyNoticeAboutTheUnitEbingSoldOrAnyOtherPartOfTheCommonhold->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerUploadSellerHadAnyNoticeAboutTheUnitEbingSoldOrAnyOtherPartOfTheCommonhold->id,
            'answer_id' => $answerSellerHadAnyNoticeAboutTheUnitBeingSoldOrAnyOtherPartOfTheCommonholdStep->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller had any notice about the unit ebing sold or any other part of the commonhold
    }

    protected function commonParts(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Common parts',
                ])
                ->make()
                ->toArray()
        );

        // 4.1 Seller aware of any disputes
        $sellerAwareOfAnyDisputesStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller know of any dispute about the use of the common parts during the last three years?',
                    'help_text' => 'Please provide details of any disputes about the use of the common parts that the seller is aware of during the last three years. This includes any disagreements or conflicts related to the shared areas or facilities within the commonhold property. This information is important for the conveyancing process as it helps to ensure that any ongoing disputes or issues are disclosed, and it allows both parties to address and resolve these matters appropriately during the property sale.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareOfAnyDisputesStep = $sellerAwareOfAnyDisputesStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '4.1_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '4.1_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareOfAnyDisputesStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareOfAnyDisputes = $sellerAwareOfAnyDisputesStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. a complaint was placed with the commonhold association due to excessive noise in the commonhold',
                        'pdfFormFieldName' => '4.1_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareOfAnyDisputes->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareOfAnyDisputes->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerAwareOfAnyDisputes->id,
            'answer_id' => $answerSellerAwareOfAnyDisputesStep->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller aware of any disputes

        // 4.2 Seller aware of proposal to lease
        $sellerAwareOfProposalToLeaseStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller know of any proposal to lease or dispose of any of the common parts?',
                    'help_text' => 'The seller should advise if they are aware of any proposals and, where necessary, provide further details.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareOfProposalToLeaseStep = $sellerAwareOfProposalToLeaseStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '4.2_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '4.2_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareOfProposalToLeaseStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareOfProposalToLease = $sellerAwareOfProposalToLeaseStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the commonhold is due to be disposed next year',
                        'pdfFormFieldName' => '4.2_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareOfProposalToLease->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareOfProposalToLease->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerAwareOfProposalToLease->id,
            'answer_id' => $answerSellerAwareOfProposalToLeaseStep->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller aware of proposal to lease

        // 4.3 Seller know of any proposal to mortgage
        $sellerKnowOfAnyProposalToMortgageStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller know of any proposal to mortgage all or any part of the common parts?',
                    'help_text' => 'The seller should advise if they are aware of any proposals and, where necessary, provide further details.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerKnowOfAnyProposalToMortgageStep = $sellerKnowOfAnyProposalToMortgageStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '4.3_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '4.3_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerKnowOfAnyProposalToMortgageStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerKnowOfAnyProposalToMortgage = $sellerKnowOfAnyProposalToMortgageStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the commonhold association plan to mortgage the full commonhold next year',
                        'pdfFormFieldName' => '4.3_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerKnowOfAnyProposalToMortgage->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerKnowOfAnyProposalToMortgage->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerKnowOfAnyProposalToMortgage->id,
            'answer_id' => $answerSellerKnowOfAnyProposalToMortgageStep->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller know of any proposal to mortgage
    }

    protected function insurance(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Insurance',
                ])
                ->make()
                ->toArray()
        );

        // 5.1 Copy of the insurance policy
        $copyOfTheInsurancePolicyStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the insurance policy the unit being sold (whether or not the policy also covers other property) and evidence of payment of the latest premium:',
                    'sub_heading' => '(If the commonhold association arranges the insurance, please obtain particulars from the association)',
                    'help_text' => 'The seller should provide copies of the buildings insurance policy and a receipt for payment.',
                ])
                ->make()
                ->toArray()
        );

        $answerCopyOfTheInsurancePolicyStep = $copyOfTheInsurancePolicyStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '5.1',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCopyOfTheInsurancePolicyStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End Copy of the insurance policy

        // 5.2 Common parts insurance
        $commonPartsInsuranceStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'If the common parts are separately insured, please obtain and provide a copy of the insurance policy covering the common parts and evidence of payment of the latest premium from the commonhold association:',
                    'help_text' => 'The seller should provide copies of the buildings insurance policy and a receipt for payment.',
                ])
                ->make()
                ->toArray()
        );

        $answerCommonPartsInsuranceStep = $commonPartsInsuranceStep->answers()->create(
            Answer::factory()
            ->state([
                'type' => AnswerType::File,
                'details' => [
                    'pdfFieldPrefix' => '5.2',
                ],
            ])
                    ->make()
                    ->toArray()
        );

        $answerCommonPartsInsuranceStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End Common parts insurance
    }

    protected function consents(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Consents',
                ])
                ->make()
                ->toArray()
        );

        // 6.1 Commonhold association consent
        $commonholdAssociationConsentStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller know if the commonhold association has given its consent to the transfer of part only of any of the units?',
                    'help_text' => 'The seller should advise if they are aware of any such consents and where necessary, provide further details.',
                ])
                ->make()
                ->toArray()
        );

        $answerCommonholdAssociationConsentStep = $commonholdAssociationConsentStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '6.1_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '6.1_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCommonholdAssociationConsentStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextCommonholdAssociationConsent = $commonholdAssociationConsentStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the commonhold association has given its consent to the transfer of part only of the unit',
                        'pdfFormFieldName' => '6.1_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextCommonholdAssociationConsent->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextCommonholdAssociationConsent->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextCommonholdAssociationConsent->id,
            'answer_id' => $answerCommonholdAssociationConsentStep->id,
            'selected_value' => 'Yes',
        ]);
        // End Commonhold association consent

        // 6.2 Commonhold association refused content
        $commonholdAssociationRefusedConsentStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller know if the commonhold association has refused to give its consent to the transfer of part only of any of the units?',
                    'help_text' => 'The seller should advise if they are aware of any such refusals and where necessary, provide further details.',
                ])
                ->make()
                ->toArray()
        );

        $answerCommonholdAssociationRefusedConsentStep = $commonholdAssociationRefusedConsentStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '6.2_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '6.2_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCommonholdAssociationRefusedConsentStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextCommonholdAssociationRefusedConsent = $commonholdAssociationRefusedConsentStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the commonhold association has refused to give its consent to the transfer of part only of the unit',
                        'pdfFormFieldName' => '6.2_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextCommonholdAssociationRefusedConsent->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextCommonholdAssociationRefusedConsent->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextCommonholdAssociationRefusedConsent->id,
            'answer_id' => $answerCommonholdAssociationRefusedConsentStep->id,
            'selected_value' => 'Yes',
        ]);
        // End Commonhold association refused content
    }

    protected function complaints(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Complaints',
                ])
                ->make()
                ->toArray()
        );

        // 7.1 Seller received any complaints from the commonhold
        $sellerReceivedAnyComplaintsFromTheCommonholdStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the seller received any complaint from the commonhold association, another unit-holder or the occupier of any unit about anything you have or have not done?',
                    'help_text' => 'Please provide information about any current or past complaints. This needs to include the cause of the complaint (e.g. complaints relating to noise) and any action taken to resolve matters.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerReceivedAnyComplaintsFromTheCommonholdStep = $sellerReceivedAnyComplaintsFromTheCommonholdStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '7.1_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '7.1_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerReceivedAnyComplaintsFromTheCommonholdStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerReceivedAnyComplaintsFromTheCommonhold = $sellerReceivedAnyComplaintsFromTheCommonholdStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the seller received a complaint due to excessive noise',
                        'pdfFormFieldName' => '7.1_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerReceivedAnyComplaintsFromTheCommonhold->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerReceivedAnyComplaintsFromTheCommonhold->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerReceivedAnyComplaintsFromTheCommonhold->id,
            'answer_id' => $answerSellerReceivedAnyComplaintsFromTheCommonholdStep->id,
            'selected_value' => 'Yes',
        ]);
        // End Seller received any complaints from the commonhold

        // 7.2 Seller received any complaints from the commonhold that has not been resolved
        $sellerReceivedAnyComplaintsFromTheCommonholdThatHasNotBeenResolvedStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the seller complained to the commonhold association, another unit-holder or the occupier of any unit about anything they have or have not done?',
                    'help_text' => 'Please provide information about any current or past complaints. This needs to include the cause of the complaint (e.g. complaints relating to noise) and any action taken to resolve matters.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerReceivedAnyComplaintsFromTheCommonholdThatHasNotBeenResolvedStep = $sellerReceivedAnyComplaintsFromTheCommonholdThatHasNotBeenResolvedStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '7.2_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '7.2_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerReceivedAnyComplaintsFromTheCommonholdThatHasNotBeenResolvedStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerReceivedAnyComplaintsFromTheCommonholdThatHasNotBeenResolved = $sellerReceivedAnyComplaintsFromTheCommonholdThatHasNotBeenResolvedStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the commonhold association has refused to a transfer the part for the southern units',
                        'pdfFormFieldName' => '7.2_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerReceivedAnyComplaintsFromTheCommonholdThatHasNotBeenResolved->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerReceivedAnyComplaintsFromTheCommonholdThatHasNotBeenResolved->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerReceivedAnyComplaintsFromTheCommonholdThatHasNotBeenResolved->id,
            'answer_id' => $answerSellerReceivedAnyComplaintsFromTheCommonholdThatHasNotBeenResolvedStep->id,
            'selected_value' => 'Yes',
        ]);
        // End Seller received any complaints from the commonhold that has not been resolved

        // 7.3 Copy of any decision made by the commonhold association
        $copyOfAnyDecisionMadeByTheCommonholdAssociationStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of any decision made by the ombudsman affecting the unit or the common parts:',
                    'help_text' => 'There may be a commonhold ombudsman who will be able to investigate and settle disputes.',
                ])
                ->make()
                ->toArray()
        );

        $answerCopyOfAnyDecisionMadeByTheCommonholdAssociationStep = $copyOfAnyDecisionMadeByTheCommonholdAssociationStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '7.3',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCopyOfAnyDecisionMadeByTheCommonholdAssociationStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End Copy of any decision made by the commonhold association
    }

    protected function rightsForTheDeveloper(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Rights for the developer',
                ])
                ->make()
                ->toArray()
        );

        // 8.1 Has the developer ceased to be entitled to exercise any of the rights?
        $hasTheDeveloperCeasedToBeEntitledToExerciseAnyOfTheRightsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the developer ceased to be entitled to exercise any of the rights?',
                    'help_text' => 'Development rights\' may be rights to complete the building work, rights in connection with marketing units or the right to appoint directors of the commonhold association.',
                ])
                ->make()
                ->toArray()
        );

        $answerHasTheDeveloperCeasedToBeEntitledToExerciseAnyOfTheRightsStep = $hasTheDeveloperCeasedToBeEntitledToExerciseAnyOfTheRightsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '8.1_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '8.1_no'],
                            ['value' => 'Not applicable', 'pdfFormFieldName' => '8.1_not_applicable'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerHasTheDeveloperCeasedToBeEntitledToExerciseAnyOfTheRightsStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextHasTheDeveloperCeasedToBeEntitledToExerciseAnyOfTheRights = $hasTheDeveloperCeasedToBeEntitledToExerciseAnyOfTheRightsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the developer is no longer entitled to exercise rights due to them not complying with the terms of the Lease',
                        'pdfFormFieldName' => '8.1_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextHasTheDeveloperCeasedToBeEntitledToExerciseAnyOfTheRights->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextHasTheDeveloperCeasedToBeEntitledToExerciseAnyOfTheRights->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextHasTheDeveloperCeasedToBeEntitledToExerciseAnyOfTheRights->id,
            'answer_id' => $answerHasTheDeveloperCeasedToBeEntitledToExerciseAnyOfTheRightsStep->id,
            'selected_value' => 'Yes',
        ]);
        // End Has the developer ceased to be entitled to exercise any of the rights?
    }
}
