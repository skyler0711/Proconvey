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

class Enquiry_DrivewayHardstand extends Seeder
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
                'name' => 'Driveway / Hardstand',
                'description' => 'Information about the driveway on the property',
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
                        $query->where('question', 'Have any of the following changes been made to the whole or any part of the property (including the garden)?');
                    })->first()->id,
                    'selected_value' => 'Driveway/Hardstanding',
                ])
                ->make()
                ->toArray()
        );

        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Drive / Hardstand',
                ])
                ->make()
                ->toArray()
        );

        // Permeable
        $permeableStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the driveway/hardstand use permeable (or porous) surfacing which allows water to drain (such as gravel, permeable concreate block paving or porous asphalt)?',
                    'help_text' => 'A permeable or porous driveway/hardstand refers to a surface that allows rainwater to drain through, reducing surface water runoff and the risk of flooding. Examples of permeable surfaces include gravel, permeable concrete block paving, or porous asphalt. Unlike traditional solid surfaces like concrete or tarmac, permeable surfacing allows water to filter through the ground, which is beneficial for the environment and local drainage systems. If your driveway/hardstand uses one of these permeable surfacing options, it helps to maintain a more sustainable and eco-friendly property.',
                ])
                ->make()
                ->toArray()
        );

        $permeableAnswer = $permeableStep->answers()->create(
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

        $permeableAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Dropped Kerb
        $droppedKerbStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the driveway/hardstand accessed via dropped kerb?',
                    'help_text' => 'A dropped kerb refers to the lowered section of the pavement or roadside that provides access to a driveway or hardstand from the road. If your driveway or hardstand is accessed via a dropped kerb, it means that a designated and safer point of entry and exit has been created, allowing vehicles to smoothly transition from the road to your property\'s driveway/hardstand. This access arrangement is important for ensuring safe and convenient vehicle movement and is often a requirement to comply with local regulations and maintain the integrity of the pavement and roadside.',
                ])
                ->make()
                ->toArray()
        );

        $droppedKerbAnswer = $droppedKerbStep->answers()->create(
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

        $droppedKerbAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Dropped kerb details
        $droppedKerbDetailsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Was the kerb dropped after the construction of the property?',
                    'help_text' => 'Please verify if the dropped kerb was installed after the construction of the property. This information is relevant as it indicates any changes made to the property\'s access points or driveways since its original construction. Knowing the timing of the dropped kerb installation can help in understanding the property\'s history of modifications and potential compliance with local regulations. If the dropped kerb was added after the property\'s construction, it may have required additional permissions and approvals from local authorities.',
                ])
                ->make()
                ->toArray()
        );

        $droppedKerbDetailsStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $droppedKerbDetailsStep->id,
            'answer_id' => $droppedKerbAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $droppedKerbDetailsAnswer = $droppedKerbDetailsStep->answers()->create(
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

        $droppedKerbDetailsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Consent form
        $consentFormStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a Highways Consent form:',
                    'help_text' => 'Highways Consent is a formal permission granted by the local highways authority, allowing property owners to carry out specific alterations or access arrangements that affect the public highway, such as dropping a kerb or making changes to the driveway. If you have made any changes to the driveway or hardstand that required permission from the local highways authority, you should have obtained a Highways Consent form. This form serves as proof that the necessary approvals were granted before carrying out the modifications. If you have not obtained this form yet, you may need to contact your local highways authority to ensure compliance with the regulations and obtain the appropriate consent for any past or future changes',
                ])
                ->make()
                ->toArray()
        );

        $consentFormAnswer = $consentFormStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $consentFormAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $consentFormStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $consentFormStep->id,
            'answer_id' => $droppedKerbDetailsAnswer->id,
            'selected_value' => 'Yes',
        ]);
    }
}
