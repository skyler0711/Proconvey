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

class Enquiry_Porch extends Seeder
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
                'name' => 'Porch',
                'description' => 'Information about the porch on the property',
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
                    'selected_value' => 'Porch',
                ])
                ->make()
                ->toArray()
        );

        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Porch',
                ])
                ->make()
                ->toArray()
        );

        // Area
        $areaStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the ground floor area (measured externally) exceed 3 square metres?',
                    'help_text' => 'Adding a porch to any external door of your house is considered to be permitted development, not requiring an application for planning permission, provided that the ground floor area (measured externally) would not exceed three square metres.',
                ])
                ->make()
                ->toArray()
        );

        $areaAnswer = $areaStep->answers()->create(
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

        $areaAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Height
        $heightStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is any part of the porch more than 3 meters above ground level?',
                    'help_text' => 'The highest part of the porch should not measure more than three metres up from ground level. If this is not the case, you will need to provide planning permissions and building regulations.',
                ])
                ->make()
                ->toArray()
        );

        $heightAnswer = $heightStep->answers()->create(
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

        $heightAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $heightMoreDetailsAnswer = $heightStep->answers()->create(
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

        $heightMoreDetailsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $heightMoreDetailsAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $heightMoreDetailsAnswer->id,
            'answer_id' => $heightAnswer->id,
            'selected_value' => 'Yes',
        ]);

        // Boundary
        $boundaryStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is any part of the porch within 2 meters of any boundary of the property?',
                    'help_text' => 'No part of the porch should be within two metres of any boundary of the property and the highway. If this is not the case, you will need to provide planning permissions and building regulations.',
                ])
                ->make()
                ->toArray()
        );

        $boundaryAnswer = $boundaryStep->answers()->create(
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

        $boundaryAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Public boundary
        $publicBoundaryStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is any part of the porch within 2 meters of any public footpath or road?',
                    'help_text' => 'No part of the porch should be within two metres of any boundary of the property and the highway. If this is not the case, you will need to provide planning permissions and building regulations.',
                ])
                ->make()
                ->toArray()
        );

        $publicBoundaryAnswer = $publicBoundaryStep->answers()->create(
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

        $publicBoundaryAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $publicBoundaryMoreDetailsAnswer = $publicBoundaryStep->answers()->create(
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

        $publicBoundaryMoreDetailsAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $publicBoundaryMoreDetailsAnswer->id,
            'answer_id' => $publicBoundaryAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $publicBoundaryMoreDetailsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Entrance door
        $entranceDoorStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the front entrance door between the existing house and the porch remain in place?',
                    'help_text' => 'The front entrance door between the existing house and the new porch must remain in place or be replaced with a new door. If the house has ramped or level access for disabled people, the porch must not adversely affect access.',
                ])
                ->make()
                ->toArray()
        );

        $entranceDoorAnswer = $entranceDoorStep->answers()->create(
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

        $entranceDoorAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $entranceDoorMoreDetailsAnswer = $entranceDoorStep->answers()->create(
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

        $entranceDoorMoreDetailsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $entranceDoorMoreDetailsAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $entranceDoorMoreDetailsAnswer->id,
            'answer_id' => $entranceDoorAnswer->id,
            'selected_value' => 'No',
        ]);
    }
}
