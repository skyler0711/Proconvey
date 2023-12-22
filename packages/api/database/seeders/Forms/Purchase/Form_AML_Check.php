<?php

namespace Database\Seeders\Forms\Purchase;

use App\Enums\AnswerType;
use App\Enums\FormGroup;
use App\Enums\PropertyType;
use App\Models\Form;
use Illuminate\Database\Seeder;

class Form_AML_Check extends Seeder
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
                'name' => 'Anti Money Laundering Details',
                'group' => FormGroup::GettingStarted,
                'description' => 'This section aims to gather AML information relating to the purchase.',
                'order_number' => 5,
                'type' => PropertyType::Purchase,
                'order_number' => 13,
            ])
            ->create();

        // 1.0 Giftor details
        $antiMoneyLaunderingSection = $form->sections()->create([
            'name' => 'Anti Money Laundering',
        ]);

        $detailProvisionStep = $antiMoneyLaunderingSection->steps()->create([
            'question' => 'Please provide the last 3 months bank statements for {{BUYER FULL NAME}}. Please be sure to provide any bank statements where the money for the purchase is being held.',
            'help_text' => 'Please enter the address of the property you are buying.',
        ]);

        $detailProvisionStep->answers()->create([
            'type' => AnswerType::File,
        ]);
    }
}
