<?php

namespace Database\Seeders\Forms\Purchase;

use App\Enums\AnswerType;
use App\Enums\ConditionType;
use App\Enums\FormGroup;
use App\Enums\FormType;
use App\Enums\OverviewPdfField;
use App\Enums\PropertyType;
use App\Enums\StepType;
use App\Models\Form;
use Illuminate\Database\Seeder;

class GettingStarted_TheProperty extends Seeder
{
    private $globalBuyerCapacityAnswer;

    private $globalNumberOfBuyersAnswer;

    private $globalConfirmedSellersAnswer;

    private $globalPurchaseThroughEstateAgentAnswer;

    private $globalConveyancingFirmNameAnswer;

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
                'description' => 'This section aims to gather initial information relating to the purchase.',
                'order_number' => 1,
                'type' => PropertyType::Purchase,
                'ta_form_template' => FormType::GettingStarted,
            ])
            ->create();

        $this->theProperty($form);
        $this->theBuyers($form);
        $this->theOwnership($form);
        $this->thePurchase($form);
        $this->theSellers($form);
        $this->thePurchaseFunds($form);
        $this->theSendingMoneyToYou($form);
        $this->theStampDuty($form);
    }

    private function theProperty(Form $form)
    {
        // Property Section
        $propertySection = $form->sections()->create([
            'name' => 'The Property',
        ]);

        // 1.1 Address step
        $propertySection->steps()->create([
            'question' => 'Please confirm the address of the property you are buying:',
            'help_text' => 'Please confirm the address of the property you are purchasing as it appears on the Property Title Deeds. This information is crucial to ensure that the property identified in the title deeds matches the one you are intending to buy. Verify the address carefully, and if there are any discrepancies or uncertainties, consult with your solicitor or conveyancer for clarification and guidance. Having the correct address is vital for a smooth and accurate property transaction.',
            'type' => StepType::Address,
        ]);

        // 1.2 Start of property type step
        $propertyTypeStep = $propertySection->steps()->create([
            'question' => 'Please select the property type:',
            'help_text' => '<p><strong>FREEHOLD</strong> - You own the property and the land it sits on. This is the most common form of ownership. Most UK properties are Freehold.</p><p><strong>LEASEHOLD</strong> - You own the property for a fixed period of time but a freeholder owns the land/building. Flats are typically Leasehold.</p><p><strong>COMMONHOLD</strong> - You own the freehold of individual flats, houses and non-residential units in a building or land. It is an alternative to Leasehold as commonhold owners own the property with no time limit.</p><p><strong>SHARED BUYERSHIP</strong> - You own a share of the property and pay a mortgage that share. You also pay rent to a landlord on the remaining share.</p>',
            'type' => StepType::Tenure,
        ]);

        // 1.2a Buyer Percentage
        $buyerPercentageStep = $propertySection->steps()->create([
            'question' => 'Please enter the percentage of the property the Buyers are purchasing:',
            'help_text' => 'TPlease enter the percentage of the property you are purchasing. This refers to the share or proportion of ownership you will have in the property. For example, if you are buying the entire property, you would enter 100%. If you are purchasing only a portion of the property, such as in shared ownership, you would enter the specific percentage you are buying (e.g., 25%, 50%, etc.). Be sure to provide the accurate percentage to reflect your ownership share in the property.',
        ]);

        $buyerPercentageStep->conditions()->create([
            'answer_id' => $propertyTypeStep->answers->first()->id,
            'selected_value' => 'Shared ownership',
        ]);

        $buyerPercentageStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Shared ownership percentage',
                'pdfFormFieldName' => OverviewPdfField::SharedOwnershipPercentage,
            ],
        ]);
        // End ---

        // 1.3 Start of property type
        $propertyTypeStep2 = $propertySection->steps()->create([
            'question' => 'Please select the property type:',
            'help_text' => '<p>Please select the type of property you are purchasing:</p><p><strong>House:</strong>A standalone residential property constructed on its own land.</p><p><strong>Flat/Apartment:</strong>Self-contained residential units within a larger building, often sharing common areas such as hallways and entrances.</p><p><strong>Land/Plot:</strong>Undeveloped land or a vacant plot, providing potential buyers with the opportunity to build their own home or explore development possibilities.</p>',
        ]);

        $propertyTypeAnswer2 = $propertyTypeStep2->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'House'],
                    ['value' => 'Flat'],
                    ['value' => 'Land'],
                ],
                'pdfFormFieldName' => OverviewPdfField::PropertyType,
            ],
        ]);

        $propertyTypeAnswer2->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 1.3a Start of house type
        $houseTypeStep = $propertySection->steps()->create([
            'question' => 'Please select the type of house:',
            'help_text' => '<p>Please select the closest match from the provided options. If you can\'t find your exact house type, no worries! Simply choose "Other" and add your specific type in the text box below.</p><p><strong>Bungalow:</strong> A bungalow is a single-story house, typically with a low-pitched roof and a spacious layout. Bungalows are known for their convenient accessibility, as all the rooms are situated on the same level.</p><p><strong>Cottage:</strong> A cottage is a small, cosy dwelling, often associated with a rural or picturesque setting. Cottages typically have a charming, rustic aesthetic and are characterized by their compact size, thatched roofs, and traditional architectural features.</p><p><strong>Detached:</strong> A detached property refers to a standalone house that is not connected to any other structures. It is surrounded by open space on all sides, offering privacy and independence from neighbouring properties.</p><p><strong>Semi-detached:</strong> A semi-detached house is a residential property that shares a common wall with another house. These houses are built in pairs, sharing a central wall, with each house having its own separate entrance and garden.</p><p><strong>Terrace:</strong> A terrace, also known as a townhouse or a row house, is a series of attached houses that are joined together in a continuous row. They share side walls with neighbouring properties and typically have multiple floors, allowing for efficient land use in urban areas.</p><p><strong>End of terrace:</strong> An end of terrace property is the last house in a row of attached houses. It is situated at the end of the terrace, with only one side attached to another property. This configuration often provides additional windows, allowing for increased natural light and potential for side access to the property.</p>',
        ]);

        $houseTypeStep->conditions()->create([
            'answer_id' => $propertyTypeAnswer2->id,
            'selected_value' => 'House',
        ]);

        $houseTypeAnswer = $houseTypeStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Bungalow'],
                    ['value' => 'Cottage'],
                    ['value' => 'Detached'],
                    ['value' => 'Semi-detached'],
                    ['value' => 'Terrace'],
                    ['value' => 'End of terrace'],
                    ['value' => 'Other'],
                ],
                'pdfFormFieldName' => OverviewPdfField::PropertySubType,
            ],
        ]);

        $houseTypeAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $houseSetOtherAnswer = $houseTypeStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'pdfFormFieldName' => OverviewPdfField::PropertySubType,
            ],
        ]);

        $houseSetOtherAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $houseSetOtherAnswer->conditions()->create([
            'answer_id' => $houseTypeAnswer->id,
            'selected_value' => 'Other',
        ]);

        // End ---

        // 1.3b Start of flat type
        $flatTypeStep = $propertySection->steps()->create([
            'question' => 'Please select the type of flat:',
            'help_text' => '<p>Please select the closest match from the provided options. If you can\'t find your exact flat type, no worries! Simply choose "Other" and add your specific type in the text box below.</p><p><strong>Apartment:</strong> An apartment, also known as a flat, is a self-contained residential unit within a larger building or complex. Apartments typically consist of multiple rooms, including bedrooms, a living area, a kitchen, and a bathroom. They offer a compact and efficient living space and are commonly rented or owned by individuals or families.</p><p><strong>Studio:</strong>A studio apartment, also called an efficiency apartment or a bachelor apartment, is a small, self-contained living space that combines the living area, bedroom, and kitchenette into a single room. Studio apartments are designed to maximize space and often appeal to individuals or couples looking for a minimalist and affordable housing option.</p><p><strong>Penthouse:</strong>A penthouse is a luxurious residential unit typically located on the top floor or multiple top floors of a building. Penthouse apartments are known for their upscale features, spacious layouts, and panoramic views. They often include exclusive amenities, such as private terraces, rooftop access, or dedicated elevators, offering a high-end living experience.</p><p><strong>Maisonette:</strong>A maisonette is a residential unit that occupies two or more levels within a larger building. Maisonettes usually have their own private entrance and may be attached to other units or standalone. They provide the feel of a house with the convenience of an apartment, offering separate living and sleeping areas on different floors.</p>',
        ]);

        $flatTypeStep->conditions()->create([
            'answer_id' => $propertyTypeAnswer2->id,
            'selected_value' => 'Flat',
        ]);

        $flatTypeAnswer = $flatTypeStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Apartment'],
                    ['value' => 'Studio'],
                    ['value' => 'Penthouse'],
                    ['value' => 'Maisonette'],
                    ['value' => 'Other'],
                ],
                'pdfFormFieldName' => OverviewPdfField::PropertySubType,
            ],
        ]);

        $flatTypeAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $flatSetOtherAnswer = $flatTypeStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'pdfFormFieldName' => OverviewPdfField::PropertySubType,
            ],
        ]);

        $flatSetOtherAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $flatSetOtherAnswer->conditions()->create([
            'answer_id' => $flatTypeAnswer->id,
            'selected_value' => 'Other',
        ]);
        // End ---

        // 1.3c Start of land type
        $landTypeStep = $propertySection->steps()->create([
            'question' => 'Please select the type of land:',
            'help_text' => '<p>Please select the closest match from the provided options. If you can\'t find the exact land type, no worries! Simply choose "Other" and add your specific type in the text box below.</p><p><strong>Urban/Built-up Land:</strong> Urban or built-up land refers to areas that are developed and characterised by human-made structures, such as residential, commercial, and industrial buildings, roads, and infrastructure.</p><p><strong>Agricultural Land:</strong> Agricultural land is used for farming activities and the cultivation of crops or the raising of livestock. It includes fields, pastures, and farmland where agricultural practices are carried out to produce food, fibre, or other agricultural products.</p><p><strong>Rangeland:</strong> Rangeland is a type of land predominantly used for grazing livestock, such as cattle, sheep, or horses. It consists of natural grasslands, shrublands, or open range areas where animals can graze on vegetation that naturally occurs in the area.</p><p><strong>Forest:</strong> Forest land is characterised by a significant coverage of trees and vegetation. It encompasses wooded areas with diverse tree species, undergrowth, and wildlife. Forests serve various ecological functions, including providing habitat for wildlife, regulating climate, preserving soil integrity, and offering resources like timber, fuelwood, and medicinal plants.</p>',
        ]);

        $landTypeStep->conditions()->create([
            'answer_id' => $propertyTypeAnswer2->id,
            'selected_value' => 'Land',
        ]);

        $landTypeAnswer = $landTypeStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Urban/Built-up'],
                    ['value' => 'Agricultural'],
                    ['value' => 'Rangeland'],
                    ['value' => 'Forest'],
                    ['value' => 'Other'],
                ],
                'pdfFormFieldName' => OverviewPdfField::PropertySubType,
            ],
        ]);

        $landTypeAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $landSetOtherAnswer = $landTypeStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'pdfFormFieldName' => OverviewPdfField::PropertySubType,
            ],
        ]);

        $landSetOtherAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $landSetOtherAnswer->conditions()->create([
            'answer_id' => $landTypeAnswer->id,
            'selected_value' => 'Other',
        ]);
        // End ---

        // 1.4 Start of current property use
        $currentPropertyUseStep = $propertySection->steps()->create([
            'question' => 'Please select the current use of the property:',
            'help_text' => '<p><strong>PRIVATE</strong> - individuals using property primarily for their individual purposes and for their own enjoyment.</p><p><strong>RENTAL</strong> - homes that are inhabited by tenants on a lease or other type of rental agreement.</p><p><strong>COMMERCIAL</strong> - real estate that is used for business activities.</p>',
        ]);

        $currentPropertyUseAnswer = $currentPropertyUseStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Private'],
                    ['value' => 'Rental'],
                    ['value' => 'Commercial'],
                ],
                'pdfFormFieldName' => OverviewPdfField::CurrentUse,
            ],
        ]);

        $currentPropertyUseAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 1.5 Start of intended property use
        $intendedPropertyUseStep = $propertySection->steps()->create([
            'question' => 'Please select the intended use of the property:',
            'help_text' => '<p><strong>PRIVATE</strong> - individuals using property primarily for their individual purposes and for their own enjoyment.</p><p><strong>RENTAL</strong> - homes that are inhabited by tenants on a lease or other type of rental agreement.</p><p><strong>COMMERCIAL</strong> - real estate that is used for business activities.</p>',
        ]);

        $intendedPropertyUseAnswer = $intendedPropertyUseStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Private'],
                    ['value' => 'Rental'],
                    ['value' => 'Commercial'],
                ],
                'pdfFormFieldName' => OverviewPdfField::IntendedUse,
            ],
        ]);

        $intendedPropertyUseAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 1.6 Start of lawyer check
        $lawyerCheckStep = $propertySection->steps()->create([
            'question' => 'Is there anything in particular you would like your lawyer to check regarding the property?',
            'help_text' => 'If there are any specific concerns or questions you have about the property, it\'s essential to communicate them to your conveyancer or solicitor. They will be able to conduct the necessary checks and investigations to address your queries and ensure that you are well-informed about the property\'s condition and legal status. ',
        ]);

        $lawyerCheckAnswer1 = $lawyerCheckStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'],
                    ['value' => 'No'],
                ],
            ],
        ]);

        $lawyerCheckAnswer1->validationRules()->create([
            'rule' => 'required',
        ]);

        $lawyerCheckAnswer2 = $lawyerCheckStep->answers()->create([
            'type' => AnswerType::Textarea,
            'details' => [
                'label' => 'Specific aspects of the property the buyers would like to be looked into',
                'pdfFormFieldName' => OverviewPdfField::FurtherInformation,
            ],
        ]);

        $lawyerCheckAnswer2->validationRules()->create([
            'rule' => 'required',
        ]);

        $lawyerCheckAnswer2->conditions()->create([
            'answer_id' => $lawyerCheckAnswer1->id,
            'selected_value' => 'Yes',
        ]);
        // End ---
    }

    private function theBuyers(Form $form)
    {
        // Buyer Section
        $buyerSection = $form->sections()->create([
            'name' => 'The Buyers',
        ]);

        // 2.1 Number of buyers step
        $numberOfBuyersStep = $buyerSection->steps()->create([
            'question' => 'Please confirm the number of buyers:',
            'help_text' => 'Please provide the total number of individuals who are involved in the purchase of the property, even if you are the only one handling the purchase. Please be aware that attorneys and deputies are NOT considered buyers.',
        ]);

        $numberOfBuyersAnswer = $numberOfBuyersStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => '1'],
                    ['value' => '2'],
                    ['value' => '3'],
                    ['value' => '4'],
                ],
            ],
        ]);

        $this->globalNumberOfBuyersAnswer = $numberOfBuyersAnswer;

        $numberOfBuyersAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 2.2 Start of buyers details section (REPEATABLE, based on 2.1)
        $buyerSection->steps()->create([
            'type' => StepType::BuyerExpanded,
            'question' => 'Please enter the details of the Buyers:',
            'help_text' => 'Please provide the details of all the buyers involved in the purchase of the property. Include their current full names, addresses, contact information, and any other relevant details required. If you are the only buyer, provide your own details. Attorneys and deputies are not considered buyers in this context, so please do not include their information. Ensure that all the information provided is accurate and up-to-date. If you have any questions or concerns about filling out this section, feel free to ask your conveyancer or solicitor for guidance.',
            'repeatable_answer_id' => $numberOfBuyersAnswer->id,
        ]);
        // End ---
    }

    private function theOwnership(Form $form)
    {
        // Owners section
        $ownersSection = $form->sections()->create([
            'name' => 'Ownership',
        ]);

        // 3.1 Start of buyer capacity step
        $buyerCapacityStep = $ownersSection->steps()->create([
            'question' => 'In what capacity do the buyers wish to hold the property?',
            'help_text' => '<p>Please specify how the buyers wish to hold the property:</p><p><strong>Joint tenants:</strong> It means that all buyers will collectively own the property equally. In case of the death of one buyer, their share automatically passes to the surviving buyers.</p><p><strong>Tenants in common in equal shares:</strong> This means that each buyer will own an equal share of the property. If one buyer passes away, their share will not automatically transfer to the other buyers; it will be dealt with according to their will or inheritance laws.</p><p><strong>Tenants in common in unequal shares:</strong> If you choose this option, you can specify the percentage or fraction of the property that each buyer will own. In the event of the death of one buyer, their share will not automatically transfer to the others; it will be managed according to their will or inheritance laws.</p><p>Carefully consider which option best suits your situation and intentions for property ownership. If you have any doubts or need legal advice, consult your conveyancer or solicitor for further clarification.</p>',
        ]);

        $buyerCapacityAnswer = $buyerCapacityStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Joint tenants'], // Skips to section 4
                    ['value' => 'Tenants in common in equal shares'], // Skips to section 4
                    ['value' => 'Tenants in common in unequal shares'], // Continue through section
                ],
                'pdfFormFieldName' => OverviewPdfField::BuyerCapacity,
            ],
        ]);

        $this->globalBuyerCapacityAnswer = $buyerCapacityAnswer;

        $buyerCapacityAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 3.2 Start of held proportions step (REPEATABLE, based on 2.1)
        $heldProportionsStep = $ownersSection->steps()->create([
            'question' => 'Please confirm in what proportions the property will be held?',
            'help_text' => '<p>If you have chosen "Tenants in common in unequal shares" as the way to hold the property, you must indicate the specific percentage or fraction of the property that each buyer will own.</p><p>For example, if there are two buyers, Buyer A and Buyer B, and they have decided to hold the property as tenants in common in unequal shares, you might specify that Buyer A will own 60% of the property, and Buyer B will own 40%.</p><p>It\'s essential to provide accurate and clear information to avoid any confusion or disputes in the future. If you have any uncertainties or need guidance, consult with your conveyancer or solicitor to ensure the correct proportions are specified.</p>',
            'repeatable_answer_id' => $this->globalNumberOfBuyersAnswer->id,
        ]);

        $heldProportionsStep->conditions()->create([
            'answer_id' => $this->globalBuyerCapacityAnswer->id,
            'selected_value' => 'Tenants in common in unequal shares',
        ]);

        $heldProportionsAnswer = $heldProportionsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Percentage',
                'pdfFormFieldName' => OverviewPdfField::SharedOwnershipPercentageValue,
            ],
        ]);

        $heldProportionsAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 3.3 Start of trust deed prepared step
        $trustDeedPreparedStep = $ownersSection->steps()->create([
            'question' => 'Do you wish for a Trust Deed to be prepared?',
            'help_text' => '<p>A Trust Deed is a legal document that outlines the rights and obligations of individuals or entities in relation to property or assets held in trust. If you choose to have a Trust Deed prepared, it will establish the terms and conditions of the trust, specify the beneficiaries, and clarify the roles and responsibilities of the trustees.</p><p>The decision to have a Trust Deed prepared is significant and can have implications for the ownership and management of the property. It is recommended to seek advice from a solicitor or legal professional to understand the implications fully and ensure the Trust Deed is tailored to your specific needs and circumstances.</p>',
        ]);

        $trustDeedPreparedStep->conditions()->create([
            'answer_id' => $this->globalBuyerCapacityAnswer->id,
            'selected_value' => 'Tenants in common in unequal shares',
        ]);

        $trustDeedPreparedAnswer = $trustDeedPreparedStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'],
                    ['value' => 'No'],
                ],
                'pdfFormFieldName' => OverviewPdfField::TrustDeed,
            ],
        ]);

        $trustDeedPreparedAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 3.4 - 3.5 Start of trust deed set out step
        $trustDeedSetOutStep = $ownersSection->steps()->create([
            'question' => 'How should the Trust Deed be set out?',
            'help_text' => '<p>The Trust Deed should be set out based on the chosen option for holding the property. If the buyers wish to hold the property in specific percentages, then the Trust Deed should be structured accordingly with clear indications of each buyer\'s share, for example, "Mr. Jones 25% and Mrs. Jones 75%."</p><p>On the other hand, if the buyers prefer to hold the property in specific amounts and percentages, then the Trust Deed should be formulated to reflect this, such as "the first £10,000 to Mr. Brown and then the remainder to be split 25% to Mr. Jones and 75% to Mrs. Jones."</p><p>It is important to ensure that the Trust Deed accurately reflects the intended distribution and ownership of the property among the buyers as per their chosen option. Seeking professional legal advice can be beneficial in drafting the Trust Deed to ensure it meets all legal requirements and addresses the buyers\' specific preferences.</p>',
        ]);

        $trustDeedSetOutStep->conditions()->create([
            'answer_id' => $this->globalBuyerCapacityAnswer->id,
            'selected_value' => 'Tenants in common in unequal shares',
            'type' => ConditionType::AND,
        ]);

        $trustDeedSetOutStep->conditions()->create([
            'answer_id' => $trustDeedPreparedAnswer->id,
            'selected_value' => 'Yes',
            'type' => ConditionType::AND,
        ]);

        $trustDeedSetOutAnswer = $trustDeedSetOutStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Percentages'],
                    ['value' => 'Amounts & percentages'],
                ],
            ],
        ]);

        $trustDeedSetOutAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $trustDeedSetOutPercentagesAnswer = $trustDeedSetOutStep->answers()->create([
            'type' => AnswerType::Textarea,
            'details' => [
                'placeholder' => 'i.e. Mr. Jones 25% & Mrs. Jones 75%',
            ],
        ]);

        $trustDeedSetOutPercentagesAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $trustDeedSetOutPercentagesAnswer->conditions()->create([
            'answer_id' => $trustDeedSetOutAnswer->id,
            'selected_value' => 'Percentages',
        ]);

        $trustDeedSetOutAmountsAndPercentagesAnswer = $trustDeedSetOutStep->answers()->create([
            'type' => AnswerType::Textarea,
            'details' => [
                'placeholder' => 'i.e. the first £10,000 to Mr Brown AND then the remainder to be split 25% to Mr Jones & 75% to Mrs Jones',
                'pdfFormFieldName' => OverviewPdfField::TrustDeedDetails,
            ],
        ]);

        $trustDeedSetOutAmountsAndPercentagesAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $trustDeedSetOutAmountsAndPercentagesAnswer->conditions()->create([
            'answer_id' => $trustDeedSetOutAnswer->id,
            'selected_value' => 'Amounts & percentages',
        ]);
        // End ---
    }

    private function thePurchase(Form $form)
    {
        // 4.0 Purchase Section
        $purchaseSection = $form->sections()->create([
            'name' => 'The Purchase',
        ]);

        // 4.1 Agreed price step
        $agreedPriceStep = $purchaseSection->steps()->create([
            'question' => 'Agreed purchase price',
            'help_text' => 'Please provide the agreed purchase price for the property, including any deposit already paid. This is the total amount you have agreed to pay for the property as part of the sale agreement. Make sure to provide the exact and accurate figure to ensure a smooth transaction.',
        ]);

        $agreedPriceAnswer = $agreedPriceStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Agreed purchase price',
                'pdfFormFieldName' => OverviewPdfField::Price,
            ],
        ]);

        $agreedPriceAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 4.2 Purchased at auction step
        $purchasedAtAuctionStep = $purchaseSection->steps()->create([
            'type' => StepType::SoldStatus,
            'question' => 'Was the property purchased at auction?',
            'help_text' => 'Purchasing a property at auction means buying it through a public sale process, where potential buyers place bids, and the highest bidder wins the property. It\'s essential to provide this information to ensure a comprehensive understanding of the property\'s acquisition history.',
        ]);

        $purchasedAtAuctionAnswer = $purchasedAtAuctionStep->answers->first();
        // End ---

        // 4.2a Deposit paid step
        $depositPaidStep = $purchaseSection->steps()->create([
            'question' => 'Has any deposit already been paid?',
            'help_text' => 'A deposit is a sum of money paid by the buyer as a commitment to complete the purchase and secure the property. It\'s crucial to provide this information accurately as it affects the overall financial arrangements for the transaction.',
        ]);

        $depositPaidStep->conditions()->create([
            'answer_id' => $purchasedAtAuctionAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $depositPaidAnswer1 = $depositPaidStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'],
                    ['value' => 'No'],
                ],
                'pdfFormFieldName' => OverviewPdfField::DepositPaid,
            ],
        ]);

        $depositPaidAnswer1->validationRules()->create([
            'rule' => 'required',
        ]);

        $depositPaidAnswer2 = $depositPaidStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Deposit already paid',
                'pdfFormFieldName' => OverviewPdfField::DepositPaidAmount,
            ],
        ]);

        $depositPaidAnswer2->conditions()->create([
            'answer_id' => $depositPaidAnswer1->id,
            'selected_value' => 'Yes',
        ]);

        $depositPaidAnswer2->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 4.3 Through estate agent step
        $purchaseThroughEstateAgentStep = $purchaseSection->steps()->create([
            'question' => 'Are you purchasing the property through an estate agent?',
            'help_text' => 'An estate agent is a professional who acts as an intermediary between buyers and sellers, assisting with property transactions and negotiations. If you are using an estate agent in this purchase, you will need to provide the name and contact information of the estate agent you are working with.',
        ]);

        $purchaseThroughEstateAgentAnswer = $purchaseThroughEstateAgentStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'], // Continue to 4.3a, End form
                    ['value' => 'No'], // Skip to to 4.4
                ],
                'pdfFormFieldName' => OverviewPdfField::PurchaseThroughEstateAgent,
            ],
        ]);

        // Hide remaining sections if client has an estate agent
        $this->globalPurchaseThroughEstateAgentAnswer = $purchaseThroughEstateAgentAnswer;

        $purchaseThroughEstateAgentAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 4.3a Estate agent details step
        $purchaseSectionStep = $purchaseSection->steps()->create([
            'question' => 'Please enter the details of the estate agent:',
            'help_text' => 'If you are working with an estate agent for the sale of your property, please provide their details. This will help your conveyancer or solicitor stay in touch with the estate agent and ensure effective communication throughout the selling process.',
            'type' => StepType::EstateAgent,
        ]);

        $purchaseSectionStep->conditions()->create([
            'answer_id' => $this->globalPurchaseThroughEstateAgentAnswer->id, // 4.3
            'selected_value' => 'Yes',
        ]);
        // End ---

        // 4.4 Start of purchase dependant on sale step
        $purchaseDependsOnSaleStep = $purchaseSection->steps()->create([
            'question' => 'Is this purchase dependant upon a sale?',
            'help_text' => 'Please let us know if this purchase is dependant upon the sale of another property. Providing this information is helpful for your conveyancer or solicitor to understand if there is a related property sale involved in your transaction.',
        ]);

        $purchaseDependsOnSaleStep->conditions()->create([
            'answer_id' => $this->globalPurchaseThroughEstateAgentAnswer->id, // 4.3
            'selected_value' => 'No',
        ]);

        $purchaseDependsOnSaleAnswer = $purchaseDependsOnSaleStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'], // Continue to 4.4a
                    ['value' => 'No'], // Skip to section 5
                ],
                'pdfFormFieldName' => OverviewPdfField::DependentOnSale,
            ],
        ]);

        $purchaseDependsOnSaleAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 4.4a Start of confirm address step
        $confirmAddressStep = $purchaseSection->steps()->create([
            'question' => 'Please confirm the address of the dependant sale:',
            'help_text' => 'Please provide the address of the property you are selling as part of this purchase. This information is important for your conveyancer or solicitor to understand the context of the transaction and ensure a smooth process during the sale and purchase of the properties.',
        ]);

        $confirmAddressAnswer = $confirmAddressStep->answers()->create([
            'type' => AnswerType::Address,
            'details' => [
                'label' => 'Address',
                'pdfFormFieldName' => OverviewPdfField::DependentOnSaleAddress,
            ],
        ]);

        $confirmAddressAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $confirmAddressStep->conditions()->create([
            'answer_id' => $this->globalPurchaseThroughEstateAgentAnswer->id, // 4.3
            'selected_value' => 'No',
        ]);

        $confirmAddressStep->conditions()->create([
            'answer_id' => $purchaseDependsOnSaleAnswer->id, // 4.4
            'selected_value' => 'Yes',
        ]);
        // End

        // 4.4b Conveyancing firm name step
        $conveyancingFirmNameStep = $purchaseSection->steps()->create([
            'question' => 'Is the firm dealing with your purchase also dealing with the dependent sale?',
            'help_text' => 'It is likely that the conveyancer or solicitor who is dealing with your purchase is also dealing with your sale, but this is not always the case. Please confirm the appointed conveyancing firm for the sale. This information helps to keep all parties informed about the status of the transactions and ensures a coordinated process.',
        ]);

        $conveyancingFirmNameStep->conditions()->create([
            'answer_id' => $this->globalPurchaseThroughEstateAgentAnswer->id, // 4.3
            'selected_value' => 'No',
        ]);

        $conveyancingFirmNameStep->conditions()->create([
            'answer_id' => $purchaseDependsOnSaleAnswer->id, // 4.4
            'selected_value' => 'Yes',
        ]);

        $conveyancingFirmNameAnswer = $conveyancingFirmNameStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'], // Skip to section 5
                    ['value' => 'No'], // Continue to 4.5, end form
                ],
            ],
        ]);

        // Hide remaining sections if the conveyor is not handling the sale
        $this->globalConveyancingFirmNameAnswer = $conveyancingFirmNameAnswer;

        $conveyancingFirmNameAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 4.5 Start firm details step
        $firmDetailsStep = $purchaseSection->steps()->create([
            'question' => 'Please enter the details of the firm dealing with the dependent sale?',
            'help_text' => 'Please enter the details of the firm dealing with the sale of your property. All parties involved in your sale and purchase will need to be in contact throughout the transaction.',
        ]);

        $firmDetailsStep->conditions()->create([
            'answer_id' => $this->globalPurchaseThroughEstateAgentAnswer->id, // 4.3
            'selected_value' => 'No',
        ]);

        $firmDetailsStep->conditions()->create([
            'answer_id' => $this->globalConveyancingFirmNameAnswer->id, // 4.4b
            'selected_value' => 'No',
        ]);

        $firmDetailsStep->conditions()->create([
            'answer_id' => $purchaseDependsOnSaleAnswer->id, // 4.4
            'selected_value' => 'Yes',
        ]);

        $firmDetailsAnswer = $firmDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Company Name',
                'pdfFormFieldName' => OverviewPdfField::LegalRepresentationName,
            ],
        ]);

        $firmDetailsAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $firmDetailsPhoneNumberAnswer = $firmDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Phone Number',
                'pdfFormFieldName' => OverviewPdfField::LegalRepresentationPhone,
            ],
        ]);

        $firmDetailsPhoneNumberAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $firmDetailsEmailAnswer = $firmDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Email',
                'pdfFormFieldName' => OverviewPdfField::LegalRepresentationEmail,
            ],
        ]);

        $firmDetailsEmailAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $firmDetailsStep->answers()->create([
            'type' => AnswerType::Address,
            'details' => [
                'label' => 'Address',
                'pdfFormFieldName' => OverviewPdfField::LegalRepresentationAddress,
            ],
        ]);
    }

    public function theSellers(Form $form)
    {
        // 5.0 Sellers section
        $sellersSection = $form->sections()->create([
            'name' => 'The Sellers',
        ]);

        $sellersSection->conditions()->create([
            'answer_id' => $this->globalPurchaseThroughEstateAgentAnswer->id, // 4.3
            'selected_value' => 'No',
            'type' => ConditionType::OR,
        ]);

        $sellersSection->conditions()->create([
            'answer_id' => $this->globalConveyancingFirmNameAnswer->id, // 4.4b
            'selected_value' => 'Yes',
            'type' => ConditionType::OR,
        ]);

        // 5.1 Start of buyers relationship step
        $buyersRelationshipStep = $sellersSection->steps()->create([
            'question' => 'Please confirm the buyer(s) relationship to the seller(s):',
            'help_text' => 'The reason why we need to know the buyer(s) relationship to the seller(s) is to ensure transparency and compliance with relevant laws and regulations. Certain relationships, such as family members or business partners, might impact the nature of the transaction and may have legal implications. Understanding the relationship will help your conveyancer or solicitor in determining any potential conflicts of interest and ensuring a smooth and lawful property transfer process.',
        ]);

        $buyersRelationshipAnswer1 = $buyersRelationshipStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Family'], // Continue to 5.2
                    ['value' => 'Friend'], // Continue to 5.2
                    ['value' => 'None'], // End Form
                    ['value' => 'Other'], // Load answers, end form
                ],
                'pdfFormFieldName' => OverviewPdfField::RelationshipToSeller,
            ],
        ]);

        // If the relationship is other or unknown, end the form
        $this->globalConveyancingFirmNameAnswer = $buyersRelationshipAnswer1;

        $buyersRelationshipAnswer1->validationRules()->create([
            'rule' => 'required',
        ]);

        $buyersRelationshipAnswer2 = $buyersRelationshipStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Other Relationship',
            ],
        ]);

        $buyersRelationshipAnswer2->validationRules()->create([
            'rule' => 'required',
        ]);

        $buyersRelationshipAnswer2->conditions()->create([
            'answer_id' => $buyersRelationshipAnswer1->id,
            'selected_value' => 'Other',
        ]);
        // End ---

        // 5.2 Start of confirmed sellers step
        $confirmedSellersStep = $sellersSection->steps()->create([
            'question' => 'Please confirm the number of sellers for the property:',
            'help_text' => "It is useful to know the number of sellers for the property to accurately process the sale transaction. The number of sellers may affect the legal documentation and procedures involved in the sale, such as the number of signatures required on contracts and deeds. This information would help ensuring that all sellers are properly identified and involved in the sale process. If you are unsure of the number of sellers, don't worry! Just select 'Not known'.",
        ]);

        $confirmedSellersStep->conditions()->create([
            'answer_id' => $buyersRelationshipAnswer1->id, // 5.1
            'selected_value' => 'Family',
            'type' => ConditionType::OR,
        ]);

        $confirmedSellersStep->conditions()->create([
            'answer_id' => $buyersRelationshipAnswer1->id, // 5.1
            'selected_value' => 'Friend',
            'type' => ConditionType::OR,
        ]);

        $confirmedSellersAnswer = $confirmedSellersStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => '1'], // Continue to 5.3
                    ['value' => '2'], // Continue to 5.3
                    ['value' => '3'], // Continue to 5.3
                    ['value' => '4'], // Continue to 5.3
                    ['value' => 'Not known'], // End form
                ],
            ],
        ]);

        $this->globalConfirmedSellersAnswer = $confirmedSellersAnswer;

        $confirmedSellersAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 5.3 Start of seller details step (REPEATABLE, based on 5.2)
        $sellerDetailsStep = $sellersSection->steps()->create([
            'type' => StepType::Seller,
            'question' => 'Please provide details of the Seller(s):',
            'help_text' => 'Please provide the details of the seller(s) involved in the sale of the property. Ensuring accurate seller information is essential for the proper processing of the transaction. Rest assured that all provided information will be handled securely and confidentially.',
            'repeatable_answer_id' => $confirmedSellersAnswer->id,
        ]);

        $sellerDetailsStep->conditions()->create([
            'answer_id' => $confirmedSellersAnswer->id, // 5.2
            'selected_value' => '1',
            'type' => ConditionType::OR,
        ]);

        $sellerDetailsStep->conditions()->create([
            'answer_id' => $confirmedSellersAnswer->id, // 5.2
            'selected_value' => '2',
            'type' => ConditionType::OR,
        ]);

        $sellerDetailsStep->conditions()->create([
            'answer_id' => $confirmedSellersAnswer->id, // 5.2
            'selected_value' => '3',
            'type' => ConditionType::OR,
        ]);

        $sellerDetailsStep->conditions()->create([
            'answer_id' => $confirmedSellersAnswer->id, // 5.2
            'selected_value' => '4',
            'type' => ConditionType::OR,
        ]);

        $sellerDetailsStep->conditions()->create([
            'answer_id' => $confirmedSellersAnswer->id, // 5.2
            'selected_value' => 'Not known',
            'type' => ConditionType::OR,
        ]);
        // End ---

        // 5.4 Start of seller conveyancer step
        $sellerConveyancerStep = $sellersSection->steps()->create([
            'question' => "Please provide details of the Seller's conveyancer:",
            'help_text' => 'To facilitate a seamless transaction, it is important that you provide the details of the seller\'s conveyancing firm. Your conveyancer or solicitor will need to be in contact with the seller\'s conveyancer or solicitor throughout the transaction.',

        ]);

        $sellerConveyancerStep->conditions()->create([
            'answer_id' => $confirmedSellersAnswer->id, // 5.2
            'selected_value' => '1',
            'type' => ConditionType::OR,
        ]);

        $sellerConveyancerStep->conditions()->create([
            'answer_id' => $confirmedSellersAnswer->id, // 5.2
            'selected_value' => '2',
            'type' => ConditionType::OR,
        ]);

        $sellerConveyancerStep->conditions()->create([
            'answer_id' => $confirmedSellersAnswer->id, // 5.2
            'selected_value' => '3',
            'type' => ConditionType::OR,
        ]);

        $sellerConveyancerStep->conditions()->create([
            'answer_id' => $confirmedSellersAnswer->id, // 5.2
            'selected_value' => '4',
            'type' => ConditionType::OR,
        ]);

        $sellerConveyancerNotKnownAnswer = $sellerConveyancerStep->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not Known',
            ],
        ]);

        $sellerConveyancerCompanyNameAnswer = $sellerConveyancerStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Company Name',
                'pdfFormFieldName' => OverviewPdfField::SellerCompanyName,
            ],
        ]);

        $sellerConveyancerCompanyNameAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $sellerConveyancerCompanyNameAnswer->conditions()->create([
            'answer_id' => $sellerConveyancerNotKnownAnswer->id,
            'selected_value' => '0',
        ]);

        $sellerConveyancerPhoneNumberAnswer = $sellerConveyancerStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Phone Number',
                'pdfFormFieldName' => OverviewPdfField::SellerCompanyPhoneNumber,
            ],
        ]);

        $sellerConveyancerPhoneNumberAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $sellerConveyancerPhoneNumberAnswer->conditions()->create([
            'answer_id' => $sellerConveyancerNotKnownAnswer->id,
            'selected_value' => '0',
        ]);

        $sellerConveyancerAddressAnswer = $sellerConveyancerStep->answers()->create([
            'type' => AnswerType::Address,
        ]);

        $sellerConveyancerAddressAnswer->conditions()->create([
            'answer_id' => $sellerConveyancerNotKnownAnswer->id,
            'selected_value' => '0',
        ]);
    }

    public function thePurchaseFunds(Form $form)
    {
        // 6.0 Purchase Funds section
        $purchaseFundsSection = $form->sections()->create([
            'name' => 'Purchase Funds',
        ]);

        $purchaseFundsSection->conditions()->create([
            'answer_id' => $this->globalConfirmedSellersAnswer->id, // 5.2
            'selected_value' => '1',
            'type' => ConditionType::OR,
        ]);

        $purchaseFundsSection->conditions()->create([
            'answer_id' => $this->globalConfirmedSellersAnswer->id, // 5.2
            'selected_value' => '2',
            'type' => ConditionType::OR,
        ]);

        $purchaseFundsSection->conditions()->create([
            'answer_id' => $this->globalConfirmedSellersAnswer->id, // 5.2
            'selected_value' => '3',
            'type' => ConditionType::OR,
        ]);

        $purchaseFundsSection->conditions()->create([
            'answer_id' => $this->globalConfirmedSellersAnswer->id, // 5.2
            'selected_value' => '4',
            'type' => ConditionType::OR,
        ]);

        // 6.1 Start of buyer mortgage step
        $buyersUsingMortgageStep = $purchaseFundsSection->steps()->create([
            'question' => 'Are any of the buyers using a mortgage to purchase the property?',
            'help_text' => 'A mortgage is a loan from a bank or financial institution that is used to finance the purchase of the property. Having this information is crucial for the conveyancing process as it helps ensure that all parties involved are aware of the financing arrangements for the property purchase.',
        ]);

        $buyersUsingMortgageAnswer = $buyersUsingMortgageStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'], // Continue to 6.1a, 6.1b
                    ['value' => 'No'], // Skip to 6.2
                ],
            ],
        ]);

        $buyersUsingMortgageAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 6.1a Start of mortgage lender step
        $buyersMortgageLenderStep = $purchaseFundsSection->steps()->create([
            'question' => 'Please enter the name of the mortgage lender:',
            'help_text' => 'Please provide the name of the mortgage lender, which is the financial institution or bank that will be providing the mortgage loan to finance the purchase of the property. The mortgage lender is responsible for assessing your eligibility for the loan, setting the terms and conditions of the mortgage, and handling the disbursement of funds for the property purchase. Having this information is essential for the conveyancing process as it allows all parties involved to be aware of the mortgage lender\'s involvement and facilitates smooth communication and coordination during the property transaction.',
        ]);

        $buyersMortgageLenderStep->conditions()->create([
            'answer_id' => $buyersUsingMortgageAnswer->id, // 6.1
            'selected_value' => 'Yes',
        ]);

        $buyersMortgageLenderAnswer = $buyersMortgageLenderStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Mortgage Lender',
                'pdfFormFieldName' => OverviewPdfField::MortgageLender,
            ],
        ]);

        $buyersMortgageLenderAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 6.1b Start of mortgage amount step
        $buyersMortgageAmountStep = $purchaseFundsSection->steps()->create([
            'question' => 'Please enter the amount of the mortgage:',
            'help_text' => 'Please confirm the full amount of your mortgage, excluding any cashback amount. The amount of the mortgage is the total sum of money that you are borrowing from the mortgage lender to finance the purchase of the property. This amount represents the loan that you will need to repay over the agreed-upon period, along with any applicable interest and fees. It is a critical piece of information for the conveyancing process, as it allows all parties involved to understand the financial arrangements and obligations related to the property transaction. Providing the accurate mortgage amount ensures that the conveyancing solicitors and other parties can proceed with the necessary paperwork and legal steps smoothly.',
        ]);

        $buyersMortgageAmountStep->conditions()->create([
            'answer_id' => $buyersUsingMortgageAnswer->id, // 6.1
            'selected_value' => 'Yes',
        ]);

        $buyersMortgageAmountAnswer = $buyersMortgageAmountStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Mortgage Amount',
                'pdfFormFieldName' => OverviewPdfField::MortgageAmount,
            ],
        ]);

        $buyersMortgageAmountAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        $purchaseFundsSection->steps()->create([
            'question' => 'Please enter the details of the mortgage broker:',
            'help_text' => 'A mortgage broker is a professional who helps borrowers find suitable mortgage deals from various lenders. They offer advice and support throughout the mortgage application process. Kindly provide the required information to ensure a smooth mortgage application. If you have any questions or need further clarification, feel free to ask your conveyancer or solicitor for assistance.',
            'type' => StepType::MortgageBroker,
        ]);

        // 6.2 Start of buyers saving step
        $buyersUsingSavingsStep = $purchaseFundsSection->steps()->create([
            'question' => 'Are any of the buyers using savings to contribute to the purchase price?',
            'help_text' => 'This information is vital for the conveyancing process, as it helps your solicitor or conveyancer understand the source of funds being used to complete the purchase. It also ensures compliance with money laundering regulations and provides transparency regarding the financing of the property acquisition. If any of the buyers are using savings or other sources of funds, it is essential to disclose this information to facilitate a smooth and legally compliant transaction.',
        ]);

        $buyersUsingSavingsAnswer = $buyersUsingSavingsStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'], // Continue to 6.2a
                    ['value' => 'No'], // Skip to 6.3
                ],
            ],
        ]);

        $buyersUsingSavingsAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 6.2a Start of savings amount section
        $buyersSavingsAmountStep = $purchaseFundsSection->steps()->create([
            'question' => 'Please enter the amount of the savings from:',
            'help_text' => 'This information is crucial for the conveyancing process, as it helps your solicitor or conveyancer understand the overall financial arrangement for the property acquisition. It also ensures compliance with money laundering regulations and provides transparency regarding the financing of the purchase. By disclosing the amount of savings, you help facilitate a smooth and legally compliant transaction.',
            'type' => StepType::SavingsAmount,
            'repeatable_answer_id' => $this->globalNumberOfBuyersAnswer->id,
        ]);

        $buyersSavingsAmountStep->conditions()->create([
            'answer_id' => $buyersUsingSavingsAnswer->id, // 6.2
            'selected_value' => 'Yes',
        ]);

        // End ---

        // 6.3 Start of non-buyer-contributor step
        $isNonBuyerContributorsStep = $purchaseFundsSection->steps()->create([
            'question' => 'Is any other person who is NOT a buyer contributing to the purchase price?',
            'help_text' => 'Please inform us if any person who is not listed as a buyer is contributing to the purchase price of the property. This could include financial assistance from family members, friends, or other third parties. It is important to disclose this information to ensure transparency and compliance with legal and financial regulations. If there are any such contributors you will need to provide their details and the amount they are contributing. This helps ensure a smooth and accurate conveyancing process and allows us to handle the transaction appropriately.',
        ]);

        $isNonBuyerContributorsAnswer = $isNonBuyerContributorsStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'],
                    ['value' => 'No'], //skip 6.4
                ],
            ],
        ]);

        $isNonBuyerContributorsAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 6.4 Confirm contribution method step
        $moneyContributionMethodStep = $purchaseFundsSection->steps()->create([
            'question' => 'Please confirm how the money is being contributed:',
            'help_text' => '<p><strong>Loan:</strong> A loan is a financial arrangement where a person (the lender) provides money to another person (the borrower) with the understanding that the borrower will repay the money over a specific period, typically with interest. In the context of a property purchase, a loan can be obtained from a bank, financial institution, or a private lender to help finance the purchase of the property. The borrower is obligated to repay the loan according to the agreed terms and conditions.</p><p><strong>Gift:</strong>A gift refers to a voluntary transfer of money or assets from one person (the donor) to another (the recipient) without any expectation of repayment or consideration. In the context of a property purchase, a gift can be given by a family member, friend, or any other person to one of the buyers to help them with the purchase price. Unlike a loan, a gift does not require repayment.</p><p>It\'s important to note that both loans and gifts may have legal and financial implications, and it\'s essential to disclose and document them properly during the property purchase process. Always seek professional advice from your solicitor or conveyancer to ensure compliance with applicable laws and regulations.</p>',
        ]);

        $moneyContributionMethodAnswer = $moneyContributionMethodStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Loan'], // Continue to 6.5
                    ['value' => 'Gift'], // Skip to 6.6
                    ['value' => 'Other'], // Skip to section 7.0
                ],
            ],
        ]);

        $moneyContributionMethodStep->conditions()->create([
            'answer_id' => $isNonBuyerContributorsAnswer->id, // 6.3
            'selected_value' => 'Yes',
        ]);

        $moneyContributionMethodAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $moneyContributionOtherMethodAnswer = $moneyContributionMethodStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Other',
                'pdfFormFieldName' => OverviewPdfField::PurchaseFundsOther,
            ],
        ]);

        $moneyContributionOtherMethodAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $moneyContributionOtherMethodAnswer->conditions()->create([
            'answer_id' => $moneyContributionMethodAnswer->id, // 6.4
            'selected_value' => 'Other',
        ]);

        $moneyContributionOtherAmountAnswer = $moneyContributionMethodStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Amount',
            ],
        ]);

        $moneyContributionOtherAmountAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $moneyContributionOtherAmountAnswer->conditions()->create([
            'answer_id' => $moneyContributionMethodAnswer->id, // 6.4
            'selected_value' => 'Other',
        ]);
        // End ---

        // 6.5 Start of how many loaners step
        $loanerQuantityStep = $purchaseFundsSection->steps()->create([
            'question' => 'How many people and/or companies are loaning you money?',
            'help_text' => 'Please enter the number of people and/or companies that are loaning you money for the property purchase. If you are receiving money from a joint account, please count it as two persons.',
        ]);

        $moneyContributionMethodStep->conditions()->create([
            'answer_id' => $isNonBuyerContributorsAnswer->id, // 6.3
            'selected_value' => 'Yes',
            'type' => ConditionType::AND,
        ]);

        $loanerQuantityStep->conditions()->create([
            'answer_id' => $moneyContributionMethodAnswer->id, // 6.4
            'selected_value' => 'Loan',
            'type' => ConditionType::AND,
        ]);

        $loanerQuantityAnswer = $loanerQuantityStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => '1'],
                    ['value' => '2'],
                    ['value' => '3'],
                    ['value' => '4'],
                ],
            ],
        ]);

        $loanerQuantityAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 6.5a Loaner detail step (REPEATABLE, based on 6.5)
        $loanerDetailsStep = $purchaseFundsSection->steps()->create([
            'type' => StepType::Loaner,
            'question' => 'Please provide details of the loaner(s):',
            'help_text' => 'Your conveyancer or solicitor will need the details of the loaner(s) to understand the terms and conditions of the loan agreement. This information helps ensure that the loan arrangements align with the requirements of the property purchase and that all parties involved are aware of their responsibilities. By providing the loaner(s) details, the conveyancer can verify the source of the funds and accurately document the financial transactions related to the property purchase.',
            'repeatable_answer_id' => $loanerQuantityAnswer->id,
        ]);

        $moneyContributionMethodStep->conditions()->create([
            'answer_id' => $isNonBuyerContributorsAnswer->id, // 6.3
            'selected_value' => 'Yes',
            'type' => ConditionType::AND,
        ]);

        $loanerDetailsStep->conditions()->create([
            'answer_id' => $moneyContributionMethodAnswer->id, // 6.4
            'selected_value' => 'Loan',
            'type' => ConditionType::AND,
        ]);
        // End ---

        // 6.6 Start giftor quantity step
        $giftorQuantityStep = $purchaseFundsSection->steps()->create([
            'question' => 'How many people are gifting you money? Please note that these people will be invited to ProConvey to complete ID and AML checks.?',
            'help_text' => 'Your conveyancer or solicitor will need to know how many people are gifting you money to understand the financial arrangement related to the property purchase. When indicating the number of people gifting you money, please keep in mind that money gifted from a joint account will be considered as two individuals for the purpose of documentation. This means that if multiple individuals are contributing to the gift from a joint account, each person will be counted separately. Your conveyancer or solicitor will use this information to ensure accurate reporting and compliance with legal requirements.',
        ]);

        $moneyContributionMethodStep->conditions()->create([
            'answer_id' => $isNonBuyerContributorsAnswer->id, // 6.3
            'selected_value' => 'Yes',
            'type' => ConditionType::AND,
        ]);

        $giftorQuantityStep->conditions()->create([
            'answer_id' => $moneyContributionMethodAnswer->id, // 6.4
            'selected_value' => 'Gift',
            'type' => ConditionType::AND,
        ]);

        $giftorQuantityAnswer = $giftorQuantityStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => '1'],
                    ['value' => '2'],
                    ['value' => '3'],
                    ['value' => '4'],
                ],
            ],
        ]);

        $giftorQuantityAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 6.6a Start giftor details section (REPEATABLE, based on 6.6)
        $giftorDetailsStep = $purchaseFundsSection->steps()->create([
            'type' => StepType::BuyerGiftor,
            'question' => 'Please provide details of the giftor(s):',
            'help_text' => 'Your conveyancer or solicitor will need details of the giftor(s) to comply with anti-money laundering (AML) regulations and to verify the source of funds for the property purchase. They are required to conduct due diligence on all parties involved in the transaction, including the giftor(s), to ensure that there are no illegal or fraudulent activities associated with the funds being used for the purchase. By obtaining the details of the giftor(s), your conveyancer or solicitor can perform necessary checks and ensure that the funds are legitimate and legally obtained, providing a secure and transparent property transaction process.',
            'repeatable_answer_id' => $giftorQuantityAnswer->id,
        ]);

        $moneyContributionMethodStep->conditions()->create([
            'answer_id' => $isNonBuyerContributorsAnswer->id, // 6.3
            'selected_value' => 'Yes',
            'type' => ConditionType::AND,
        ]);

        $giftorDetailsStep->conditions()->create([
            'answer_id' => $moneyContributionMethodAnswer->id, // 6.4
            'selected_value' => 'Gift',
            'type' => ConditionType::AND,
        ]);
        // End ---
    }

    public function theSendingMoneyToYou(Form $form)
    {
        // 7.0 Sending Money to You section
        $sendingMoneySection = $form->sections()->create([
            'name' => 'Sending Money to You',
        ]);

        $sendingMoneySection->conditions()->create([
            'answer_id' => $this->globalConfirmedSellersAnswer->id, // 5.2
            'selected_value' => '1',
            'type' => ConditionType::OR,
        ]);

        $sendingMoneySection->conditions()->create([
            'answer_id' => $this->globalConfirmedSellersAnswer->id, // 5.2
            'selected_value' => '2',
            'type' => ConditionType::OR,
        ]);

        $sendingMoneySection->conditions()->create([
            'answer_id' => $this->globalConfirmedSellersAnswer->id, // 5.2
            'selected_value' => '3',
            'type' => ConditionType::OR,
        ]);

        $sendingMoneySection->conditions()->create([
            'answer_id' => $this->globalConfirmedSellersAnswer->id, // 5.2
            'selected_value' => '4',
            'type' => ConditionType::OR,
        ]);

        // 7.1 Buyers bank account details step
        $buyersBankDetailsStep = $sendingMoneySection->steps()->create([
            'question' => "Please provide the buyers' bank account details:",
            'help_text' => 'Your conveyancer or solicitor may need your bank details to facilitate the deposit transfer, arrange for mortgage payments, process completion funds, and handle any refunds or overpayments during the property purchase process. Rest assured that your information will be handled securely and only shared with authorized parties.',
        ]);

        $answerBuyersAccountName = $buyersBankDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Account name',
                'pdfFormFieldName' => OverviewPdfField::BuyerAccountName,
            ],
        ]);

        $answerBuyersAccountName->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyersSortCode = $buyersBankDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Sort code',
                'pdfFormFieldName' => OverviewPdfField::BuyerSortCode,
            ],
        ]);

        $answerBuyersSortCode->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerBuyersAccountNumber = $buyersBankDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Account number',
                'pdfFormFieldName' => OverviewPdfField::BuyerAccountNumber,
            ],
        ]);

        $answerBuyersAccountNumber->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---
    }

    public function theStampDuty(Form $form)
    {
        // 8.0 Stamp duty section
        $stampDutySection = $form->sections()->create([
            'name' => 'Stamp Duty Land Tax Declaration',
        ]);

        $stampDutySection->conditions()->create([
            'answer_id' => $this->globalConfirmedSellersAnswer->id, // 5.2
            'selected_value' => '1',
            'type' => ConditionType::OR,
        ]);

        $stampDutySection->conditions()->create([
            'answer_id' => $this->globalConfirmedSellersAnswer->id, // 5.2
            'selected_value' => '2',
            'type' => ConditionType::OR,
        ]);

        $stampDutySection->conditions()->create([
            'answer_id' => $this->globalConfirmedSellersAnswer->id, // 5.2
            'selected_value' => '3',
            'type' => ConditionType::OR,
        ]);

        $stampDutySection->conditions()->create([
            'answer_id' => $this->globalConfirmedSellersAnswer->id, // 5.2
            'selected_value' => '4',
            'type' => ConditionType::OR,
        ]);
        // 8.1 Start of is property moveable step
        $isMovablePropertyStep = $stampDutySection->steps()->create([
            'question' => 'Is the property moveable (e.g a mobile home, caravan or houseboat)?',
            'help_text' => 'Moveable property refers to items that are not fixed to the ground and can be easily transported or relocated. Examples of moveable property include mobile homes, caravans, and houseboats. These structures are designed to be movable and are not permanently attached to the land like traditional houses or buildings. Identifying the property\'s mobility is essential because it can impact legal and logistical considerations during the sale. Mobile properties may have specific regulations and requirements, and their classification could affect the financing and conveyancing procedures involved in the transaction.',
        ]);

        $isMovablePropertyAnswer = $isMovablePropertyStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'],
                    ['value' => 'No'],
                ],
                'pdfFormFieldName' => OverviewPdfField::IsThePropertyMoveable,
            ],
        ]);

        $isMovablePropertyAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 8.2 Start of is property moveable step
        $propertyMixtureStep = $stampDutySection->steps()->create([
            'question' => 'Will the property be a mixture of residential and non-residential?',
            'help_text' => '<p>A property that is a mixture of residential and non-residential spaces means that it contains both areas used for living purposes (residential) and areas used for commercial or business purposes (non-residential). An example of this would be a flat with a shop located underneath it, where the flat is used as a residence, and the shop is used for business activities.</p><p>Knowing whether the property has such a mixed-use arrangement is important for potential buyers because it may have implications for planning regulations, taxation, and other legal considerations. Additionally, financing options and insurance coverage might differ for mixed-use properties compared to purely residential ones. Therefore, it\'s essential to be aware of this aspect when purchasing such a property to make informed decisions and ensure compliance with relevant laws and regulations.</p>',
        ]);

        $propertyMixtureAnswer = $propertyMixtureStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'],
                    ['value' => 'No'],
                ],
                'pdfFormFieldName' => OverviewPdfField::MixtureResidentialAndNonResidential,
            ],
        ]);

        $propertyMixtureAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        $answerFirstTimeBuyerStep = $stampDutySection->steps()->create([
            'question' => 'Stamp Duty Land Tax',
            'help_text' => '<p>The purpose of this question is to determine if the buyer has previous ownership of other properties or land, as this information may have implications for various legal and financial matters related to the purchase of the current property.</p><p>For example, if you currently own another property, it may affect your eligibility for certain tax benefits or exemptions related to the purchase of a new property. Additionally, it could influence the your ability to obtain financing or impact stamp duty liabilities. Full and accurate disclosure of any previous property ownership is crucial to ensure a smooth and lawful transaction.</p>',
            'repeatable_answer_id' => $this->globalNumberOfBuyersAnswer->id,
        ]);

        $answerFirstTimeBuyerAnswer = $answerFirstTimeBuyerStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'label' => 'Whether bought, gifted or inherited, has this buyer ever owned any residential property or land anywhere in the world?',
                'options' => [
                    ['value' => 'Yes'], // Not First Time Buyer
                    ['value' => 'No'], // First Time Buyer
                ],
            ],
        ]);

        $answerFirstTimeBuyerAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        $answerSDLTRateStep = $stampDutySection->steps()->create([
            'question' => 'Stamp Duty Land Tax',
            'help_text' => '<p>The purpose of this question is to determine if the buyer and their partner(s) will own multiple properties, which can have implications for certain tax considerations and other legal matters.</p><p>For example, if your and your spouse or civil partner already own more than one property with a value exceeding £40,000, you might be subject to additional tax liabilities, such as the Higher Rates of Stamp Duty Land Tax (SDLT) for additional residential properties. Properly disclosing this information is essential to ensure compliance with relevant tax regulations and to avoid potential penalties or legal issues related to property ownership.</p>',
            'repeatable_answer_id' => $this->globalNumberOfBuyersAnswer->id,
        ]);

        $answerSDLTRateAnswer = $answerSDLTRateStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'label' => 'After this purchase has completed will the buyer, and their spouses or civil partners, own more than one property worth more than £40,000?',
                'options' => [
                    ['value' => 'Yes'], // Not First Time Buyer
                    ['value' => 'No'], // First Time Buyer
                ],
            ],
        ]);

        $answerSDLTRateAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        // End ---

        $answerFirstTimeBuyerReliefStep = $stampDutySection->steps()->create([
            'question' => 'Stamp Duty Land Tax',
            'help_text' => '<p>If the property will be your main residence, it may have implications for certain tax benefits, such as the Principal Private Residence (PPR) relief, and may also affect eligibility for certain government schemes or benefits related to primary residence.</p><p>It is important to answer this question honestly and accurately, as it can have legal and financial implications. If you intend to use the property as your main residence, you should indicate so in your response to ensure proper documentation and compliance with relevant regulations.</p>',
            'repeatable_answer_id' => $this->globalNumberOfBuyersAnswer->id,
        ]);

        $answerFirstTimeBuyerReliefAnswer = $answerFirstTimeBuyerReliefStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'label' => 'Will the property be the main residence for this buyer?',
                'options' => [
                    ['value' => 'Yes'], // Not First Time Buyer
                    ['value' => 'No'], // First Time Buyer
                ],
            ],
        ]);

        $answerFirstTimeBuyerReliefAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        // End ---
    }
}
