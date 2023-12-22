<?php

namespace Database\Seeders\Forms\Sale;

use App\Enums\AnswerType;
use App\Enums\FormGroup;
use App\Enums\PropertyType;
use App\Models\Answer;
use App\Models\Condition;
use App\Models\Form;
use App\Models\Section;
use App\Models\Step;
use App\Models\ValidationRule;
use Illuminate\Database\Seeder;

class Enquiry_SewageTreatmentPlant extends Seeder
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
                'name' => 'Sewage Treatment Plant',
                'description' => 'Information about the sewage treatment plant on the property',
                'group' => FormGroup::Enquiry,
                'order_number' => 9,
                'type' => PropertyType::Sale,
            ])
            ->create();

        $form->conditions()->create(
            Condition::factory()
                ->state([
                    'conditionable_type' => 'form',
                    'answer_id' => Answer::whereHas('step', function ($query) {
                        $query->where('question', 'Is sewerage for the property provided by a:');
                    })->first()->id,
                    'selected_value' => 'Sewage treatment plant',
                ])
                ->make()
                ->toArray()
        );

        $this->theSystem($form);
        $this->regulation($form);
        $this->maintenance($form);
    }

    protected function theSystem(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'The system',
                ])
                ->make()
                ->toArray()
        );

        // British Standards
        $britishStandardsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Did the system comply with the British Standards in place at the time of installation?',
                    'help_text' => 'Your treatment system must meet the relevant British Standard which was in force at the time of installation. The standards currently in force for new systems are:<ul><li>BS EN 12566 for small sewage plants</li><li>BS 6297:2007 for drainage fields</li></ul>Your treatment plant met the British Standard in place at the time of installaiton if:<ul><li>It has a CE mark</li><li>The manual or other documentation that came with your tank or treatment plant has a certificate of compliance with a British Standard</li><li>Its on British Water&quot;s list of approved equipment</li></ul>',
                ])
                ->make()
                ->toArray()
        );

        $britishStandardsAnswer = $britishStandardsStep->answers()->create(
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

        $britishStandardsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Water flow
        $waterFlowStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the water from the system flow into a watercourse or into land?',
                    'help_text' => 'A watercourse is the channel that a flowing body of water follows. A sewage treatment plant treats the effluent and emits near clear water. It can discharge directly to a drainage ditch, watercourse or river. This discharge does not require a drainage field.',
                ])
                ->make()
                ->toArray()
        );

        $waterFlowAnswer = $waterFlowStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Watercourse'],
                            ['value' => 'Land'],
                            ['value' => 'Not known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $waterFlowAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Overflow
        $overflowStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there an overflow or soakaway system?',
                    'help_text' => 'An overflow or soakaway system is designed to manage excess water or effluent from a sewage treatment plant. When a sewage treatment plant reaches its capacity, the excess liquid is diverted to an overflow or soakaway system to prevent flooding or overloading of the tank.
                    As the seller, you need to confirm whether your property has an overflow or soakaway system associated with the sewage treatment plant. An overflow or soakaway system plays a crucial role in the proper functioning of a sewage treatment plant, ensuring that it operates efficiently and complies with environmental regulations. It helps to prevent potential issues such as ground saturation, odors, or contamination of water sources. If you are unsure about the existence or condition of such a system, you may need to consult previous documentation or obtain professional advice to answer this question accurately.',
                ])
                ->make()
                ->toArray()
        );

        $overflowAnswer = $overflowStep->answers()->create(
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

        $overflowAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Overflow boundaries
        $overflowBoundariesStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the overflow or soakaway system all contained within the property\'s boundaries?',
                    'help_text' => 'You need to determine whether the overflow or soakaway system associated with the sewage treatment plant is entirely contained within the boundaries of the property. An overflow or soakaway system that is entirely contained within the property`s boundaries means that it discharges the excess water or effluent on the property`s land and does not extend beyond its limits.
                    If the overflow or soakaway system is contained within the property`s boundaries, it may have implications for compliance with environmental regulations and could affect the property`s usage and future maintenance requirements. It is crucial to provide accurate information about the location and containment of the system to ensure transparency and clarity for potential buyers.
                    To answer this question, you may need to review property documents, consult with relevant authorities, or seek professional advice to confirm the exact location and containment status of the overflow or soakaway system.',
                ])
                ->make()
                ->toArray()
        );

        $overflowBoundariesStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $overflowBoundariesStep->id,
            'answer_id' => $overflowAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $overflowBoundariesAnswer = $overflowBoundariesStep->answers()->create(
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

        $overflowBoundariesAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
    }

    protected function regulation(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Regulation',
                ])
                ->make()
                ->toArray()
        );

        // Consents
        $consentsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Have all appropriate consents of the National Rivers Authority (NRA), Environment Agency (EA) or other appropriate body been obtained for the system?',
                    'help_text' => 'If you have a small sewage treatment plant, by law you must comply with the &quot;general binding rules&quot; by ensuring your system is maintained properly and does not cause pollution. Extra protection is in place in areas designated as environmentally sensitive, where people may need to apply for a permit.',
                ])
                ->make()
                ->toArray()
        );

        $consentsAnswer = $consentsStep->answers()->create(
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

        $consentsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $consentsUploadAnswer = $consentsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $consentsUploadAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $consentsUploadAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $consentsUploadAnswer->id,
            'answer_id' => $consentsAnswer->id,
            'selected_value' => 'Yes',
        ]);

        // Enforcement
        $enforcementStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has there ever been any enforcement action taken by the NRA, EA or other appropriate body?',
                    'help_text' => 'NRA, EA and other appropriate bodies anticipate that offering advice and guidance will be sufficient to ensure compliance in the majority of cases without the need for enforcement action. These bodies do have powers to take enforcement action if necessary, but this is only be where advice and guidance has failed. Please let us know if this has ever applied to you.',
                ])
                ->make()
                ->toArray()
        );

        $enforcementAnswer = $enforcementStep->answers()->create(
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

        $enforcementAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $enforcementUploadAnswer = $enforcementStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $enforcementUploadAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $enforcementUploadAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $enforcementUploadAnswer->id,
            'answer_id' => $enforcementAnswer->id,
            'selected_value' => 'Yes',
        ]);

        // Pending enforcement
        $pendingEnforcementStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there any enforcement action by the NRA, EA or other appropriate body pending?',
                    'help_text' => 'If NRA, EA or any other appropriate body has taken an enforcement action that is still pending, please provide us with as much details as possible and any documentation you may have received so far.',
                ])
                ->make()
                ->toArray()
        );

        $pendingEnforcementAnswer = $pendingEnforcementStep->answers()->create(
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

        $pendingEnforcementAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $pendingEnforcementUploadAnswer = $pendingEnforcementStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $pendingEnforcementUploadAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $pendingEnforcementUploadAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $pendingEnforcementUploadAnswer->id,
            'answer_id' => $pendingEnforcementAnswer->id,
            'selected_value' => 'Yes',
        ]);
    }

    protected function maintenance(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Maintenance and working of the system',
                ])
                ->make()
                ->toArray()
        );

        // Emptied
        $emptiedStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'How often is the system emptied?',
                    'help_text' => 'The purpose of a sewage treatment plant is to treat the wastewater as thoroughly as practically possible &ndash; and, even though such plants can often deal with more waste than a septic tank, they will still need emptying from time to time.',
                ])
                ->make()
                ->toArray()
        );

        $emptiedAnswer = $emptiedStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Monthly'],
                            ['value' => 'Annually'],
                            ['value' => 'Every 2 - 5 years'],
                            ['value' => 'Whenever required'],
                            ['value' => 'Not known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $emptiedAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Last emptied
        // TODO: Add the or conditions to this step depending on the previous step
        $lastEmptiedStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'When was the system last emptied?',
                    'help_text' => 'Please state the year that the drainage system was last emptied. Some drainage systems require regular emptying. Other drainage systems only need to be emptied occasionally.',
                ])
                ->make()
                ->toArray()
        );

        $lastEmptiedAnswer = $lastEmptiedStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Year',
                        'placeholder' => 'Select year',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $lastEmptiedAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $lastEmptiedNotKnownAnswer = $lastEmptiedStep->answers()->create(
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

        $lastEmptiedAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $lastEmptiedAnswer->id,
            'answer_id' => $lastEmptiedNotKnownAnswer->id,
            'selected_value' => '0',
        ]);

        // Cost
        $costStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'What is the approximate cost of emptying the system?',
                    'help_text' => 'The approximate cost of emptying the system.',
                ])
                ->make()
                ->toArray()
        );

        $costStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $costStep->id,
            'answer_id' => $emptiedAnswer->id,
            'selected_value' => 'Whenever required',
        ]);

        $costAnswer = $costStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Cost',
                        'placeholder' => 'e.g. £3500',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $costNotKnownAnswer = $costStep->answers()->create(
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

        $costAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $costNotKnownAnswer->id,
            'answer_id' => $emptiedAnswer->id,
            'selected_value' => '0',
        ]);

        // Working
        $workingStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the system in good working order?',
                    'help_text' => 'As a sewage treatment plant is considered a private system, all responsibility for it falls upon the property owner. If not properly cared for, your sewage treatment plant could land you in all kinds of legal and financial difficulties. This is why it is important to ensure that your drainage system remains in good working condition and continues to comply with national environment agency standards. In order to guarantee this, it is adviced to get it regularly serviced by a qualified professional.',
                ])
                ->make()
                ->toArray()
        );

        $workingAnswer = $workingStep->answers()->create(
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

        $workingAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Blockage
        $blockageStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the system ever experienced a blockage?',
                    'help_text' => 'We need to know wether you have ever experienced any type of blockage in the drains of your system.',
                ])
                ->make()
                ->toArray()
        );

        $blockageAnswer = $blockageStep->answers()->create(
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

        $blockageAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Blockage Rectified
        $blockageRectifiedStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Was the issue rectified?',
                    'help_text' => 'If you experienced a simple clog is in the drains, you may have been able to fix it on your own. Other problems may have required a specialist. Please let us know how these issues have been solved.',
                ])
                ->make()
                ->toArray()
        );

        $blockageRectifiedStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $blockageRectifiedStep->id,
            'answer_id' => $blockageAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $blockageRectifiedAnswer = $blockageRectifiedStep->answers()->create(
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

        $blockageRectifiedAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Complaints
        $complaintsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Have any neighbours ever complained about the system?',
                    'help_text' => 'We hope that you never had to deal with this but please let us know if you have any current or past disputes related to your sewage system.',
                ])
                ->make()
                ->toArray()
        );

        $complaintsAnswer = $complaintsStep->answers()->create(
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

        $complaintsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $complaintsDetailAnswer = $complaintsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $complaintsDetailAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $complaintsDetailAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $complaintsDetailAnswer->id,
            'answer_id' => $complaintsAnswer->id,
            'selected_value' => 'Yes',
        ]);

        // Maintenance
        $maintenanceStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Other than routine emptying, is the seller aware of any maintenance required in the near future?',
                    'help_text' => 'You should inform potential buyers about any maintenance requirements beyond routine emptying that may be needed in the near future for the sewage treatment plant system. For instance, if there are known issues with the system, such as a need for repairs or replacement of components, it is important to disclose this information. Providing details about any anticipated maintenance or upgrades will help buyers make informed decisions about the property and budget for potential future expenses. Openly sharing any known maintenance needs will contribute to a transparent and smooth transaction process.',
                ])
                ->make()
                ->toArray()
        );

        $maintenanceAnswer = $maintenanceStep->answers()->create(
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

        $maintenanceAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $maintenanceDetailAnswer = $maintenanceStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $maintenanceDetailAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $maintenanceDetailAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $maintenanceDetailAnswer->id,
            'answer_id' => $maintenanceAnswer->id,
            'selected_value' => 'Yes',
        ]);

        // Location
        $locationStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please describe the location of the tank and the route of connecting pipes, drains, overflow or soakaway system:',
                    'help_text' => 'It is essential to provide a clear description of the location of the sewage treatment plant and the route of connecting pipes, drains, overflow, or soakaway system to potential buyers. Include specific details about where the tank is situated on the property, such as in the front or back garden, or near any buildings or boundaries. Describe the path of the pipes and drains, whether they run underground or above ground, and indicate any visible access points or inspection covers. Additionally, mention the direction in which the overflow or soakaway system discharges, whether it flows into a watercourse, land, or remains contained within the property`s boundaries. Providing this information will give buyers a better understanding of the system`s layout and functionality, aiding them in making informed decisions about the property.',
                ])
                ->make()
                ->toArray()
        );

        $locationAnswer = $locationStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the tank is located in the rear garden and the pipework runs straight from the back door',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $locationAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Agreements
        $agreementsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are there any agreements covering the repair or maintenance of the system?',
                    'help_text' => 'Each resident is equally responsible for the shared drainage system, unless stated otherwise in your property deeds. That means that each household must take responsibility for regular drainage maintenance, septic tank emptying and any problems with the septic tank.',
                ])
                ->make()
                ->toArray()
        );

        $agreementsAnswer = $agreementsStep->answers()->create(
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

        $agreementsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $agreementsDetailAnswer = $agreementsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $agreementsDetailAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $agreementsDetailAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $agreementsDetailAnswer->id,
            'answer_id' => $agreementsAnswer->id,
            'selected_value' => 'Yes',
        ]);

        // Agreements upload
        $agreementsUploadStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of agreements covering the repair or maintenance of the system:',
                    'help_text' => 'You should check your records or documents related to the property. Look for any contracts or agreements with sewage treatment plant maintenance companies or service providers that outline the terms and conditions of maintenance or repair services. Additionally, you can consult with your property management company if you have one, as they might have information on any existing agreements related to the sewage treatment plant system.',
                ])
                ->make()
                ->toArray()
        );

        $agreementsUploadStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $agreementsUploadStep->id,
            'answer_id' => $agreementsAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $agreementsUploadAnswer = $agreementsUploadStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $agreementsUploadAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Shared maintenance
        $sharedMaintenanceStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'In what proportions are the cost of repair or maintenance shared between the properties?',
                    'sub_heading' => 'Please provide details',
                    'help_text' => 'Each household should pay an equal share of the bill for septic tank maintenance or repairs. It is advised to have a professional agreement between neighbours so that everyone knows where they stand with regards to payment and responsibility, and how much they are paying on a yearly basis. This can prevent financial disputes over the septic tank.',
                ])
                ->make()
                ->toArray()
        );

        $sharedMaintenanceStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $sharedMaintenanceStep->id,
            'answer_id' => $agreementsAnswer->id,
            'selected_value' => 'No',
        ]);

        $sharedMaintenanceAnswer = $sharedMaintenanceStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'placeholder' => 'e.g. the cost for repair and maintenance are split 50/50 with the other property owner that uses the tank',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $sharedMaintenanceAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Contribution problems
        $contributionProblemsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Have there been any problems with collecting contributions or arranging for any works to be done?',
                    'help_text' => 'The seller should advise whether there have been any problems and if so, provide further details.',
                ])
                ->make()
                ->toArray()
        );

        $contributionProblemsAnswer = $contributionProblemsStep->answers()->create(
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

        $contributionProblemsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $contributionProblemsDetailAnswer = $contributionProblemsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the neighbours refuse to pay their share of the contributions towards repair and/or maintenance',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $contributionProblemsDetailAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $contributionProblemsDetailAnswer->id,
            'answer_id' => $contributionProblemsAnswer->id,
            'selected_value' => 'Yes',
        ]);

        // Maintenance disputes
        $maintenanceDisputesStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Have there been any disputes over the use of the system, access onto other land for repair or maintenance?',
                    'help_text' => 'Disputes may involve disagreements between property owners over how the system is used, conflicts regarding access to other properties for maintenance work, or any legal disputes arising from these matters. If you are aware of any such disputes, it is essential to disclose this information. If you are uncertain about the history of any disputes, it may be helpful to review past correspondence, meeting minutes, or records related to the sewage treatment plant system`s use and maintenance.',
                ])
                ->make()
                ->toArray()
        );

        $maintenanceDisputesAnswer = $maintenanceDisputesStep->answers()->create(
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

        $maintenanceDisputesAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $maintenanceDisputesDetailAnswer = $maintenanceDisputesStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the neighbours will not allow us access to repair or maintain the tank',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $maintenanceDisputesDetailAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $maintenanceDisputesDetailAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $maintenanceDisputesDetailAnswer->id,
            'answer_id' => $maintenanceDisputesAnswer->id,
            'selected_value' => 'Yes',
        ]);
    }
}
