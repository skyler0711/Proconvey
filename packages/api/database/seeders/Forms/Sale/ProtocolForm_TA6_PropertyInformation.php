<?php

namespace Database\Seeders\Forms\Sale;

use App\Enums\AnswerType;
use App\Enums\ConditionType;
use App\Enums\FileTextAnswerTypes;
use App\Enums\FormGroup;
use App\Enums\FormType;
use App\Enums\PropertyType;
use App\Models\Answer;
use App\Models\Condition;
use App\Models\Form;
use App\Models\Section;
use App\Models\Step;
use App\Models\ValidationRule;
use Illuminate\Database\Seeder;

class ProtocolForm_TA6_PropertyInformation extends Seeder
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
                'name' => 'TA6: Property Information',
                'group' => FormGroup::Protocol,
                'description' => 'Detailed information about the property for the buyer',
                'ta_form_template' => FormType::TA6PropertyInformation,
                'order_number' => 5,
                'signature_coords' => [0.145, 0.555],
                'current_date_field' => ['signed_date1', 'signed_date2'],
                'type' => PropertyType::Sale,
            ])
            ->create();

        $answerId = Answer::whereHas('step', function ($query) {
            $query->where('question', 'Is the property for sale a freehold or leasehold?');
        })->first()->id;

        $form->conditions()->create([
            'answer_id' => $answerId,
            'selected_value' => 'Freehold',
            'type' => ConditionType::OR,
        ]);

        $form->conditions()->create([
            'answer_id' => $answerId,
            'selected_value' => 'Leasehold',
            'type' => ConditionType::OR,
        ]);

        $form->conditions()->create([
            'answer_id' => $answerId,
            'selected_value' => 'Commonhold',
            'type' => ConditionType::OR,
        ]);
        $form->conditions()->create([
            'answer_id' => $answerId,
            'selected_value' => 'Shared ownership',
            'type' => ConditionType::OR,
        ]);

        $this->propertyBoundaries($form);
        $this->disputesAndComplaints($form);
        $this->noticesAndProposals($form);
        $this->alterationsPlanningAndBuildingControl($form);
        $this->guaranteesAndWarranties($form);
        $this->insurance($form);
        $this->environmentalMatters($form);
        $this->rightsAndInformalArrangements($form);
        $this->parking($form);
        $this->otherChanges($form);
        $this->occupiers($form);
        $this->services($form);
        $this->connectionToUtilitiesAndServices($form);
        $this->transactionInformation($form);
    }

    protected function propertyBoundaries(Form $form)
    {
        // Boundaries Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Boundaries',
                ])
                ->make()
                ->toArray()
        );

        $boundaries = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Looking towards the property from the road, who owns or accepts responsibility to maintain or repair the boundary features?',
                    'help_text' => 'A boundary feature is a physical feature that separates property from a neighbouring property. They may be natural, such as hedges, or man-made such as ditches, fences or walls. If your property is on a corner plot, you can consider the "front" the side on the street from which the property is addressed.',
                ])
                ->make()
                ->toArray()
        );

        $answerBoundariesLeft = $boundaries->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'label' => 'On the left?',
                        'options' => [
                            ['value' => 'Seller', 'pdfFormFieldName' => '1.1a_seller'],
                            ['value' => 'Shared', 'pdfFormFieldName' => '1.1a_shared'],
                            ['value' => 'Neighbour', 'pdfFormFieldName' => '1.1a_neighbour'],
                            ['value' => 'Not known', 'pdfFormFieldName' => '1.1a_not_known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerBoundariesRight = $boundaries->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'label' => 'On the right?',
                        'options' => [
                            ['value' => 'Seller', 'pdfFormFieldName' => '1.1b_seller'],
                            ['value' => 'Shared', 'pdfFormFieldName' => '1.1b_shared'],
                            ['value' => 'Neighbour', 'pdfFormFieldName' => '1.1b_neighbour'],
                            ['value' => 'Not known', 'pdfFormFieldName' => '1.1b_not_known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerBoundariesRear = $boundaries->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'label' => 'At the rear?',
                        'options' => [
                            ['value' => 'Seller', 'pdfFormFieldName' => '1.1c_seller'],
                            ['value' => 'Shared', 'pdfFormFieldName' => '1.1c_shared'],
                            ['value' => 'Neighbour', 'pdfFormFieldName' => '1.1c_neighbour'],
                            ['value' => 'Not known', 'pdfFormFieldName' => '1.1c_not_known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerBoundariesFront = $boundaries->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'label' => 'At the front?',
                        'options' => [
                            ['value' => 'Seller', 'pdfFormFieldName' => '1.1d_seller'],
                            ['value' => 'Shared', 'pdfFormFieldName' => '1.1d_shared'],
                            ['value' => 'Neighbour', 'pdfFormFieldName' => '1.1d_neighbour'],
                            ['value' => 'Not known', 'pdfFormFieldName' => '1.1d_not_known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerBoundariesLeft->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerBoundariesRight->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerBoundariesRear->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerBoundariesFront->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Boundaries Step

        // Irregular Boundaries Step
        $irregualBoundaries = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are the boundaries irregular?',
                    'help_text' => 'If the property boundaries are irregular we need further information about their location. Please provide either a written description of who owns any irregular boundaries or a provide plan. If you provide a plan, please mark clearly the boundaries of the property.',
                ])
                ->make()
                ->toArray()
        );

        $answerIrregularBoundaries = $irregualBoundaries->answers()->create(
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

        $answerIrregularBoundaries->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextIrregularBoundaries = $irregualBoundaries->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. there is a parcel of land within the boundaries and the the owner of said land is responsible for repair and maintenance',
                        'pdfFormFieldName' => '1.2_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextIrregularBoundaries->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerIrregularBoundaries->id,
            'answer_id' => $answerIrregularBoundaries->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextIrregularBoundaries->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Irregular Boundaries Step

        // File Upload showing boundaries of the Property
        $boundariesPlanDocument = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a plan showing the boundaries of the property and who accepts responsibility to maintain or repair:',
                    'help_text' => 'You should provide either a written description of who owns any irregular boundaries or a plan. Where a plan is provided, sellers should clearly mark the boundaries of the property on the plan.
                    Where property is registered at Land Registry, the property boundaries will be marked but not usually in an absolute way on the title plan. Most land in England and Wales is registered with what is known as ‘general’ boundaries. This means that the registered title is not conclusive and the boundaries on the title plan may not exactly match the physical boundary features at the property.
                    There may be land that is not shown within the boundaries of the property as shown on the registered title, but which the seller is nonetheless using. The seller may have acquired title to this land through adverse possession. If this is the case, the buyer may wish to investigate whether they will also acquire title to that additional land.
                    Conversely, there may be land that lies within the boundaries of the  property as shown on the registered title, but which is being used and occupied by people other than the seller. The buyer may wish to investigate whether the people who are using the land may have acquired rights to continue doing so through adverse possession or squatting.',
                ])
                ->make()
                ->toArray()
        );

        $boundariesPlanDocument->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFormField' => '1.2_text',
                        'textAnswers' => [
                            FileTextAnswerTypes::Enclosed => 'Boundaries plan enclosed',
                            FileTextAnswerTypes::AddLater => 'Boundaries plan to follow',
                            FileTextAnswerTypes::NotApplicable => 'Boundaries plan not available',
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $boundariesPlanDocument->conditions()->create([
            'answer_id' => $answerIrregularBoundaries->id,
            'selected_value' => 'Yes',
        ]);
        // End File Upload showing boundaries of the Property

        // Seller aware of any boundary feature having been moved
        $boundaryFeature = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the seller aware of any boundary feature having been moved?',
                    'help_text' => 'Boundaries may have changed during the time you have lived at the property. We need to verify whether any boundary feature has been moved. If any property boundary has been moved, please provide details including information about the year any change took place.',
                ])
                ->make()
                ->toArray()
        );

        $answerBoundaryFeature = $boundaryFeature->answers()->create(
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

        $answerBoundaryFeature->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextBoundaryFeature = $boundaryFeature->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the rear boundary was extended by two feet in 2007',
                        'pdfFormFieldName' => '1.3_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextBoundaryFeature->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextBoundaryFeature->id,
            'answer_id' => $answerBoundaryFeature->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextBoundaryFeature->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Seller aware of any boundary feature having been moved

        // Adjacent Land or Property purchased by the seller
        $adjacentLand = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'During the seller\'s ownership, has any adjacent land or property been purchased by the seller?',
                    'help_text' => 'Boundaries may have changed during the time you have lived at the property. We need to verify whether any boundary feature has been moved. If any property boundary has been moved, please provide details including information about the year any change took place.',
                ])
                ->make()
                ->toArray()
        );

        $answerAdjacentLand = $adjacentLand->answers()->create(
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

        $answerAdjacentLand->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextAdjacentLand = $adjacentLand->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. a small parcel of land to the rear of the property was purchased in 2005. The land has been merged with the property to extend the rear garden.',
                        'pdfFormFieldName' => '1.4_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextAdjacentLand->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextAdjacentLand->id,
            'answer_id' => $answerAdjacentLand->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextAdjacentLand->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Adjacent Land or Property purchased by the seller

        // Property overhang
        $propertyOverhang = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does any part of the property or any building on the property overhang, or project under, the boundary of the neighbouring property or road?',
                    'help_text' => 'We need to identify any part of the property that overhangs or projects under, the boundary of a neighbouring property or a road. (E.g. cellars under the pavement, overhanging eaves, covered walkways, flying freeholds, projecting signs, vaults beneath ground level etc.)',
                ])
                ->make()
                ->toArray()
        );

        $answerPropertyOverhang = $propertyOverhang->answers()->create(
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

        $answerPropertyOverhang->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextPropertyOverhang = $propertyOverhang->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the small upstairs bedroom overhangs into our neighbours property',
                        'pdfFormFieldName' => '1.5_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextPropertyOverhang->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextPropertyOverhang->id,
            'answer_id' => $answerPropertyOverhang->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextPropertyOverhang->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Property overhang

        // Notice Received
        $noticeReceived = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has any notice been received under the Party Wall etc. Act 1996 in respect of any shared/party boundaries?',
                    'help_text' => 'The Party Wall etc. Act 1996 prevents an owner carrying out work to a common structure, or excavation work near to the boundary, without giving notice to the neighbouring owner. If a boundary structure such as a wall, is jointly used by you and a neighbouring property, it may be a party structure. If one owner does not comply with the legislation, any work done may have to be dismantled and the land restored to its former condition.',
                ])
                ->make()
                ->toArray()
        );

        $answerNoticeReceived = $noticeReceived->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '1.6_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '1.6_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerNoticeReceived->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextNoticeReceived = $noticeReceived->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'Please give details of any work carried out or agreed, e.g. our neighbour to the left provided us with a notice alerting us to the fact he would be replacing his wall with a fence',
                        'pdfFormFieldName' => '1.6_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextNoticeReceived->conditions()->create([
            'answer_id' => $answerNoticeReceived->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextNoticeReceived->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Notice Received

        // Notice File Upload
        $noticeFileUpload = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the notice received under the Party Wall etc. Act 1996:',
                    'help_text' => 'Please provide a copy of the notice received. The seller should have received comprehensive details of the work that was planned, the date that work would start, as well as any access requirements over your property.',
                ])
                ->make()
                ->toArray()
        );

        $noticeFileUpload->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '1.6',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $noticeFileUpload->conditions()->create([
            'conditionable_type' => 'step',
            'answer_id' => $answerNoticeReceived->id,
            'selected_value' => 'Yes',
        ]);
        // End of Notice File Upload
    }

    protected function disputesAndComplaints(Form $form)
    {
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Disputes and complaints',
                ])
                ->make()
                ->toArray()
        );

        //Any Disputes
        $anyDisputes = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Have there been any disputes or complaints regarding the property or a property nearby?',
                    'help_text' => 'Please provide information about any current or past disputes. This needs to include the cause of the dispute (e.g. complaints relating to noise) and any action taken to resolve matters.',
                ])
                ->make()
                ->toArray()
        );

        $answerAnyDisputes = $anyDisputes->answers()->create(
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

        $answerAnyDisputes->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextAnyDisputes = $anyDisputes->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. a noise compliant relating to number 4 was submitted to the council by ourselves. The issue has since been resolved.',
                        'pdfFormFieldName' => '2.1_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextAnyDisputes->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextAnyDisputes->id,
            'answer_id' => $answerAnyDisputes->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextAnyDisputes->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Any Disputes

        // Seller aware of any disputes
        $sellerAwareDisputes = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the seller aware of anything which might lead to a dispute about the property or a property nearby?',
                    'help_text' => 'Please provide information about anything that could lead to a dispute in the future.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareDisputes = $sellerAwareDisputes->answers()->create(
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

        $answerSellerAwareDisputes->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareDisputes = $sellerAwareDisputes->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the owner of the property to the rear wish to erect and extension which we believe will breach our right to light',
                        'pdfFormFieldName' => '2.2_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareDisputes->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerAwareDisputes->id,
            'answer_id' => $answerSellerAwareDisputes->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextSellerAwareDisputes->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Seller aware of any disputes
    }

    protected function noticesAndProposals(Form $form)
    {
        // Notices and proposals Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Notices and proposals',
                ])
                ->make()
                ->toArray()
        );

        // Received or Sent Notices or Proposals
        $receivedSentNotices = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Have any notices or correspondence been received or sent (e.g. from or to a neighbour, council or government department), or any negotiations or discussions taken place, which affect the property or a property nearby?',
                    'help_text' => 'Please provide copies of any letters or communications from neighbours, the local authority or government departments etc. which might affect the property.',
                ])
                ->make()
                ->toArray()
        );

        $answerRecievedSentNotices = $receivedSentNotices->answers()->create(
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

        $answerRecievedSentNotices->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextRecievedSentNotices = $receivedSentNotices->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the council has sent notices advising of major roadworks going ahead next year',
                        'pdfFormFieldName' => '3.1_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextRecievedSentNotices->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextRecievedSentNotices->id,
            'answer_id' => $answerRecievedSentNotices->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextRecievedSentNotices->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Received or Sent Notices or Proposals

        // Seller aware of any proposals to develop Property or Land nearby
        $sellerAwareProposals = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the seller aware of any proposals to develop property or land nearby, or of any proposals to make alterations to buildings nearby?',
                    'help_text' => 'Please provide details of any proposals to develop or change the use of nearby land or buildings.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareProposals = $sellerAwareProposals->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '3.2_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '3.2_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareProposals->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareProposals = $sellerAwareProposals->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the open land to the south is being used for a development of 200 homes',
                        'pdfFormFieldName' => '3.2_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareProposals->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerAwareProposals->id,
            'answer_id' => $answerSellerAwareProposals->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextSellerAwareProposals->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Seller aware of any proposals to develop Property or Land nearby
    }

    protected function alterationsPlanningAndBuildingControl(Form $form)
    {
        //Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Alterations, planning and building control',
                ])
                ->make()
                ->toArray()
        );

        // 4.1 Alterations to the property
        $alterationsProperty = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Have any of the following changes been made to the whole or any part of the property (including the garden)?',
                    'help_text' => 'Since the seller took ownership of the property, there may have been some changes or home improvements. We need to know if any of the following works were undertaken by the seller or any previous owner.',
                ])
                ->make()
                ->toArray()
        );

        $answerAlterationsProperty = $alterationsProperty->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::MultiSelect,
                    'details' => [
                        'options' => [
                            [
                                'value' => 'Basement Conversion',
                                'pdfFormFieldName' => '4.1a_text',
                            ],
                            [
                                'value' => 'Loft Conversion',
                                'pdfFormFieldName' => '4.1a_text',
                            ],
                            [
                                'value' => 'Change of use (e.g. from an office to a residence)',
                                'pdfFormFieldName' => '4.1b_yes',
                                'altText' => 'Yes',
                            ],
                            [
                                'value' => 'Conservatory',
                                'pdfFormFieldName' => '4.1d_yes',
                                'altText' => 'Yes',
                            ],
                            [
                                'value' => 'Windows/Doors since 1 April 2002',
                                'pdfFormFieldName' => '4.1c_yes',
                                'altText' => 'Yes',
                            ],
                            [
                                'value' => 'Decking',
                                'pdfFormFieldName' => '4.1a_text',
                            ],
                            [
                                'value' => 'Driveway/Hardstanding',
                                'pdfFormFieldName' => '4.1a_text',
                            ],
                            [
                                'value' => 'Extension',
                                'pdfFormFieldName' => '4.1a_text',
                            ],
                            [
                                'value' => 'Porch',
                                'pdfFormFieldName' => '4.1a_text',
                            ],
                            [
                                'value' => 'Roof Replacement',
                                'pdfFormFieldName' => '4.1a_text',
                            ],
                            [
                                'value' => 'Solar Panels',
                                'pdfFormFieldName' => '4.6c_yes',
                                'altText' => 'Yes',
                            ],
                            [
                                'value' => 'Wall Removal',
                                'pdfFormFieldName' => '4.1a_text',
                            ],
                            [
                                'value' => 'Wood Burner',
                                'pdfFormFieldName' => '4.1a_text',
                            ],
                            [
                                'value' => 'Gas Fire',
                                'pdfFormFieldName' => '4.1a_text',
                            ],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerAlterationsProperty->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $isAlternationPropertyApplicable = $alterationsProperty->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not applicable',
            ],
        ]);

        $answerAlterationsProperty->conditions()->create([
            'answer_id' => $isAlternationPropertyApplicable->id,
            'selected_value' => '0',
        ]);
        // End of Alterations to the property

        // 4.1a Year of the basement conversion
        $yearBasementConversion = $section->steps()->create([
            'question' => 'Please enter the year the basement conversion took place:',
            'help_text' => 'A conveyancer or solicitor will require to know the year the basement conversion took place.',
        ]);

        $yearBasementConversion->conditions()->create([
            'answer_id' => $answerAlterationsProperty->id,
            'selected_value' => 'Basement Conversion',
        ]);

        $answerYearBasementConversion = $yearBasementConversion->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Year',
                'placeholder' => 'Enter the year',
                'pdfFormFieldName' => '4.1a_text',
                'altText' => 'Basement conversion completed in %s',
            ],
        ]);

        $answerYearBasementConversion->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerYearBasementConversionKnown = $yearBasementConversion->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
                'pdfFormFieldName' => '4.1a_text',
                'altValue' => 'not known',
                'altText' => 'Basement completion date %s',
            ],
        ]);
        $answerYearBasementConversion->conditions()->create([
            'answer_id' => $answerYearBasementConversionKnown->id,
            'selected_value' => '0',
        ]);
        // End of Year of the basement conversion

        // 4.1b Basement Building Regulations
        $basementBuildingRegulations = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Did the basement conversion require building regulations?',
                    'help_text' => 'If you are not sure whether planning permissions or building regulations were required for the basement conversion, please <a target="_blank" href="https://www.planningportal.co.uk/info/200130/common_projects/5/basements">click here</a>.',
                ])
                ->make()
                ->toArray()
        );

        $basementBuildingRegulations->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Basement Conversion',
                ])
                ->make()
                ->toArray()
        );

        $answerBasementBuildingRegulations = $basementBuildingRegulations->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            [
                                'value' => 'Yes',
                            ],
                            [
                                'value' => 'No',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Building regulations not required for Basement Conversion',
                            ],
                            [
                                'value' => 'Not known',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Not known if building regulations required for Basement Conversion',
                            ],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerBasementBuildingRegulations->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerUploadBasementBuildingRegulations = $basementBuildingRegulations->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFormField' => '4.1a_text',
                        'linkingAnswer' => 'Basement conversion',
                        'textAnswers' => [
                            FileTextAnswerTypes::Enclosed => 'Building regulations required for Basement Conversion - Attached',
                            FileTextAnswerTypes::AddLater => 'Building regulations required for Basement Conversion - To follow',
                            FileTextAnswerTypes::NotApplicable => 'Building regulations required for Basement Conversion - Not available',
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerUploadBasementBuildingRegulations->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerUploadBasementBuildingRegulations->conditions()->create([
            'answer_id' => $answerBasementBuildingRegulations->id,
            'selected_value' => 'Yes',
        ]);
        // End of Basement Building Regulations

        // 4.1c Year of the loft conversion
        $yearLoftConversion = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the year the loft conversion took place:',
                    'help_text' => 'A conveyancer or solicitor will require to know the year the loft conversion took place.',
                ])
                ->make()
                ->toArray()
        );

        $yearLoftConversion->conditions()->create([
            'answer_id' => $answerAlterationsProperty->id,
            'selected_value' => 'Loft Conversion',
        ]);

        $answerYearLoftConversion = $yearLoftConversion->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Year',
                        'placeholder' => 'Enter the year',
                        'pdfFormFieldName' => '4.1a_text',
                        'altText' => 'Loft conversion completed in %s',
                    ],
                ])
                ->make()
                ->toArray()
        );
        $answerYearLoftConversion->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerYearLoftConversionKnown = $yearLoftConversion->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
                'pdfFormFieldName' => '4.1a_text',
                'altValue' => 'not known',
                'altText' => 'Loft conversion completion date %s',
            ],
        ]);

        $answerYearLoftConversion->conditions()->create([
            'answer_id' => $answerYearLoftConversionKnown->id,
            'selected_value' => '0',
        ]);
        // End of Year of the loft conversion

        // 4.1d Loft Building Regulations
        $loftBuildingRegulations = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Did the loft conversion require building regulations?',
                    'help_text' => 'If you are not sure whether planning permissions or building regulations were required for the loft conversion, please <a target="_blank" href="https://www.planningportal.co.uk/info/200130/common_projects/36/loft_conversion">click here</a>.',
                ])
                ->make()
                ->toArray()
        );

        $loftBuildingRegulations->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Loft Conversion',
                ])
                ->make()
                ->toArray()
        );

        $answerLoftBuildingRegulations = $loftBuildingRegulations->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            [
                                'value' => 'Yes',
                            ],
                            [
                                'value' => 'No',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Building regulations not required for Loft Conversion',
                            ],
                            [
                                'value' => 'Not known',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Not known if building regulations required for Loft Conversion',
                            ],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerLoftBuildingRegulations->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerUploadLoftBuildingRegulations = $loftBuildingRegulations->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFormField' => '4.1a_text',
                        'textAnswers' => [
                            FileTextAnswerTypes::Enclosed => 'Building regulations required for Loft Conversion - Attached',
                            FileTextAnswerTypes::AddLater => 'Building regulations required for Loft Conversion - To follow',
                            FileTextAnswerTypes::NotApplicable => 'Building regulations required for Loft Conversion - Not available',
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerUploadLoftBuildingRegulations->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerUploadLoftBuildingRegulations->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerUploadLoftBuildingRegulations->id,
            'answer_id' => $answerLoftBuildingRegulations->id,
            'selected_value' => 'Yes',
        ]);
        // End of Loft Building Regulations

        // 4.1e Change of use
        $changeOfUse = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide details about the change of use:',
                    'help_text' => 'Please provide details regarding the change of use of the property, e.g. from office to residential home.',
                ])
                ->make()
                ->toArray()
        );

        $changeOfUse->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Change of use (e.g. from an office to a residence)',
                ])
                ->make()
                ->toArray()
        );

        $answerChangeOfUse = $changeOfUse->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'Please provide details regarding the change of use of the property, e.g. from office to residential home.',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerChangeOfUse->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Change of use

        // 4.1f Year of Change of Use
        $yearChangeOfUse = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the year the change of use occurred:',
                    'help_text' => 'A conveyancer or solicitor will require to know the year the change of use took place.',
                ])
                ->make()
                ->toArray()
        );

        $yearChangeOfUse->conditions()->create(
            Condition::factory()
                ->state([
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Change of use (e.g. from an office to a residence)',
                ])
                ->make()
                ->toArray()
        );

        $answerYearChangeOfUse = $yearChangeOfUse->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Year',
                'placeholder' => 'Enter the year',
                'pdfFormFieldName' => '4.1b_year',
            ],
        ]);

        $answerYearChangeOfUse->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerYearChangeOfUseKnown = $yearChangeOfUse->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
                'pdfFormFieldName' => '4.1a_text',
                'altValue' => 'not known',
                'altText' => 'Change of use permissions completion date %s',
            ],
        ]);

        $answerYearChangeOfUse->conditions()->create([
            'answer_id' => $answerYearChangeOfUseKnown->id,
            'selected_value' => '0',
        ]);
        // End of Year of Change of Use

        // 4.1g Change of use Permission
        $changeOfUsePermission = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has a change of use permission been granted by the council?',
                    'help_text' => 'If you need more information about the change of use for your property, please <a target="_blank" href="https://www.planningportal.co.uk/info/200130/common_projects/9/change_of_use">click here</a>.',
                ])
                ->make()
                ->toArray()
        );

        $changeOfUsePermission->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Change of use (e.g. from an office to a residence)',
                ])
                ->make()
                ->toArray()
        );

        $answerChangeOfUsePermission = $changeOfUsePermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            [
                                'value' => 'Yes',
                            ],
                            [
                                'value' => 'No',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Building regulations not required for Change of Use Permissions',
                            ],
                            [
                                'value' => 'Not known',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Not known if building regulations required for Change of Use Permissions',
                            ],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerChangeOfUsePermission->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerChangeOfUsePermissionUpload = $changeOfUsePermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFormField' => '4.1a_text',
                        'textAnswers' => [
                            FileTextAnswerTypes::Enclosed => 'Council permission required for Change of Use Permissions - Attached',
                            FileTextAnswerTypes::AddLater => 'Council permission required for Change of Use Permissions - To follow',
                            FileTextAnswerTypes::NotApplicable => 'Council permission required for Change of Use Permissions - Not available',
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerChangeOfUsePermissionUpload->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerChangeOfUsePermissionUpload->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerChangeOfUsePermissionUpload->id,
            'answer_id' => $answerChangeOfUsePermission->id,
            'selected_value' => 'Yes',
        ]);
        // End of Change of use Permission

        // 4.1h Year the conservatory was built
        $yearConservatoryBuilt = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the year the conservatory was erected:',
                    'help_text' => 'A conveyancer or solicitor will require to know the year the conservatory was erected.',
                ])
                ->make()
                ->toArray()
        );

        $yearConservatoryBuilt->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Conservatory',
                ])
                ->make()
                ->toArray()
        );

        $answerYearConservatoryBuilt = $yearConservatoryBuilt->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Year',
                'placeholder' => 'Enter the year',
                'pdfFormFieldName' => '4.1d_year',
            ],
        ]);

        $answerYearConservatoryBuilt->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerYearConservatoryBuiltKnown = $yearConservatoryBuilt->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
                'pdfFormFieldName' => '4.1a_text',
                'altValue' => 'not known',
                'altText' => 'Conservatory erection completion date %s',
            ],
        ]);

        $answerYearConservatoryBuilt->conditions()->create([
            'answer_id' => $answerYearConservatoryBuiltKnown->id,
            'selected_value' => '0',
        ]);
        // End of Year the conservatory was built

        // 4.1i Conservatory Permission
        $conservatoryPermission = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Did the conservatory erection require building regulations?',
                    'help_text' => 'If you are not sure whether planning permissions or building regulations were required for the conservatory, please <a target="_blank" href="https://www.planningportal.co.uk/info/200130/common_projects/10/conservatories">click here</a>.',
                ])
                ->make()
                ->toArray()
        );

        $conservatoryPermission->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Conservatory',
                ])
                ->make()
                ->toArray()
        );

        $answerConservatoryPermission = $conservatoryPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            [
                                'value' => 'Yes',
                            ],
                            [
                                'value' => 'No',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Building regulations not required for Conservatory Erection',
                            ],
                            [
                                'value' => 'Not known',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Not known if building regulations required for Conservatory Erection',
                            ],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerConservatoryPermission->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerConservatoryPermissionUpload = $conservatoryPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFormField' => '4.1a_text',
                        'textAnswers' => [
                            FileTextAnswerTypes::Enclosed => 'Council permissions required for Conservatory Erection - Attached',
                            FileTextAnswerTypes::AddLater => 'Council permissions required for Conservatory Erection - To follow',
                            FileTextAnswerTypes::NotApplicable => 'Council permissions required for Conservatory Erection - Not available',
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerConservatoryPermissionUpload->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerConservatoryPermissionUpload->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerConservatoryPermissionUpload->id,
            'answer_id' => $answerConservatoryPermission->id,
            'selected_value' => 'Yes',
        ]);
        // End of Conservatory Permission

        // 4.1j Year of Window Installation
        $yearWindowInstallation = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => "Please enter the year the windows/doors' installation or replacement took place:",
                    'help_text' => "A conveyancer or solicitor will require to know the year the windows/doors' installation or replacement took place.",
                ])
                ->make()
                ->toArray()
        );

        $yearWindowInstallation->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Windows/Doors since 1 April 2002',
                ])
                ->make()
                ->toArray()
        );

        $answerYearWindowInstallation = $yearWindowInstallation->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Year',
                'placeholder' => 'Enter the year',
                'pdfFormFieldName' => '4.1c_year',
            ],
        ]);

        $answerYearWindowInstallation->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerYearWindowInstallationKnown = $yearWindowInstallation->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
                'pdfFormFieldName' => '4.1a_text',
                'altValue' => 'not known',
                'altText' => 'Windows/doors completion date %s',
            ],
        ]);

        $answerYearWindowInstallation->conditions()->create([
            'answer_id' => $answerYearWindowInstallationKnown->id,
            'selected_value' => '0',
        ]);
        // End of Year of Window Installation

        // 4.1k Window Installation Permission
        $windowInstallationPermission = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Did the windows/doors installation or replacement require building regulations?',
                    'help_text' => 'If you are not sure whether planning permissions or building regulations were required for the windows and doors installation, please <a target="_blank" href="https://www.planningportal.co.uk/info/200130/common_projects/14/doors_and_windows/2">click here</a>.',
                ])
                ->make()
                ->toArray()
        );

        $windowInstallationPermission->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Windows/Doors since 1 April 2002',
                ])
                ->make()
                ->toArray()
        );

        $answerWindowInstallationPermission = $windowInstallationPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            [
                                'value' => 'Yes',
                            ],
                            [
                                'value' => 'No',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Building regulations not required for Windows/Door installation',
                            ],
                            [
                                'value' => 'Not known',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Not known if building regulations required for Windows/Door installation',
                            ],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerWindowInstallationPermission->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerWindowInstallationPermissionUpload = $windowInstallationPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFormField' => '4.1a_text',
                        'textAnswers' => [
                            FileTextAnswerTypes::Enclosed => 'Building regulations required for Windows/Doors installation - Attached',
                            FileTextAnswerTypes::AddLater => 'Building regulations required for Windows/Doors installation - To follow',
                            FileTextAnswerTypes::NotApplicable => 'Building regulations required for Windows/Doors installation - Not available',
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerWindowInstallationPermissionUpload->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerWindowInstallationPermissionUpload->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerWindowInstallationPermissionUpload->id,
            'answer_id' => $answerWindowInstallationPermission->id,
            'selected_value' => 'Yes',
        ]);
        // End of Window Installation Permission

        // 4.1l Year of Decking Installation
        $yearDeckingInstallation = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the year the decking installation took place:',
                    'help_text' => 'A conveyancer or solicitor will require to know the year the decking installations took place.',
                ])
                ->make()
                ->toArray()
        );

        $yearDeckingInstallation->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Decking',
                ])
                ->make()
                ->toArray()
        );

        $answerYearDeckingInstallation = $yearDeckingInstallation->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Year',
                'placeholder' => 'Enter the year',
                'pdfFormFieldName' => '4.1a_text',
                'altText' => 'Decking completed in %s',
            ],
        ]);

        $answerYearDeckingInstallation->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerYearDeckingInstallationKnown = $yearDeckingInstallation->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
                'pdfFormFieldName' => '4.1a_text',
                'altValue' => 'not known',
                'altText' => 'Decking completion date %s',
            ],
        ]);

        $answerYearDeckingInstallation->conditions()->create([
            'answer_id' => $answerYearDeckingInstallationKnown->id,
            'selected_value' => '0',
        ]);
        // End of Year of Decking Installation

        // 4.1m Decking Installation Permission
        $deckingInstallationPermission = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Did the decking installation require building regulations?',
                    'help_text' => 'If you are not sure whether planning permissions or building regulations were required for the decking installation, please <a target="_blank" href="https://www.planningportal.co.uk/info/200130/common_projects/11/decking">click here</a>.',
                ])
                ->make()
                ->toArray()
        );

        $deckingInstallationPermission->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Decking',
                ])
                ->make()
                ->toArray()
        );

        $answerDeckingInstallationPermission = $deckingInstallationPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            [
                                'value' => 'Yes',
                            ],
                            [
                                'value' => 'No',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Building regulations not required for Decking Installation',
                            ],
                            [
                                'value' => 'Not known',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Not known if building regulations required for Decking Installation',
                            ],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerDeckingInstallationPermission->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerDeckingInstallationPermissionUpload = $deckingInstallationPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFormField' => '4.1a_text',
                        'textAnswers' => [
                            FileTextAnswerTypes::Enclosed => 'Building regulations required for Decking Installation - Attached',
                            FileTextAnswerTypes::AddLater => 'Building regulations required for Decking Installation - To follow',
                            FileTextAnswerTypes::NotApplicable => 'Building regulations required for Decking Installation - Not available',
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerDeckingInstallationPermissionUpload->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerDeckingInstallationPermissionUpload->conditions()->create([
            'answer_id' => $answerDeckingInstallationPermission->id,
            'selected_value' => 'Yes',
        ]);
        // End of Decking Installation Permission

        // 4.1n Year of Driveway Installation
        $yearDrivewayInstallation = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the year the driveway installation took place:',
                    'help_text' => 'A conveyancer or solicitor will require to know the year the driveway installation took place.',
                ])
                ->make()
                ->toArray()
        );

        $yearDrivewayInstallation->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Driveway/Hardstanding',
                ])
                ->make()
                ->toArray()
        );

        $answerYearDrivewayInstallation = $yearDrivewayInstallation->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Year',
                'placeholder' => 'Enter the year',
                'pdfFormFieldName' => '4.1a_text',
                'altText' => 'Driveway completed in %s',
            ],
        ]);

        $answerYearDrivewayInstallation->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerYearDrivewayInstallationKnown = $yearDrivewayInstallation->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
                'pdfFormFieldName' => '4.1a_text',
                'altValue' => 'not known',
                'altText' => 'Driveway completion date %s',
            ],
        ]);

        $answerYearDrivewayInstallation->conditions()->create([
            'answer_id' => $answerYearDrivewayInstallationKnown->id,
            'selected_value' => '0',
        ]);
        // End of Year of Driveway Installation

        // 4.1o Driveway Installation Permission
        $drivewayInstallationPermission = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Did the driveway installation require building regulations?',
                    'help_text' => 'If you are not sure whether planning permissions or building regulations were required for the driveway installation, please <a target="_blank" href="https://www.planningportal.co.uk/info/200130/common_projects/44/patio_and_driveway">click here</a>.',
                ])
                ->make()
                ->toArray()
        );

        $drivewayInstallationPermission->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Driveway/Hardstanding',
                ])
                ->make()
                ->toArray()
        );

        $answerDrivewayInstallationPermission = $drivewayInstallationPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            [
                                'value' => 'Yes',
                            ],
                            [
                                'value' => 'No',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Building regulations not require for Driveway Installation',
                            ],
                            [
                                'value' => 'Not known',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Not known if building regulations required for Driveway Installation',
                            ],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerDrivewayInstallationPermission->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerDrivewayInstallationPermissionUpload = $drivewayInstallationPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFormField' => '4.1a_text',
                        'textAnswers' => [
                            FileTextAnswerTypes::Enclosed => 'Building regulations required for Driveway Installation - Attached',
                            FileTextAnswerTypes::AddLater => 'Building regulations required for Driveway Installation - To follow',
                            FileTextAnswerTypes::NotApplicable => 'Building regulations required for Driveway Installation - Not available',
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerDrivewayInstallationPermissionUpload->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerDrivewayInstallationPermissionUpload->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerDrivewayInstallationPermissionUpload->id,
            'answer_id' => $answerDrivewayInstallationPermission->id,
            'selected_value' => 'Yes',
        ]);
        // End of Driveway Installation Permission

        // 4.1p Year of Extension Installation
        $yearExtensionInstallation = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the year the extension was erected:',
                    'help_text' => 'A conveyancer or solicitor will require to know the year the extension was erected.',
                ])
                ->make()
                ->toArray()
        );

        $yearExtensionInstallation->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Extension',
                ])
                ->make()
                ->toArray()
        );

        $answerYearExtensionInstallation = $yearExtensionInstallation->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Year',
                'placeholder' => 'Enter the year',
                'pdfFormFieldName' => '4.1a_text',
                'altText' => 'Extension completed in %s',
            ],
        ]);

        $answerYearExtensionInstallation->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerYearExtensionInstallationKnown = $yearExtensionInstallation->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
                'pdfFormFieldName' => '4.1a_text',
                'altValue' => 'not known',
                'altText' => 'Extension completion date %s',
            ],
        ]);

        $answerYearExtensionInstallation->conditions()->create([
            'answer_id' => $answerYearExtensionInstallationKnown->id,
            'selected_value' => '0',
        ]);
        // End of Year of Extension Installation

        // 4.1q Extension Installation Permission
        $extensionInstallationPermission = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Did the extension installation require building regulations?',
                    'help_text' => 'If you are not sure whether planning permissions or building regulations were required for the extension, please <a target="_blank" href="https://www.planningportal.co.uk/info/200130/common_projects/17/extensions">click here</a>.',
                ])
                ->make()
                ->toArray()
        );

        $extensionInstallationPermission->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Extension',
                ])
                ->make()
                ->toArray()
        );

        $answerExtensionInstallationPermission = $extensionInstallationPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            [
                                'value' => 'Yes',
                            ],
                            [
                                'value' => 'No',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Building regulations not required for Extension Installation',
                            ],
                            [
                                'value' => 'Not known',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Not known if building regulations required for Extension Installation',
                            ],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerExtensionInstallationPermission->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerExtensionInstallationPermissionUpload = $extensionInstallationPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFormField' => '4.1a_text',
                        'textAnswers' => [
                            FileTextAnswerTypes::Enclosed => 'Building regulations required for Extension Installation - Attached',
                            FileTextAnswerTypes::AddLater => 'Building regulations required for Extension Installation - To follow',
                            FileTextAnswerTypes::NotApplicable => 'Building regulations required for Extension Installation - Not available',
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerExtensionInstallationPermissionUpload->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerExtensionInstallationPermissionUpload->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerExtensionInstallationPermissionUpload->id,
            'answer_id' => $answerExtensionInstallationPermission->id,
            'selected_value' => 'Yes',
        ]);
        // End of Extension Installation Permission

        // 4.1r Details about the extention
        $detailsAboutTheExtension = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide details about the extension:',
                    'help_text' => 'Please provide details regarding the type of extension and where it is located on the property.',
                ])
                ->make()
                ->toArray()
        );

        $detailsAboutTheExtension->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Extension',
                ])
                ->make()
                ->toArray()
        );

        $answerDetailsAboutTheExtension = $detailsAboutTheExtension->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'Please provide details regarding the type of extension and where it is located on the property, e.g. kitchen extension on the south facing side of the property.',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerDetailsAboutTheExtension->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Details about the extention

        // 4.1s Year of Porch Installation
        $yearPorchInstallation = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the year the porch was erected:',
                    'help_text' => 'A conveyancer or solicitor will require the year the porch was erected.',
                ])
                ->make()
                ->toArray()
        );

        $yearPorchInstallation->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Porch',
                ])
                ->make()
                ->toArray()
        );

        $answerYearPorchInstallation = $yearPorchInstallation->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Year',
                'placeholder' => 'Enter the year',
                'pdfFormFieldName' => '4.1a_text',
                'altText' => 'Porch completed in %s',
            ],
        ]);

        $answerYearPorchInstallation->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerYearPorchInstallationKnown = $yearPorchInstallation->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
                'pdfFormFieldName' => '4.1a_text',
                'altValue' => 'not known',
                'altText' => 'Porch completion date %s',
            ],
        ]);

        $answerYearPorchInstallation->conditions()->create([
            'answer_id' => $answerYearPorchInstallationKnown->id,
            'selected_value' => '0',
        ]);
        // End of Year of Porch Installation

        // 4.1t Porch Installation Permission
        $porchInstallationPermission = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Did the porch erection require building regulations?',
                    'help_text' => 'If you are not sure whether planning permissions or building regulations were required for the porch erection, please <a target="_blank" href="https://www.planningportal.co.uk/info/200130/common_projects/46/porches">click here</a>.',
                ])
                ->make()
                ->toArray()
        );

        $porchInstallationPermission->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Porch',
                ])
                ->make()
                ->toArray()
        );

        $answerPorchInstallationPermission = $porchInstallationPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            [
                                'value' => 'Yes',
                            ],
                            [
                                'value' => 'No',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Building regulations not required for Porch Installation',
                            ],
                            [
                                'value' => 'Not known',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Not known if building regulations required for Porch Installation',
                            ],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerPorchInstallationPermission->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerPorchInstallationPermissionUpload = $porchInstallationPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFormField' => '4.1a_text',
                        'textAnswers' => [
                            FileTextAnswerTypes::Enclosed => 'Building regulations required for Porch Installation - Attached',
                            FileTextAnswerTypes::AddLater => 'Building regulations required for Porch Installation - To follow',
                            FileTextAnswerTypes::NotApplicable => 'Building regulations required for Porch Installation - Not available',
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerPorchInstallationPermissionUpload->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerPorchInstallationPermissionUpload->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerPorchInstallationPermissionUpload->id,
            'answer_id' => $answerPorchInstallationPermission->id,
            'selected_value' => 'Yes',
        ]);
        // End of Porch Installation Permission

        // 4.1u Year of Roof Replacement Installation
        $yearRoofReplacementInstallation = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the year the roof replacement took place:',
                    'help_text' => 'A conveyancer or solicitor will require to know the year the roof replacement took place.',
                ])
                ->make()
                ->toArray()
        );

        $yearRoofReplacementInstallation->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Roof Replacement',
                ])
                ->make()
                ->toArray()
        );

        $answerYearRoofReplacementInstallation = $yearRoofReplacementInstallation->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Year',
                'placeholder' => 'Enter the year',
                'pdfFormFieldName' => '4.1a_text',
                'altText' => 'Roof completed in %s',
            ],
        ]);

        $answerYearRoofReplacementInstallation->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerYearRoofReplacementInstallationKnown = $yearRoofReplacementInstallation->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
                'pdfFormFieldName' => '4.1a_text',
                'altValue' => 'not known',
                'altText' => 'Roof completion date %s',
            ],
        ]);

        $answerYearRoofReplacementInstallation->conditions()->create([
            'answer_id' => $answerYearRoofReplacementInstallationKnown->id,
            'selected_value' => '0',
        ]);
        // End of Year of Roof Replacement Installation

        // 4.1v Roof Replacement Installation Permission
        $roofReplacementInstallationPermission = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Did the roof replacement require building regulations?',
                    'help_text' => 'If you are not sure whether planning permissions or building regulations were required for the roof replacement, please <a target="_blank" href="https://www.planningportal.co.uk/info/200130/common_projects/47/roof">click here</a>.',
                ])
                ->make()
                ->toArray()
        );

        $roofReplacementInstallationPermission->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Roof Replacement',
                ])
                ->make()
                ->toArray()
        );

        $answerRoofReplacementInstallationPermission = $roofReplacementInstallationPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            [
                                'value' => 'Yes',
                            ],
                            [
                                'value' => 'No',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Building regulations not required for Roof Replacement',
                            ],
                            [
                                'value' => 'Not known',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Not known if building regulations required for Roof Replacement',
                            ],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerRoofReplacementInstallationPermission->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerRoofReplacementInstallationPermissionUpload = $roofReplacementInstallationPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFormField' => '4.1a_text',
                        'textAnswers' => [
                            FileTextAnswerTypes::Enclosed => 'Building regulations required for Roof Replacement - Attached',
                            FileTextAnswerTypes::AddLater => 'Building regulations required for Roof Replacement - To follow',
                            FileTextAnswerTypes::NotApplicable => 'Building regulations required for Roof Replacement - Not available',
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerRoofReplacementInstallationPermissionUpload->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerRoofReplacementInstallationPermissionUpload->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerRoofReplacementInstallationPermissionUpload->id,
            'answer_id' => $answerRoofReplacementInstallationPermission->id,
            'selected_value' => 'Yes',
        ]);
        // End of Roof Replacement Installation Permission

        // 4.1w Year of Solar Panel Installation
        $yearSolarPanelInstallation = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the year the solar panels installation took place:',
                    'help_text' => 'A conveyancer or solicitor will require to know the year the solar panel installation took place.',
                ])
                ->make()
                ->toArray()
        );

        $yearSolarPanelInstallation->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Solar Panels',
                ])
                ->make()
                ->toArray()
        );

        $answerYearSolarPanelInstallation = $yearSolarPanelInstallation->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Year',
                'placeholder' => 'Enter the year',
                'pdfFormFieldName' => '4.6a_year',
            ],
        ]);

        $answerYearSolarPanelInstallation->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerYearSolarPanelInstallationKnown = $yearSolarPanelInstallation->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
            ],
        ]);

        $answerYearSolarPanelInstallation->conditions()->create([
            'answer_id' => $answerYearSolarPanelInstallationKnown->id,
            'selected_value' => '0',
        ]);
        // End of Year of Solar Panel Installation

        // 4.1x Solar Panel Ownership
        $solarPanelInstallationPermission = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are the solar panels owned outright?',
                    'help_text' => 'Solar panels can be owned outright by the seller or the roof space can be leased to a solar panel company. If the roof space is leased, the panels will belong to the solar panel company for the duration of the loan.',
                ])
                ->make()
                ->toArray()
        );

        $solarPanelInstallationPermission->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Solar Panels',
                ])
                ->make()
                ->toArray()
        );

        $answerSolarPanelInstallationPermission = $solarPanelInstallationPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '4.6b_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '4.6b_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSolarPanelInstallationPermission->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Solar Panel Ownership

        // 4.1y Solar Panel Lease
        $solarPanelLease = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has a long lease of the roof/air space been granted to a solar panel provider?',
                    'help_text' => 'If a property is sold with a lease relating to the roof space to a solar provider, the terms of the lease will be taken on by the new homeowner, who will need to be vetted by the solar panel company. ',
                ])
                ->make()
                ->toArray()
        );

        $solarPanelLease->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Solar Panels',
                ])
                ->make()
                ->toArray()
        );

        $answerSolarPanelLease = $solarPanelLease->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '4.6c_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '4.6c_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSolarPanelLease->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerSolarPanelLeaseUpload = $solarPanelLease->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '1.6',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSolarPanelLeaseUpload->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerSolarPanelLeaseUpload->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerSolarPanelLeaseUpload->id,
            'answer_id' => $answerSolarPanelLease->id,
            'selected_value' => 'Yes',
        ]);
        // End of Solar Panel Lease

        // 4.1z Year of Wall Removed
        $yearWallRemoved = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm the year the wall was removed:',
                    'help_text' => 'A conveyancer or solicitor will require to know the year the wall was removed.',
                ])
                ->make()
                ->toArray()
        );

        $yearWallRemoved->conditions()->create([
            'answer_id' => $answerAlterationsProperty->id,
            'selected_value' => 'Wall Removal',
        ]);

        $answerYearWallRemoved = $yearWallRemoved->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Year',
                'placeholder' => 'Enter the year',
                'pdfFormFieldName' => '4.1a_text',
                'altText' => 'Wall removal completed in %s',
            ],
        ]);

        $answerYearWallRemoved->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerYearWallRemovedKnown = $yearWallRemoved->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
                'pdfFormFieldName' => '4.1a_text',
                'altValue' => 'not known',
                'altText' => 'Wall removal completion date %s',
            ],
        ]);

        $answerYearWallRemoved->conditions()->create([
            'answer_id' => $answerYearWallRemovedKnown->id,
            'selected_value' => '0',
        ]);
        // End of Year of Wall Removed

        // 4.1aa Wall Removed Installation Permission
        $wallRemovedInstallationPermission = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Did the wall removal require building regulations?',
                    'help_text' => 'If you are not sure whether you need planning permissions or building regulations for your wall removal please <a target="_blank" href="https://www.planningportal.co.uk/info/200130/common_projects/33/internal_walls">click here</a>.',
                ])
                ->make()
                ->toArray()
        );

        $wallRemovedInstallationPermission->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Wall Removal',
                ])
                ->make()
                ->toArray()
        );

        $answerWallRemovedInstallationPermission = $wallRemovedInstallationPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            [
                                'value' => 'Yes',
                            ],
                            [
                                'value' => 'No',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Building regulations not required for Wall Removal',
                            ],
                            [
                                'value' => 'Not known',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Not known if building regulations required for Wall Removal',
                            ],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerWallRemovedInstallationPermission->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerWallRemovedInstallationPermissionUpload = $wallRemovedInstallationPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFormField' => '4.1a_text',
                        'textAnswers' => [
                            FileTextAnswerTypes::Enclosed => 'Building regulations required for Wall Removal - Attached',
                            FileTextAnswerTypes::AddLater => 'Building regulations required for Wall Removal - To follow',
                            FileTextAnswerTypes::NotApplicable => 'Building regulations required for Wall Removal - Not available',
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerWallRemovedInstallationPermissionUpload->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerWallRemovedInstallationPermissionUpload->conditions()->create([
            'answer_id' => $answerWallRemovedInstallationPermission->id,
            'selected_value' => 'Yes',
        ]);
        // End of Wall Removed Installation Permission

        // 4.1ab Wall Load Bearing
        $wallLoadBearing = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm whether the wall was load bearing:',
                    'help_text' => 'Load bearing walls are walls that support the structure of a building.',
                ])
                ->make()
                ->toArray()
        );

        $wallLoadBearing->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Wall Removal',
                ])
                ->make()
                ->toArray()
        );

        $answerWallLoadBearing = $wallLoadBearing->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'No'],
                            ['value' => 'Not known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerWallLoadBearing->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Wall Load Bearing

        // 4.1ac Wall Removed details
        $wallRemovedDetails = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide further details about the wall that was removed:',
                    'help_text' => 'Please provide details regarding which wall was removed in the property.',
                ])
                ->make()
                ->toArray()
        );

        $wallRemovedDetails->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Wall Removal',
                ])
                ->make()
                ->toArray()
        );

        $wallRemovedDetails->conditions()->create([
            'answer_id' => $answerWallLoadBearing->id,
            'selected_value' => 'Yes',
        ]);

        $answerWallRemovedDetails = $wallRemovedDetails->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'Please confirm which wall was removed at the property, e.g. The wall between the bathroom and bedroom 1.',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerWallRemovedDetails->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Wall Removed details

        // 4.1ad Year of wood fire was installed
        $yearWoodFireInstalled = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm the year the wood fire was installed:',
                    'help_text' => 'A conveyancer or solicitor will require to know the year the wood fire was installed.',
                ])
                ->make()
                ->toArray()
        );

        $yearWoodFireInstalled->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Wood Burner',
                ])
                ->make()
                ->toArray()
        );

        $answerYearWoodFireInstalled = $yearWoodFireInstalled->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Year',
                'placeholder' => 'Enter the year',
                'pdfFormFieldName' => '4.1a_text',
                'altText' => 'Wood fire completed in %s',
            ],
        ]);

        $answerYearWoodFireInstalled->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerYearWoodFireInstalledKnown = $yearWoodFireInstalled->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
                'pdfFormFieldName' => '4.1a_text',
                'altValue' => 'not known',
                'altText' => 'Wood fire completion date %s',
            ],
        ]);

        $answerYearWoodFireInstalled->conditions()->create([
            'answer_id' => $answerYearWoodFireInstalledKnown->id,
            'selected_value' => '0',
        ]);
        // End of Year of wood fire was installed

        // 4.1ae Wood Fire Installation Permission
        $woodFireInstallationPermission = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Did the wood fire installation require building regulations?',
                    'help_text' => 'When considering the wood fire installation in your property, it is essential to determine whether the conversion required compliance with building regulations. Building regulations are standards and guidelines set by the local authorities to ensure that any structural alterations and developments meet safety, health, and energy efficiency standards.',
                ])
                ->make()
                ->toArray()
        );

        $woodFireInstallationPermission->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Wood Burner',
                ])
                ->make()
                ->toArray()
        );

        $answerWoodFireInstallationPermission = $woodFireInstallationPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            [
                                'value' => 'Yes',
                            ],
                            [
                                'value' => 'No',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Building regulations not required for Wood Fire installation',
                            ],
                            [
                                'value' => 'Not known',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Not known if building regulations required for Wood Fire installation',
                            ],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerWoodFireInstallationPermission->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerWoodFireRegulationsUpload = $woodFireInstallationPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFormField' => '4.1a_text',
                        'textAnswers' => [
                            FileTextAnswerTypes::Enclosed => 'Building regulations required Wood Fire installation - Attached',
                            FileTextAnswerTypes::AddLater => 'Building regulations required Wood Fire installation - To follow',
                            FileTextAnswerTypes::NotApplicable => 'Building regulations required Wood Fire installation - Not available',
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerWoodFireRegulationsUpload->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerWoodFireRegulationsUpload->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerWoodFireRegulationsUpload->id,
            'answer_id' => $answerWoodFireInstallationPermission->id,
            'selected_value' => 'Yes',
        ]);
        // End of Wood Fire Installation Permission

        // 4.1af Year of Gas Fire installation
        $yearGasFireInstalled = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm the year the gas fire was installed:',
                    'help_text' => 'A conveyancer or solicitor will require to know the year the gas fire was installed.',
                ])
                ->make()
                ->toArray()
        );

        $yearGasFireInstalled->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Gas Fire',
                ])
                ->make()
                ->toArray()
        );

        $answerYearGasFireInstalled = $yearGasFireInstalled->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Year',
                'placeholder' => 'Enter the year',
                'pdfFormFieldName' => '4.1a_text',
                'altText' => 'Gas fire completed in %s',
            ],
        ]);

        $answerYearGasFireInstalled->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerYearGasFireInstalledKnown = $yearGasFireInstalled->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
                'pdfFormFieldName' => '4.1a_text',
                'altValue' => 'not known',
                'altText' => 'Gas fire completion date %s',
            ],
        ]);

        $answerYearGasFireInstalled->conditions()->create([
            'answer_id' => $answerYearGasFireInstalledKnown->id,
            'selected_value' => '0',
        ]);
        // End of Year of Gas Fire installation

        // 4.1ag Gas Fire Installation Permission
        $gasFireInstallationPermission = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Did the gas fire installation require building regulations?',
                    'help_text' => 'When considering the gas fire installation in your property, it is essential to determine whether the conversion required compliance with building regulations. Building regulations are standards and guidelines set by the local authorities to ensure that any structural alterations and developments meet safety, health, and energy efficiency standards.',
                ])
                ->make()
                ->toArray()
        );

        $gasFireInstallationPermission->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAlterationsProperty->id,
                    'selected_value' => 'Gas Fire',
                ])
                ->make()
                ->toArray()
        );

        $answerGasFireInstallationPermission = $gasFireInstallationPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            [
                                'value' => 'Yes',
                            ],
                            [
                                'value' => 'No',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Building regulations not required for Gas Fire installation',
                            ],
                            [
                                'value' => 'Not known',
                                'pdfFormFieldName' => '4.1a_text',
                                'altText' => 'Not known if building regulations required for Gas Fire installation',
                            ],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerGasFireInstallationPermission->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerGasFireRegulationsUpload = $gasFireInstallationPermission->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFormField' => '4.1a_text',
                        'textAnswers' => [
                            FileTextAnswerTypes::Enclosed => 'Building regulations required for Gas Fire installation - Attached',
                            FileTextAnswerTypes::AddLater => 'Building regulations required for Gas Fire installation - To follow',
                            FileTextAnswerTypes::NotApplicable => 'Building regulations required for Gas Fire installation - Not available',
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerGasFireRegulationsUpload->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerGasFireRegulationsUpload->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerGasFireRegulationsUpload->id,
            'answer_id' => $answerGasFireInstallationPermission->id,
            'selected_value' => 'Yes',
        ]);
        // End of Gas Fire Installation Permission

        // Works finished
        $worksFinished = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are any of the works unfinished?',
                    'help_text' => 'Please provide details regarding all unfinished building and alteration work.',
                ])
                ->make()
                ->toArray()
        );

        $answerWorksFinished = $worksFinished->answers()->create(
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

        $answerTextWorksFinished = $worksFinished->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'Please provide comprehensive details regarding any unfinished building and alteration work currently present on the property. We are interested in understanding the scope and nature of these ongoing projects, including information about the specific areas or rooms affected, the work completed so far, and any estimated timeline or plans for their completion.',
                        'pdfFormFieldName' => '4.3_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextWorksFinished->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextWorksFinished->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextWorksFinished->id,
            'answer_id' => $answerWorksFinished->id,
            'selected_value' => 'Yes',
        ]);
        // End of Works finished

        // Seller aware of any breaches of planning permission
        $sellerAwareOfBreaches = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the seller aware of any breaches of planning permission conditions or building regulations consent conditions, unfinished work that does not have all the necessary consents?',
                    'help_text' => 'Please provide details regarding any work that does not comply with planning permission or Building Regulations, unfinished work or work that does not have the necessary consents. Where possible, please explain why the work does not comply.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareOfBreaches = $sellerAwareOfBreaches->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '4.4_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '4.4_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareOfBreaches->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareOfBreaches = $sellerAwareOfBreaches->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'pdfFormFieldName' => '4.4_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareOfBreaches->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareOfBreaches->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerAwareOfBreaches->id,
            'answer_id' => $answerSellerAwareOfBreaches->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller aware of any breaches of planning permission

        // Building Control Issues to resolve (4.4)
        $buildingControlIssues = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are there any planning or building control issues to resolve?',
                    'help_text' => 'Please provide details regarding any work that does not comply with planning permission or Building Regulations, unfinished work or work that does not have the necessary consents. Where possible, please explain why the work does not comply.',
                ])
                ->make()
                ->toArray()
        );

        $answerBuildingControlIssues = $buildingControlIssues->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '4.5_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '4.5_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerBuildingControlIssues->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextBuildingControlIssues = $buildingControlIssues->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'pdfFormFieldName' => '4.5_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextBuildingControlIssues->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextBuildingControlIssues->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextBuildingControlIssues->id,
            'answer_id' => $answerBuildingControlIssues->id,
            'selected_value' => 'Yes',
        ]);
        // End of Building Control Issues to resolve

        // Building or part of the property listed
        $buildingOrPartOfPropertyListed = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the property or any part of it a listed building?',
                    'help_text' => 'Listed buildings are buildings of special architectural or historical interest. Properties can be listed as Grade I, Grade II* or Grade II. If a property is listed, you will require consent in order to make any changes to the property. You can find out if a property is listed by searching the National Heritage List for England or Wales by clicking here.',
                ])
                ->make()
                ->toArray()
        );

        $answerBuildingOrPartOfPropertyListed = $buildingOrPartOfPropertyListed->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '4.7a_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '4.7a_no'],
                            ['value' => 'Not known', 'pdfFormFieldName' => '4.7a_not_known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerBuildingOrPartOfPropertyListed->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Building or part of the property listed

        // Building or part of the property in a conservation area
        $buildingOrPartOfPropertyConservationArea = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the property or any part of it in a conservation area?',
                    'help_text' => 'Conservation areas are areas of special historical or architectural interest. These may be designated for conservation by local planning authorities.',
                ])
                ->make()
                ->toArray()
        );

        $answerBuildingOrPartOfPropertyConservationArea = $buildingOrPartOfPropertyConservationArea->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '4.7b_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '4.7b_no'],
                            ['value' => 'Not known', 'pdfFormFieldName' => '4.7b_not_known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerBuildingOrPartOfPropertyConservationArea->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Building or part of the property in a conservation area

        // Upload documents for a property

        $uploadDocumentsForListing = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide any relevant documents related to the listed building or the conservation area:',
                    'help_text' => 'Please provide copies of the Tree Preservation Orders and local authorities permission for works, where relevant. Please contact your Local Authority if you do not hold these documents.',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForListing->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '4.7ba',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForListing->conditions()->create([
            'answer_id' => $answerBuildingOrPartOfPropertyConservationArea->id,
            'selected_value' => 'Yes',
            'type' => ConditionType::OR,
        ]);

        $uploadDocumentsForListing->conditions()->create([
            'answer_id' => $answerBuildingOrPartOfPropertyListed->id,
            'selected_value' => 'Yes',
            'type' => ConditionType::OR,
        ]);

        // End of Upload documents for a property

        // Trees on the property
        $treesOnTheProperty = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are any of the trees on the property subject to a Tree Preservation Order?',
                    'help_text' => 'A Tree Preservation Order (TPO) protects trees that are desirable or useful in a local area. They are written orders made by a local planning authority. It is an offence to cut down, top, lop, uproot, wilfully damage or wilfully destroy a protected tree without the planning authority\'s permission.  If your property is subject to this, you will need to supply copies of the Tree Preservation Orders and local authorities permission for works, where relevant.',
                ])
                ->make()
                ->toArray()
        );

        $answerTreesOnTheProperty = $treesOnTheProperty->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '4.8_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '4.8_no'],
                            ['value' => 'Not known', 'pdfFormFieldName' => '4.8_not_known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTreesOnTheProperty->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Trees on the property

        // Terms of the order
        $termsOfTheOrder = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Have the terms of the order been complied with?',
                    'help_text' => 'We need to confirm that the terms and conditions set out in the Tree Preservation Order (TPO) have been complied with. It is an offence to cut down, top, lop, uproot, wilfully damage or wilfully destroy a protected tree without the planning authority\'s permission.',
                ])
                ->make()
                ->toArray()
        );

        $termsOfTheOrder->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerTreesOnTheProperty->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $answerTermsOfTheOrder = $termsOfTheOrder->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '4.8a_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '4.8a_no'],
                            ['value' => 'Not known', 'pdfFormFieldName' => '4.8a_not_known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTermsOfTheOrder->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Terms of the order

        // Upload documents for trees on property
        $uploadDocumentsForTreesOnProperty = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide any relevant documents related to the Tree Preservation Order:',
                    'help_text' => 'Please provide copies of the Tree Preservation Orders and local authorities permission for works, where relevant. Please contact your Local Authority if you do not hold these documents.',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForTreesOnProperty->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerTreesOnTheProperty->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForTreesOnProperty->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '4.8b',
                    ],
                ])
                ->make()
                ->toArray()
        );
        // End of Upload documents for trees on property
    }

    protected function guaranteesAndWarranties(Form $form)
    {
        //section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Guarantees and warranties',
                ])
                ->make()
                ->toArray()
        );

        // Guarantees and warranties
        $guaranteesAndWarranties = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the property benefit from any of the following guarantees or warranties?',
                    'help_text' => 'The buyer will wish to know if there are any guarantees or warranties that benefit the property.',
                ])
                ->make()
                ->toArray()
        );

        $answerGuaranteesAndWarranties = $guaranteesAndWarranties->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::MultiSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'New home warranty (e.g. NHBC or similar)', 'pdfFormFieldName' => '5.1a_yes'],
                            ['value' => 'Damp proofing', 'pdfFormFieldName' => '5.1b_yes'],
                            ['value' => 'Timber treatment', 'pdfFormFieldName' => '5.1c_yes'],
                            ['value' => 'Windows, roof lights, roof windows or glazed doors', 'pdfFormFieldName' => '5.1d_yes'],
                            ['value' => 'Electrical work', 'pdfFormFieldName' => '5.1e_yes'],
                            ['value' => 'Roofing', 'pdfFormFieldName' => '5.1f_yes'],
                            ['value' => 'Central heating', 'pdfFormFieldName' => '5.1g_yes'],
                            ['value' => 'Underpinning', 'pdfFormFieldName' => '5.1h_yes'],
                            ['value' => 'Other', 'pdfFormFieldName' => '5.1i_yes'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerGuaranteesAndWarranties->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $isGuaranteesAndWarrantiesApplicable = $guaranteesAndWarranties->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not applicable',
            ],
        ]);

        $answerGuaranteesAndWarranties->conditions()->create([
            'answer_id' => $isGuaranteesAndWarrantiesApplicable->id,
            'selected_value' => '0',
        ]);
        // End of Guarantees and warranties

        // Upload documents for New home Warranty
        $uploadDocumentsForNewHomeWarranty = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the New home warranty (e.g. NHBC or similar):',
                    'help_text' => 'A New Homes Warranty is designed to protect homeowners of newly built, converted or refurbished properties in the first 10 years from structural defects. If you are planning to sell your newly built property after completion you will need this new build guarantee.',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForNewHomeWarranty->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'answer_id' => $answerGuaranteesAndWarranties->id,
                    'selected_value' => 'New home warranty (e.g. NHBC or similar)',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForNewHomeWarranty->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '5.1a',
                    ],
                ])
                ->make()
                ->toArray()
        );
        // End of Upload documents for New home Warranty

        // Upload documents for Damp proofing
        $uploadDocumentsForDampProofing = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the Damp proofing guarantee:',
                    'help_text' => 'Home owners with damp problems often find themselves in a difficult situation when it comes to selling their house to prospective buyers who might treat the damp problem out to be more serious than it actually is just to discount their offer. Most damp proofing companies can offer some type of damp proof guarantee, and this will cover the customer for any future issues relating to damp affected areas.',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForDampProofing->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'answer_id' => $answerGuaranteesAndWarranties->id,
                    'selected_value' => 'Damp proofing',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForDampProofing->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '5.1b',
                    ],
                ])
                ->make()
                ->toArray()
        );
        // End of Upload documents for Damp proofing

        // Upload documents for Timber treatment
        $uploadDocumentsForTimberTreatment = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the Timber treatment guarantee:',
                    'help_text' => 'The most common timber issues are an infestation of wood-boring insects such as woodworm, dry rot and wet rot. For timber  that is used in damp places, treatment is essential to avoid the wood from deteriorating and ruining its quality.',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForTimberTreatment->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'answer_id' => $answerGuaranteesAndWarranties->id,
                    'selected_value' => 'Timber treatment',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForTimberTreatment->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '5.1c',
                    ],
                ])
                ->make()
                ->toArray()
        );
        // End of Upload documents for Timber treatment

        // Upload documents for Windows, roof lights, roof windows or glazed doors
        $uploadDocumentsForWindowsRoofLightsRoofWindowsOrGlazedDoors = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the guarantee or warranty for windows, roof lights, roof windows or glazed doors:',
                    'help_text' => 'To facilitate potential buyers` understanding of the property`s window and door warranty coverage and to ensure transparency in our dealings, we kindly request a copy of the guarantee or warranty for windows, roof lights, roof windows, or glazed doors. This document provides assurance to the buyer that these specific features are covered by a warranty, offering protection against defects and ensuring their proper functioning within the specified period.',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForWindowsRoofLightsRoofWindowsOrGlazedDoors->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'answer_id' => $answerGuaranteesAndWarranties->id,
                    'selected_value' => 'Windows, roof lights, roof windows or glazed doors',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForWindowsRoofLightsRoofWindowsOrGlazedDoors->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '5.1d',
                    ],
                ])
                ->make()
                ->toArray()
        );
        // End of Upload documents for Windows, roof lights, roof windows or glazed doors

        // Upload documents for Electrical Work
        $uploadDocumentsForElectricalWork = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the electrical work guarantee or warranty:',
                    'help_text' => 'To ensure potential buyers have a comprehensive understanding of the property electrical work protection and to provide transparency in our discussions, we kindly request a copy of the electrical work guarantee or warranty. This document offers assurance to the buyer that the electrical installations and related works in the property are covered by a warranty, ensuring compliance with safety standards and addressing any potential electrical issues that may arise within the specified period.',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForElectricalWork->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'answer_id' => $answerGuaranteesAndWarranties->id,
                    'selected_value' => 'Electrical work',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForElectricalWork->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '5.1e',
                    ],
                ])
                ->make()
                ->toArray()
        );
        // End of Upload documents for Electrical Work

        // Upload documents for Roofing
        $uploadDocumentsForRoofing = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the roofing warranty:',
                    'help_text' => 'All roof works should have some form of guarantee. Buyer will take comfort knowing that a roof is water tight and won’t require maintenance for some time to come.',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForRoofing->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'answer_id' => $answerGuaranteesAndWarranties->id,
                    'selected_value' => 'Roofing',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForRoofing->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '5.1f',
                    ],
                ])
                ->make()
                ->toArray()
        );
        // End of Upload documents for Roofing

        // Upload documents for Central Heating
        $uploadDocumentsForCentralHeating = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the central heating guarantee or warranty:',
                    'help_text' => 'All new boilers come with a warranty, or sometimes a guarantee, from the boiler manufacturer.  If the boiler is quite old or hasn’t been serviced for a long time it would offer the buyer peace of mind to know that it is in good working order and therefore not likely to face any imminent unexpected costs should it fail. If you are aware of any issues with your boiler and don’t declare them, the buyers could have a case for recompense should that boiler breakdown.',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForCentralHeating->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'answer_id' => $answerGuaranteesAndWarranties->id,
                    'selected_value' => 'Central heating',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForCentralHeating->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '5.1g',
                    ],
                ])
                ->make()
                ->toArray()
        );

        // End of Upload documents for Central Heating

        // Upload documents for Underpinning
        $uploadDocumentsForUnderpinning = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the underpinning guarantee:',
                    'help_text' => 'Underpinning is a construction method used to support buildings that have been affected by subsidence or other structural issues. Subsidence is what happens when a property moves, and its foundations need to be repaired – underpinning is one method of repairing those foundations. Many contractors will offer a guarantee for underpinning work, covering a certain period of time – usually a number of years. However, most underpinning guarantees only cover against the original cause of subsidence and the underpinning work itself and not future issues.',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForUnderpinning->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'answer_id' => $answerGuaranteesAndWarranties->id,
                    'selected_value' => 'Underpinning',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForUnderpinning->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '5.1h',
                    ],
                ])
                ->make()
                ->toArray()
        );
        // End of Upload documents for Underpinning

        // Upload documents for Other
        $uploadDocumentsForOther = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of any other guarantees or warranties:',
                    'help_text' => 'Please provide details of any claims made under a guarantee or warranty that relates to the property. This should include details of when the claim was made, what the claim was for, and any remedy provided.',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForOther->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'answer_id' => $answerGuaranteesAndWarranties->id,
                    'selected_value' => 'Other',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsForOther->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '5.1i',
                    ],
                ])
                ->make()
                ->toArray()
        );
        // End of Upload documents for Other

        // Any claims made

        $anyClaimsMade = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Have any claims been made under any of the guarantees or warranties?',
                    'help_text' => 'You should provide details of any claims made under a guarantee or warranty that relates to the property. This should include details of when the claim was made, what the claim was for, and any remedy provided.',
                ])
                ->make()
                ->toArray()
        );
        $anyClaimsMade->conditions()->create([
            'answer_id' => $answerGuaranteesAndWarranties->id,
            'selected_value' => 'New home warranty (e.g. NHBC or similar)',
            'type' => ConditionType::OR,
        ]);
        $anyClaimsMade->conditions()->create([
            'answer_id' => $answerGuaranteesAndWarranties->id,
            'selected_value' => 'Damp proofing',
            'type' => ConditionType::OR,
        ]);
        $anyClaimsMade->conditions()->create([
            'answer_id' => $answerGuaranteesAndWarranties->id,
            'selected_value' => 'Timber treatment',
            'type' => ConditionType::OR,
        ]);
        $anyClaimsMade->conditions()->create([
            'answer_id' => $answerGuaranteesAndWarranties->id,
            'selected_value' => 'Windows, roof lights, roof windows or glazed doors',
            'type' => ConditionType::OR,
        ]);
        $anyClaimsMade->conditions()->create([
            'answer_id' => $answerGuaranteesAndWarranties->id,
            'selected_value' => 'Electrical work',
            'type' => ConditionType::OR,
        ]);
        $anyClaimsMade->conditions()->create([
            'answer_id' => $answerGuaranteesAndWarranties->id,
            'selected_value' => 'Roofing',
            'type' => ConditionType::OR,
        ]);
        $anyClaimsMade->conditions()->create([
            'answer_id' => $answerGuaranteesAndWarranties->id,
            'selected_value' => 'Central heating',
            'type' => ConditionType::OR,
        ]);
        $anyClaimsMade->conditions()->create([
            'answer_id' => $answerGuaranteesAndWarranties->id,
            'selected_value' => 'Underpinning',
            'type' => ConditionType::OR,
        ]);
        $anyClaimsMade->conditions()->create([
            'answer_id' => $answerGuaranteesAndWarranties->id,
            'selected_value' => 'Other',
            'type' => ConditionType::OR,
        ]);

        $answerAnyClaimsMade = $anyClaimsMade->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '5.2_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '5.2_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerAnyClaimsMade->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextAnyClaimsMade = $anyClaimsMade->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. a claim was made against the window installers in 2015 as one of the panels broke',
                        'pdfFormFieldName' => '5.2_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextAnyClaimsMade->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextAnyClaimsMade->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $answerTextAnyClaimsMade->id,
                    'answer_id' => $answerAnyClaimsMade->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );
        // End of Any claims made
    }

    protected function insurance(Form $form)
    {
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Insurance',
                ])
                ->make()
                ->toArray()
        );

        // Property insurance
        $propertyInsurance = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the property insured?',
                    'help_text' => 'Please state whether or not you took out insurance on the property.',
                ])
                ->make()
                ->toArray()
        );

        $answerPropertyInsurance = $propertyInsurance->answers()->create(
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

        $answerPropertyInsurance->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Property insurance

        // Property not Insured
        $propertyNotInsured = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please give a brief description as to why the property is not insured:',
                    'help_text' => 'If you do not insure your property, you should explain why not.',
                ])
                ->make()
                ->toArray()
        );

        $propertyNotInsured->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'answer_id' => $answerPropertyInsurance->id,
                    'selected_value' => 'No',
                ])
                ->make()
                ->toArray()
        );

        $answerpropertyNotInsured = $propertyNotInsured->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the property is vacant and insurance is therefore not a requirement',
                        'pdfFormFieldName' => '6.2_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerpropertyNotInsured->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Property not Insured

        // Landlord insure flat or building
        $landlordInsureFlatOrBuilding = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'If the property is a flat, does the landlord insure the building?',
                    'help_text' => 'Please state whether or not you took out insurance on the property.',
                ])
                ->make()
                ->toArray()
        );

        $answerLandlordInsureFlatOrBuilding = $landlordInsureFlatOrBuilding->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '6.3_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '6.3_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerLandlordInsureFlatOrBuilding->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Landlord insure flat or building

        // Abnormal Rise in Premiums Insurance
        $abnormalRiseInPremiumsInsurance = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has any building insurance taken out by the seller ever been subject to: An abnormal rise in premiums?',
                    'help_text' => 'We need to know whether the cost of your building insurance has risen by an unusual amount.',
                ])
                ->make()
                ->toArray()
        );

        $answerAbnormalRiseInPremiumsInsurance = $abnormalRiseInPremiumsInsurance->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '6.4a_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '6.4a_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerAbnormalRiseInPremiumsInsurance->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextAbnormalRiseInPremiumsInsurance = $abnormalRiseInPremiumsInsurance->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. there was an abnormal rise in premiums in 2005 due to local ground subsidence',
                        'pdfFormFieldName' => '6.4_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextAbnormalRiseInPremiumsInsurance->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextAbnormalRiseInPremiumsInsurance->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextAbnormalRiseInPremiumsInsurance->id,
            'answer_id' => $answerAbnormalRiseInPremiumsInsurance->id,
            'selected_value' => 'Yes',
        ]);

        // End of Abnormal Rise in Premiums Insurance

        // High excess in Building Insurance
        $highExcessInBuildingInsurance = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has any building insurance taken out by the seller ever been subject to:',
                    'sub_heading' => 'High excesses?',
                    'help_text' => 'We need to know whether any part of the building insurance is, or has even been, subject to high excesses.',
                ])
                ->make()
                ->toArray()
        );

        $answerHighExcessInBuildingInsurance = $highExcessInBuildingInsurance->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '6.5_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '6.5_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerHighExcessInBuildingInsurance->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextHighExcessInBuildingInsurance = $highExcessInBuildingInsurance->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the insurance has been subject to high excesses due to one of the owners declaring bankruptcy',
                        'pdfFormFieldName' => '6.5_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextHighExcessInBuildingInsurance->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextHighExcessInBuildingInsurance->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextHighExcessInBuildingInsurance->id,
            'answer_id' => $answerHighExcessInBuildingInsurance->id,
            'selected_value' => 'Yes',
        ]);
        // End of High excess in Building Insurance

        // Unusual Conditions in Building Insurance
        $unusualConditionsInBuildingInsurance = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has any building insurance taken out by the seller ever been subject to:',
                    'sub_heading' => 'Unusual conditions?',
                    'help_text' => 'We need to know whether the building insurance has any unusual conditions.',
                ])
                ->make()
                ->toArray()
        );

        $answerUnusualConditionsInBuildingInsurance = $unusualConditionsInBuildingInsurance->answers()->create(
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

        $answerUnusualConditionsInBuildingInsurance->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextUnusualConditionsInBuildingInsurance = $unusualConditionsInBuildingInsurance->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the insurers wish to send a professional to the property once a year to inspect the thatched roof',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextUnusualConditionsInBuildingInsurance->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextUnusualConditionsInBuildingInsurance->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextUnusualConditionsInBuildingInsurance->id,
            'answer_id' => $answerUnusualConditionsInBuildingInsurance->id,
            'selected_value' => 'Yes',
        ]);
        // End of Unusual Conditions in Building Insurance

        // Refusal in Building Insurance
        $refusalInBuildingInsurance = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has any building insurance taken out by the seller ever been subject to:',
                    'sub_heading' => 'Refusal?',
                    'help_text' => 'We need to know whether building insurance for the property has ever been refused.',
                ])
                ->make()
                ->toArray()
        );

        $answerRefusalInBuildingInsurance = $refusalInBuildingInsurance->answers()->create(
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

        $answerRefusalInBuildingInsurance->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextRefusalInBuildingInsurance = $refusalInBuildingInsurance->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. we were awarded a high value insurance claim in 2005 and the insurer refused our renewal',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextRefusalInBuildingInsurance->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextRefusalInBuildingInsurance->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextRefusalInBuildingInsurance->id,
            'answer_id' => $answerRefusalInBuildingInsurance->id,
            'selected_value' => 'Yes',
        ]);
        // End of Refusal in Building Insurance

        // Seller maade any buiilding insurance claims
        $sellerMadeAnyBuildingInsuranceClaims = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the seller made any buildings insurance claims?',
                    'help_text' => 'We need to know whether building insurance for the property has ever been refused.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerMadeAnyBuildingInsuranceClaims = $sellerMadeAnyBuildingInsuranceClaims->answers()->create(
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

        $answerSellerMadeAnyBuildingInsuranceClaims->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerMadeAnyBuildingInsuranceClaims = $sellerMadeAnyBuildingInsuranceClaims->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. a buildings insurance claim was carried out in 2005 due to roof tiles blowing off due to high winds',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerMadeAnyBuildingInsuranceClaims->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerMadeAnyBuildingInsuranceClaims->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerMadeAnyBuildingInsuranceClaims->id,
            'answer_id' => $answerSellerMadeAnyBuildingInsuranceClaims->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller maade any buiilding insurance claims
    }

    protected function environmentalMatters(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Environmental Matters',
                ])
                ->make()
                ->toArray()
        );

        // Any parts of the property been flooded
        $anyPartsOfThePropertyBeenFlooded = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has any part of the property (whether buildings or surrounding garden or land) ever been flooded?',
                    'help_text' => 'You need to disclose not only whether water entered your home, but any effect it had on your garden, driveway or surrounding land.',
                ])
                ->make()
                ->toArray()
        );

        $answerAnyPartsOfThePropertyBeenFlooded = $anyPartsOfThePropertyBeenFlooded->answers()->create(
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

        $answerAnyPartsOfThePropertyBeenFlooded->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextAnyPartsOfThePropertyBeenFlooded = $anyPartsOfThePropertyBeenFlooded->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'Please state when the flooding occured and identify the parts that flooded, e.g. the flooding occurred in 2007 and affected the whole of the bottom ground floor',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextAnyPartsOfThePropertyBeenFlooded->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextAnyPartsOfThePropertyBeenFlooded->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextAnyPartsOfThePropertyBeenFlooded->id,
            'answer_id' => $answerAnyPartsOfThePropertyBeenFlooded->id,
            'selected_value' => 'Yes',
        ]);
        // End of Any parts of the property been flooded

        // Type of Flooding occured
        $typeOfFloodingOccured = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'What type of flooding occurred?',
                    'help_text' => 'GROUNDWATER FLOODING: when the water level in the ground rises above the surface level. This is most likely to occur in low lying areas underlain by permeable rocks.
                    SEWER FLOODING: caused when sewers overflow due to the amount of water travelling into them.
                    SURFACE WATER FLOODING: when heavy rainfall overwhelms the drainage capacity of an area.
                    COASTAL FLOODING: when high tides or severe weather breach sea defences, flooding the surrounding land.
                    RIVER FLOODING: when a watercourse cannot cope with the water draining into it from the surrounding land.',

                ])
                ->make()
                ->toArray()
        );

        $typeOfFloodingOccured->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAnyPartsOfThePropertyBeenFlooded->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $answerTypeOfFloodingOccured = $typeOfFloodingOccured->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::MultiSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Ground water', 'pdfFormFieldName' => '7.2a_yes'],
                            ['value' => 'Sewer flooding', 'pdfFormFieldName' => '7.2b_yes'],
                            ['value' => 'Surface water', 'pdfFormFieldName' => '7.2c_yes'],
                            ['value' => 'Coastal flooding', 'pdfFormFieldName' => '7.2d_yes'],
                            ['value' => 'River flooding', 'pdfFormFieldName' => '7.2e_yes'],
                            ['value' => 'Other'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTypeOfFloodingOccured->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Type of Flooding occured

        // Details about the flooding
        $detailsAboutTheFlooding = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide further details about the flooding:',
                ])
                ->make()
                ->toArray()
        );

        $detailsAboutTheFlooding->conditions()->create([
            'answer_id' => $answerTypeOfFloodingOccured->id,
            'selected_value' => 'Other',
        ]);

        $answerDetailsAboutTheFlooding = $detailsAboutTheFlooding->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the flooding was caused by a broken pipe leaking water whilst we were away on holiday',
                        'pdfFormFieldName' => '7.2f_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerDetailsAboutTheFlooding->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Details about the flooding

        // Flood Risk Report
        $floodRiskReport = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has a Flood Risk Report been prepared?',
                    'help_text' => 'You need to state whether a Flood Risk Report has been prepared for the property and supply a copy of this report. If you need more information about the types of flooding and Flood Risk Reports please click here.',
                ])
                ->make()
                ->toArray()
        );

        $answerFloodRiskReport = $floodRiskReport->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '7.3_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '7.3_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerFloodRiskReport->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerFloodRiskReportUpload = $floodRiskReport->answers()->create(
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

        $answerFloodRiskReportUpload->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerFloodRiskReportUpload->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerFloodRiskReportUpload->id,
            'answer_id' => $answerFloodRiskReport->id,
            'selected_value' => 'Yes',
        ]);
        // End of Flood Risk Report

        // Radon test on the property
        $radonTestOnTheProperty = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has a radon test been carried out on the property?',
                    'information' => 'Radon is a naturally occurring inert radioactive gas found in the ground. Some parts of England and Wales are more adversely affected by it than others. Remedial action is advised for properties with a test result above the recommended action level.',
                    'help_text' => 'Radon is a radioactive product of natural uranium which is present in all rocks and soil and enters the property from the ground. You can find out if your property is in a Radon Affected Area by completing an online search.
                    If you are selling in a Radon Affected Area you may want to consider the following points:
                    - If you have previously tested your property, find the result (Contact your test provider if necessary).
                    - If you have not tested, the new owner will be advised to do so when they move in.
                    - You and your solicitor should be prepared to be asked about a retention.',
                ])
                ->make()
                ->toArray()
        );

        $answerRadonTestOnTheProperty = $radonTestOnTheProperty->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '7.4_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '7.4_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerRadonTestOnTheProperty->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Radon test on the property

        // Document upload Radon
        $documentUploadRadon = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the Radon test report:',
                    'help_text' => 'You need to supply a copy of any Radon report and specify whether or not the test result was below the recommended action level of 200 Becquerels per cubic metre of indoor air.',
                ])
                ->make()
                ->toArray()
        );

        $documentUploadRadon->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerRadonTestOnTheProperty->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $documentUploadRadon->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '7.4a',
                    ],
                ])
                ->make()
                ->toArray()
        );
        // End of Document upload Radon

        // Radon Test under action level
        $radonTestUnderActionLevel = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Was the test result below the recommended action level?',
                    'information' => 'Radon is a naturally occurring inert radioactive gas found in the ground. Some parts of England and Wales are more adversely affected by it than others. Remedial action is advised for properties with a test result above the recommended action level.',
                    'help_text' => 'The Target Level of 200 Bq m-3 is the ideal outcome for remediation works in existing buildings and protective measures in new buildings.',
                ])
                ->make()
                ->toArray()
        );

        $radonTestUnderActionLevel->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerRadonTestOnTheProperty->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $answerRadonTestUnderActionLevel = $radonTestUnderActionLevel->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '7.4b_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '7.4b_yes'],
                            ['value' => 'Not known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerRadonTestUnderActionLevel->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Radon Test under action level

        // Remedial measures
        $remedialMeasures = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Were any remedial measures undertaken on construction to reduce Radon gas levels in the property?',
                    'information' => 'Radon is a naturally occurring inert radioactive gas found in the ground. Some parts of England and Wales are more adversely affected by it than others. Remedial action is advised for properties with a test result above the recommended action level.',
                    'help_text' => 'Building Regulations require that Radon remedial measures are installed in all new buildings in high radon areas. You can install simple remedial measures to reduce Radon levels. Remedial measures can be "basic" (typically a gas resistant membrane across the ground footprint of the property) or "full" measures (such a fitted "standby sump" that can be activated if needed, or provision for adding powered ventilation to suspended floors).',
                ])
                ->make()
                ->toArray()
        );

        $answerRemedialMeasures = $remedialMeasures->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '7.5_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '7.5_no'],
                            ['value' => 'Not known', 'pdfFormFieldName' => '7.5_not_known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerRemedialMeasures->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Remedial measures

        // Energy Efficiency Document Upload
        $energyEfficiencyDocumentUpload = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the EPC for the property:',
                    'help_text' => 'An Energy Performance Certificate (EPC) rates the propertys energy efficiency level from A (most efficient) to G (least efficient). It is valid for 10 years and it must be provided whenever a property is built, sold, or rented.',

                ])
                ->make()
                ->toArray()
        );

        $energyEfficiencyDocumentUpload->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '7.6',
                    ],
                ])
                ->make()
                ->toArray()
        );
        // End of Energy Efficiency Document Upload

        // Funded by Green Deal Scheme
        $fundedByGreenDealScheme = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Have any installations in the property been financed under the Green Deal scheme? ',
                    'help_text' => 'The EPC should reveal whether or not the property has a Green Deal. The Green Deal is a Government initiative designed to help homeowners increase the energy efficiency of their home. It allows homeowners to pay for some or all energy-saving improvements, such as loft insulation and cavity wall insulation, over a period of time through their electricity bill.',
                ])
                ->make()
                ->toArray()
        );

        $answerFundedByGreenDealScheme = $fundedByGreenDealScheme->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '7.7_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '7.7_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerFundedByGreenDealScheme->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextFundedByGreenDealScheme = $fundedByGreenDealScheme->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please provide details',
                        'placeholder' => 'e.g. insulation was installed in 2002 under the Green Deal scheme',
                        'pdfFormFieldName' => '7.7_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextFundedByGreenDealScheme->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextFundedByGreenDealScheme->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextFundedByGreenDealScheme->id,
            'answer_id' => $answerFundedByGreenDealScheme->id,
            'selected_value' => 'Yes',
        ]);
        // End of Funded by Green Deal Scheme

        // Latest electricity bill
        $latestElectricityBill = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of your latest electricity bill:',
                    'help_text' => 'Please provide a scanned copy or digital version of your latest electricity bill. Providing a copy of your latest electricity bill allows potential buyers to assess the property`s energy consumption and associated costs. This information helps buyers make informed decisions and gain insights into the property`s energy efficiency.',
                ])
                ->make()
                ->toArray()
        );

        $latestElectricityBill->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '7.7',
                    ],
                ])
                ->make()
                ->toArray()
        );
        // End of Latest electricity bill

        // Affected by Japanese Knotweed
        $affectedByJapaneseKnotweed = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the property affected by Japanese Knotweed?',
                    'help_text' => 'If you are unsure that Japanese knotweed exists above or below ground or whether it has previously been managed on your property, please answer Not known.',
                ])
                ->make()
                ->toArray()
        );

        $answerAffectedByJapaneseKnotweed = $affectedByJapaneseKnotweed->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '7.8_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '7.8_no'],
                            ['value' => 'Not known', 'pdfFormFieldName' => '7.8_not_known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerAffectedByJapaneseKnotweed->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Affected by Japanese Knotweed

        // Japanese Knotweed Management Plan
        $japaneseKnotweedManagementPlan = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please state whether there is Japanese knotweed management and treatment plan in place and supply a copy with any insurance linked to the plan:',
                    'help_text' => 'If No is chosen as an answer you MUST BE CERTAIN that no rhizome (root) is present in the ground of the property, or within 3 metres of the property boundary even if there are no visible signs above ground.',
                ])
                ->make()
                ->toArray()
        );

        $japaneseKnotweedManagementPlan->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'answer_id' => $answerAffectedByJapaneseKnotweed->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $answerjapaneseKnotweedManagementPlan = $japaneseKnotweedManagementPlan->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '7.8a_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '7.8a_no'],
                            ['value' => 'Not known', 'pdfFormFieldName' => '7.8a_not_known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerjapaneseKnotweedManagementPlan->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerJapaneseKnotweedManagementPlanUpload = $japaneseKnotweedManagementPlan->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '7.8a',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerJapaneseKnotweedManagementPlanUpload->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerJapaneseKnotweedManagementPlanUpload->id,
            'answer_id' => $answerjapaneseKnotweedManagementPlan->id,
            'selected_value' => 'Yes',
        ]);

        $answerJapaneseKnotweedManagementPlanUpload->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Japanese Knotweed Management Plan
    }

    protected function rightsAndInformalArrangements(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Rights and informal arrangements',
                ])
                ->make()
                ->toArray()
        );

        // Ownership Responsibilities
        $ownershipResponsibilities = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does ownership of the property carry a responsibility to contribute towards the cost of any jointly-used services? (e.g.maintenance of a private road, a shared driveway, a boundary or drain)',
                    'help_text' => 'Please provide details of any costs you may be paying for any jointly-used services. This needs to include information about the amount, the frequency of payments and who receives the payment.',
                ])
                ->make()
                ->toArray()
        );

        $answerOwnershipResponsibilities = $ownershipResponsibilities->answers()->create(
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

        $answerOwnershipResponsibilities->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextOwnershipResponsibilitiesDetails = $ownershipResponsibilities->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the road leading to the property is maintained privately with the cost is shared between every house on the street',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextOwnershipResponsibilitiesDetails->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextOwnershipResponsibilitiesDetails->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextOwnershipResponsibilitiesDetails->id,
            'answer_id' => $answerOwnershipResponsibilities->id,
            'selected_value' => 'Yes',
        ]);
        // End of Ownership Responsibilities

        // Rights or Arrangements
        $rightsOrArrangements = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the property benefit from any rights or arrangements over any neighbouring property (this includes any rights of way)?',
                    'help_text' => 'Please provide details of any rights or arrangements that the property has over a neighbouring property. This may include rights of access, such as a road or footpath, or use of a shared driveway.',
                ])
                ->make()
                ->toArray()
        );

        $answerRightsOrArrangements = $rightsOrArrangements->answers()->create(
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

        $answerRightsOrArrangements->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextRightsOrArrangementsDetails = $rightsOrArrangements->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the property benefits from a right of easement allowing access to our rear garden through our neighbours land',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextRightsOrArrangementsDetails->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextRightsOrArrangementsDetails->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextRightsOrArrangementsDetails->id,
            'answer_id' => $answerRightsOrArrangements->id,
            'selected_value' => 'Yes',
        ]);
        // End of Rights or Arrangements

        // Prevent access to the property
        $preventAccessToTheProperty = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has anyone taken steps to prevent access to the property, or to complain about or demand payment for access to the property?',
                    'help_text' => 'We need to inform the buyer information about the rights of access to the property. Please provide details of any steps taken by neighbours or others to prevent access to the property, or to complain about or demand payment for access to the property. This may include details of what action was taken, the reasons for access being denied and any steps taken by the seller to resolve the situation.',
                ])
                ->make()
                ->toArray()
        );

        $answerPreventAccessToTheProperty = $preventAccessToTheProperty->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '8.3_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '8.3_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerPreventAccessToTheProperty->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextPreventAccessToThePropertyDetails = $preventAccessToTheProperty->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. our neighbours will not allow us to access our rear garden though their property',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextPreventAccessToThePropertyDetails->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextPreventAccessToThePropertyDetails->id,
            'answer_id' => $answerPreventAccessToTheProperty->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextPreventAccessToThePropertyDetails->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Prevent access to the property

        // 8.4a Following rights benefit
        $followingRightsBenefit = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller know if any of the following rights benefit the property:',
                    'sub_heading' => 'Rights of light',
                    'help_text' => 'Rights to light give homeowners the right to natural light through defined apertures (e.g. through windows) on buildings on their land.',
                ])
                ->make()
                ->toArray()
        );

        $answerFollowingRightsBenefit = $followingRightsBenefit->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '8.4a_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '8.4a_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerFollowingRightsBenefit->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Following rights benefit

        // Following rights benefit
        $followingRightsBenefit = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller know if any of the following rights benefit the property:',
                    'sub_heading' => 'Rights of support from adjoining properties?',
                    'help_text' => 'Rights of support are where one building or part of a building gives support to a neighbouring building or another part of the same building.',
                ])
                ->make()
                ->toArray()
        );

        $answerFollowingRightsBenefit = $followingRightsBenefit->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '8.4b_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '8.4b_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerFollowingRightsBenefit->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Following rights benefit

        // Following Customery Rights
        $followingCustomeryRights = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller know if any of the following rights benefit the property:',
                    'sub_heading' => 'Customary rights (e.g. rights deriving from local traditions)?',
                    'help_text' => 'Customary rights are enjoyed by the inhabitants of a local community as a result of tradition or custom.',
                ])
                ->make()
                ->toArray()
        );

        $answerFollowingCustomeryRights = $followingCustomeryRights->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '8.4c_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '8.4c_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerFollowingCustomeryRights->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Following Customery Rights

        // Mines and minerals
        $minesAndMinerals = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller know if any of the following arrangements affect the property:',
                    'sub_heading' => 'Other people’s rights to mines and minerals under the land?',
                    'help_text' => 'This is a legal right retained by a former landowner to extract any mines or minerals beneath the ground.',
                ])
                ->make()
                ->toArray()
        );

        $answerMinesAndMinerals = $minesAndMinerals->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '8.5a_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '8.5a_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerMinesAndMinerals->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextMinesAndMineralsDetails = $minesAndMinerals->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the previous owners reserve the right to access the property regarding the removal of certain minerals',
                        'pdfFormFieldName' => '8.5_text',
                    ],
                ])
                ->make()
                ->toArray()
        );
        $answerTextMinesAndMineralsDetails->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextMinesAndMineralsDetails->id,
            'answer_id' => $answerMinesAndMinerals->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextMinesAndMineralsDetails->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Mines and minerals

        // Chancel repair liability
        $chancelRepairLiability = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller know if any of the following arrangements affect the property:',
                    'sub_heading' => 'Chancel repair liability?',
                    'help_text' => 'Chancel repair liability is an obligation to repair or contribute to the cost of repairing the chancel of a parish church. A property does not have to be near to or within sight of a church for its owner to be liable to contribute to the repair of the chancel.',
                ])
                ->make()
                ->toArray()
        );

        $answerChancelRepairLiability = $chancelRepairLiability->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '8.5b_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '8.5b_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerChancelRepairLiability->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextChancelRepairLiability = $chancelRepairLiability->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the property falls within the boundaries of the local parish and the property owners are liable for its costs or repair',
                        'pdfFormFieldName' => '8.5_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextChancelRepairLiability->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextChancelRepairLiability->id,
            'answer_id' => $answerChancelRepairLiability->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextChancelRepairLiability->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Chancel repair liability

        // Rights of way
        $rightsOfWay = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller know if any of the following arrangements affect the property: Other people’s rights to take things from the land (such as timber, hay or fish)?',
                    'help_text' => 'Some people may have rights to enter another’s land and take something from the land such as crops, timber, pasture, fish, game or minerals.',
                ])
                ->make()
                ->toArray()
        );

        $answerRightsOfWay = $rightsOfWay->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '8.5c_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '8.5c_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerRightsOfWay->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextRightsOfWay = $rightsOfWay->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the previous owners reserve the right to enter the land regarding the removal of timber',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextRightsOfWay->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextRightsOfWay->id,
            'answer_id' => $answerRightsOfWay->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextRightsOfWay->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Rights of way

        // Rights or arrangements
        $rightsOrArrangements = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are there any other rights or arrangements affecting the property?',
                    'help_text' => 'Please provide details of any other rights or arrangements affecting the property, including any rights of way.',
                ])
                ->make()
                ->toArray()
        );

        $answerRightsOrArrangements = $rightsOrArrangements->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '8.6_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '8.6_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextRightsOrArrangements = $rightsOrArrangements->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the previous owners reserve the right to enter the land regarding the removal of timber',
                        'pdfFormFieldName' => '8.6_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextRightsOrArrangements->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextRightsOrArrangements->id,
            'answer_id' => $answerRightsOrArrangements->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextRightsOrArrangements->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Rights or arrangements

        // Service crossing neighbouring property
        $serviceCrossingNeighbouringProperty = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Do any drains, pipes or wires serving the property cross any neighbour’s property?',
                    'help_text' => 'Please provide details of any other rights or arrangements affecting the property, including any rights of way.',
                ])
                ->make()
                ->toArray()
        );

        $answerServiceCrossingNeighbouringProperty = $serviceCrossingNeighbouringProperty->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '8.7_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '8.7_no'],
                            ['value' => 'Not known', 'pdfFormFieldName' => '8.7_not_known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerServiceCrossingNeighbouringProperty->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Service crossing neighbouring property

        // Service crossing your property
        $serviceCrossingYourProperty = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Do any drains, pipes or wires leading to any neighbour’s property cross your property?',
                    'help_text' => 'You may have agreements or arrangements relating to drains, pipes or wires that cross your property. This may include permissions for a neighbour to access your property for the purposes of maintenance.',
                ])
                ->make()
                ->toArray()
        );

        $answerServiceCrossingYourProperty = $serviceCrossingYourProperty->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '8.8_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '8.8_no'],
                            ['value' => 'Not known', 'pdfFormFieldName' => '8.8_not_known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerServiceCrossingYourProperty->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Service crossing your property

        // Agreement about drains, pipes or wires
        $agreementAboutDrainsPipesOrWires = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there any agreement or arrangement about drains, pipes or wires?',
                    'help_text' => 'You may have agreements or arrangements relating to drains, pipes or wires that cross your property. This may include permissions for a neighbour to access your property for the purposes of maintenance.',
                ])
                ->make()
                ->toArray()
        );

        $answerAgreementAboutDrainsPipesOrWires = $agreementAboutDrainsPipesOrWires->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '8.9_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '8.9_no'],
                            ['value' => 'Not known', 'pdfFormFieldName' => '8.9_not_known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextAgreementAboutDrainsPipesOrWires = $agreementAboutDrainsPipesOrWires->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the supply of water to the property is through a private water pipe of which the costs for repair are shared between the ourselves and the neighbours',
                        'pdfFormFieldName' => '8.9_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextAgreementAboutDrainsPipesOrWires->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextAgreementAboutDrainsPipesOrWires->id,
            'answer_id' => $answerAgreementAboutDrainsPipesOrWires->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextAgreementAboutDrainsPipesOrWires->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Agreement about drains, pipes or wires

        // Agreement about drains, pipes or wires Document upload
        $agreementAboutDrainsPipesOrWiresDocumentUpload = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of any documentation relating to any arrangement about drains, pipes or wires:',
                    'help_text' => 'To complete the due diligence process and provide potential buyers with a comprehensive understanding of the property arrangements concerning drains, pipes, or wires, we kindly request a copy of any documentation relating to such arrangements.
                    This may include formal agreements, easements, licenses, or any other relevant documentation that outlines the details of the arrangement and its implications. By sharing this documentation, you allow potential buyers to assess the scope and responsibilities associated with utility infrastructure on the property.
                    Your cooperation in providing this documentation is highly appreciated as it ensures transparency and facilitates a smooth and informed property transaction for all parties involved.',
                ])
                ->make()
                ->toArray()
        );

        $answerAgreementAboutDrainsPipesOrWiresDocumentUpload = $agreementAboutDrainsPipesOrWiresDocumentUpload->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '8.9a',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerAgreementAboutDrainsPipesOrWiresDocumentUpload->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $agreementAboutDrainsPipesOrWiresDocumentUpload->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $agreementAboutDrainsPipesOrWiresDocumentUpload->id,
                    'answer_id' => $answerAgreementAboutDrainsPipesOrWires->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );
        // End of Agreement about drains, pipes or wires Document upload
    }

    protected function parking(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Parking',
                ])
                ->make()
                ->toArray()
        );

        // Parking
        $parking = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are there any parking arrangements at the property?',
                    'help_text' => 'Please describe what the parking arrangements are (e.g. garage, car port, driveway, allocated parking space, on-street parking etc). If a license or permit is required to park vehicles at the property, this should be stated in the answer to this question.',
                ])
                ->make()
                ->toArray()
        );

        $answerParking = $parking->answers()->create(
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

        $answerParking->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextParking = $parking->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'Please describe what the parking arrangements are,.e.g. garage, car port, driveway, allocated parking space, on-street parking etc',
                        'pdfFormFieldName' => '9.1_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextParking->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextParking->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextParking->id,
            'answer_id' => $answerParking->id,
            'selected_value' => 'Yes',
        ]);
        // End Parking

        // Parking in controller parking zone
        $parkingInControlledParkingZone = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the property in a controlled parking zone?',
                    'help_text' => 'A controlled parking zone (CPZ) is an area where there are restrictions on parking during certain times. These restrictions only apply to public roads. The hours when the parking restrictions are in operation will be shown on the signs at the entrance to the area.',
                ])
                ->make()
                ->toArray()
        );

        $answerParkingInControlledParkingZone = $parkingInControlledParkingZone->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '9.2_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '9.2_no'],
                            ['value' => 'Not known', 'pdfFormFieldName' => '9.2_not_known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerParkingInControlledParkingZone->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Parking in controller parking zone
    }

    protected function otherChanges(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Other changes',
                ])
                ->make()
                ->toArray()
        );

        // Upcoming charges
        $upcomingCharges = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller have to pay any charges relating to the property (excluding any payments such as council tax, utility charges, etc.), for example payments to a management company?',
                    'help_text' => 'If the property is leasehold, there might be lease expenses such as service charges and ground rent. We will ask you for more details about these on the on the Leasehold Information Section (TA7).',
                ])
                ->make()
                ->toArray()
        );

        $answerUpcomingCharges = $upcomingCharges->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '10.1_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '10.1_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerUpcomingCharges->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextUpcomingCharges = $upcomingCharges->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. there is an annual service charge payable to a management company for the maintenance of common areas',
                        'pdfFormFieldName' => '10.1_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextUpcomingCharges->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextUpcomingCharges->id,
            'answer_id' => $answerUpcomingCharges->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextUpcomingCharges->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Upcoming charges
    }

    protected function occupiers(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Occupiers',
                ])
                ->make()
                ->toArray()
        );

        // Occupiers
        $occupiers = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller live at the property?',
                    'help_text' => 'Please specify whether or not you currently live at the property you are selling.',
                ])
                ->make()
                ->toArray()
        );

        $answerOccupiers = $occupiers->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '11.1_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '11.1_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerOccupiers->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Occupiers

        // Occupiers other than sellers
        $occupiersOtherThanSellers = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does anyone else (other than the sellers), aged 17 or over, live at the property?',
                    'help_text' => 'Anyone else over the age of 17 who lives at the property may have specific rights (e.g. a lease or licence) to continue living at the property unless they agree to vacate the property before completion.',
                ])
                ->make()
                ->toArray()
        );

        $answerOccupiersOtherThanSellers = $occupiersOtherThanSellers->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '11.2_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '11.2_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerOccupiersOtherThanSellers->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Occupiers other than sellers

        // Occupiers other than sellers details
        $occupiersOtherThanSellersDetails = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please give the full names of any occupiers (other than the sellers) aged 17 or over:',
                    'help_text' => 'Anyone else over the age of 17 who lives at the property may have specific rights (e.g. a lease or licence) to continue living at the property unless they agree to vacate the property before completion.',
                ])
                ->make()
                ->toArray()
        );

        $answerOccupiersOtherThanSellersDetails = $occupiersOtherThanSellersDetails->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. Sally Lucy Jones',
                        'pdfFormFieldName' => '11.3_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerOccupiersOtherThanSellersDetails->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $occupiersOtherThanSellersDetails->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $occupiersOtherThanSellers->id,
                    'answer_id' => $answerOccupiersOtherThanSellers->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );
        // End of Occupiers other than sellers details

        // Occupiers other than sellers tenants or lodgers
        $occupiersOtherThanSellersTenantsOrLodgers = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are any of the occupiers (other than the sellers) aged 17 or over, tenants or lodgers?',
                    'help_text' => 'If the property is currently subject to a tenancy and is being sold with vacant possession, the tenancy will need to be terminated by serving the appropriate notice on the tenant. Sellers and buyers should speak to their solicitor where this applies.',
                ])
                ->make()
                ->toArray()
        );

        $answerOccupiersOtherThanSellersTenantsOrLodgers = $occupiersOtherThanSellersTenantsOrLodgers->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Tenants', 'pdfFormFieldName' => '11.4_yes'],
                            ['value' => 'Lodgers', 'pdfFormFieldName' => '11.4_yes'],
                            ['value' => 'Neither', 'pdfFormFieldName' => '11.4_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerOccupiersOtherThanSellersTenantsOrLodgers->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $occupiersOtherThanSellersTenantsOrLodgers->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $occupiersOtherThanSellers->id,
                    'answer_id' => $answerOccupiersOtherThanSellers->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );
        // End of Occupiers other than sellers tenants or lodgers

        // Property with Possessions
        $propertyWithPossessions = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the property being sold with vacant possession?',
                    'help_text' => 'If there are any occupiers, you need to state whether or not the property will be vacant on completion (when the purchase money is paid and the title to the property passes from the seller to the buyer).',
                ])
                ->make()
                ->toArray()
        );

        $propertyWithPossessions->conditions()->create([
            'answer_id' => $answerOccupiersOtherThanSellers->id,
            'selected_value' => 'Yes',
        ]);

        $answerPropertyWithPossessions = $propertyWithPossessions->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '11.5_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '11.5_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerPropertyWithPossessions->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Property with Possessions

        // Occupiers leaving
        $occupiersLeaving = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Have all the occupiers aged 17 or over agreed to leave prior to completion?',
                    'help_text' => 'All adults living at the property must sign the sale contract to confirm that they will leave the property before completion. If they do not sign, they may have a right to continue living at the property after completion.',
                ])
                ->make()
                ->toArray()
        );

        $answerOccupiersLeaving = $occupiersLeaving->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '11.5a_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '11.5a_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerOccupiersLeaving->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $occupiersLeaving->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $propertyWithPossessions->id,
                    'answer_id' => $answerPropertyWithPossessions->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );
        // End of Occupiers leaving

        // Occupiers aggreement
        $occupiersAgreement = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Have all the occupiers aged 17 or over agreed to sign the sale contract?',
                    'help_text' => 'If occupiers aged 17 or over have not agreed to sign the sale contract, you should provide evidence to the buyer that the property will be vacant at completion. Where this is the case, buyers should speak to their solicitor.',
                ])
                ->make()
                ->toArray()
        );

        $answerOccupiersAgreement = $occupiersAgreement->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '11.5b_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '11.5b_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerOccupiersAgreement->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $occupiersAgreement->conditions()->create(
            Condition::factory()
                ->state([
                    'answer_id' => $answerPropertyWithPossessions->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );
        // End of Occupiers agreement

        // Occupiers agreement details document
        $occupiersAgreementDetailsDocument = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide evidence that the property will be vacant on completion:',
                    'help_text' => 'If there are any occupiers, you need to state whether or not the property will be vacant on completion (when the purchase money is paid and the title to the property passes from the seller to the buyer).',
                ])
                ->make()
                ->toArray()
        );

        $answerOccupiersAgreementDetailsDocument = $occupiersAgreementDetailsDocument->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '11.5b',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerOccupiersAgreementDetailsDocument->validationRules()->create([
            'rule' => 'required',
        ]);

        $occupiersAgreementDetailsDocument->conditions()->create([
            'answer_id' => $answerOccupiersAgreement->id,
            'selected_value' => 'No',
        ]);

        $occupiersAgreementDetailsDocument->conditions()->create([
            'answer_id' => $answerOccupiersLeaving->id,
            'selected_value' => 'No',
        ]);

        $occupiersAgreementDetailsDocument->conditions()->create([
            'answer_id' => $occupiersAgreement->id,
            'selected_value' => 'No',
        ]);
        // End of Occupiers agreement details document
    }

    protected function services(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Services',
                ])
                ->make()
                ->toArray()
        );

        // Electrical Installation
        $electricalInstallation = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the whole or any part of the electrical installation been tested by a qualified and registered electrician?',
                    'help_text' => 'Please let us know whether the whole or part of the electrical installation has been tested by an electrician who is qualified and registered with an approved body such as: Electrical Contractors Association (ECA), National Association for Professional Inspectors and Testers (NAPIT) or Ascertiva (formerly NICEIC).',
                ])
                ->make()
                ->toArray()
        );

        $answerElectricalInstallation = $electricalInstallation->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '12.1_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '12.1_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerElectricalInstallation->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Electrical Installation

        // Electrical year test
        $electricalYearTest = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please state the year it was tested:',
                    'help_text' => 'Please state the year the test was carried out and supply a copy of the test certificate.',
                ])
                ->make()
                ->toArray()
        );

        $answerElectricalYearTest = $electricalYearTest->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Year',
                'placeholder' => 'Enter the year',
                'pdfFormFieldName' => '4.1a_text',
                'altText' => 'Basement conversion completed in %s',
            ],
        ]);

        $answerElectricalYearTest->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerElectricalYearTestKnown = $electricalYearTest->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
                'pdfFormFieldName' => '4.1a_text',
                'altValue' => 'not known',
                'altText' => 'Basement completion date %s',
            ],
        ]);

        $answerElectricalYearTest->conditions()->create([
            'answer_id' => $answerElectricalYearTestKnown->id,
            'selected_value' => '0',
        ]);

        $electricalYearTest->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $electricalInstallation->id,
                    'answer_id' => $answerElectricalInstallation->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );
        // End of Electrical year test

        // Electrical test certificate
        $electricalTestCertificate = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the electrical test certificate:',
                ])
                ->make()
                ->toArray()
        );

        $answerElectricalTestCertificate = $electricalTestCertificate->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '12.1a',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerElectricalTestCertificate->validationRules()->create([
            'rule' => 'required',
        ]);

        $electricalTestCertificate->conditions()->create([
            'conditionable_type' => 'step',
            'answer_id' => $answerElectricalInstallation->id,
            'selected_value' => 'Yes',
        ]);
        // End of Electrical test certificate

        // Any electrical work
        $anyElectricalWork = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the property been rewired or had any electrical installation work carried out since 1 January 2005?',
                    'help_text' => 'From 1 January 2005, all electrical work must be carried out in accordance with Building Regulations (BS7671) and comply with the safety standards. To prove this you will need to provide the BS7671 Electrical Safety Certificate, the installers Building Regulations Compliance Certificate or Building Control Completion Certificate.',
                ])
                ->make()
                ->toArray()
        );

        $answerAnyElectricalWork = $anyElectricalWork->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '12.2_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '12.2_no'],
                            ['value' => 'Not known', 'pdfFormFieldName' => '12.2_not_known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerAnyElectricalWork->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Any electrical work

        // Electrical Safety Certificate
        $electricalSafetyCertificate = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the signed BS7671 Electrical Safety Certificate:',
                    'help_text' => 'To ensure the safety and compliance of the electrical installation at the property, we kindly request a copy of the signed BS7671 Electrical Safety Certificate. This certificate serves as essential documentation, providing verification that all electrical work carried out at the property adheres to Building Regulations (BS7671) and complies with the required safety standards.',
                ])
                ->make()
                ->toArray()
        );

        $electricalSafetyCertificate->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'conditionable_id' => $electricalSafetyCertificate->id,
                    'answer_id' => $answerAnyElectricalWork->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $electricalSafetyCertificate->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '12.2a',
                    ],
                ])
                ->make()
                ->toArray()
        );
        // End of Electrical Safety Certificate

        // Electrical Compliance Certificate
        $electricalComplianceCertificate = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the Installer’s Building Regulations Compliance Certificate:',
                    'help_text' => 'The BS7671 Electrical Safety Certificate confirms that the installation and rewiring work has been completed in accordance with the safety standards set out in the building regulations. If you have this certificate please provide it in order to evidence compliance with requirements for electrical work carried out.',
                ])
                ->make()
                ->toArray()
        );

        $electricalComplianceCertificate->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'conditionable_id' => $electricalComplianceCertificate->id,
                    'answer_id' => $answerAnyElectricalWork->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $electricalComplianceCertificate->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '12.2b',
                    ],
                ])
                ->make()
                ->toArray()
        );
        // End of Electrical Compliance Certificate

        // Building Control Completion Certificate
        $buildingControlCompletionCertificate = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of Building Control Completion Certificate:',
                    'help_text' => 'The Building Control Completion Certificate confirms that the work was carried out in accordance with building regulations. If you have this certificate please provide it in order to evidence compliance with requirements for electrical work carried out.',
                ])
                ->make()
                ->toArray()
        );

        $buildingControlCompletionCertificate->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'conditionable_id' => $buildingControlCompletionCertificate->id,
                    'answer_id' => $answerAnyElectricalWork->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $buildingControlCompletionCertificate->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '12.2c',
                    ],
                ])
                ->make()
                ->toArray()
        );
        // End of Building Control Completion Certificate

        // Central Heating
        $centralHeating = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the property have a central heating system?',
                    'help_text' => 'A central heating system provides warmth to the number of spaces within a building and optionally also able to heat domestic hot water from one main source of heat. If you are not sure of what type of heating system the property has in place, you should be able to find this information on your EPC.',
                ])
                ->make()
                ->toArray()
        );

        $answerCentralHeating = $centralHeating->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '12.3_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '12.3_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCentralHeating->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Central Heating

        // Type of central heating
        $typeOfCentralHeating = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'What type of system is it?',
                    'help_text' => 'When identifying the type of central heating system in the property, please specify one of the following options:
                        Gas: A gas central heating system uses a gas boiler to heat water, which then circulates through radiators or underfloor heating to warm the property.
                        Electric: An electric central heating system utilizes electrical elements to generate heat, which is then distributed through radiators, electric panel heaters, or underfloor heating.
                        Heating oil: A heating oil central heating system relies on an oil-fired boiler to heat water, which is then circulated to provide warmth through radiators or underfloor heating.
                        Liquid petroleum gas (LPG): An LPG central heating system operates similarly to a gas system, but it uses liquid petroleum gas as the fuel source instead of natural gas.
                        Coal: A coal central heating system uses coal as the primary fuel for heating the property.
                        Biomass: A biomass central heating system employs renewable organic materials such as wood pellets, logs, or chips to generate heat for the property.
                        Including this information allows potential buyers to understand the type of central heating system in place, its energy source, and potential implications for maintenance and running costs. This knowledge assists buyers in making informed decisions about the property heating infrastructure during the purchasing process.',
                ])
                ->make()
                ->toArray()
        );

        $typeOfCentralHeating->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'conditionable_id' => $typeOfCentralHeating->id,
                    'answer_id' => $answerCentralHeating->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $answerTypeOfCentralHeating = $typeOfCentralHeating->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Gas', 'pdfFormFieldName' => '12.3_text'],
                            ['value' => 'Electric', 'pdfFormFieldName' => '12.3_text'],
                            ['value' => 'Heating oil', 'pdfFormFieldName' => '12.3_text'],
                            ['value' => 'Liquid petroleum gas (LPG)', 'pdfFormFieldName' => '12.3_text'],
                            ['value' => 'Coal', 'pdfFormFieldName' => '12.3_text'],
                            ['value' => 'Biomass', 'pdfFormFieldName' => '12.3_text'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTypeOfCentralHeating->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Type of central heating

        // Year System Installed
        $yearSystemInstalled = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Was the heating system installed on or after 1 April 2005?',
                    'help_text' => 'Completion certificates are necessary to show that the installation of the central heating system was carried out in accordance with Building Regulations.',
                ])
                ->make()
                ->toArray()
        );

        $yearSystemInstalled->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'conditionable_id' => $yearSystemInstalled->id,
                    'answer_id' => $answerCentralHeating->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $answerYearSystemInstalled = $yearSystemInstalled->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'No'],
                            ['value' => 'Not known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextYearSystemInstalled = $yearSystemInstalled->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => '',
                        'pdfFormFieldName' => '12.3b_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextYearSystemInstalled->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextYearSystemInstalled->id,
            'answer_id' => $answerYearSystemInstalled->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextYearSystemInstalled->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextYearSystemInstalled->id,
            'answer_id' => $answerYearSystemInstalled->id,
            'selected_value' => 'No',
        ]);

        $answerTextYearSystemInstalled->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Year System Installed

        // Completion Certificate
        $completionCertificate = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the ‘completion certificate’ (e.g. CORGI or Gas Safe Register) or the ‘exceptional circumstances’ form:',
                    'help_text' => 'To ensure the safety and compliance of the gas installation in the property, we kindly request a copy of the ‘completion certificate‘ issued by a recognized authority such as CORGI (Council for Registered Gas Installers) or Gas Safe Register. This certificate confirms that the gas work has been carried out by a qualified and registered gas engineer, meeting the required safety standards.
                    Alternatively, if a standard efficiency boiler was installed after 1st April 2005, and exceptional circumstances permit its use, please provide the ‘exceptional circumstances‘ form. This form should detail the specific reasons and justifications for using a standard efficiency boiler despite the regulations requiring a condensing boiler.
                    The documentation ensures transparency and provides confidence to potential buyers about the gas installation‘s safety and compliance, making it an integral part of the property‘s information during the selling process.',
                ])
                ->make()
                ->toArray()
        );

        $completionCertificate->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'conditionable_id' => $completionCertificate->id,
                    'answer_id' => $answerCentralHeating->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $completionCertificate->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '12.3b',
                    ],
                ])
                ->make()
                ->toArray()
        );
        // End of Completion Certificate

        // Heating working order
        $heatingWorkingOrder = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the heating system in good working order?',
                    'help_text' => 'Please state whether or not the heating system is in good working order.',
                ])
                ->make()
                ->toArray()
        );

        $heatingWorkingOrder->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'conditionable_id' => $heatingWorkingOrder->id,
                    'answer_id' => $answerCentralHeating->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $heatingWorkingOrder->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '12.3c_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '12.3c_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );
        // End of Heating working order

        // Heating Last Serviced
        $heatingLastServiced = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'In what year was the heating system last serviced or maintained?',
                    'help_text' => 'Please state the year that the heating system was last serviced and provide a copy of the inspection report.',
                ])
                ->make()
                ->toArray()
        );

        $heatingLastServiced->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'conditionable_id' => $heatingLastServiced->id,
                    'answer_id' => $answerCentralHeating->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $heatingLastServicedYear = $heatingLastServiced->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Year',
                'placeholder' => 'Enter the year',
                'pdfFormFieldName' => '12.3d_text',
                'altText' => 'Heating system last serviced in %s',
            ],
        ]);

        $heatingLastServicedYear->validationRules()->create([
            'rule' => 'required',
        ]);

        $heatingLastServicedNotKnown = $heatingLastServiced->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
                'pdfFormFieldName' => '12.3d_text',
                'altValue' => 'not known',
                'altText' => 'Heating system last serviced in %s',
            ],
        ]);

        $heatingLastServicedYear->conditions()->create([
            'answer_id' => $heatingLastServicedNotKnown->id,
            'selected_value' => '0',
        ]);
        // End of Heating Last Serviced

        // Heating Inspection Document
        $heatingInspectionDocument = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please supply a copy of the heating system inspection report:',
                    'help_text' => 'To ensure transparency and to provide potential buyers with a comprehensive understanding of the heating system condition, we kindly request a copy of the heating system inspection report. This report should be conducted by a qualified and registered heating engineer or professional, assessing the system functionality, safety, and overall condition. The inspection report helps buyers make informed decisions about the property heating system and identifies any necessary repairs, maintenance, or upgrades that may be required. By sharing this report, you demonstrate a commitment to providing accurate information and ensuring the heating system optimal performance for the new homeowner.',
                ])
                ->make()
                ->toArray()
        );

        $heatingInspectionDocument->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'step',
                    'conditionable_id' => $heatingInspectionDocument->id,
                    'answer_id' => $answerCentralHeating->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $heatingInspectionDocument->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '12.3d',
                    ],
                ])
                ->make()
                ->toArray()
        );
        // End of Heating Inspection Document

        // Foul water drainage
        $foulWaterDrainage = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm whether the property is connected to the following mains: Foul water drainage?',
                    'help_text' => 'Foul water drainage drains the used water from toilets, sinks, baths, showers, washing machines and dishwashers.',
                ])
                ->make()
                ->toArray()
        );

        $answerFoulWaterDrainage = $foulWaterDrainage->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '12.4a_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '12.4a_no'],
                            ['value' => 'Not known', 'pdfFormFieldName' => '12.4a_not_known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerFoulWaterDrainage->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Foul water drainage

        // Surface water drainage
        $surfaceWaterDrainage = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm whether the property is connected to the following mains: Surface water drainage?',
                    'help_text' => 'Please provide confirmation on whether the property is connected to the mains for surface water drainage. Surface water drainage refers to the system that manages rainwater runoff from the property and ensures it is safely directed away to prevent flooding and water damage. Having a well-maintained and connected surface water drainage system is vital for protecting the property and its surroundings during heavy rainfall or adverse weather conditions. By confirming this information, potential buyers can assess the property drainage capabilities and ensure proper water management, promoting a safe and sustainable living environment.',
                ])
                ->make()
                ->toArray()
        );

        $answerSurfaceWaterDrainage = $surfaceWaterDrainage->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '12.4b_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '12.4b_no'],
                            ['value' => 'Not known', 'pdfFormFieldName' => '12.4b_not_known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSurfaceWaterDrainage->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Surface water drainage

        // Provided sewerage
        $providedSewerage = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is sewerage for the property provided by a:',
                    'help_text' => 'Please indicate the type of sewerage system that serves the property: Septic Tank, Sewage Treatment Plant, or Cesspool. Each of these systems handles wastewater and sewage differently.
                    Septic Tank: A septic tank is an underground wastewater treatment system used in properties without access to a mains sewer. It consists of a large tank that collects and treats sewage through natural processes, separating solids from liquids. The effluent is then discharged into a drainfield for further purification by the soil.
                    Sewage Treatment Plant: A sewage treatment plant is a system that treats wastewater on-site before discharging it into the environment. It utilizes various biological, chemical, and physical processes to remove contaminants from sewage, ensuring the effluent is safe for discharge.
                    Cesspool: A cesspool is a sealed, underground holding tank used to collect sewage and wastewater. Unlike a septic tank, cesspools do not treat sewage, and the contents must be periodically pumped out and disposed of properly.
                    Knowing the type of sewerage system is crucial for buyers to understand the property sewage management and any potential responsibilities or maintenance involved with the system.',
                ])
                ->make()
                ->toArray()
        );

        $answerProvidedSewerage = $providedSewerage->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Septic tank', 'pdfFormFieldName' => '12.5a_yes'],
                            ['value' => 'Sewage treatment plant', 'pdfFormFieldName' => '12.5.1b_yes'],
                            ['value' => 'Cesspool', 'pdfFormFieldName' => '12.5.1c_yes'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerProvidedSewerage->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $providedSewerage->conditions()->create([
            'answer_id' => $answerFoulWaterDrainage->id,
            'selected_value' => 'No',
        ]);

        $providedSewerage->conditions()->create([
            'answer_id' => $answerSurfaceWaterDrainage->id,
            'selected_value' => 'No',
        ]);
        // End of Provided sewerage

        // Septic tank replaced
        $septicTankReplaced = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'When was the septic tank last replaced or upgraded?',
                    'help_text' => "Unfortunately, septic systems don't last forever. With regular maintenance and pumping, your septic system can last many years. However, after decades of wear and tear, the system will need to be replaced. Please state the year that the septic tank was last replaced or upgraded.",
                ])
                ->make()
                ->toArray()
        );

        $answerSepticTankReplacedYear = $septicTankReplaced->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Year',
                        'placeholder' => 'Enter year',
                        'pdfFormFieldName' => '12.7_year',
                        'altText' => 'Septic tank replaced in %s',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSepticTankReplacedYear->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerNotKnownSepticTankReplaced = $septicTankReplaced->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
                'pdfFormFieldName' => '12.7_text',
                'altValue' => 'not known',
                'altText' => 'Septic tank replaced in %s',
            ],
        ]);

        $answerSepticTankReplacedYear->conditions()->create([
            'answer_id' => $answerNotKnownSepticTankReplaced->id,
            'selected_value' => '0',
        ]);

        $septicTankReplaced->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $providedSewerage->id,
                    'answer_id' => $answerProvidedSewerage->id,
                    'selected_value' => 'Septic tank',
                ])
                ->make()
                ->toArray()
        );
        // End of Septic tank replaced

        // Sewage treatment last serviced
        $sewageTreatmentLastServiced = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'When was the sewage treatment plant last serviced?',
                    'help_text' => 'Please state the year that the sewage treatment plant was last serviced.',
                ])
                ->make()
                ->toArray()
        );

        $sewageTreatmentLastServiced->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Year',
                        'placeholder' => 'Enter year',
                        'pdfFormFieldName' => '12.8_year',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $sewageTreatmentLastServiced->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Not known', 'pdfFormFieldName' => '12.8_year'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $sewageTreatmentLastServiced->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $providedSewerage->id,
                    'answer_id' => $answerProvidedSewerage->id,
                    'selected_value' => 'Sewage treatment plant',
                ])
                ->make()
                ->toArray()
        );
        // End of Sewage treatment last serviced

        // System shared with other properties
        $systemSharedWithOtherProperties = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the system shared with other properties?',
                    'help_text' => 'It’s not uncommon for rural neighbours to share a septic tank. In these cases, whose land the tank is located on will be important, as will whether there’s a proper legal agreement in place to cover access and responsibilities for maintenance.',
                ])
                ->make()
                ->toArray()
        );

        $answerSystemSharedWithOtherProperties = $systemSharedWithOtherProperties->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '12.6_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '12.6_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSystemSharedWithOtherProperties->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerSystemSharedWithOtherProperties->conditions()->create([
            'answer_id' => $answerProvidedSewerage->id,
            'selected_value' => 'Septic tank',
        ]);

        $systemSharedWithOtherProperties->conditions()->create([
            'answer_id' => $answerNotKnownSepticTankReplaced->id,
            'selected_value' => 'Not known',
        ]);
        // End of System shared with other properties

        // Number Of Properties Shared With
        $numberOfPropertiesSharedWith = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'How many properties share the system?',
                    'help_text' => 'If you are not sure you may find this information on your title deeds however, this information may not be up to date. The best and easiest way to find how many properties share your septic system is to ask your neighbours.',
                ])
                ->make()
                ->toArray()
        );

        $numberOfPropertiesSharedWith->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $systemSharedWithOtherProperties->id,
                    'answer_id' => $answerSystemSharedWithOtherProperties->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $numberOfPropertiesSharedWith->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Dropdown,
                    'details' => [
                        'label' => 'Choose number of properties',
                        'placeholder' => 'Enter number of properties',
                        'options' => [
                            ['value' => '1'],
                            ['value' => '2'],
                            ['value' => '3'],
                            ['value' => '4'],
                            ['value' => '5'],
                            ['value' => '6'],
                            ['value' => '7'],
                            ['value' => '8'],
                            ['value' => '9'],
                            ['value' => '10'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $numberOfPropertiesSharedWith->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Not known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );
        // End of Number Of Properties Shared With

        // System last emptied
        $systemLastEmptied = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'When was the system last emptied?',
                    'help_text' => 'Please state the year that the drainage system was last emptied. Some drainage systems require regular emptying. Other drainage systems only need to be emptied occasionally.',
                ])
                ->make()
                ->toArray()
        );

        $answerSystemLastEmptiedYear = $systemLastEmptied->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Year',
                        'placeholder' => 'Enter year',
                        'pdfFormFieldName' => '12.7_year',
                    ],
                ])
                ->make()
                ->toArray()
        );
        $answerSystemLastEmptiedYear->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerSystemLastEmptied = $systemLastEmptied->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
            ],
        ]);
        $answerSystemLastEmptied->validationRules()->create([
            'rule' => 'nullable',
        ]);

        $answerSystemLastEmptiedYear->conditions()->create([
            'answer_id' => $answerSystemLastEmptied->id,
            'selected_value' => 0,
        ]);

        $systemLastEmptied->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $providedSewerage->id,
                    'answer_id' => $answerProvidedSewerage->id,
                    'selected_value' => 'Septic tank',
                ])
                ->make()
                ->toArray()
        );
        // End of System last emptied

        // System Installed
        $systemInstalled = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'When was the system installed?',
                    'help_text' => 'Please state the year that the drainage system was installed. Some systems installed after 1 January 1991 require building regulations approval, environmental permits or registration. If you need more information about permits and registration please click here.',
                ])
                ->make()
                ->toArray()
        );

        $answerSystemInstalledYear = $systemInstalled->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Year',
                        'placeholder' => 'Enter year',
                        'pdfFormFieldName' => '12.9_year',
                    ],
                ])
                ->make()
                ->toArray()
        );
        $answerSystemInstalledYear->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerSystemInstalled = $systemInstalled->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
            ],
        ]);
        $answerSystemInstalled->validationRules()->create([
            'rule' => 'nullable',
        ]);

        $answerSystemInstalledYear->conditions()->create([
            'answer_id' => $answerSystemInstalled->id,
            'selected_value' => 0,
        ]);

        $systemInstalled->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $providedSewerage->id,
                    'answer_id' => $answerProvidedSewerage->id,
                    'selected_value' => 'Septic tank',
                ])
                ->make()
                ->toArray()
        );
        // End of System Installed

        // Outside the boundary of the property
        $outsideTheBoundaryOfTheProperty = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is any part of the septic tank, sewage treatment plant (including any soakaway or outfall) or cesspool, or the access to it, outside the boundary of the property?',
                    'help_text' => 'Where a septic tank, sewage treatment plant or cesspool is in place at the property, we need to know whether any part of the system lies outside the boundary of the property. Please provide details of how access to the system is obtained.',
                ])
                ->make()
                ->toArray()
        );

        $answerOutsideTheBoundaryOfTheProperty = $outsideTheBoundaryOfTheProperty->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '12.10_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '12.10_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerOutsideTheBoundaryOfTheProperty->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $outsideTheBoundaryOfTheProperty->conditions()->create([
            'answer_id' => $answerProvidedSewerage->id,
            'selected_value' => 'Septic tank',
        ]);
        // End of Outside the boundary of the property

        // Plan of the system
        $planOfTheSystem = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please supply a plan showing the location of the system and how access is obtained:',
                ])
                ->make()
                ->toArray()
        );

        $planOfTheSystem->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '12.10',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $planOfTheSystem->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $outsideTheBoundaryOfTheProperty->id,
                    'answer_id' => $answerOutsideTheBoundaryOfTheProperty->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );
        // End of Plan of the system
    }

    protected function connectionToUtilitiesAndServices(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Connection to utilities and services',
                ])
                ->make()
                ->toArray()
        );

        // Mains electricity
        $mainsElectricity = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there mains electricity at the property?',
                    'help_text' => 'Please state the year that the drainage system was installed. Some systems installed after 1 January 1991 require building regulations approval, environmental permits or registration. If you need more information about permits and registration please click here.',
                ])
                ->make()
                ->toArray()
        );

        $answerMainsElectricity = $mainsElectricity->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '13_electric_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '13_electric_no'],
                            ['value' => 'Not known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerMainsElectricity->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Mains electricity

        // Name of Electricity Provider
        $nameOfElectricityProvider = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'What is the name of your electricity provider?',
                    'help_text' => 'Your energy supplier is the company you pay your electricity bill to.',
                ])
                ->make()
                ->toArray()
        );

        $answerNameOfElectricityProvider = $nameOfElectricityProvider->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Providers name',
                        'placeholder' => 'Providers name',
                        'pdfFormFieldName' => '12_electric_provider',
                    ],
                ])
                ->make()
                ->toArray()
        );
        $answerNameOfElectricityProvider->validationRules()->create([
            'rule' => 'required',
        ]);

        $nameOfElectricityProvider->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $mainsElectricity->id,
                    'answer_id' => $answerMainsElectricity->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $answerNameOfElectricityProviderKnown = $nameOfElectricityProvider->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Checkbox,
                    'details' => [
                        'label' => 'Not known',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerNameOfElectricityProvider->conditions()->create([
            'answer_id' => $answerNameOfElectricityProviderKnown->id,
            'selected_value' => '0',
        ]);
        // End of Name of Electricity Provider

        // Electricity Meter Location
        $electricityMeterLocation = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Where is the electricity meter located?',
                    'help_text' => 'Meters will generally be located at the point where the power enters your property so look up to see if you can see where the power line comes in from the road. For most households, domestic electricity meters will look like a square shaped box on a wall often displaying 6 digits on an LCD display. Your meter will usually be on an outside wall of your house.',
                ])
                ->make()
                ->toArray()
        );

        $answerElectricityMeterLocation = $electricityMeterLocation->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Location of meter',
                        'placeholder' => 'Please describe where the electricity meter is located,.e.g. under the stairs, on the wall in the back garden etc.',
                        'pdfFormFieldName' => '12_electric_meter',
                    ],
                ])
                ->make()
                ->toArray()
        );
        $answerElectricityMeterLocation->validationRules()->create([
            'rule' => 'required',
        ]);

        $electricityMeterLocation->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $mainsElectricity->id,
                    'answer_id' => $answerMainsElectricity->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $answerElectricityMeterLocation->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerElectricityMeterLocationKnown = $electricityMeterLocation->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Checkbox,
                    'details' => [
                        'label' => 'Not known',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerElectricityMeterLocation->conditions()->create([
            'answer_id' => $answerElectricityMeterLocationKnown->id,
            'selected_value' => '0',
        ]);
        // End of Electricity Meter Location

        // Gas Mains
        $gasMains = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there a gas main at the property?',
                    'help_text' => 'Mains gas is the natural gas that is distributed to buildings through a pipeline infrastructure. In the UK, mains gas is supplied to more than 21 million homes and is the most popular fuel for heating and cooking.',
                ])
                ->make()
                ->toArray()
        );

        $answerGasMains = $gasMains->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '13_gas_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '13_gas_no'],
                            ['value' => 'Not known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerGasMains->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Gas Mains

        // Name of Gas Provider
        $nameOfGasProvider = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'What is the name of your mains gas provider?',
                    'help_text' => 'Your gas supplier is the company you pay your gas bill to.',
                ])
                ->make()
                ->toArray()
        );

        $answerNameOfGasProvider = $nameOfGasProvider->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Providers name',
                        'placeholder' => 'Providers name',
                        'pdfFormFieldName' => '13_gas_provider',
                    ],
                ])
                ->make()
                ->toArray()
        );
        $answerNameOfGasProvider->validationRules()->create([
            'rule' => 'required',
        ]);

        $nameOfGasProvider->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $gasMains->id,
                    'answer_id' => $answerGasMains->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $answerNameOfGasProviderKnown = $nameOfGasProvider->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Checkbox,
                    'details' => [
                        'label' => 'Not known',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerNameOfGasProvider->conditions()->create([
            'answer_id' => $answerNameOfGasProviderKnown->id,
            'selected_value' => '0',
        ]);
        // End of Name of Gas Provider

        // Gas Meter Location
        $gasMeterLocation = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Where is the mains gas meter located?',
                    'help_text' => 'Gas meters located inside are normally found inside a kitchen (it will normally be concealed in a cupboard) or a hallway, and will most likely be inside a mounted meter cupboard or a meter box. If you live in a flat, your meters might be located in a communal cupboard or the basement.',
                ])
                ->make()
                ->toArray()
        );

        $answerGasMeterLocation = $gasMeterLocation->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Location of meter',
                        'placeholder' => 'Please describe where the gas meter is located,.e.g. under the stairs, on the wall in the back garden etc.',
                        'pdfFormFieldName' => '13_gas_meter',
                    ],
                ])
                ->make()
                ->toArray()
        );
        $answerGasMeterLocation->validationRules()->create([
            'rule' => 'required',
        ]);

        $gasMeterLocation->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $gasMains->id,
                    'answer_id' => $answerGasMains->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $answerGasMeterLocation->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerGasMeterLocationKnown = $gasMeterLocation->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
            ],
        ]);
        $answerGasMeterLocation->conditions()->create([
            'answer_id' => $answerGasMeterLocationKnown->id,
            'selected_value' => '0',
        ]);
        // End of Gas Meter Location

        // Water Mains
        $waterMains = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there mains water at the property?',
                    'help_text' => 'Mains water is water supplied by the public water supply system.',
                ])
                ->make()
                ->toArray()
        );

        $answerWaterMains = $waterMains->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '13_water_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '13_water_no'],
                            ['value' => 'Not known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerWaterMains->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Water Mains

        // Name of Water Provider
        $nameOfWaterProvider = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'What is the name of your water provider?',
                    'help_text' => 'Your water supplier is the company you pay your water bill to.',
                ])
                ->make()
                ->toArray()
        );

        $answerNameOfWaterProvider = $nameOfWaterProvider->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Providers name',
                        'placeholder' => 'Providers name',
                        'pdfFormFieldName' => '13_water_provider',
                    ],
                ])
                ->make()
                ->toArray()
        );
        $answerNameOfWaterProvider->validationRules()->create([
            'rule' => 'required',
        ]);

        $nameOfWaterProvider->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $waterMains->id,
                    'answer_id' => $answerWaterMains->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $answerNameOfWaterProviderKnown = $nameOfWaterProvider->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
            ],
        ]);
        $answerNameOfWaterProvider->conditions()->create([
            'answer_id' => $answerNameOfWaterProviderKnown->id,
            'selected_value' => '0',
        ]);
        // End of Name of Water Provider

        // Stopcock Location
        $stopcockLocation = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Where is the stopcock located?',
                    'help_text' => 'Your internal stopcock will be located inside your home and is usually found just after the water pipe enters the house. If you have the plans of your house or building, the stopcock will usually be marked SC. Otherwise it is most likely under your kitchen sink.',
                ])
                ->make()
                ->toArray()
        );

        $answerStopcockLocation = $stopcockLocation->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Location of stopcock',
                        'placeholder' => 'Please describe where the stopcock is located,.e.g. under the stairs, on he wall in the back garden etc.',
                        'pdfFormFieldName' => '13_water_stopcock',
                    ],
                ])
                ->make()
                ->toArray()
        );
        $answerStopcockLocation->validationRules()->create([
            'rule' => 'required',
        ]);

        $stopcockLocation->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $waterMains->id,
                    'answer_id' => $answerWaterMains->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $answerStopcockLocation->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerStopcockLocationKnown = $stopcockLocation->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
            ],
        ]);
        $answerStopcockLocation->conditions()->create([
            'answer_id' => $answerStopcockLocationKnown->id,
            'selected_value' => '0',
        ]);
        // End of Stopcock Location

        // Water Meter Location
        $waterMeterLocation = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Where is the water meter located?',
                    'help_text' => 'You will usually find your water meter under the kitchen sink where your water supply comes into your home. It could also be in an underground box in the garden, or the footpath outside your property (look for a small round plastic lid).',
                ])
                ->make()
                ->toArray()
        );

        $answerWaterMeterLocation = $waterMeterLocation->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Location of meter',
                        'placeholder' => 'Please describe where the water meter is located, e.g. under the stairs, on the wall in the back garden etc.',
                        'pdfFormFieldName' => '13_water_meter',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $waterMeterLocation->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $waterMains->id,
                    'answer_id' => $answerWaterMains->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $answerWaterMeterLocation->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerWaterMeterLocationKnown = $waterMeterLocation->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
            ],
        ]);
        $answerWaterMeterLocation->conditions()->create([
            'answer_id' => $answerWaterMeterLocationKnown->id,
            'selected_value' => '0',
        ]);
        // End of Water Meter Location

        // Sewerage Mains
        $sewerageMains = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there mains sewerage at the property?',
                    'help_text' => 'Mains sewerage are pipelines running under the public street that collect wastewater and connect homes to the main sewer line. The main sewer carries the sewage to the sewage treatment plant where the wastewater is treated before being safely discharged.',
                ])
                ->make()
                ->toArray()
        );

        $answerSewerageMains = $sewerageMains->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '13_sewerage_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '13_sewerage_no'],
                            ['value' => 'Not known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSewerageMains->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Sewerage Mains

        // Name of Sewerage Provider
        $nameOfSewerageProvider = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'What is the name of your mains sewerage provider?',
                    'help_text' => 'Mains sewerage charges are usually included in your water bill.',
                ])
                ->make()
                ->toArray()
        );

        $answerNameOfSewerageProvider = $nameOfSewerageProvider->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Providers name',
                        'placeholder' => 'Providers name',
                        'pdfFormFieldName' => '13_sewerage_provider',
                    ],
                ])
                ->make()
                ->toArray()
        );
        $answerNameOfSewerageProvider->validationRules()->create([
            'rule' => 'required',
        ]);

        $nameOfSewerageProvider->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $sewerageMains->id,
                    'answer_id' => $answerSewerageMains->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $answerNameOfSewerageProviderKnown = $nameOfSewerageProvider->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
            ],
        ]);
        $answerNameOfSewerageProvider->conditions()->create([
            'answer_id' => $answerNameOfSewerageProviderKnown->id,
            'selected_value' => '0',
        ]);
        // End of Name of Sewerage Provider

        // Telephone Line
        $telephoneLine = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there a telephone line at the property?',
                    'help_text' => 'If you want to check if your house or flat has a phone line, then the easiest way is to plug a home phone into the landline by using the phone jack. If you hear a dial tone, this means that there is a phone line connected to your house. If you do not know where your phone jack is, your best bet would be to find the cable coming into the home and trace its location. Look on the outside of your home where the electric meter is located, usually your demark (telephone box) will be located next to it. This should give you a point of reference in the attic / basement to trace the wire.',
                ])
                ->make()
                ->toArray()
        );

        $answerTelephoneLine = $telephoneLine->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '13_telephone_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '13_telephone_no'],
                            ['value' => 'Not known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTelephoneLine->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Telephone Line

        // Telephone Line Provider
        $telephoneLineProvider = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'What is the name of your telephone line provider?',
                    'help_text' => 'If you need to find out which company the phone is with, you can do so by calling the number 150 from the landline.',
                ])
                ->make()
                ->toArray()
        );

        $answerNameOfTelephoneLineProvider = $telephoneLineProvider->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Providers name',
                        'placeholder' => 'Providers name',
                        'pdfFormFieldName' => '13_telephone_provider',
                    ],
                ])
                ->make()
                ->toArray()
        );
        $answerNameOfTelephoneLineProvider->validationRules()->create([
            'rule' => 'required',
        ]);

        $telephoneLineProvider->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $telephoneLine->id,
                    'answer_id' => $answerTelephoneLine->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $answerTelephoneLineProviderKnown = $telephoneLineProvider->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
            ],
        ]);
        $answerNameOfTelephoneLineProvider->conditions()->create([
            'answer_id' => $answerTelephoneLineProviderKnown->id,
            'selected_value' => '0',
        ]);
        // End of Telephone Line Provider

        // Cable Connection
        $cableConnection = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there cable connection at the property?',
                    'help_text' => 'Cable connection is a high-speed connection that provides your cable TV connection and allows you to access the Internet. If you are not sure whether you have cable connection, look at the type of plug your modem is connected to on the wall. If it is connected with a coaxial cable, then you are most likely dealing with a cable internet connection. However, if the other end of that coaxial cable connects to a satellite outside your home, then you have a satellite connection.',
                ])
                ->make()
                ->toArray()
        );

        $answerCableConnection = $cableConnection->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '13_cable_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '13_cable_no'],
                            ['value' => 'Not known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCableConnection->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Cable Connection

        // Cable Provider
        $cableProvider = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'What is the name of your cable provider?',
                    'help_text' => 'Your cable connection is the company you pay your TV and Wi-Fi bill to.',
                ])
                ->make()
                ->toArray()
        );

        $answerNameOfCableProvider = $cableProvider->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Providers name',
                        'placeholder' => 'Providers name',
                        'pdfFormFieldName' => '13_cable_provider',
                    ],
                ])
                ->make()
                ->toArray()
        );
        $answerNameOfCableProvider->validationRules()->create([
            'rule' => 'required',
        ]);

        $cableProvider->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'answer',
                    'conditionable_id' => $cableConnection->id,
                    'answer_id' => $answerCableConnection->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        $answerNameOfCableProviderKnown = $cableProvider->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
            ],
        ]);
        $answerNameOfCableProvider->conditions()->create([
            'answer_id' => $answerNameOfCableProviderKnown->id,
            'selected_value' => '0',
        ]);
        // End of Cable Provider
    }

    protected function transactionInformation(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Transaction information',
                ])
                ->make()
                ->toArray()
        );

        // Dependant Sale
        $dependantSale = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is this sale dependent on the seller completing the purchase of another property on the same day?',
                    'help_text' => 'We need to know if you wish to buy another property at the same time. This will enable the buyer to understand whether or not there is a linked chain of sales and purchases.',
                ])
                ->make()
                ->toArray()
        );

        $answerDependantSale = $dependantSale->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '14.1_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '14.1_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerDependantSale->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Dependant Sale

        // Special Requirements
        $specialRequirements = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller have any special requirements about a moving date?',
                    'help_text' => 'Please state whether there are any special requirements about a moving date so that this can be negotiated with the buyer if necessary.',
                ])
                ->make()
                ->toArray()
        );

        $answerSpecialRequirements = $specialRequirements->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '14.2_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '14.2_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSpecialRequirements->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSpecialRequirements = $specialRequirements->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the seller wants the moving date to be at the end of November',
                        'pdfFormFieldName' => '14.2_text',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSpecialRequirements->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $answerTextSpecialRequirements->id,
            'answer_id' => $answerSpecialRequirements->id,
            'selected_value' => 'Yes',
        ]);
        $answerTextSpecialRequirements->validationRules()->create([
            'rule' => 'required',
        ]);
        // End of Special Requirements

        // Mortgage Requirements
        $mortgageRequirements = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Will the sale price be sufficient to repay all mortgages and charges secured on the property?',
                    'help_text' => 'It is important for the buyer and the buyer’s solicitors to know that the amount being paid for the property is enough to pay off outstanding mortgages. If there isn’t enough it doesn’t mean the sale cannot proceed but it may mean that some extra steps need to be taken.',
                ])
                ->make()
                ->toArray()
        );

        $answerMortgageRequirements = $mortgageRequirements->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '14.3_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '14.3_no'],
                            ['value' => 'No mortgage', 'pdfFormFieldName' => '14.3_no_mortgage'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerMortgageRequirements->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Mortgage Requirements

        // Property Condition
        $propertyCondition = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Will the seller ensure that:',
                    'sub_heading' => 'All rubbish is removed from the property (including from the loft, garden, outbuildings, garages and sheds) and that the property will be left in a clean and tidy condition?',
                    'help_text' => 'You are required by law to leave the property as “vacant possession”, meaning empty of all belongings. We will ask you to complete the Fittings and Contents Form which will allow you to list all the items that you may want or may have agreed to leave with the buyer. Please be aware that if possessions are left at the property, the buyer is entitled to arrange for removal and claim the costs of removal from the seller. Solicitors will pass on any information from the buyer about items left in order to give the seller an opportunity to collect the possessions.',
                ])
                ->make()
                ->toArray()
        );

        $answerPropertyCondition = $propertyCondition->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '14.4a_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '14.4a_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerPropertyCondition->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Property Condition

        // Property Light Condition
        $propertyLightCondition = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Will the seller ensure that:',
                    'sub_heading' => 'If light fittings are removed, the fittings will be replaced with ceiling rose, flex, bulb holder and bulb?',
                    'help_text' => 'You are required by law to leave the property as “vacant possession”, meaning empty of all belongings. We will ask you to complete the Fittings and Contents Form which will allow you to list all the items that you may want or may have agreed to leave with the buyer. Please be aware that if possessions are left at the property, the buyer is entitled to arrange for removal and claim the costs of removal from the seller. Solicitors will pass on any information from the buyer about items left in order to give the seller an opportunity to collect the possessions.',
                ])
                ->make()
                ->toArray()
        );

        $answerPropertyLightCondition = $propertyLightCondition->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '14.4b_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '14.4b_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerPropertyLightCondition->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Property Light Condition

        // Property fittings or contents
        $propertyFittingsOrContents = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Will the seller ensure that:',
                    'sub_heading' => 'Reasonable care will be taken when removing any other fittings or contents?',
                    'help_text' => 'You are required by law to leave the property as “vacant possession”, meaning empty of all belongings. We will ask you to complete the Fittings and Contents Form which will allow you to list all the items that you may want or may have agreed to leave with the buyer. Please be aware that if possessions are left at the property, the buyer is entitled to arrange for removal and claim the costs of removal from the seller. Solicitors will pass on any information from the buyer about items left in order to give the seller an opportunity to collect the possessions.',
                ])
                ->make()
                ->toArray()
        );

        $answerPropertyFittingsOrContents = $propertyFittingsOrContents->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '14.4c_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '14.4c_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerPropertyFittingsOrContents->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Property fittings or contents

        // Content and keys
        $contentAndKeys = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Will the seller ensure that:',
                    'sub_heading' => 'Keys to all windows and doors and details of alarm codes will be left at the property or with the estate agent?',
                    'help_text' => 'You are required by law to leave the property as “vacant possession”, meaning empty of all belongings. We will ask you to complete the Fittings and Contents Form which will allow you to list all the items that you may want or may have agreed to leave with the buyer. Please be aware that if possessions are left at the property, the buyer is entitled to arrange for removal and claim the costs of removal from the seller. Solicitors will pass on any information from the buyer about items left in order to give the seller an opportunity to collect the possessions.',
                ])
                ->make()
                ->toArray()
        );

        $answerContentAndKeys = $contentAndKeys->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '14.4d_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '14.4d_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerContentAndKeys->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Content and keys
    }
}
