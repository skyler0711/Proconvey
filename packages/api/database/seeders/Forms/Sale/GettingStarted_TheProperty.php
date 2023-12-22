<?php

namespace Database\Seeders\Forms\Sale;

use App\Enums\AnswerType;
use App\Enums\ConditionType;
use App\Enums\FormGroup;
use App\Enums\FormType;
use App\Enums\PropertyType;
use App\Enums\StepType;
use App\Models\Answer;
use App\Models\Form;
use App\Models\Section;
use App\Models\Step;
use App\Models\ValidationRule;
use Illuminate\Database\Seeder;

class GettingStarted_TheProperty extends Seeder
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
                'name' => 'Getting started: The property',
                'group' => FormGroup::GettingStarted,
                'description' => 'Main information about the sale of your property',
                'order_number' => 1,
                'type' => PropertyType::Sale,
                'ta_form_template' => FormType::GettingStarted,
            ])
            ->create();

        $this->theProperty($form);
        $this->theOwners($form);
        $this->theSale($form);
        $this->theBuyers($form);
    }

    private function theProperty(Form $form)
    {
        // Property Section
        $sectionProperty = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'The Property',
                ])
                ->make()
                ->toArray()
        );

        // Address step
        $sectionProperty->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm the address of the property you are selling as it appears on the Property Title Deeds:',
                    'help_text' => 'Please enter the address of the property you are selling.',
                    'type' => StepType::Address,
                ])
                ->make()
                ->toArray()
        );

        // Property type step
        $sectionProperty->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the property for sale a freehold or leasehold?',
                    'help_text' => '<p><strong>FREEHOLD</strong> - If you own the property and the land it sits on. This is the most common form of ownership. Most UK houses are Freehold.</p><p><strong>LEASEHOLD</strong> - If you own the property but the freeholder owns the land/building. Flats are typically Leasehold.</p>',
                    'type' => StepType::Tenure,
                ])
                ->make()
                ->toArray()
        );
        //End of Property Section

        // Property type step
        $propertyTypeStep = $sectionProperty->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please select the property type below:',
                    'help_text' => 'Please select the type of property you are selling.',
                ])
                ->make()
                ->toArray()
        );

        $propertyTypeAnswer = $propertyTypeStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'House'],
                            ['value' => 'Flat'],
                            ['value' => 'Land'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $propertyTypeAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        //End of Property Type Section

        // Type of house step
        $houseType = $sectionProperty->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please select the type of house below:',
                    'help_text' => '<p>Please select the closest match from the provided options. If you can\'t find your exact house type, no worries! Simply choose "Other" and add your specific type in the text box below.</p>

                    <p><b>Bungalow:</b> A bungalow is a single-story house, typically with a low-pitched roof and a spacious layout. Bungalows are known for their convenient accessibility, as all the rooms are situated on the same level.</p>

                    <p><b>Cottage:</b> A cottage is a small, cosy dwelling, often associated with a rural or picturesque setting. Cottages typically have a charming, rustic aesthetic and are characterized by their compact size, thatched roofs, and traditional architectural features.</p>

                    <p><b>Detached:</b> A detached property refers to a standalone house that is not connected to any other structures. It is surrounded by open space on all sides, offering privacy and independence from neighbouring properties.</p>

                    <p><b>Semi-detached:</b> A semi-detached house is a residential property that shares a common wall with another house. These houses are built in pairs, sharing a central wall, with each house having its own separate entrance and garden.</p>

                    <p><b>Terrace:</b> A terrace, also known as a townhouse or a row house, is a series of attached houses that are joined together in a continuous row. They share side walls with neighbouring properties and typically have multiple floors, allowing for efficient land use in urban areas.</p>

                    <p><b>End of terrace:</b> An end of terrace property is the last house in a row of attached houses. It is situated at the end of the terrace, with only one side attached to another property. This configuration often provides additional windows, allowing for increased natural light and potential for side access to the property.</p>',
                ])
                ->make()
                ->toArray()
        );

        $answerHouseType = $houseType->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Bungalow'],
                            ['value' => 'Cottage'],
                            ['value' => 'Detached'],
                            ['value' => 'Semi-detached'],
                            ['value' => 'Terraced'],
                            ['value' => 'End of terrace'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerHouseType->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $houseType->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $houseType->id,
            'answer_id' => $propertyTypeAnswer->id,
            'selected_value' => 'House',

        ]);
        // End of Type of house step

        // Type of flat step
        $flatType = $sectionProperty->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please select the type of flat below:',
                    'help_text' => '<p>Please select the closest match from the provided options. If you can\'t find your exact flat type, no worries! Simply choose "Other" and add your specific type in the text box below.</p>

                    <p><b>Apartment:</b> An apartment, also known as a flat, is a self-contained residential unit within a larger building or complex. Apartments typically consist of multiple rooms, including bedrooms, a living area, a kitchen, and a bathroom. They offer a compact and efficient living space and are commonly rented or owned by individuals or families.</p>

                    <p><b>Studio:</b> A studio apartment, also called an efficiency apartment or a bachelor apartment, is a small, self-contained living space that combines the living area, bedroom, and kitchenette into a single room. Studio apartments are designed to maximize space and often appeal to individuals or couples looking for a minimalist and affordable housing option.</p>

                    <p><b>Penthouse:</b> A penthouse is a luxurious residential unit typically located on the top floor or multiple top floors of a building. Penthouse apartments are known for their upscale features, spacious layouts, and panoramic views. They often include exclusive amenities, such as private terraces, rooftop access, or dedicated elevators, offering a high-end living experience.</p>

                    <p><b>Maisonette:</b> A maisonette is a residential unit that occupies two or more levels within a larger building. Maisonettes usually have their own private entrance and may be attached to other units or standalone. They provide the feel of a house with the convenience of an apartment, offering separate living and sleeping areas on different floors.</p>',
                ])
                ->make()
                ->toArray()
        );

        $answerFlatType = $flatType->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Apartment'],
                            ['value' => 'Studio'],
                            ['value' => 'Penthouse'],
                            ['value' => 'Maisonette'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerFlatType->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $flatType->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $flatType->id,
            'answer_id' => $propertyTypeAnswer->id,
            'selected_value' => 'Flat',

        ]);
        // End of Type of flat step

        // Type of land step
        $landType = $sectionProperty->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please select the type of land below:',
                    'help_text' => '<p>Please select the closest match from the provided options. If you can\'t find the exact land type, no worries! Simply choose "Other" and add your specific type in the text box below.</p>

                    <p><b>Urban/Built-up Land:</b> Urban or built-up land refers to areas that are developed and characterised by human-made structures, such as residential, commercial, and industrial buildings, roads, and infrastructure.</p>

                    <p><b>Agricultural Land:</b> Agricultural land is used for farming activities and the cultivation of crops or the raising of livestock. It includes fields, pastures, and farmland where agricultural practices are carried out to produce food, fibre, or other agricultural products.</p>

                    <p><b>Rangeland:</b> Rangeland is a type of land predominantly used for grazing livestock, such as cattle, sheep, or horses. It consists of natural grasslands, shrublands, or open range areas where animals can graze on vegetation that naturally occurs in the area.</p>

                    <p><b>Forest:</b> Forest land is characterised by a significant coverage of trees and vegetation. It encompasses wooded areas with diverse tree species, undergrowth, and wildlife. Forests serve various ecological functions, including providing habitat for wildlife, regulating climate, preserving soil integrity, and offering resources like timber, fuelwood, and medicinal plants.</p>',
                ])
                ->make()
                ->toArray()
        );

        $answerLandType = $landType->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Urban/Built-up'],
                            ['value' => 'Agricultural'],
                            ['value' => 'Rangeland'],
                            ['value' => 'Forest'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerLandType->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $landType->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $landType->id,
            'answer_id' => $propertyTypeAnswer->id,
            'selected_value' => 'Land',

        ]);
        // End of Type of land step

        // Use of the property
        $useOfProperty = $sectionProperty->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Current use of the property?',
                    'help_text' => '<p>Please select the option that best describes the current use of the property you are selling.</p>

                    <p><b>Private:</b> The property is used exclusively by the owner or their family members for residential purposes.</p>

                    <p><b>Rental:</b> The property is leased or rented out to tenants for residential purposes, providing housing accommodation.</p>

                    <p><b>Commercial:</b> The property is utilised for business or commercial activities, such as retail, offices, or other commercial ventures.</p>',
                ])
                ->make()
                ->toArray()
        );

        $answerUseOfProperty = $useOfProperty->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Private'],
                            ['value' => 'Rental'],
                            ['value' => 'Commercial'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerUseOfProperty->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
    }

    private function theOwners(Form $form)
    {
        // Owners section
        $sectionOwners = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'The Owners',
                ])
                ->make()
                ->toArray()
        );

        // Owners count step
        $stepOwnersCount = $sectionOwners->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm the number of owners on the Title Deeds:',
                    'help_text' => 'Please make sure you select the number of owners listed on the Title Deeds, even if you are the only one dealing with the sale of the property.',
                ])
                ->make()
                ->toArray()
        );

        $answerOwnersCount = $stepOwnersCount->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => '1'],
                            ['value' => '2'],
                            ['value' => '3'],
                            ['value' => '4'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerOwnersCount->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Owner details step
        $sectionOwners->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the details of the owner(s) as they appear on the Title Deeds:',
                    'help_text' => 'Please enter the current full names of all owners. If any of these names have changed, the other owners will need to provide proof of their name change.',
                    'repeatable_answer_id' => $answerOwnersCount->id,
                    'type' => StepType::OwnerName,
                ])
                ->make()
                ->toArray()
        );
    }

    private function theSale(Form $form)
    {
        // Section
        $sectionSale = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'The Sale',
                ])
                ->make()
                ->toArray()
        );

        // Estate agent step
        $stepEstateAgent = $sectionSale->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are you selling the property through an estate agent?',
                    'help_text' => '<p>An estate agent is a professional who assists individuals in buying, selling, or renting properties. Please indicate your method of selling by selecting one of the following options:</p> 
                    <p>Select YES if you have enlisted the services of an estate agent to handle the sale of your property, select this option.</p>
                    <p>Select NO if you are handling the sale of the property yourself without the involvement of an estate agent, choose this option.</p>',
                ])
                ->make()
                ->toArray()
        );

        $answerEstateAgent = $stepEstateAgent->answers()->create(
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

        $answerEstateAgent->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Estate agent details step
        $stepEstateAgentDetails = $sectionSale->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the details of the estate agent:',
                    'help_text' => 'If you are working with an estate agent for the sale of your property, please provide their details. This will help your conveyancer or solicitor stay in touch with the estate agent and ensure effective communication throughout the selling process.',
                    'type' => StepType::EstateAgent,
                ])
                ->make()
                ->toArray()
        );

        $stepEstateAgentDetails->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $stepEstateAgentDetails->id,
            'answer_id' => $answerEstateAgent->id,
            'selected_value' => 'Yes',
        ]);

        // Property sold step
        $stepPropertySold = $sectionSale->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the property been sold yet?',
                    'help_text' => 'By knowing if the property has been sold or is still available, we can engage with interested parties more effectively and provide them with the right details. It also allows us to communicate promptly and avoid any confusion.',
                ])
                ->make()
                ->toArray()
        );

        $answerPropertySold = $stepPropertySold->answers()->create(
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

        $answerPropertySold->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Property sale price
        $stepSalePrice = $sectionSale->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'What is the agreed sale price of the property?',
                    'help_text' => 'In order to proceed with the necessary documentation, please provide the agreed sale price for the property. If a sale price has been determined and agreed upon, please let us know the exact amount for accurate record-keeping. Your cooperation in sharing this information is crucial for ensuring a professional and streamlined sale process.',
                    'type' => StepType::SalePrice,
                ])
                ->make()
                ->toArray()
        );

        $stepSalePrice->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $stepSalePrice->id,
            'answer_id' => $answerPropertySold->id,
            'selected_value' => 'Yes',
        ]);
    }

    private function theBuyers(Form $form)
    {
        // Section
        $sectionBuyers = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'The Buyers',
                ])
                ->make()
                ->toArray()
        );

        $sellersRelationshipStep = $sectionBuyers->steps()->create([
            'question' => 'Please confirm the seller(s) relationship to the buyer(s):',
            'help_text' => 'This question is important for verifying the relationship between the seller(s) and buyer(s) in the current transaction. By confirming your relationship, we ensure transparency and legitimacy in the process.',
        ]);

        $sellersRelationshipAnswer1 = $sellersRelationshipStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Family'],
                    ['value' => 'Friend'],
                    ['value' => 'None'],
                    ['value' => 'Other'],
                ],
            ],
        ]);

        $sellersRelationshipAnswer1->validationRules()->create([
            'rule' => 'required',
        ]);

        $sellersRelationshipAnswer2 = $sellersRelationshipStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Other Relationship',
            ],
        ]);

        $sellersRelationshipAnswer2->validationRules()->create([
            'rule' => 'required',
        ]);

        $sellersRelationshipAnswer2->conditions()->create([
            'answer_id' => $sellersRelationshipAnswer1->id,
            'selected_value' => 'Other',
        ]);

        // End ---

        // Number of buyers step
        $stepNumberOfBuyers = $sectionBuyers->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm the number of buyers for the property:',
                    'help_text' => 'Please provide a clear confirmation of the number of buyers involved in the purchase of the property. This includes any co-purchasers or additional individuals participating in the transaction. Accurate documentation is crucial for a smooth and successful transaction. To ensure this, it is important that you specify the exact count of buyers.',
                ])
                ->make()
                ->toArray()
        );

        $answerNumberOfBuyers = $stepNumberOfBuyers->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => '1'],
                    ['value' => '2'],
                    ['value' => '3'],
                    ['value' => '4'],
                    ['value' => 'Not known'],
                ],
            ],
        ]);

        $stepNumberOfBuyers->conditions()->create([
            'answer_id' => $sellersRelationshipAnswer1->id,
            'selected_value' => 'Family',
            'type' => ConditionType::OR,
        ]);

        $stepNumberOfBuyers->conditions()->create([
            'answer_id' => $sellersRelationshipAnswer1->id,
            'selected_value' => 'Friend',
            'type' => ConditionType::OR,
        ]);

        // End

        // Buyer details step

        $buyerDetailsStep = $sectionBuyers->steps()->create([
            'type' => StepType::Buyer,
            'question' => 'Please provide details of the buyer(s):',
            'help_text' => 'Please provide the details of the buyer(s) involved in the purchase of the property. Ensuring accurate buyer information is essential for the proper processing of the transaction. Rest assured that all provided information will be handled securely and confidentially.',
            'repeatable_answer_id' => $answerNumberOfBuyers->id,
        ]);

        $buyerDetailsStep->conditions()->create([
            'answer_id' => $answerNumberOfBuyers->id,
            'selected_value' => '1',
            'type' => ConditionType::OR,
        ]);

        $buyerDetailsStep->conditions()->create([
            'answer_id' => $answerNumberOfBuyers->id,
            'selected_value' => '2',
            'type' => ConditionType::OR,
        ]);

        $buyerDetailsStep->conditions()->create([
            'answer_id' => $answerNumberOfBuyers->id,
            'selected_value' => '3',
            'type' => ConditionType::OR,
        ]);

        $buyerDetailsStep->conditions()->create([
            'answer_id' => $answerNumberOfBuyers->id,
            'selected_value' => '4',
            'type' => ConditionType::OR,
        ]);

        $buyerDetailsStep->conditions()->create([
            'answer_id' => $answerNumberOfBuyers->id,
            'selected_value' => 'Not known',
            'type' => ConditionType::OR,
        ]);

        // End ---

        // Buyer Conveyancer step
        $buyerConveyancerStep = $sectionBuyers->steps()->create([
            'question' => "Please provide details of the buyer's conveyancer:",
            'help_text' => 'To facilitate a seamless transaction, it is important that you provide the details of the buyer\'s conveyancing firm. Your conveyancer or solicitor will need to be in contact with the buyer\'s conveyancer or solicitor throughout the transaction.',
            'type' => StepType::BuyersSolicitor,
        ]);

        $this->theSendingMoney($form, $sectionBuyers);
    }

    private function theSendingMoney(Form $form)
    {
        $sectionSendingMoney = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Sending Money To You',
                ])
                ->make()
                ->toArray()
        );

        // Sellers bank account details step
        $sellersBankDetailsStep = $sectionSendingMoney->steps()->create([
            'question' => "Please provide the sellers' bank account details:",
            'help_text' => 'To ensure a smooth and secure transaction process, we kindly request you to provide the sellers\' bank account details. This information is required as a law firm may need to send or return money to you throughout the transaction, such as deposit funds or the proceeds from the sale. Please note that the provided bank account details will be treated with the utmost confidentiality and will only be used for the purposes of the transaction.',
        ]);

        $answerSellersAccountName = $sellersBankDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Account name',
            ],
        ]);

        $answerSellersAccountName->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerSellersSortCode = $sellersBankDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Sort code',
            ],
        ]);

        $answerSellersSortCode->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerSellersAccountNumber = $sellersBankDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Account number',
            ],
        ]);

        $answerSellersAccountNumber->validationRules()->create([
            'rule' => 'required',
        ]);
    }
}
