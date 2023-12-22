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

class Enquiry_SolarPanelsOwned extends Seeder
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
                'name' => 'Solar Panels (Owned)',
                'description' => 'Information about the owned solar panels on the property',
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
                        $query->where('question', 'Are the solar panels owned outright?');
                    })->first()->id,
                    'selected_value' => 'Yes',
                ])
                ->make()
                ->toArray()
        );

        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Solar panels',
                ])
                ->make()
                ->toArray()
        );

        // MCS Certificate
        $mcsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide the Microgeneration Certificate Scheme (MCS) certificate:',
                    'help_text' => 'An MCS certificate is proof that your installation has been designed, installed and commissioned to the highest standard using only MCS certified products by an MCS certified installer. Please note that an MCS certificate is not a mandatory or legal requirement for system installation.',
                ])
                ->make()
                ->toArray()
        );

        $mcsAnswer = $mcsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $mcsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Planning
        $planningStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide the planning permission for the installation:',
                    'help_text' => 'If you are not sure whether you need planning permissions or building regulations for your wall removal please click here. ',
                ])
                ->make()
                ->toArray()
        );

        $planningAnswer = $planningStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $planningAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Highest
        $highestStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are the panels installed above the highest part of the roof (excluding the chimney)?',
                    'help_text' => 'Panels can&quot;t be installed above the highest part of the roof (excluding the chimney). If the answer is &quot;Yes&quot; it is likely that the buyer&quot;s solicitor will request further information in this regard.',
                ])
                ->make()
                ->toArray()
        );

        $highestAnswer = $highestStep->answers()->create(
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

        $highestAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Projection
        $projectionStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Do the panels project more than 200mm from the roof slope or wall surface?',
                    'help_text' => 'Panels can&quot;t stick out more than 20 cm from the roof (although it would be pretty unusual if they did). If the answer is &quot;Yes&quot; it is likely that the buyer&quot;s solicitor will request further information in this regard.',
                ])
                ->make()
                ->toArray()
        );

        $projectionAnswer = $projectionStep->answers()->create(
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

        $projectionAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Problems
        $problemsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Have any problems been experienced in respect of the PV system?',
                    'help_text' => 'For the most part, solar panels are very low maintenance and can be left to generate free renewable energy for your home. However, from time to time, you might encounter some solar PV problems such as inverters that need replacing, hot spots, low efficiency rating, birds nesting under the panels etc.',
                ])
                ->make()
                ->toArray()
        );

        $problemsAnswer = $problemsStep->answers()->create(
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

        $problemsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $problemsDetailAnswer = $problemsStep->answers()->create(
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

        $problemsDetailAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $problemsDetailAnswer->id,
            'answer_id' => $problemsAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $problemsDetailAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
    }
}
