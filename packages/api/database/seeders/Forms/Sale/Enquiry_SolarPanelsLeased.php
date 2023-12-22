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

class Enquiry_SolarPanelsLeased extends Seeder
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
                'name' => 'Solar Panels (Leased)',
                'description' => 'Information about the leased solar panels on the property',
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
                    'selected_value' => 'No',
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
                    'help_text' => 'The Microgeneration Certificate Scheme (MCS) is a quality assurance program that certifies the installation of renewable energy systems in residential and commercial properties. It ensures that these systems meet specific standards and criteria for performance, safety, and environmental impact. The MCS certificate serves as evidence that the renewable energy system has been installed correctly and is eligible for incentives and support schemes. If you cannot find the certificate, you can contact the installer or supplier to obtain a copy. Please note that an MCS certificate is not a mandatory or legal requirement for system installation.',
                ])
                ->make()
                ->toArray()
        );

        $uploadMcsAnswer = $mcsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $uploadMcsAnswer->validationRules()->create(
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
                    'help_text' => 'For solar panels, the planning permissions may not always be required, especially if the installation falls under permitted development rights. However, you may need a Microgeneration Certificate Scheme (MCS) certificate to demonstrate that the solar panels meet the necessary standards and qualify for certain incentives, such as Feed-in Tariffs.',
                ])
                ->make()
                ->toArray()
        );

        $uploadPlanningAnswer = $planningStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $uploadPlanningAnswer->validationRules()->create(
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
                    'help_text' => 'The solar panels should not be positioned at a height that exceeds the highest point of the roof itself (excluding any chimneys on the property). If the panels are installed higher than the roof\'s peak, it may have implications for planning regulations and could potentially require additional permissions or alterations to meet the appropriate standards. It is essential to ensure that the solar panel installation complies with the guidelines and regulations set forth by the local authorities to avoid any issues in the future.',
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
                    'help_text' => 'This measurement is taken from the point where the panels are attached to the roof or wall to the furthest outward point of the panels. If the panels extend more than 200mm from the surface, it may have implications for planning permissions and could require additional assessments to ensure compliance with local regulations. It\'s important to verify that the solar panel installation adheres to the appropriate guidelines and standards to avoid any potential issues with the authorities.',
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

        // Renewable benefits
        $renewableStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm the amount of the renewable benefits payment received by the seller under the lease:',
                    'help_text' => 'The renewable benefits payment refers to any financial incentives or payments received by the seller for generating renewable energy through their solar panel installation. These benefits often include Feed-in Tariffs (FiTs) or other similar schemes that allow homeowners to receive payments for the excess electricity they generate and feed back into the grid. You are required to confirm the amount of these payments received during the lease period. Disclosing this information is important for potential buyers to understand the financial benefits associated with the property\'s renewable energy system and its potential impact on their future utility costs.',
                ])
                ->make()
                ->toArray()
        );

        $renewableAnswer = $renewableStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Rent price',
                        'placeholder' => 'e.g. £3500',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $renewableAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $renewableNotApplicableAnswer = $renewableStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Checkbox,
                    'details' => [
                        'label' => 'Not applicable',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $renewableAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $renewableAnswer->id,
            'answer_id' => $renewableNotApplicableAnswer->id,
            'selected_value' => '0',
        ]);

        // Benefits dates
        $datesStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm when these are paid',
                    'help_text' => 'Please confirm the frequency and timing of when you have received the renewable benefits payment under the lease for your solar panel installation. This information will help potential buyers understand the payment schedule and how it aligns with their financial planning and utility cost considerations. Knowing when these payments are made is crucial for buyers who are interested in the property\'s renewable energy system and its potential financial benefits.',
                ])
                ->make()
                ->toArray()
        );

        $datesStep->conditions()->create([
            'answer_id' => $renewableNotApplicableAnswer->id,
            'selected_value' => '0',
        ]);

        $datesAnswer = $datesStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Dates',
                        'placeholder' => 'September 1st - Yearly',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $datesAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $dateAnswerNotApplicableAnswer = $datesStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Checkbox,
                    'details' => [
                        'label' => 'Not applicable',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $datesAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $datesAnswer->id,
            'answer_id' => $dateAnswerNotApplicableAnswer->id,
            'selected_value' => '0',
        ]);

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
            'answer_id' => $renewableNotApplicableAnswer->id,
            'selected_value' => '0',
        ]);

        $problemsDetailAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Provider
        $providerStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide the contact details for the solar panels\' provider:',
                    'help_text' => 'Your solar panels&quot; provider is the company you pay your electricity bill to.',
                ])
                ->make()
                ->toArray()
        );

        $providerNotKnownAnswer = $providerStep->answers()->create(
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

        $providerAnswer = $providerStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Name of Provider',
                        'placeholder' => 'Enter name of provider',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $providerAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $providerAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $providerAnswer->id,
            'answer_id' => $providerNotKnownAnswer->id,
            'selected_value' => '0',
        ]);

        $providerPhoneAnswer = $providerStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Phone number',
                        'placeholder' => '+44 ---- -- -- --',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $providerPhoneAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $providerPhoneAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $providerPhoneAnswer->id,
            'answer_id' => $providerNotKnownAnswer->id,
            'selected_value' => '0',
        ]);

        $providerAddressAnswer = $providerStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Address,
                    'details' => [
                        'label' => 'Address',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $providerAddressAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $providerAddressAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $providerAddressAnswer->id,
            'answer_id' => $providerNotKnownAnswer->id,
            'selected_value' => '0',
        ]);

        // Feed in tariff agreement
        $feedInTarrifAgreementStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the Feed In Tariff Agreement from the provider:',
                    'help_text' => 'A feed-in tariff pays you for excess electricity generated by your solar PV system, and not used in your home. It is designed to encourage investment in renewable energy, feed-in tariff rates vary, but they can help reduce your energy bill.',
                ])
                ->make()
                ->toArray()
        );

        $feedInTarrifAgreementAnswer = $feedInTarrifAgreementStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $feedInTarrifAgreementAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Change of ownership
        $changeOfOwnershipStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the Change of Ownership form from the provider:',
                    'help_text' => 'If your solar panels were purchased before you bought your home, you should have received a a form for the transfer of ownership signed by the previous owner and you will be required to do the same for the buyer. You will need to contact you solar lease company.',
                ])
                ->make()
                ->toArray()
        );

        $changeOfOwnershipAnswer = $changeOfOwnershipStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $changeOfOwnershipAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Insurance
        $insuranceStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the insurance in respect of the PV system:',
                    'help_text' => 'Solar PV system insurances typically cover the cost of replacing the faulty or damaged panel with a new working one. Most equipment warranties of solar panels last between 10-15 years but some premium panels have warranties for upto 25 years. ',
                ])
                ->make()
                ->toArray()
        );

        $insuranceAnswer = $insuranceStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $insuranceAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
    }
}
