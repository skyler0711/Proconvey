<?php

namespace Database\Seeders\Forms\Sale;

use App\Enums\AnswerType;
use App\Enums\ConditionType;
use App\Enums\FormGroup;
use App\Enums\FormType;
use App\Enums\PropertyType;
use App\Models\Answer;
use App\Models\Form;
use App\Models\Section;
use App\Models\Step;
use App\Models\ValidationRule;
use Illuminate\Database\Seeder;

class ProtocolForm_TA7_LeaseholdInformation extends Seeder
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
                'name' => 'TA7: Leasehold Information',
                'group' => FormGroup::Protocol,
                'description' => 'Leasehold information specific to the property',
                'ta_form_template' => FormType::TA7LeaseholdInformation,
                'order_number' => 6,
                'signature_coords' => [0.1, 0.115],
                'current_date_field' => ['date1', 'date2', 'date3', 'date4'],
                'type' => PropertyType::Sale,
            ])
            ->create();

        $answerId = Answer::whereHas('step', function ($query) {
            $query->where('question', 'Is the property for sale a freehold or leasehold?');
        })->first()->id;

        $form->conditions()->create([
            'answer_id' => $answerId,
            'selected_value' => 'Leasehold',
            'type' => ConditionType::OR,
        ]);
        $form->conditions()->create([
            'answer_id' => $answerId,
            'selected_value' => 'Shared ownership',
            'type' => ConditionType::OR,
        ]);

        $this->theProperty($form);
        $this->ownershipAndManagement($form);
        $this->relevantdocuments($form);
        $this->contactDetails($form);
        $this->maintenanceAndServiceCharges($form);
        $this->notices($form);
        $this->consents($form);
        $this->complaints($form);
        $this->alterations($form);
        $this->enfranchisement($form);
        $this->buildingSafety($form);
    }

    protected function theProperty(Form $form)
    {
        // Bondaries Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'The Property',
                ])
                ->make()
                ->toArray()
        );

        // Type of Leasehold
        $typeOfLeaseholdStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'What type of leasehold property does the seller own?',
                    'help_text' => 'FLAT: a long tenancy where the purchaser owns the right of occupation and to use of the flat for a long period established the term of the lease. SHARED OWNERSHIP: when a purchaser takes out a mortgage on a share of a property and pays rent to a landlord on the remaining share. LONG LEASEHOLD HOUSE: long-term tenancy when a purchaser owns time to occupy the property but does not own the building.',
                ])
                ->make()
                ->toArray()
        );

        $answerTypeOfLeasehold = $typeOfLeaseholdStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Flat', 'pdfFormFieldName' => '1.1_flat'],
                            ['value' => 'Shared Ownership', 'pdfFormFieldName' => '1.1_shared_ownership'],
                            ['value' => 'Long Leasehold House', 'pdfFormFieldName' => '1.1_long_leasehold_house'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTypeOfLeasehold->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Leasehold

        // Seller Pay Rent
        $sellerPayRentStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller pay rent for the property?',
                    'help_text' => 'Rent is a fixed amount of money that the sellers pays regularly for the use of the property.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerPayRent = $sellerPayRentStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '1.2_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '1.2_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerPayRent->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Seller Pay Rent

        // Yearly Rent
        $yearlyRentStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'How much rent is due each year?',
                    'help_text' => 'The annual rent is the rent payable by the tenant to the landlord during each respective year of the term beginning with the initial rent. This information should be on the sellers tenancy agreement.',
                ])
                ->make()
                ->toArray()
        );

        $answerYearlyRent = $yearlyRentStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Price',
                        'placeholder' => 'e.g. £3500',
                        'pdfFormFieldName' => '1.2a_rent_per_year',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerYearlyRent->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $yearlyRentStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $yearlyRentStep->id,
            'answer_id' => $answerSellerPayRent->id,
            'selected_value' => 'Yes',
        ]);
        // End of Yearly Rent

        // Rent payment
        $rentPaymentStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'How regularly is the rent paid?',
                    'help_text' => 'We need to know the frequency of your rent payments. You should be able to find this information on your tenancy agreement.',
                ])
                ->make()
                ->toArray()
        );

        $answerRentPayment = $rentPaymentStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Monthly', 'pdfFormFieldName' => '1.2b_rent_paid_regularly'],
                            ['value' => 'Quarterly', 'pdfFormFieldName' => '1.2b_rent_paid_regularly'],
                            ['value' => 'Annually', 'pdfFormFieldName' => '1.2b_rent_paid_regularly'],
                            ['value' => 'Other', 'pdfFormFieldName' => '1.2b_rent_paid_regularly'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerRentPayment->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $rentPaymentStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $rentPaymentStep->id,
            'answer_id' => $answerSellerPayRent->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextRentPayment = $rentPaymentStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. fortnightly',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextRentPayment->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextRentPayment->id,
            'answer_id' => $answerRentPayment->id,
            'selected_value' => 'Other',
        ]);

        $answerTextRentPayment->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Rent payment

        // Rent subject to increase
        $rentSubjectToIncreaseStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the rent subject to increase?',
                    'help_text' => 'We need to know whether the rent is subject to any increases. This information can usually be found on the sellers tenancy agreement.',
                ])
                ->make()
                ->toArray()
        );

        $answerRentSubjectToIncrease = $rentSubjectToIncreaseStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '1.2c_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '1.2c_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerRentSubjectToIncrease->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $rentSubjectToIncreaseStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $yearlyRentStep->id,
            'answer_id' => $answerSellerPayRent->id,
            'selected_value' => 'Yes',
        ]);
        // End of Rent subject to increase

        // Rent increase frequency
        $rentIncreaseFrequencyStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'How often is the rent revised?',
                    'help_text' => 'We need to know the frequency of the rent revisions. This information can usually be found on the sellers tenancy agreement.',
                ])
                ->make()
                ->toArray()
        );

        $answerRentIncreaseFrequency = $rentIncreaseFrequencyStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Weekly', 'pdfFormFieldName' => '1.2d_rent_revised'],
                            ['value' => 'Monthly', 'pdfFormFieldName' => '1.2d_rent_revised'],
                            ['value' => 'Annually', 'pdfFormFieldName' => '1.2d_rent_revised'],
                            ['value' => 'Other', 'pdfFormFieldName' => '1.2d_rent_revised'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerRentIncreaseFrequency->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $rentIncreaseFrequencyStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $rentIncreaseFrequencyStep->id,
            'answer_id' => $answerRentSubjectToIncrease->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextRentIncreaseFrequency = $rentIncreaseFrequencyStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Textarea,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. Fortnightly',
                        'pdfFormFieldName' => '1.2d_increase_calculation',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextRentIncreaseFrequency->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextRentIncreaseFrequency->id,
            'answer_id' => $answerRentIncreaseFrequency->id,
            'selected_value' => 'Other',
        ]);

        $answerTextRentIncreaseFrequency->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Rent increase frequency

        // Rent increase calculation
        $rentIncreaseCalculationStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'How is the increase calculated?',
                    'help_text' => 'We need to know how the rent increase is calculated. This information can usually be found on the sellers tenancy agreement.',
                ])
                ->make()
                ->toArray()
        );

        $answerTextIncreaseCalculation = $rentIncreaseCalculationStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. Set figure, doubling, in line with Retail Price Index, Consumer Price Index, etc.',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $rentIncreaseCalculationStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $rentIncreaseCalculationStep->id,
            'answer_id' => $answerRentSubjectToIncrease->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextIncreaseCalculation->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Rent increase calculation
    }

    protected function ownershipAndManagement(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Ownership and management',
                ])
                ->make()
                ->toArray()
        );

        // Ownership of the freehold
        $ownershipOfTheFreeholdStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Who owns the freehold?',
                    'help_text' => 'The freehold can be owned by a person or company. These companies are sometimes managed by the tenants.',
                ])
                ->make()
                ->toArray()
        );

        $answerOwnershipOfTheFreehold = $ownershipOfTheFreeholdStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'A person or company that is not controlled by the tenants', 'pdfFormFieldName' => '2.1a_yes'],
                            ['value' => 'A person or company that the tenants control', 'pdfFormFieldName' => '2.1b_yes'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerOwnershipOfTheFreehold->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Ownership of the freehold

        // Headlease
        $headleaseStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there a headlease?',
                    'help_text' => 'A headlease is the primary lease that is signed between a tenant and a property manager. The tenant, or head lessee, is contractually responsible for the terms of the lease.',
                ])
                ->make()
                ->toArray()
        );

        $answerHeadlease = $headleaseStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '2.2a_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '2.2a_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerHeadlease->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Headlease

        // Headlease Person or Company
        $headleasePersonOrCompanyStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the head leaseholder a person or company that is controlled by the tenants?',
                    'help_text' => 'The head leaseholder can be a person or a company. These companies are sometimes managed by the tenants.',
                ])
                ->make()
                ->toArray()
        );

        $answerHeadleasePersonOrCompany = $headleasePersonOrCompanyStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '2.2ab_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '2.2ab_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerHeadleasePersonOrCompany->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $headleasePersonOrCompanyStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $headleasePersonOrCompanyStep->id,
            'answer_id' => $answerHeadlease->id,
            'selected_value' => 'Yes',
        ]);
        // End of Headlease Person or Company

        // Management of the building
        $managementOfTheBuildingStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Who is responsible for managing the building?',
                    'help_text' => 'Normally, the landlord or freeholder will be responsible for the overall management of the building. However, leaseholders can also choose to manage the building themselves in a Right to Manage. Alternatively, it has become increasingly common for landlords to use a managing agent to manage and maintain the building. Managing agents take directions from the landlord and can bring an organised, professional approach to the managing of a building. ',
                ])
                ->make()
                ->toArray()
        );

        $answerManagementOfTheBuilding = $managementOfTheBuildingStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'The Freeholder', 'pdfFormFieldName' => '2.3a_yes'],
                            ['value' => 'The head leaseholder', 'pdfFormFieldName' => '2.3b_yes'],
                            ['value' => 'A management company named in the lease of the property', 'pdfFormFieldName' => '2.3c_yes'],
                            ['value' => 'A Right to Manage company set up by the tenants under statutory rights', 'pdfFormFieldName' => '2.3d_yes'],
                            ['value' => 'Other', 'pdfFormFieldName' => '2.3e_yes'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerManagementOfTheBuilding->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextManagementOfTheBuilding = $managementOfTheBuildingStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'placeholder' => 'Please, provide details',
                        'pdfFormFieldName' => '2.3e_other',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextManagementOfTheBuilding->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextManagementOfTheBuilding->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextManagementOfTheBuilding->id,
            'answer_id' => $answerManagementOfTheBuilding->id,
            'selected_value' => 'Other',
        ]);
        // End of Management of the building

        // Struck off companies house
        $struckOffCompaniesHouseStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has any tenants’ management company been dissolved or struck off the register at Companies House?',
                    'help_text' => 'The seller should provide details of any tenants management companies that have been dissolved or struck off the register at Companies House.',
                ])
                ->make()
                ->toArray()
        );

        $answerStruckOffCompaniesHouse = $struckOffCompaniesHouseStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '2.4_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '2.4_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerStruckOffCompaniesHouse->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Struck off companies house

        // Management
        $managementStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the landlord, tenants’ management company or Right to Manage company employ a managing agent to collect rent or manage the building?',
                    'help_text' => 'The seller should advise whether the managing agent has been employed to collect rent or manage the building.',
                ])
                ->make()
                ->toArray()
        );

        $answerManagement = $managementStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '2.5_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '2.5_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerManagement->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Management
    }

    protected function relevantDocuments(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Relevant Documents',
                ])
                ->make()
                ->toArray()
        );

        // Copy of the lease
        $copyOfTheLeaseStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the lease and any supplemental deeds:',
                    'help_text' => 'The seller should provide a copy of the lease and any supplemental deeds.',
                ])
                ->make()
                ->toArray()
        );

        $answerCopyOfTheLease = $copyOfTheLeaseStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '3.1a',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCopyOfTheLease->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End copy of the lease

        // Regulations made by the landlords or tenants
        $regulationsMadeByTheLandlordsOrTenantsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of any regulations made by the landlord or by the tenants management company additional to those in the lease:',
                    'help_text' => 'The seller should provide a copy of any regulations made by the landlord or by the tenants management company.',
                ])
                ->make()
                ->toArray()
        );

        $answerRegulationsMadeByTheLandlordsOrTenants = $regulationsMadeByTheLandlordsOrTenantsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '3.1b',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerRegulationsMadeByTheLandlordsOrTenants->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Regulations made by the landlords or tenants

        // Correspondence between the landlord and tenants
        $correspondenceBetweenTheLandlordAndTenantsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of any correspondence from the landlord, the management company and the managing agent:',
                    'help_text' => 'The seller should provide a copy of any correspondence from the landlord, the management company and the managing agent.',
                ])
                ->make()
                ->toArray()
        );

        $correspondenceBetweenTheLandlordAndTenantAnswer = $correspondenceBetweenTheLandlordAndTenantsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '3.2',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $correspondenceBetweenTheLandlordAndTenantAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Correspondence between the landlord and tenants

        // Copy of Invoices and Demands
        $copyOfInvoicesAndDemandsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of any invoices or demands and any statements and receipts for the payment of maintenance or service charges for the last three years:',
                    'help_text' => 'The seller should provide a copy of any invoice, demands, statements or receipts for the payment of maintenance or service charges for the last three years. Service charges are charges made for maintenance on a property',
                ])
                ->make()
                ->toArray()
        );

        $answerCopyOfInvoicesAndDemands = $copyOfInvoicesAndDemandsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '3.3a',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCopyOfInvoicesAndDemands->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Copy of Invoices and Demands

        // Copy of ground rent for the last 3 years
        $copyOfGroundRentForTheLast3YearsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of any invoices or demands and any statements and receipts for the payment of ground rent for the last three years:',
                    'help_text' => 'The seller should provide a copy of any invoice, demands, statements or receipts for the payment of maintenance or ground rent for the last three years. Ground rent is rent paid under the terms of a lease by the owner of a building to the owner of the land on which it is built.',
                ])
                ->make()
                ->toArray()
        );

        $answerCopyOfGroundRentForTheLast3Years = $copyOfGroundRentForTheLast3YearsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '3.3b',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCopyOfGroundRentForTheLast3Years->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Copy of ground rent for the last 3 years

        // Responsibility for the buildings insurance
        $responsibilityForTheBuildingsInsuranceStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Who is responsible for arranging the buildings insurance on the property?',
                    'help_text' => 'Buildings insurance on a leasehold property is typically, but not always, dealt with by the landlord or the management company.',
                ])
                ->make()
                ->toArray()
        );

        $answerResponsibilityForTheBuildingsInsurance = $responsibilityForTheBuildingsInsuranceStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Seller'],
                            ['value' => 'Management Company'],
                            ['value' => 'Landlord'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerResponsibilityForTheBuildingsInsurance->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Responsibility for the buildings insurance

        // Building Insurance Premium
        $buildingInsurancePremiumStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the buildings insurance arranged by the seller and a receipt for payment of the last premium:',
                    'help_text' => 'The seller should provide a copy of any invoice, demands, statements or receipts for the payment of maintenance or service charges for the last three years.',
                ])
                ->make()
                ->toArray()
        );

        $answerBuildingInsurancePremium = $buildingInsurancePremiumStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '3.4a',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerBuildingInsurancePremium->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Building Insurance Premium

        // Building Insurance Receipt for current year
        $buildingInsuranceReceiptForCurrentYearStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the buildings insurance arranged by the landlord or management company and the schedule for the current year:',
                    'help_text' => 'The seller should provide a copy of any invoice, demands, statements or receipts for the payment of maintenance or service charges for the last three years.',
                ])
                ->make()
                ->toArray()
        );

        $answerBuildingInsuranceReceiptForCurrentYear = $buildingInsuranceReceiptForCurrentYearStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '3.4b',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerBuildingInsuranceReceiptForCurrentYear->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // The Landlord
        $theLandlordStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the landlord:',
                    'help_text' => 'Landlords are sometimes a company or management company controlled by the tenants. Right to Manage (RTM) gives leaseholders the ability to take over the management of their block.',
                ])
                ->make()
                ->toArray()
        );

        $answerTheLandlord = $theLandlordStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'A company controlled by the tenants'],
                            ['value' => 'A tenants’ management company'],
                            ['value' => 'A Right to Manage company managing the building'],
                            ['value' => 'None of the above'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTheLandlord->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of The Landlord

        //Memorandum and Articles of Association
        $memorandumAndArticlesOfAssociationStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the Memorandum and Articles of Association:',
                    'help_text' => '"MEMORANDUM OF ASSOCIATION - a legal statement signed by all initial shareholders or guarantors agreeing to form the company.
                    ARTICLES OF ASSOCIATION - a written rules about running the company agreed by the shareholders or guarantors, directors and the company secretary."',
                ])
                ->make()
                ->toArray()
        );

        $answerMemorandumAndArticlesOfAssociation = $memorandumAndArticlesOfAssociationStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '3.5a',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerMemorandumAndArticlesOfAssociation->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $memorandumAndArticlesOfAssociationStep->conditions()->create([
            'conditionable_type' => 'step',
            'answer_id' => $answerTheLandlord->id,
            'selected_value' => 'A company controlled by the tenants',
            'type' => ConditionType::OR,
        ]);
        $memorandumAndArticlesOfAssociationStep->conditions()->create([
            'conditionable_type' => 'step',
            'answer_id' => $answerTheLandlord->id,
            'selected_value' => 'A tenants’ management company',
            'type' => ConditionType::OR,
        ]);
        $memorandumAndArticlesOfAssociationStep->conditions()->create([
            'conditionable_type' => 'step',
            'answer_id' => $answerTheLandlord->id,
            'selected_value' => 'A Right to Manage company managing the building',
            'type' => ConditionType::OR,
        ]);
        // End of Memorandum and Articles of Association

        // Share or Membership Certificate
        $shareOrMembershipCertificateStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the share or membership certificate:',
                    'help_text' => 'A share certificate is a certificate issued by a company certifying that on the date the certificate is issued a certain person is the registered owner of shares in the company. A membership certificate is a similar document, which a company issue to their members to show percentage of ownership.',
                ])
                ->make()
                ->toArray()
        );

        $answerShareOrMembershipCertificate = $shareOrMembershipCertificateStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '3.5b',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerShareOrMembershipCertificate->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $shareOrMembershipCertificateStep->conditions()->create([
            'conditionable_type' => 'step',
            'answer_id' => $answerTheLandlord->id,
            'selected_value' => 'A company controlled by the tenants',
            'type' => ConditionType::OR,
        ]);
        $shareOrMembershipCertificateStep->conditions()->create([
            'conditionable_type' => 'step',
            'answer_id' => $answerTheLandlord->id,
            'selected_value' => 'A tenants’ management company',
            'type' => ConditionType::OR,
        ]);
        $shareOrMembershipCertificateStep->conditions()->create([
            'conditionable_type' => 'step',
            'answer_id' => $answerTheLandlord->id,
            'selected_value' => 'A Right to Manage company managing the building',
            'type' => ConditionType::OR,
        ]);
        // End of Share or Membership Certificate

        // Company Accounts
        $companyAccountsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the company accounts for the past three years:',
                    'help_text' => 'The seller should provide a copy of the company accounts for the last three years.',
                ])
                ->make()
                ->toArray()
        );

        $answerCompanyAccounts = $companyAccountsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '3.5c',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCompanyAccounts->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $companyAccountsStep->conditions()->create([
            'conditionable_type' => 'step',
            'answer_id' => $answerTheLandlord->id,
            'selected_value' => 'A company controlled by the tenants',
            'type' => ConditionType::OR,
        ]);
        $companyAccountsStep->conditions()->create([
            'conditionable_type' => 'step',
            'answer_id' => $answerTheLandlord->id,
            'selected_value' => 'A tenants’ management company',
            'type' => ConditionType::OR,
        ]);
        $companyAccountsStep->conditions()->create([
            'conditionable_type' => 'step',
            'answer_id' => $answerTheLandlord->id,
            'selected_value' => 'A Right to Manage company managing the building',
            'type' => ConditionType::OR,
        ]);
        // End of Company Accounts
    }

    protected function contactDetails(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Contact Details',
                ])
                ->make()
                ->toArray()
        );

        // Contact Details
        $contactDetailsLandlordStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide contact details for the following, where appropriate:',
                    'sub_heading' => 'Landlord',
                    'help_text' => 'The landlord may be, for example, a private individual, a housing association, or a management company owned by the residents.',
                ])
                ->make()
                ->toArray()
        );

        $answerLandlordContactDetailsFullName = $contactDetailsLandlordStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Name',
                        'placeholder' => 'Enter name',
                        'pdfFormFieldName' => '4.1_landlord_name',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerLandlordContactDetailsFullName->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerLandlordContactDetailsAddress = $contactDetailsLandlordStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Address,
                    'details' => [
                        'label' => 'Address',
                        'placeholder' => 'Enter address',
                        'pdfFormFieldName' => '4.1_landlord_address_line_1',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerLandlordContactDetailsAddress->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerLandlordContactDetailsPhone = $contactDetailsLandlordStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Phone number',
                        'placeholder' => '+44 ---- -- -- --',
                        'pdfFormFieldName' => '4.1_landlord_telephone',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerLandlordContactDetailsPhone->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerLandlordContactDetailsEmailAddress = $contactDetailsLandlordStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Email address',
                        'placeholder' => 'name@company.com',
                        'pdfFormFieldName' => '4.1_landlord_email',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerLandlordContactDetailsEmailAddress->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $isLandlordContactDetailsApplicable = $contactDetailsLandlordStep->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not applicable',
            ],
        ]);

        $answerLandlordContactDetailsPhone->conditions()->create([
            'answer_id' => $isLandlordContactDetailsApplicable->id,
            'selected_value' => 0,
        ]);

        $answerLandlordContactDetailsEmailAddress->conditions()->create([
            'answer_id' => $isLandlordContactDetailsApplicable->id,
            'selected_value' => 0,
        ]);

        $answerLandlordContactDetailsAddress->conditions()->create([
            'answer_id' => $isLandlordContactDetailsApplicable->id,
            'selected_value' => 0,
        ]);

        $answerLandlordContactDetailsFullName->conditions()->create([
            'answer_id' => $isLandlordContactDetailsApplicable->id,
            'selected_value' => 0,
        ]);

        // Management
        $contactDetailsManagementStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide contact details for the following, where appropriate:',
                    'sub_heading' => 'Management or Right to Manage Company',
                    'help_text' => 'The landlord may be, for example, a private individual, a housing association, or a management company owned by the residents.',
                ])
                ->make()
                ->toArray()
        );

        $answerManagementContactDetailsFullName = $contactDetailsManagementStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Full name',
                        'placeholder' => 'Enter full name',
                        'pdfFormFieldName' => '4.1_management_name',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerManagementContactDetailsFullName->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerManagementContactDetailsAddress = $contactDetailsManagementStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Address,
                    'details' => [
                        'label' => 'Address',
                        'placeholder' => 'Enter address',
                        'pdfFormFieldName' => '4.1_management_address_line_1',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerManagementContactDetailsAddress->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerManagementContactDetailsEmailAddress = $contactDetailsManagementStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Email address',
                        'placeholder' => 'name@company.com',
                        'pdfFormFieldName' => '4.1_managing_agent_email',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerManagementContactDetailsEmailAddress->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerManagementContactDetailsPhone = $contactDetailsManagementStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Phone number',
                        'placeholder' => '+44 ---- -- -- --',
                        'pdfFormFieldName' => '4.1_managing_agent_telephone',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerManagementContactDetailsPhone->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Management
        $isManagementContactDetailsApplicable = $contactDetailsManagementStep->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not applicable',
            ],
        ]);

        $answerManagementContactDetailsPhone->conditions()->create([
            'answer_id' => $isManagementContactDetailsApplicable->id,
            'selected_value' => 0,
        ]);

        $answerManagementContactDetailsEmailAddress->conditions()->create([
            'answer_id' => $isManagementContactDetailsApplicable->id,
            'selected_value' => 0,
        ]);

        $answerManagementContactDetailsAddress->conditions()->create([
            'answer_id' => $isManagementContactDetailsApplicable->id,
            'selected_value' => 0,
        ]);

        $answerManagementContactDetailsFullName->conditions()->create([
            'answer_id' => $isManagementContactDetailsApplicable->id,
            'selected_value' => 0,
        ]);

        // Company

        $contactDetailsCompanyStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide contact details for the following, where appropriate:',
                    'sub_heading' => 'Management Agent',
                    'help_text' => 'The landlord may be, for example, a private individual, a housing association, or a management company owned by the residents.',
                ])
                ->make()
                ->toArray()
        );

        $answerCompanyContactDetailsFullName = $contactDetailsCompanyStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Name',
                        'placeholder' => 'Enter name',
                        'pdfFormFieldName' => '4.1_managing_agent_name',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCompanyContactDetailsFullName->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerCompanyContactDetailsAddress = $contactDetailsCompanyStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Address,
                    'details' => [
                        'label' => 'Address',
                        'placeholder' => 'Enter address',
                        'pdfFormFieldName' => '4.1_managing_agent_address_line_1',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCompanyContactDetailsAddress->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerCompanyContactDetailsEmailAddress = $contactDetailsCompanyStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Email address',
                        'placeholder' => 'name@company.com',
                        'pdfFormFieldName' => '4.1_managing_agent_email',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCompanyContactDetailsEmailAddress->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerCompanyContactDetailsPhone = $contactDetailsCompanyStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Phone number',
                        'placeholder' => '+44 ---- -- -- --',
                        'pdfFormFieldName' => '4.1_managing_agent_telephone',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCompanyContactDetailsPhone->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $isLandlordContactDetailsApplicable = $contactDetailsCompanyStep->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not applicable',
            ],
        ]);

        $answerCompanyContactDetailsPhone->conditions()->create([
            'answer_id' => $isLandlordContactDetailsApplicable->id,
            'selected_value' => 0,
        ]);

        $answerCompanyContactDetailsEmailAddress->conditions()->create([
            'answer_id' => $isLandlordContactDetailsApplicable->id,
            'selected_value' => 0,
        ]);

        $answerCompanyContactDetailsAddress->conditions()->create([
            'answer_id' => $isLandlordContactDetailsApplicable->id,
            'selected_value' => 0,
        ]);

        $answerCompanyContactDetailsFullName->conditions()->create([
            'answer_id' => $isLandlordContactDetailsApplicable->id,
            'selected_value' => 0,
        ]);
    }

    protected function maintenanceAndServiceCharges(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Maintenance and Service Charges',
                ])
                ->make()
                ->toArray()
        );

        // What year was the exterior of the building last decorated?
        $exteriorDecoratedStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'In what year was the outside of the building last decorated?',
                    'help_text' => 'There’s no fixed timescale in which the outside of the building should be redecorated. However, many landlords choose to redecorate around once every five years, and generally at the end of long tenancies.',
                ])
                ->make()
                ->toArray()
        );

        $exteriorDecoratedYearAnswer = $exteriorDecoratedStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Year',
                        'placeholder' => 'Enter year',
                        'pdfFormFieldName' => '5.2_year',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $exteriorDecoratedYearAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $isExteriorDecoratedStepYearKnown = $exteriorDecoratedStep->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
                'pdfFormFieldName' => '5.2_not_known',
            ],
        ]);

        $exteriorDecoratedYearAnswer->conditions()->create([
            'answer_id' => $isExteriorDecoratedStepYearKnown->id,
            'selected_value' => 0,
        ]);
        // End of What year was the exterior of the building last decorated?

        // What year was the interior communal parts last decorated?
        $interiorDecoratedStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'In what year were any internal communal parts last decorated?',
                    'help_text' => 'There’s no fixed timescale in which the inside of the building should be redecorated. However, many landlords choose to redecorate around once every five years, and generally at the end of long tenancies.',
                ])
                ->make()
                ->toArray()
        );

        $interiorDecoratedYearAnswer = $interiorDecoratedStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Year',
                        'placeholder' => 'Enter year',
                        'pdfFormFieldName' => '5.3_year',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $interiorDecoratedYearAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $isInternalDecoratedStepYearKnown = $interiorDecoratedStep->answers()->create([
            'type' => AnswerType::Checkbox,
            'details' => [
                'label' => 'Not known',
                'pdfFormFieldName' => '5.3_not_known',
            ],
        ]);

        $interiorDecoratedYearAnswer->conditions()->create([
            'answer_id' => $isInternalDecoratedStepYearKnown->id,
            'selected_value' => 0,
        ]);

        // End of What year was the interior communal parts last decorated?

        // Seller contribution
        $sellerContributionStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller contribute to the cost of maintaining the building?',
                    'help_text' => 'There might be lease expenses that are used to contribute towards the cost of maintaining the building.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerContributionStep = $sellerContributionStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '5.4_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '5.4_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerContributionStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Seller contribution

        // Seller aware of upcoming service charge
        $sellerAwareStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller know of any expense likely to be shown in the service charge accounts within the next three years?',
                    'help_text' => 'The seller should advise if they are aware of any upcoming costs not usually incurred annually. E.g the cost of redecoration of outside or communal areas not usually incurred annually.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareStep = $sellerAwareStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '5.5_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '5.5_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareStep = $sellerAwareStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the building is due to be fully repainted in 2 years time and the cost is split between the owners',
                        'pdfFormFieldName' => '5.5_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $sellerAwareStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $sellerAwareStep->id,
            'answer_id' => $answerSellerContributionStep->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextSellerAwareStep->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerAwareStep->id,
            'answer_id' => $answerSellerAwareStep->id,
            'selected_value' => 'Yes',
        ]);

        // End of Seller aware of upcoming service charge

        // Problems in the last 3 years regarding service charge
        $problemsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller know of any problems in the last three years regarding the level of service charges or with the management?',
                    'help_text' => 'The seller should give details of any issues regarding the level of the service charge or with the management of the property',
                ])
                ->make()
                ->toArray()
        );

        $answerProblemsStep = $problemsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '5.6_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '5.6_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextProblemsStep = $problemsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the building is due to be fully repainted in 2 years time and the cost is split between the owners',
                        'pdfFormFieldName' => '5.6_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerProblemsStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextProblemsStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $problemsStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $problemsStep->id,
            'answer_id' => $answerSellerContributionStep->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextProblemsStep->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextProblemsStep->id,
            'answer_id' => $answerProblemsStep->id,
            'selected_value' => 'Yes',
        ]);
        // End of Problems in the last 3 years regarding service charge

        // Seller service charge challenged
        $sellerChallengedStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the seller challenged the service charge or any expense in the last three years?',
                    'help_text' => 'The seller should advise whether they are aware of any issues and, where necessary, provide further details.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerChallengedStep = $sellerChallengedStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '5.7_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '5.7_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerChallengedStep = $sellerChallengedStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the building is due to be fully repainted in 2 years time and the cost is split between the owners',
                        'pdfFormFieldName' => '5.7_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerChallengedStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerChallengedStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $sellerChallengedStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $sellerChallengedStep->id,
            'answer_id' => $answerSellerContributionStep->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextSellerChallengedStep->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerChallengedStep->id,
            'answer_id' => $answerSellerChallengedStep->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller service charge challenged

        // Seller aware of cladding or other defects
        $sellerAwareDefectsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller know of the existence or suspected existence in the building of cladding or any defects that may create a building safety risk?',
                    'help_text' => 'The seller should advise whether they are aware of any issues and, where necessary, provide further details.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareDefectsStep = $sellerAwareDefectsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '5.8_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '5.8_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareDefectsStep = $sellerAwareDefectsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the building is due to be fully repainted in 2 years time and the cost is split between the owners',
                        'pdfFormFieldName' => '5.8_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareDefectsStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareDefectsStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $sellerAwareDefectsStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $sellerAwareDefectsStep->id,
            'answer_id' => $answerSellerContributionStep->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextSellerAwareDefectsStep->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerAwareDefectsStep->id,
            'answer_id' => $answerSellerAwareDefectsStep->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller aware of cladding or other defects

        // Seller aware of any difficulties
        $sellerAwareDifficultiesStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the seller aware of any difficulties encountered in collecting the service charges from other flat owners?',
                    'help_text' => 'The seller should give details of any difficulties encountered in collecting the service charges from the other flat owners.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareDifficultiesStep = $sellerAwareDifficultiesStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '5.9_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '5.9_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareDifficultiesStep = $sellerAwareDifficultiesStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the building is due to be fully repainted in 2 years time and the cost is split between the owners',
                        'pdfFormFieldName' => '5.9_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareDifficultiesStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareDifficultiesStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $sellerAwareDifficultiesStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $sellerAwareDifficultiesStep->id,
            'answer_id' => $answerSellerContributionStep->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextSellerAwareDifficultiesStep->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerAwareDifficultiesStep->id,
            'answer_id' => $answerSellerAwareDifficultiesStep->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller aware of any difficulties

        // Seller owe and service charge contributions
        $sellerOweContributionsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does the seller owe any service charges, rent, insurance premium or other financial contribution?',
                    'help_text' => 'The seller should give details of owed service charges, rent insurance premiums or financial contributions and the amount owed.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerOweContributionsStep = $sellerOweContributionsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '5.10_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '5.10_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerOweContributionsStep = $sellerOweContributionsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the building is due to be fully repainted in 2 years time and the cost is split between the owners',
                        'pdfFormFieldName' => '5.10_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerOweContributionsStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerOweContributionsStep->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $sellerOweContributionsStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $sellerOweContributionsStep->id,
            'answer_id' => $answerSellerContributionStep->id,
            'selected_value' => 'Yes',
        ]);

        $answerTextSellerOweContributionsStep->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerOweContributionsStep->id,
            'answer_id' => $answerSellerOweContributionsStep->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller owe and service charge contributions
    }

    protected function notices(Form $form)
    {
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Notices',
                ])
                ->make()
                ->toArray()
        );

        // Has the seller received a notice that the landlord wants to sell the building?
        $sellerReceivedNotice = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the seller received a notice that the landlord wants to sell the building? ',
                    'help_text' => 'A notice may be in a printed form or in the form of a letter. Section 21 of the Housing Act 1988 requires the landlord to issue notice to sell.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerRecievedNotice = $sellerReceivedNotice->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '6.1_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '6.1_no'],
                            ['value' => 'Lost', 'pdfFormFieldName' => '6.1_lost'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerUploadSellerReceivedNotice = $sellerReceivedNotice->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '6.1',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerUploadSellerReceivedNotice->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerUploadSellerReceivedNotice->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerUploadSellerReceivedNotice->id,
            'answer_id' => $answerSellerRecievedNotice->id,
            'selected_value' => 'Yes',
        ]);
        // End of Has the seller received a notice that the landlord wants to sell the building?

        // Has the seller recieved any other notice about the building?
        $sellerRecievedOtherNotice = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the seller received any other notice about the building, its use, its condition or its repair and maintenance?',
                    'help_text' => 'The seller could have received this type of notice from the landlord or the management company.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerRecievedOtherNotice = $sellerRecievedOtherNotice->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '6.2_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '6.2_no'],
                            ['value' => 'Lost', 'pdfFormFieldName' => '6.2_lost'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerUploadSellerRecievedOtherNotice = $sellerRecievedOtherNotice->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '6.2',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerRecievedOtherNotice->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerUploadSellerRecievedOtherNotice->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerUploadSellerRecievedOtherNotice->id,
            'answer_id' => $answerSellerRecievedOtherNotice->id,
            'selected_value' => 'Yes',
        ]);
        // End of Has the seller received any other notice about the building?
    }

    protected function consents(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Consents',
                ])
                ->make()
                ->toArray()
        );

        // Seller aware of any changes in the terms
        $sellerAwareOfChanges = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the seller aware of any changes in the terms of the lease or of the landlord giving any consents under the lease?',
                    'help_text' => 'A consent may be given in a formal document, a letter or orally. Landlords cannot change the lease terms without agreeing such change with the leaseholder. The reason for this is that as the lease is a contract between two parties, the leaseholder and the landlord, both parties must agree to change (referred to also as variation) of the terms of the lease.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareOfChanges = $sellerAwareOfChanges->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '7.1_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '7.1_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareOfChanges = $sellerAwareOfChanges->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the landlord added a new term in the lease stating that pets are not allowed',
                        'pdfFormFieldName' => '7.1_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareOfChanges->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareOfChanges->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareOfChanges->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerAwareOfChanges->id,
            'answer_id' => $answerSellerAwareOfChanges->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller aware of any changes in the terms

        // Upload documentations regarding change in the terms
        $uploadDocuments = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide any documentation regarding changes in the terms of the lease or of the landlord giving any consents under the lease:',
                    'help_text' => 'The seller should provide copies of any documentation regarding changes in the terms of lease or of the landlord giving consents under the lease.',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsFile = $uploadDocuments->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '7.1',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $uploadDocumentsFile->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $uploadDocuments->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $uploadDocuments->id,
            'answer_id' => $answerSellerAwareOfChanges->id,
            'selected_value' => 'Yes',
        ]);
        // End of Upload documentations regarding change in the terms
    }

    protected function complaints(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Complaints',
                ])
                ->make()
                ->toArray()
        );

        // Seller received any complaints
        $sellerReceivedComplaints = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the seller received any complaints from the landlord, the management company or any neighbour about anything the seller has or has not done? ',
                    'help_text' => 'Please provide information about any current or past complaints. This needs to include the cause of the complaint (e.g. complaints relating to noise) and any action taken to resolve matters.  ',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerReceivedComplaints = $sellerReceivedComplaints->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '8.1_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '8.1_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerReceivedComplaints = $sellerReceivedComplaints->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the landlord complained about the noise from the neighbour’s flat',
                        'pdfFormFieldName' => '8.1_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerReceivedComplaints->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerReceivedComplaints->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerReceivedComplaints->id,
            'answer_id' => $answerSellerReceivedComplaints->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller received any complaints

        // Seller complained or had cause to complain to or about the landlord
        $sellerComplainedToLandlord = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the seller complained or had cause to complain to or about the landlord, the management company, or any neighbour?',
                    'help_text' => 'Please provide information about any current or past complaints. This needs to include the cause of the complaint (e.g. complaints relating to noise) and any action taken to resolve matters.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerComplainedToLandlord = $sellerComplainedToLandlord->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '8.2_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '8.2_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerComplainedToLandlord->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerComplainedToLandlord = $sellerComplainedToLandlord->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the landlord complained about the noise from the neighbour’s flat',
                        'pdfFormFieldName' => '8.2_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerComplainedToLandlord->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerComplainedToLandlord->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerComplainedToLandlord->id,
            'answer_id' => $answerSellerComplainedToLandlord->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller complained or had cause to complain to or about the landlord
    }

    protected function alterations(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Alterations',
                ])
                ->make()
                ->toArray()
        );

        // Seller aware of any alterations
        $sellerAwareOfAlterations = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the seller aware of any alterations having been made to the property since the lease was originally granted?',
                    'help_text' => 'Since the lease was originally granted their may have been some changes or home improvements. We need to which works were undertaken.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareOfAlterations = $sellerAwareOfAlterations->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '9.1_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '9.1_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareOfAlterations->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareOfAlterations = $sellerAwareOfAlterations->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the landlord added a new term in the lease stating that pets are not allowed',
                        'pdfFormFieldName' => '9.2_details',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareOfAlterations->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerTextSellerAwareOfAlterations->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerTextSellerAwareOfAlterations->id,
            'answer_id' => $answerSellerAwareOfAlterations->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller aware of any alterations

        // Landlords consent obtained
        $landlordsConsentObtained = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Was the landlord\'s consent obtained for the alterations?',
                    'help_text' => 'This form of consent is used when a tenant requests the landlord\'s permission to make alterations to the property.',
                ])
                ->make()
                ->toArray()
        );

        $answerLandlordsConsentObtained = $landlordsConsentObtained->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '9.2_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '9.2_no'],
                            ['value' => 'Not known', 'pdfFormFieldName' => '9.2_not_known'],
                            ['value' => 'Not required', 'pdfFormFieldName' => '9.2_not_required'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerLandlordsConsentObtained->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerUploadLandlordConsentObtained = $landlordsConsentObtained->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '9.3',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerUploadLandlordConsentObtained->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $landlordsConsentObtained->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $landlordsConsentObtained->id,
            'answer_id' => $answerSellerAwareOfAlterations->id,
            'selected_value' => 'Yes',
        ]);

        $answerUploadLandlordConsentObtained->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerUploadLandlordConsentObtained->id,
            'answer_id' => $answerLandlordsConsentObtained->id,
            'selected_value' => 'Yes',
        ]);
        // End of Landlords consent obtained
    }

    protected function enfranchisement(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Enfranchisement',
                ])
                ->make()
                ->toArray()
        );

        // Seller owned the property for atleast 2 years
        $sellerOwnedPropertyForAtleast2Years = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the seller owned the property for at least two years?',
                    'help_text' => '‘Enfranchisement’ is the right of a tenant to purchase the freehold from their landlord and the right of the tenant to extend the term of the lease. For enfranchisement to happen the seller must have typically owned the lease of the house concerned for at least two years.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerOwnedPropertyForAtleast2Years = $sellerOwnedPropertyForAtleast2Years->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '10.1_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '10.1_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerOwnedPropertyForAtleast2Years->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Seller owned the property for atleast 2 years

        // Extended lease
        $extendedLease = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the seller served on the landlord a formal notice stating the seller\'s wish to buy the freehold or be granted an extended lease?',
                    'help_text' => 'An enfranchisement notice is the first piece of legal documentation on the journey towards buying the freehold or extending the lease.',
                ])
                ->make()
                ->toArray()
        );

        $answerExtendedLease = $extendedLease->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '10.2_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '10.2_no'],
                            ['value' => 'Lost', 'pdfFormFieldName' => '10.2_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerExtendedLease->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerUploadExtendedLease = $extendedLease->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '10.2',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerUploadExtendedLease->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerUploadExtendedLease->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerUploadExtendedLease->id,
            'answer_id' => $answerExtendedLease->id,
            'selected_value' => 'Yes',
        ]);
        // End of Extended lease

        // Seller aware of the service of any notice
        $sellerAwareOfServiceOfNotice = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the seller aware of the service of any notice relating to the possible collective purchase of the freehold of the building or part of it by a group of tenants?',
                    'help_text' => 'This notice, also known as an Initial Notice, is served by the leaseholders on the freeholder to exercise their rights to collective enfranchisement (buying the freehold).',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareOfServiceOfNotice = $sellerAwareOfServiceOfNotice->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '10.3_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '10.3_no'],
                            ['value' => 'Not known', 'pdfFormFieldName' => '10.3_not_known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareOfServiceOfNotice->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerUploadSellerAwareOfServiceOfNotice = $sellerAwareOfServiceOfNotice->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '10.3',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerUploadSellerAwareOfServiceOfNotice->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerUploadSellerAwareOfServiceOfNotice->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerUploadSellerAwareOfServiceOfNotice->id,
            'answer_id' => $answerSellerAwareOfServiceOfNotice->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller aware of the service of any notice

        // Is the seller aware of any response to any notice above?
        $sellerAwareOfAnyResponseToNotice = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the seller aware of any response to a notice disclosed in replies above?',
                    'help_text' => 'The freeholder is required to reply to the notices with a Counter-Notice.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareOfAnyResponseToNotice = $sellerAwareOfAnyResponseToNotice->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '10.4_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '10.4_no'],
                            ['value' => 'Lost', 'pdfFormFieldName' => '10.4_not_known'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerAwareOfAnyResponseToNotice->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerUploadSellerAwareOfAnyResponseToNotice = $sellerAwareOfAnyResponseToNotice->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '10.4',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerUploadSellerAwareOfAnyResponseToNotice->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerUploadSellerAwareOfAnyResponseToNotice->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerUploadSellerAwareOfAnyResponseToNotice->id,
            'answer_id' => $answerSellerAwareOfAnyResponseToNotice->id,
            'selected_value' => 'Yes',
        ]);
        // End of Is the seller aware of any response to any notice above?
    }

    protected function buildingSafety(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Building safety, cladding and the leaseholder deed of certificate',
                ])
                ->make()
                ->toArray()
        );

        // Remedation Works on the building
        $remedationWorksOnTheBuilding = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Have any remediation works on the building been proposed or carried out? If Yes, please provide details of the remediation works proposed and evidence of any carried out.',
                    'help_text' => 'Remediation Works are the works, measures, steps and techniques for the purpose of limiting, removing, remedying, monitoring, cleaning-up or containing the presence of Hazardous Materials in relation to the property.',
                ])
                ->make()
                ->toArray()
        );

        $answerRemedationWorksOnTheBuilding = $remedationWorksOnTheBuilding->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '11.1_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '11.1_no'],
                            ['value' => 'Not applicable', 'pdfFormFieldName' => '11.1_not_applicable'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerRemedationWorksOnTheBuilding->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerUploadRemedationWorksOnTheBuilding = $remedationWorksOnTheBuilding->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPRefix' => '11.1',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerUploadRemedationWorksOnTheBuilding->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerUploadRemedationWorksOnTheBuilding->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerUploadRemedationWorksOnTheBuilding->id,
            'answer_id' => $answerRemedationWorksOnTheBuilding->id,
            'selected_value' => 'Yes',
        ]);
        // End of Remedation Works on the building

        // Qualifying lease
        $qualifyingLease = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the lease of the property a qualifying lease?',
                    'help_text' => 'A lease is qualifying if it meets all of the following criteria: (a)it is a long lease of a single dwelling in a relevant building, (b)the tenant under the lease is liable to pay a service charge, (c)the lease was granted before 14 February 2022, and. (d)at the beginning of 14 February 2022 (“the qualifying time”)',
                ])
                ->make()
                ->toArray()
        );

        $answerQualifyingLease = $qualifyingLease->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '11.2_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '11.2_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerQualifyingLease->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of qualifying lease

        // Is the leaseholder deed of certificate
        $leaseholderDeedOfCertificate = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there a Leaseholder Deed of Certificate for the property?	Is the leaseholder deed of certificate in place?',
                    'help_text' => 'Tenants sometimes qualify for collective purchase of the freehold, which happens when a group of tenants buy the freehold of a property.',
                ])
                ->make()
                ->toArray()
        );

        $answerLeaseholderDeedOfCertificate = $leaseholderDeedOfCertificate->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '11.3_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '11.3_no'],
                            ['value' => 'Not applicable', 'pdfFormFieldName' => '11.3_not_applicable'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerLeaseholderDeedOfCertificate->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Is the leaseholder deed of certificate

        // Did the current leaseholder complete the deed of certificate
        $currentLeaseholderCompleteDeedOfCertificate = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Did the seller (the current leaseholder) complete the deed of certificate or was it completed by a previous leaseholder?',
                    'help_text' => 'The Leaseholder Deed of Certificate provides a permanent record as to how the flat was owned or occupied on 14 February 2022 (the critical reference date for the purposes of Schedule 8 of the Building Safety Act 2022) and evidences that the flat is a qualifying lease for the purposes of the Schedule 8 protections.',
                ])
                ->make()
                ->toArray()
        );

        $answerCurrentLeaseholderCompleteDeedOfCertificate = $currentLeaseholderCompleteDeedOfCertificate->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Current leaseholder', 'pdfFormFieldName' => '11.3a_current_leaseholder'],
                            ['value' => 'Previous leaseholder', 'pdfFormFieldName' => '11.3a_previous_leaseholder'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCurrentLeaseholderCompleteDeedOfCertificate->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $currentLeaseholderCompleteDeedOfCertificate->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $currentLeaseholderCompleteDeedOfCertificate->id,
            'answer_id' => $answerLeaseholderDeedOfCertificate->id,
            'selected_value' => 'Yes',
        ]);

        //End of Did the current leaseholder complete the deed of certificate

        // Upload deed of certificate
        $uploadDeedOfCertificate = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please supply a copy of the leaseholder deed of certificate and the accompanying evidence:',
                    'help_text' => 'The Leaseholder Deed of Certificate provides a permanent record as to how the flat was owned or occupied on 14 February 2022 (the critical reference date for the purposes of Schedule 8 of the Building Safety Act 2022) and evidences that the flat is a qualifying lease for the purposes of the Schedule 8 protections.',
                ])
                ->make()
                ->toArray()
        );

        $answerUploadDeedOfCertificate = $uploadDeedOfCertificate->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '11.3b',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerUploadDeedOfCertificate->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $uploadDeedOfCertificate->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $uploadDeedOfCertificate->id,
            'answer_id' => $answerLeaseholderDeedOfCertificate->id,
            'selected_value' => 'Yes',
        ]);
        // End of Upload deed of certificate

        // Notified the landlord or freeholder been notified of the intention to sell
        $notifiedTheLandlordOrFreeholderBeenNotified = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the freeholder / landlord been notified of the intention to sell?',
                    'help_text' => 'The seller should advise whether they have received any intention to sell the property from the freeholder and/or landlord.',
                ])
                ->make()
                ->toArray()
        );

        $answerNotifiedTheLandlordOrFreeholderBeenNotified = $notifiedTheLandlordOrFreeholderBeenNotified->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '11.4_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '11.4_no'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerNotifiedTheLandlordOrFreeholderBeenNotified->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Notified the landlord or freeholder been notified of the intention to sell

        // Seller received a landlords certificate
        $sellerRecievedALandlordsCertificate = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the seller received a Landlord’s Certificate and the accompanying evidence?',
                    'help_text' => 'This is a record of the inspections that have taken place and acts as proof of compliance with the 1998 regulations set out by the government.',
                ])
                ->make()
                ->toArray()
        );

        $answerSellerRecievedALandlordsCertificate = $sellerRecievedALandlordsCertificate->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes', 'pdfFormFieldName' => '11.5_yes'],
                            ['value' => 'No', 'pdfFormFieldName' => '11.5_no'],
                            ['value' => 'Not applicable'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerSellerRecievedALandlordsCertificate->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerUploadLandlordsCertificate = $sellerRecievedALandlordsCertificate->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFieldPrefix' => '11.5',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerUploadLandlordsCertificate->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerUploadLandlordsCertificate->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerUploadLandlordsCertificate->id,
            'answer_id' => $answerSellerRecievedALandlordsCertificate->id,
            'selected_value' => 'Yes',
        ]);
        // End of Seller received a landlords certificate
    }
}
