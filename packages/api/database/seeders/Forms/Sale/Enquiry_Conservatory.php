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

class Enquiry_Conservatory extends Seeder
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
                'name' => 'Conservatory',
                'description' => 'Information about the conservatory on the property',
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
                    'selected_value' => 'Conservatory',
                ])
                ->make()
                ->toArray()
        );

        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Conservatory',
                ])
                ->make()
                ->toArray()
        );

        // Fronting property
        $frontingPropertyStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the conservatory on any wall of the property such that it fronts the public footpath or road?',
                    'help_text' => 'If you have a conservatory on the front or side of your home, then it cannot be closer to a public highway (basically a footpath or road) than your home is.',
                ])
                ->make()
                ->toArray()
        );

        $frontingPropertyAnswer = $frontingPropertyStep->answers()->create(
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

        $frontingPropertyAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Ground level
        $groundLevelStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the conservatory only on the ground level of the property?',
                    'help_text' => 'A conservatory needs the same foundations as any building work so it must be built at ground level.',
                ])
                ->make()
                ->toArray()
        );

        $groundLevelAnswer = $groundLevelStep->answers()->create(
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

        $groundLevelAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Size
        $sizeStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the size of the conservatory less than 15% of the original size of the property?',
                    'help_text' => 'Conservatories can, but not always, be installed without planning permission if it takes 15% or less of the un-extended house volume on a detached / semi-detached house up or 10% on a terraced house.',
                ])
                ->make()
                ->toArray()
        );

        $sizeAnswer = $sizeStep->answers()->create(
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

        $sizeAnswer->validationRules()->create(
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
                    'question' => 'Is the conservatory less than 70 cubic meters in total?',
                    'help_text' => 'Conservatories can, but not always, be installed without planning permission if it takes up 70 cubic metres or less of the property\'s volume.',
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
                    'question' => 'Is the overall height of the conservatory less than 4 meters?',
                    'help_text' => 'Typically, a conservatory cannot be taller than the roof of the home, but it also cannot be taller than four metres.',
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

        // Boundary
        $boundaryStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the conservatory within 1 meter of any boundary of your property?',
                    'help_text' => 'Some conservatories cannot be installed within 1 metre of any boundaries of the property.',
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

        // Floor area
        $floorAreaStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the floor area of the conservatory less than 30 square meters?',
                    'help_text' => 'Conservatories are normally, but not always, exempt from building regulations when they are built at ground level and are less than 30 square metres in floor area.',
                ])
                ->make()
                ->toArray()
        );

        $floorAreaAnswer = $floorAreaStep->answers()->create(
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

        $floorAreaAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Glazing
        $glazingStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the roof og the conservatory glazed with translucent or transparent materials?',
                    'help_text' => 'Building Regulations were changed back in 2010 to remove the guidance on the definition of a conservatory. After this, there is no definition for the amount of glazing that is required for the structure to be classed as a conservatory. This paved the way for tiled conservatory roofs to be retrofitted to many of the existing structures. However, some local councils still appear to be operating under the idea that 75% of a conservatory&quot;s roof needs to be glazed or it will become subject to Building Regulations.',
                ])
                ->make()
                ->toArray()
        );

        $glazingAnswer = $glazingStep->answers()->create(
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

        $glazingAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Safety glass
        $safetyGlassStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the conservatory glazed with safety glass at low level? (i.e. from the floor to 800mm in any wall and up to 1500mm in any door)',
                    'help_text' => 'Irrespective of whether your conservatory is exempt, glazing in some of the windows and doors requires them to be either strengthened or laminated safety glass so that they meet British Standard BSEN12600. Normally, the areas, which need safety glass, are doors and side panels, also where the glass in windows is within 800mm of floor level.',
                ])
                ->make()
                ->toArray()
        );

        $safetyGlassAnswer = $safetyGlassStep->answers()->create(
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

        $safetyGlassAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Seperated
        $seperatedStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the conservatory permanently separated from the rest of the property by means of a door?',
                    'help_text' => 'Some conservatories are exempt from most parts of Building Regulations provided they keep the external grade separating doors.',
                ])
                ->make()
                ->toArray()
        );

        $seperatedAnswer = $seperatedStep->answers()->create(
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

        $seperatedAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
    }
}
