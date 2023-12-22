<?php

namespace Database\Seeders\Forms\Remortgage;

use App\Enums\AnswerType;
use App\Enums\ConditionType;
use App\Enums\FormGroup;
use App\Enums\FormType;
use App\Enums\OverviewPdfField;
use App\Enums\PropertyType;
use App\Enums\StepType;
use App\Models\Form;
use Illuminate\Database\Seeder;

class GettingStarted_Remortgaging extends Seeder
{
    private $globalMortgagerQuantityAnswer;

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
                'name' => 'Getting Started - Remortgaging',
                'group' => FormGroup::GettingStarted,
                'description' => 'This section aims to gather initial information relating to the remortgage.',
                'order_number' => 1,
                'type' => PropertyType::Remortgage,
                'ta_form_template' => FormType::GettingStarted,
            ])
            ->create();

        $this->theProperty($form);
        $this->theRemortgagers($form);
        $this->theOwnership($form);
        $this->theRemortgage($form);
        $this->theCurrentOwnership($form);
        $this->theRemortgageFunds($form);
        $this->theSendingMoneyToYou($form);
        $this->theMortgageLoansAndCharges($form);
    }

    public function theProperty(Form $form)
    {
        // 1.0 Property section
        $propertySection = $form->sections()->create([
            'name' => 'The Property',
        ]);

        // 1.1 Property address
        $propertySection->steps()->create([
            'question' => 'Please confirm the address of the property you are remortgaging:',
            'type' => StepType::Address,
            'help_text' => 'Please confirm the address of the property you are remortgaging as it appears on the Property Title Deeds. This information is crucial to ensure that the property identified in the title deeds matches the one you are intending to remortgage. Verify the address carefully, and if there are any discrepancies or uncertainties, consult with your solicitor or conveyancer for clarification and guidance. Having the correct address is vital for a smooth and accurate property transaction.',
        ]);
        // End ---

        // 1.2 Property type
        $propertyTypeStep = $propertySection->steps()->create([
            'question' => 'Please select the property type:',
            'help_text' => '<p>Please select the property type from the options below:</p><p><strong>FREEHOLD</strong> When you own the property and the land it sits on outright, and there is no time limit on your ownership. You are responsible for all maintenance and repairs.</p><p><strong>LEASEHOLD</strong> You have the right to occupy the property for a fixed period, typically long-term, under a lease agreement with the freeholder. The lease outlines your rights and responsibilities during the lease term.</p><p><strong>COMMONHOLD</strong> A relatively new form of property ownership where you own a freehold unit within a larger development. You also have a share of the freehold of the common parts, jointly with other unit owners.</p><p><strong>SHARED BUYERSHIP</strong> You buy a share of the property (usually from 25% to 75%) and pay rent on the remaining share. You have the option to buy more shares in the future, known as "staircasing."</p><p>Choose the option that best describes the type of property you are buying. If you are uncertain about the implications of each type, seek advice from your solicitor or conveyancer to ensure you fully understand the terms and conditions associated with your chosen property type.</p>',
            'type' => StepType::Tenure,
        ]);
        // End ---

        // 1.2a Re-mortgage ownership
        $remortgageOwnershipStep = $propertySection->steps()->create([
            'question' => 'Please enter the percentage of the property you will own after the remortgage completes:',
            'help_text' => 'Please enter the percentage of the property you are purchasing. This refers to the share or proportion of ownership you will have in the property. For example, if you are buying the entire property, you would enter 100%. If you are purchasing only a portion of the property, such as in shared ownership, you would enter the specific percentage you are buying (e.g., 25%, 50%, etc.). Be sure to provide the accurate percentage to reflect your ownership share in the property.',
        ]);

        $remortgageOwnershipStep->conditions()->create([
            'answer_id' => $propertyTypeStep->answers->first()->id, // 1.2
            'selected_value' => 'Shared ownership',
        ]);

        $remortgageOwnershipStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Shared ownership percentage',
            ],
        ]);
        // End ---

        // 1.3 Start of property type
        $propertyTypeStep2 = $propertySection->steps()->create([
            'question' => 'Please select the property type:',
            'help_text' => '<p>Please select the type of property you are purchasing:</p><p><strong>House</strong>: A standalone residential property constructed on its own land.</p><p><strong>Flat/Apartment: Self-contained residential units within a larger building, often sharing common areas such as hallways and entrances.</p><p><strong>Land/Plot:</strong>Undeveloped land or a vacant plot, providing potential buyers with the opportunity to build their own home or explore development possibilities.</p>',
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
            'answer_id' => $propertyTypeAnswer2->id, // 1.3
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

        $houseOtherAnswer = $houseTypeStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'pdfFormFieldName' => OverviewPdfField::PropertySubType,
            ],
        ]);

        $houseOtherAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $houseOtherAnswer->conditions()->create([
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
            'answer_id' => $propertyTypeAnswer2->id, // 1.3
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

        $flatOtherAnswer = $flatTypeStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'pdfFormFieldName' => OverviewPdfField::PropertySubType,
            ],
        ]);

        $flatOtherAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $flatOtherAnswer->conditions()->create([
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

        $landOtherAnswer = $landTypeStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'pdfFormFieldName' => OverviewPdfField::PropertySubType,
            ],
        ]);

        $landOtherAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $landOtherAnswer->conditions()->create([
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
    }

    public function theRemortgagers(Form $form)
    {
        // 2.0 Re-mortgager section
        $mortgagerSection = $form->sections()->create([
            'name' => 'The Buyers (Remortgagers)',
        ]);

        // 2.1 Property address
        $mortgagerQuantityStep = $mortgagerSection->steps()->create([
            'question' => 'Please confirm the number of people on the new mortgage:',
            'help_text' => 'Please make sure you select the total number of people on the new mortgage, even if you are the only one dealing with the remortgage of the property. This information is crucial for the remortgage application process to accurately reflect the number of borrowers involved. If you have any questions or uncertainties, don\'t hesitate to seek guidance from your conveyancer or solicitor to ensure all necessary details are provided accurately.',
        ]);

        $mortgagerQuantityAnswer = $mortgagerQuantityStep->answers()->create([
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

        $this->globalMortgagerQuantityAnswer = $mortgagerQuantityAnswer;

        $mortgagerQuantityAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 2.2 Re-mortgager forms (REPEATABLE, based on 2.1 selection)
        $mortgagerSection->steps()->create([
            'type' => StepType::Mortgager,
            'question' => 'Please enter the details of the remortgagers:',
            'help_text' => 'Please provide the details of all the individuals involved in the remortgage process (including any new owner that you would like to add on the new mortgage). Include the full names, addresses, and any other required information for each remortgager. If you are the only person involved in the remortgage, you can simply provide your own details. However, if there are multiple remortgagers, ensure you provide the information for each individual accurately. This information is necessary for the remortgage application and legal documentation, so it\'s essential to provide accurate and up-to-date details. If you have any questions or need assistance, feel free to consult your conveyancer or solicitor for guidance throughout the process.',
            'repeatable_answer_id' => $mortgagerQuantityAnswer->id,
        ]);
        // End ---
    }

    public function theOwnership(Form $form)
    {
        // 3.0 Ownership section
        $ownershipSection = $form->sections()->create([
            'name' => 'Ownership',
        ]);

        // 3.1 Mortgager capacity
        $mortgagerQuantityStep = $ownershipSection->steps()->create([
            'question' => 'In what capacity do the buyers wish to hold the property?',
            'help_text' => '<p>Please specify how the buyers wish to hold the property:</p><p><strong>Joint tenants:</strong> It means that all buyers will collectively own the property equally. In case of the death of one buyer, their share automatically passes to the surviving buyers.</p><p><strong>Tenants in common in equal shares:</strong> This means that each buyer will own an equal share of the property. If one buyer passes away, their share will not automatically transfer to the other buyers; it will be dealt with according to their will or inheritance laws.</p><p><strong>Tenants in common in unequal shares:</strong> If you choose this option, you can specify the percentage or fraction of the property that each buyer will own. In the event of the death of one buyer, their share will not automatically transfer to the others; it will be managed according to their will or inheritance laws.</p><p>Carefully consider which option best suits your situation and intentions for property ownership. If you have any doubts or need legal advice, consult your conveyancer or solicitor for further clarification.</p>',
        ]);

        $mortgagerQuantityAnswer = $mortgagerQuantityStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Joint tenants'],                       // Skips to section 4
                    ['value' => 'Tenants in common in equal shares'],   // Skips to section 4
                    ['value' => 'Tenants in common in unequal shares'], // Continue through section
                ],
                'pdfFormFieldName' => OverviewPdfField::BuyerCapacity,
            ],
        ]);

        $mortgagerQuantityAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 3.2 Held proportions (REPEATABLE, based on 2.1)
        $proportionsStep = $ownershipSection->steps()->create([
            'question' => 'Please confirm in what proportions the property will be held?',
            'help_text' => '<p>If you have chosen "Tenants in common in unequal shares" as the way to hold the property, you must indicate the specific percentage or fraction of the property that each buyer will own.</p><p>For example, if there are two buyers, Buyer A and Buyer B, and they have decided to hold the property as tenants in common in unequal shares, you might specify that Buyer A will own 60% of the property, and Buyer B will own 40%.</p><p>It\'s essential to provide accurate and clear information to avoid any confusion or disputes in the future. If you have any uncertainties or need guidance, consult with your conveyancer or solicitor to ensure the correct proportions are specified.</p>',
            'repeatable_answer_id' => $this->globalMortgagerQuantityAnswer->id,
        ]);

        $proportionsStep->conditions()->create([
            'answer_id' => $mortgagerQuantityAnswer->id, // 3.1
            'selected_value' => 'Tenants in common in unequal shares',
        ]);

        $proportionsAnswer = $proportionsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Percentage',
            ],
        ]);

        $proportionsAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 3.3 Trust deed
        $trustDeedStep = $ownershipSection->steps()->create([
            'question' => 'Do you wish for a Trust Deed to be prepared?',
            'help_text' => '<p>A Trust Deed is a legal document that outlines the rights and obligations of individuals or entities in relation to property or assets held in trust. If you choose to have a Trust Deed prepared, it will establish the terms and conditions of the trust, specify the beneficiaries, and clarify the roles and responsibilities of the trustees.</p><p>The decision to have a Trust Deed prepared is significant and can have implications for the ownership and management of the property. It is recommended to seek advice from a solicitor or legal professional to understand the implications fully and ensure the Trust Deed is tailored to your specific needs and circumstances.</p>',
        ]);

        $trustDeedStep->conditions()->create([
            'answer_id' => $mortgagerQuantityAnswer->id, // 3.1
            'selected_value' => 'Tenants in common in unequal shares',
        ]);

        $trustDeedAnswer = $trustDeedStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'],
                    ['value' => 'No'],
                ],
                'pdfFormFieldName' => OverviewPdfField::TrustDeed,
            ],
        ]);

        $trustDeedAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 3.3a - 3.3b trust deed set out step
        $trustDeedSetOutStep = $ownershipSection->steps()->create([
            'question' => 'How should the Trust Deed be set out?',
            'help_text' => '<p>The Trust Deed should be set out based on the chosen option for holding the property. If the buyers wish to hold the property in specific percentages, then the Trust Deed should be structured accordingly with clear indications of each buyer\'s share, for example, "Mr. Jones 25% and Mrs. Jones 75%."</p><p>On the other hand, if the buyers prefer to hold the property in specific amounts and percentages, then the Trust Deed should be formulated to reflect this, such as "the first £10,000 to Mr. Brown and then the remainder to be split 25% to Mr. Jones and 75% to Mrs. Jones."</p><p>It is important to ensure that the Trust Deed accurately reflects the intended distribution and ownership of the property among the buyers as per their chosen option. Seeking professional legal advice can be beneficial in drafting the Trust Deed to ensure it meets all legal requirements and addresses the buyers\' specific preferences.</p>',
        ]);

        $trustDeedSetOutStep->conditions()->create([
            'answer_id' => $mortgagerQuantityAnswer->id, // 3.1
            'selected_value' => 'Tenants in common in unequal shares',
            'type' => ConditionType::AND,
        ]);

        $trustDeedSetOutStep->conditions()->create([
            'answer_id' => $trustDeedAnswer->id, // 3.3
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
            'answer_id' => $trustDeedSetOutAnswer->id, // 3.3a
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
            'answer_id' => $trustDeedSetOutAnswer->id, // 3.3a
            'selected_value' => 'Amounts & percentages',
        ]);
        // End ---
    }

    public function theRemortgage(Form $form)
    {
        // 4.0 Re-mortgage section
        $remortgageSection = $form->sections()->create([
            'name' => 'The Remortgage',
        ]);

        // 4.1 Dependent sale
        $dependentSaleStep = $remortgageSection->steps()->create([
            'question' => 'Is this remortgage dependant upon a sale of another property?',
            'help_text' => 'Please let us know if this remortgage is dependant upon the sale of another property. Providing this information is helpful for your conveyancer or solicitor to understand if there is a related property sale involved in your transaction.',
        ]);

        $dependentSaleAnswer = $dependentSaleStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'], // Continue to 4.1a, 4.1b
                    ['value' => 'No'],  // Skips to section 5
                ],
                'pdfFormFieldName' => OverviewPdfField::DependentOnSale,
            ],
        ]);

        $dependentSaleAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 4.1a Dependent sale address
        $dependentAddressStep = $remortgageSection->steps()->create([
            'question' => 'Please confirm the address of the dependant sale:',
            'help_text' => 'Please provide the address of the property you are selling as part of this remortgage. This information is important for your conveyancer or solicitor to understand the context of the transaction and ensure a smooth process during the sale and remortgage of the properties.',
        ]);

        $dependentAddressAnswer = $dependentAddressStep->answers()->create([
            'type' => AnswerType::Address,
            'details' => [
                'label' => 'Address',
                'pdfFormFieldName' => OverviewPdfField::DependentOnSaleAddress,
            ],
        ]);

        $dependentAddressAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        $dependentAddressStep->conditions()->create([
            'answer_id' => $dependentSaleAnswer->id, // 4.1
            'selected_value' => 'Yes',
        ]);
        // End ---

        // 4.1b Firm association to dependent address
        $dependentFirmAddressGuessStep = $remortgageSection->steps()->create([
            'question' => 'Is the firm dealing with your remortgage also dealing with the dependent sale?',
            'help_text' => 'It is likely that the conveyancer or solicitor who is dealing with your remortgae is also dealing with your sale, but this is not always the case. Please confirm the appointed conveyancing firm for the sale. This information helps to keep all parties informed about the status of the transactions and ensures a coordinated process.',
        ]);

        $dependentFirmAddressGuessStep->conditions()->create([
            'answer_id' => $dependentSaleAnswer->id, // 4.1
            'selected_value' => 'Yes',
        ]);

        $dependentFirmAddressGuessAnswer = $dependentFirmAddressGuessStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'], // Skips to section 5
                    ['value' => 'No'],  // Continue to 4.2
                ],
            ],
        ]);

        $dependentFirmAddressGuessAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 4.2 Start firm details step
        $dependentFirmDetailsStep = $remortgageSection->steps()->create([
            'question' => 'Please enter the details of the firm dealing with the dependent sale:',
            'help_text' => 'Please enter the details of the firm dealing with the sale of your property. All parties involved in your sale and remortgage will need to be in contact throughout the transaction.',
        ]);

        $dependentFirmDetailsStep->conditions()->create([
            'answer_id' => $dependentSaleAnswer->id,  // 4.1a
            'selected_value' => 'Yes',
        ]);

        $dependentFirmDetailsStep->conditions()->create([
            'answer_id' => $dependentFirmAddressGuessAnswer->id, // 4.1b
            'selected_value' => 'No',
        ]);

        // Company name
        $dependentFirmDetailsAnswer = $dependentFirmDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Company name',
                'pdfFormFieldName' => OverviewPdfField::LegalRepresentationName,
            ],
        ]);

        $dependentFirmDetailsAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        // Phone number
        $dependentFirmDetailsPhoneNumberAnswer = $dependentFirmDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Phone number',
                'pdfFormFieldName' => OverviewPdfField::LegalRepresentationPhone,
            ],
        ]);

        $dependentFirmDetailsPhoneNumberAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        // Email
        $dependentFirmDetailsEmailAnswer = $dependentFirmDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Email',
                'pdfFormFieldName' => OverviewPdfField::LegalRepresentationEmail,
            ],
        ]);

        $dependentFirmDetailsEmailAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        // Address
        $dependentFirmDetailsStep->answers()->create([
            'type' => AnswerType::Address,
            'details' => [
                'label' => 'Address',
                'pdfFormFieldName' => OverviewPdfField::LegalRepresentationAddress,
            ],
        ]);
        // End ---
    }

    public function theCurrentOwnership(Form $form)
    {
        // 5.0 Current ownership
        $ownershipSection = $form->sections()->create([
            'name' => 'Current Ownership',
        ]);

        // 5.1 Remortgagers are current owners
        $isCurrentOwnersStepStep = $ownershipSection->steps()->create([
            'question' => 'Are all of the remortgagers currently the owners of the property?',
            'help_text' => 'Please confirm whether all the remortgagers listed are currently the legal owners of the property. If there are any remortgagers who are not currently listed as owners, you should select "No." Otherwise, if all the remortgagers are current owners, select "Yes." Providing accurate information is important to ensure a smooth and valid remortgage process. If you have any doubts or questions, it\'s advisable to consult with your conveyancer or solicitor for guidance.',
        ]);

        $isCurrentOwnersStepAnswer = $isCurrentOwnersStepStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'], // Skip to section 6
                    ['value' => 'No'], // Continue to 5.2
                ],
            ],
        ]);

        $isCurrentOwnersStepAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End

        // 5.2 Current owner details (REPEATABLE, based on 2.2)
        $currentOwnerDetailsStep = $ownershipSection->steps()->create([
            'type' => StepType::Owner,
            'question' => 'Please confirm the number of current owners of the property:',
            'repeatable_answer_id' => $this->globalMortgagerQuantityAnswer->id,
        ]);

        $currentOwnerDetailsStep->conditions()->create([
            'answer_id' => $isCurrentOwnersStepAnswer->id,
            'selected_value' => 'No',
        ]);
    }

    public function theRemortgageFunds(Form $form)
    {
        // 6.0
        $remortgageFundsSection = $form->sections()->create([
            'name' => 'Remortgage Funds',
        ]);

        // 6.1a Mortgage lender
        $mortgageLenderStep = $remortgageFundsSection->steps()->create([
            'type' => StepType::MortgageLender,
            'question' => 'Please enter the name of the mortgage lender:',
            'help_text' => 'Please provide the name of the mortgage lender, which is the financial institution or bank that will be providing the mortgage loan to finance the remortgage of the property. The mortgage lender is responsible for assessing your eligibility for the loan, setting the terms and conditions of the mortgage, and handling the disbursement of funds for the property remortgage. Having this information is essential for the conveyancing process as it allows all parties involved to be aware of the mortgage lender\'s involvement and facilitates smooth communication and coordination during the property transaction. ',
        ]);

        $mortgageLenderAnswer = $mortgageLenderStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Mortgage lender',
                'pdfFormFieldName' => OverviewPdfField::MortgageLender,
            ],
        ]);

        $mortgageLenderAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 6.1b Mortgage amount
        $mortgageAmountStep = $remortgageFundsSection->steps()->create([
            'type' => StepType::MortgageAmount,
            'question' => 'Please enter the amount of the new mortgage:',
            'help_text' => 'Please confirm the full amount of your mortgage, excluding any cashback amount. The amount of the mortgage is the total sum of money that you are borrowing from the mortgage lender to finance the remortgage of the property.',
        ]);

        $mortgageAmountAnswer = $mortgageAmountStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Mortgage amount',
                'pdfFormFieldName' => OverviewPdfField::Price,
            ],
        ]);

        $mortgageAmountAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        $remortgageFundsSection->steps()->create([
            'question' => 'Please enter the details of the mortgage broker:',
            'help_text' => 'A mortgage broker is a professional who helps borrowers find suitable mortgage deals from various lenders. They offer advice and support throughout the mortgage application process. Kindly provide the required information to ensure a smooth mortgage application. If you have any questions or need further clarification, feel free to ask your conveyancer or solicitor for assistance.',
            'type' => StepType::MortgageBroker,
        ]);

        // 6.2 Buyers using savings
        $usingSavingsToRemortgageStep = $remortgageFundsSection->steps()->create([
            'question' => 'Are any of the buyers using savings to complete the remortgage?',
            'help_text' => 'This information is vital for the conveyancing process, as it helps your solicitor or conveyancer understand the source of funds being used to complete the remortgage. It also ensures compliance with money laundering regulations and provides transparency regarding the financing of the property acquisition. If any of the remortgagers are using savings or other sources of funds, it is essential to disclose this information to facilitate a smooth and legally compliant transaction.',
        ]);

        $usingSavingsToRemortgageAnswer = $usingSavingsToRemortgageStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'], // Show 6.2a
                    ['value' => 'No'], // Continue to 6.3
                ],
            ],
        ]);

        $usingSavingsToRemortgageAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 6.2a Buyers savings amount
        $savingsAmountStep = $remortgageFundsSection->steps()->create([
            'question' => 'Please enter the amount of the savings from:',
            'help_text' => 'This information is crucial for the conveyancing process, as it helps your solicitor or conveyancer understand the overall financial arrangement for the property acquisition. It also ensures compliance with money laundering regulations and provides transparency regarding the financing of the remortgage. By disclosing the amount of savings, you help facilitate a smooth and legally compliant transaction.',
            'repeatable_answer_id' => $this->globalMortgagerQuantityAnswer->id,
            'type' => StepType::SavingsAmount,
        ]);

        $savingsAmountStep->conditions()->create([
            'answer_id' => $usingSavingsToRemortgageAnswer->id, // 6.2
            'selected_value' => 'Yes',
        ]);
        // End ---

        // 6.3 Buyers using savings
        $nonRemortgagerContributorsStep = $remortgageFundsSection->steps()->create([
            'question' => 'Is any other person who is NOT a remortgager contributing to the purchase price?',
            'help_text' => 'Please inform us if any person who is not listed as a remortgager is contributing to the re price of the property. This could include financial assistance from family members, friends, or other third parties. It is important to disclose this information to ensure transparency and compliance with legal and financial regulations. If there are any such contributors you will need to provide their details and the amount they are contributing. This helps ensure a smooth and accurate conveyancing process and allows us to handle the transaction appropriately.',
        ]);

        $nonRemortgagerContributorsAnswer = $nonRemortgagerContributorsStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'],
                    ['value' => 'No'],
                ],
            ],
        ]);

        $nonRemortgagerContributorsAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 6.4 Confirm contribution method step
        $moneyContributionMethodStep = $remortgageFundsSection->steps()->create([
            'question' => 'Please confirm how the money is being contributed:',
            'help_text' => '<p><strong>Loan:</strong> A loan is a financial arrangement where a person (the lender) provides money to another person (the borrower) with the understanding that the borrower will repay the money over a specific period, typically with interest. In the context of a property purchase, a loan can be obtained from a bank, financial institution, or a private lender to help finance the purchase of the property. The borrower is obligated to repay the loan according to the agreed terms and conditions.</p><p><strong>Gift:</strong>A gift refers to a voluntary transfer of money or assets from one person (the donor) to another (the recipient) without any expectation of repayment or consideration. In the context of a property purchase, a gift can be given by a family member, friend, or any other person to one of the buyers to help them with the purchase price. Unlike a loan, a gift does not require repayment.</p><p>It\'s important to note that both loans and gifts may have legal and financial implications, and it\'s essential to disclose and document them properly during the property purchase process. Always seek professional advice from your solicitor or conveyancer to ensure compliance with applicable laws and regulations.</p>',
        ]);

        $nonRemortgagerContributorsStep->conditions()->create([
            'answer_id' => $nonRemortgagerContributorsAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $moneyContributionMethodAnswer = $moneyContributionMethodStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Loan'],    // Skip to 6.5
                    ['value' => 'Gift'],    // Skip to 6.6
                    ['value' => 'Other'],   // Show hidden form, skip to section 7.0
                ],
            ],
        ]);

        $moneyContributionMethodAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        // Other means
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
            'answer_id' => $moneyContributionMethodAnswer->id,
            'selected_value' => 'Other',
        ]);

        // Other amount
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
            'answer_id' => $moneyContributionMethodAnswer->id,
            'selected_value' => 'Other',
        ]);
        // End ---

        // 6.5 Start of how many loaners step
        $loanerQuantityStep = $remortgageFundsSection->steps()->create([
            'question' => 'How many people and/or companies are loaning you money?',
            'help_text' => 'Please enter the number of people and/or companies that are loaning you money for the property remortgage. If you are receiving money from a joint account, please count it as two persons.',
        ]);

        $loanerQuantityStep->conditions()->create([
            'answer_id' => $moneyContributionMethodAnswer->id,
            'selected_value' => 'Loan',
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
        $loanerDetailsStep = $remortgageFundsSection->steps()->create([
            'type' => StepType::Loaner,
            'question' => 'Please provide details of the loaner(s):',
            'help_text' => 'Your conveyancer or solicitor will need the details of the loaner(s) to understand the terms and conditions of the loan agreement. This information helps ensure that the loan arrangements align with the requirements of the property purchase and that all parties involved are aware of their responsibilities. By providing the loaner(s) details, the conveyancer can verify the source of the funds and accurately document the financial transactions related to the property remortgage.',
            'repeatable_answer_id' => $loanerQuantityAnswer->id,
        ]);

        $nonRemortgagerContributorsStep->conditions()->create([
            'answer_id' => $nonRemortgagerContributorsAnswer->id,
            'selected_value' => 'Yes',
            'type' => ConditionType::AND,
        ]);

        $loanerDetailsStep->conditions()->create([
            'answer_id' => $moneyContributionMethodAnswer->id,
            'selected_value' => 'Loan',
            'type' => ConditionType::AND,
        ]);
        // End ---

        // 6.6 Start giftor quantity step
        $giftorQuantityStep = $remortgageFundsSection->steps()->create([
            'question' => 'How many people are gifting you money? Please note that these people will be invited to ProConvey to complete ID and AML checks.?',
            'help_text' => 'Your conveyancer or solicitor will need to know how many people are gifting you money to understand the financial arrangement related to the property remortgage. When indicating the number of people gifting you money, please keep in mind that money gifted from a joint account will be considered as two individuals for the purpose of documentation. This means that if multiple individuals are contributing to the gift from a joint account, each person will be counted separately. Your conveyancer or solicitor will use this information to ensure accurate reporting and compliance with legal requirements.',
        ]);

        $nonRemortgagerContributorsStep->conditions()->create([
            'answer_id' => $nonRemortgagerContributorsAnswer->id,
            'selected_value' => 'Yes',
            'type' => ConditionType::AND,
        ]);

        $giftorQuantityStep->conditions()->create([
            'answer_id' => $moneyContributionMethodAnswer->id,
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

        // 6.6a Start giftor details section (REPEATABLE)
        $giftorDetailsStep = $remortgageFundsSection->steps()->create([
            'type' => StepType::RemortgageGiftor,
            'question' => 'Please provide details of the giftor(s):',
            'help_text' => 'Your conveyancer or solicitor will need details of the giftor(s) to comply with anti-money laundering (AML) regulations and to verify the source of funds for the property remortgage. They are required to conduct due diligence on all parties involved in the transaction, including the giftor(s), to ensure that there are no illegal or fraudulent activities associated with the funds being used for the remortgage. By obtaining the details of the giftor(s), your conveyancer or solicitor can perform necessary checks and ensure that the funds are legitimate and legally obtained, providing a secure and transparent property transaction process.',
            'repeatable_answer_id' => $giftorQuantityAnswer->id,
        ]);

        $giftorDetailsStep->conditions()->create([
            'answer_id' => $moneyContributionMethodAnswer->id,
            'selected_value' => 'Gift',
        ]);
        // End ---
    }

    public function theSendingMoneyToYou(Form $form)
    {
        // 7.0 Sending Money to You section
        $sendingMoneySection = $form->sections()->create([
            'name' => 'Sending Money to You',
        ]);

        // 7.1 Buyers bank account details step
        $buyersBankDetailsStep = $sendingMoneySection->steps()->create([
            'question' => "Please provide the remortgagers' bank account details:",
            'help_text' => 'Your conveyancer or solicitor may need your bank details to facilitate the deposit transfer, arrange for mortgage payments, process completion funds, and handle any refunds or overpayments during the property purchase process. Rest assured that your information will be handled securely and only shared with authorized parties.',
        ]);

        // Account name
        $buyerAccountNameAnswer = $buyersBankDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Account name',
                'pdfFormFieldName' => OverviewPdfField::BuyerAccountName,
            ],
        ]);

        $buyerAccountNameAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        // Sort code
        $buyerSortCodeAnswer = $buyersBankDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Sort code',
                'pdfFormFieldName' => OverviewPdfField::BuyerSortCode,
            ],
        ]);

        $buyerSortCodeAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        // Account number
        $buyerAccountNumberAnswer = $buyersBankDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Account number',
                'pdfFormFieldName' => OverviewPdfField::BuyerAccountNumber,
            ],
        ]);

        $buyerAccountNumberAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---
    }

    public function theMortgageLoansAndCharges(Form $form)
    {
        // 8.0 Mortgages, loans & charge section
        $mortgageAndLoansSection = $form->sections()->create([
            'name' => 'Mortgages, Loans & Charges',
        ]);

        // 8.1 Mortgages or loans against property
        $mortgageOrLoansInPlaceStep = $mortgageAndLoansSection->steps()->create([
            'question' => 'Are there any mortgages, charges or loans secured against the property?',
            'help_text' => 'Please provide information regarding any mortgages, charges, or loans secured against the property. It is important to disclose this information as your conveyancer or solicitor will handle the repayment of all mortgages or charges on the day of the remortgage. Please ensure the accuracy of the provided information, as it will help facilitate a seamless repayment process. Rest assured that all information will be treated confidentially.',
        ]);

        $mortgageOrLoansInPlaceAnswer = $mortgageOrLoansInPlaceStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'], // Continue to 8.2
                    ['value' => 'No'], // Hide remaining steps, end form
                ],
            ],
        ]);

        $mortgageOrLoansInPlaceAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 8.2 Outstanding mortgages quantity
        $outstandingMortgageQuantityStep = $mortgageAndLoansSection->steps()->create([
            'question' => 'How many mortgages, charges or loans are secured against the property?',
            'help_text' => 'Please indicate the number of mortgages, charges, or loans secured against the property. In most cases, there is typically only one mortgage, charge, or loan associated with the property. If there are additional mortgages, charges, or loans, please provide the accurate count. This information is crucial for ensuring proper legal documentation and a smooth transaction process.',
        ]);

        $outstandingMortgageQuantityStep->conditions()->create([
            'answer_id' => $mortgageOrLoansInPlaceAnswer->id, // 8.1
            'selected_value' => 'Yes',
        ]);

        $outstandingMortgageQuantityAnswer = $outstandingMortgageQuantityStep->answers()->create([
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

        $outstandingMortgageQuantityAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 8.3 Outstanding mortgage details (REPEATABLE, based on 8.2)
        $outstandingMortgageDetailsStep = $mortgageAndLoansSection->steps()->create([
            'type' => StepType::MortgageChargeLoan,
            'question' => 'Please enter the details of each mortgage, charge or loan:',
            'help_text' => 'Please provide the details of each mortgage, charge, or loan associated with the property. Your lawyer  will utilise this information to contact the beneficiary of each mortgage, charge, or loan to determine the final redemption figure that needs to be paid. Your cooperation in providing accurate and up-to-date information is crucial for a smooth and successful transaction. Rest assured that all information will be handled securely and confidentially.',
            'repeatable_answer_id' => $outstandingMortgageQuantityAnswer->id,
        ]);

        $outstandingMortgageDetailsStep->conditions()->create([
            'answer_id' => $mortgageOrLoansInPlaceAnswer->id, // 8.1
            'selected_value' => 'Yes',
        ]);
    }
}
