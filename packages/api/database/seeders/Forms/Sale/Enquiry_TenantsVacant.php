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

class Enquiry_TenantsVacant extends Seeder
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
                'name' => 'Tenants (Vacant)',
                'description' => 'Information about the vacant tenants on the property',
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
                        $query->where('question', 'Is the property being sold with vacant possession?');
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
                    'name' => 'Notice to vacate',
                ])
                ->make()
                ->toArray()
        );

        // Notice
        $noticeStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has notice to vacate the property been given to the tenant(s), under Ground 2 in Schedule 2 in the Housing Act 1988?',
                    'help_text' => 'Ground 2 in Schedule 2 of the Housing Act 1988 is a legal basis for seeking possession of the property. It allows a landlord to serve notice to a tenant when they wish to sell the property, and this notice is a necessary step for obtaining possession in certain circumstances.',
                ])
                ->make()
                ->toArray()
        );

        $noticeAnswer = $noticeStep->answers()->create(
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

        $noticeAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
    }
}
