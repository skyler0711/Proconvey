<?php

namespace Database\Seeders\Forms\Sale;

use App\Enums\AnswerType;
use App\Enums\ConditionType;
use App\Enums\FormGroup;
use App\Enums\FormType;
use App\Enums\OverviewPdfField;
use App\Enums\PropertyType;
use App\Enums\StepType;
use App\Models\Answer;
use App\Models\Form;
use App\Models\Section;
use App\Models\Step;
use App\Models\ValidationRule;
use Illuminate\Database\Seeder;

class GettingStarted_MortgagesChargesLoans extends Seeder
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
                'name' => 'Getting started: Mortgages and related transactions',
                'group' => FormGroup::GettingStarted,
                'description' => 'Mortgages or charges secured against the property',
                'order_number' => 4,
                'type' => PropertyType::Sale,
                'ta_form_template' => FormType::GettingStartedMortgages,
            ])
            ->create();

        $this->mortgagesChargesLoans($form);
        $this->relatedTransactions($form);
    }

    protected function mortgagesChargesLoans(Form $form)
    {
        // Mortgages, Charges or Loans section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Mortgages, Charges or Loans',
                ])
                ->make()
                ->toArray()
        );

        // Any mortgages step
        $anyMortgages = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are there any mortgages or charges secured against the property?',
                    'help_text' => 'Your solicitor will repay all mortgages/charges over the property on the day of the sale. Payment will be made using the sale proceeds.',
                ])
                ->make()
                ->toArray()
        );

        $answerAnyMortgages = $anyMortgages->answers()->create(
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

        $answerAnyMortgages->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Number of mortgages step
        $noOfMortgagesStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'How many mortgages, charges or loans are secured against the property?',
                    'help_text' => 'In most cases there will only be one mortgage, charge or loan over the property.',
                ])
                ->make()
                ->toArray()
        );

        $noOfMortgagesStep->conditions()->create([
            'answer_id' => $answerAnyMortgages->id,
            'selected_value' => 'Yes',
        ]);

        $noOfMortgagesAnswer = $noOfMortgagesStep->answers()->create(
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
                            ['value' => '6'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $noOfMortgagesAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Mortgage details step
        $mortgageDetailsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide details of each mortgage, charge or loan:',
                    'help_text' => 'Please provide further details of each mortgage, charge or loan. Your conveyancer/solicitor will use this information to contact the beneficiary of the mortgage, charge or loan to determine the final redemption figure to be paid.',
                    'repeatable_answer_id' => $noOfMortgagesAnswer->id,
                    'type' => StepType::Charges,
                ])
                ->make()
                ->toArray()
        );

        $mortgageDetailsStep->conditions()->create([
            'answer_id' => $noOfMortgagesAnswer->id,
            'selected_value' => '1',
            'type' => ConditionType::OR,
        ]);

        $mortgageDetailsStep->conditions()->create([
            'answer_id' => $noOfMortgagesAnswer->id,
            'selected_value' => '2',
            'type' => ConditionType::OR,
        ]);

        $mortgageDetailsStep->conditions()->create([
            'answer_id' => $noOfMortgagesAnswer->id,
            'selected_value' => '3',
            'type' => ConditionType::OR,
        ]);

        $mortgageDetailsStep->conditions()->create([
            'answer_id' => $noOfMortgagesAnswer->id,
            'selected_value' => '4',
            'type' => ConditionType::OR,
        ]);

        $mortgageDetailsStep->conditions()->create([
            'answer_id' => $noOfMortgagesAnswer->id,
            'selected_value' => '5',
            'type' => ConditionType::OR,
        ]);

        $mortgageDetailsStep->conditions()->create([
            'answer_id' => $noOfMortgagesAnswer->id,
            'selected_value' => '6',
            'type' => ConditionType::OR,
        ]);
    }

    protected function relatedTransactions(Form $form)
    {
        // Related transactions section
        $relatedTransactionsSection = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Related transactions',
                ])
                ->make()
                ->toArray()
        );

        // Affect sale step
        $affectSaleStep = $relatedTransactionsSection->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are any of the owners buying a property which could affect the sale?',
                    'help_text' => 'It is useful for your conveyancer/solicitor to know if you are buying your next house while you sell your current one.',
                ])
                ->make()
                ->toArray()
        );

        $affectSaleAnswer = $affectSaleStep->answers()->create(
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

        $affectSaleAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $affectedSaleAddressStep = $relatedTransactionsSection->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the address of each property purchase that relates to the sale:',
                    'help_text' => 'Please enter the address of the property you are buying.',
                    'type' => StepType::MortgageRelatedTransactions,
                ])
                ->make()
                ->toArray()
        );

        $affectedSaleAddressStep->conditions()->create([
            'answer_id' => $affectSaleAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $affectedSaleOwnerAnswer = $affectedSaleAddressStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::PersonMultiSelect,
                    'details' => [
                        'label' => 'Related Transaction',
                        'placeholder' => 'Select owner(s)',
                        'pdfFormFieldName' => OverviewPdfField::SellerRelatedTransactions,
                    ],
                ])
                ->make()
                ->attributesToArray()
        );

        $affectedSaleOwnerAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $affectedSaleAddressAnswer = $affectedSaleAddressStep->answers()->create([
            'type' => AnswerType::Address,
            'details' => [
                'label' => 'Address',
                'pdfFormFieldName' => OverviewPdfField::SellerRelatedTransactionAddresses,
            ],
        ]);

        $affectedSaleAddressAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
    }
}
