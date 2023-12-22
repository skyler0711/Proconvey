<?php

namespace Database\Seeders\Forms\Purchase;

use App\Enums\AnswerType;
use App\Enums\FormGroup;
use App\Enums\FormType;
use App\Enums\PropertyType;
use App\Models\Form;
use Illuminate\Database\Seeder;

class Form_Giftor_Details extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $form = Form::factory()
            ->state([
                'name' => 'Giftor Details',
                'group' => FormGroup::GettingStarted,
                'description' => 'This section aims to gather giftor information relating to the purchase.',
                'order_number' => 4,
                'type' => PropertyType::Purchase,
                'ta_form_template' => FormType::Giftor,
            ])
            ->create();

        // 1.0 Giftor details
        $giftorSection = $form->sections()->create([
            'name' => 'Giftor Details',
        ]);

        // 1.1 Giftor details
        $giftorDetailsStep = $giftorSection->steps()->create([
            'question' => 'Please enter your details:',
        ]);

        // Title
        $title = $giftorDetailsStep->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Title',
                'options' => [
                    ['value' => 'Mr'],
                    ['value' => 'Mrs'],
                    ['value' => 'Miss'],
                    ['value' => 'Ms'],
                ],
            ],
        ]);

        $title->validationRules()->create([
            'rule' => 'required',
        ]);

        // First name
        $firstName = $giftorDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'First name',
            ],
        ]);

        $firstName->validationRules()->create([
            'rule' => 'required',
        ]);

        // Middle name (optional)
        $giftorDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Middle name(s)',
            ],
        ]);

        // Surname
        $surname = $giftorDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Surname',
            ],
        ]);

        $surname->validationRules()->create([
            'rule' => 'required',
        ]);

        // Contact number
        $contactNumber = $giftorDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Main contact number',
            ],
        ]);

        $contactNumber->validationRules()->create([
            'rule' => 'required',
        ]);

        // Address
        $address = $giftorDetailsStep->answers()->create([
            'type' => AnswerType::Address,
            'details' => [
                'label' => 'Correspondence address',
            ],
        ]);

        $address->validationRules()->create([
            'rule' => 'required',
        ]);

        // Email
        $email = $giftorDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Email',
            ],
        ]);

        $email->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 1.1 Giftor amount
        $giftorAmountStep = $giftorSection->steps()->create([
            'question' => 'Please enter the amount of the gift:',
        ]);

        // Amount
        $amount = $giftorAmountStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Amount',
            ],
        ]);

        $amount->validationRules()->create([
            'rule' => 'required',
        ]);
        // End
    }
}
