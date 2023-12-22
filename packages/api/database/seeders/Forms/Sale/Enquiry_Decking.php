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

class Enquiry_Decking extends Seeder
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
                'name' => 'Decking',
                'description' => 'Information about the decking on the property',
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
                    'selected_value' => 'Decking',
                ])
                ->make()
                ->toArray()
        );

        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Decking',
                ])
                ->make()
                ->toArray()
        );

        // Height
        $heightStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the decking less than 30cm off the ground?',
                    'help_text' => 'Your decking may require building regulations if it is installed more than 30cm off the ground.',
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

        // Area
        $areaStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the decking cover more than 50% of the garden area (together with other extensions, outbuildings etc.)?',
                    'help_text' => 'If the decking structure, together with other outdoor buildings, sheds and extensions, covers an area, which is larger than 50% of the total garden space, it may require building regulations.',
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

        // Front of property
        $frontStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the decking on land that forms the front of the property?',
                    'help_text' => 'None of the decking or platform is on land forward of a wall forming the principal elevation (that means not in front of the house).',
                ])
                ->make()
                ->toArray()
        );

        $frontAnswer = $frontStep->answers()->create(
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

        $frontAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
    }
}
