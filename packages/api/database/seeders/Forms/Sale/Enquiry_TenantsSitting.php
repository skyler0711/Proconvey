<?php

namespace Database\Seeders\Forms\Sale;

use App\Enums\AnswerType;
use App\Enums\ConditionType;
use App\Enums\FormGroup;
use App\Enums\PropertyType;
use App\Models\Answer;
use App\Models\Condition;
use App\Models\Form;
use App\Models\Section;
use App\Models\Step;
use App\Models\ValidationRule;
use Illuminate\Database\Seeder;

class Enquiry_TenantsSitting extends Seeder
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
                'name' => 'Tenancy (Sitting)',
                'description' => 'Information about the sitting tenants on the property',
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
                    'selected_value' => 'No',
                ])
                ->make()
                ->toArray()
        );

        $this->aboutTheTenants($form);
        $this->rent($form);
        $this->deposit($form);
        $this->disputesAndComplaints($form);
        $this->regulations($form);
        $this->documents($form);
        $this->additionalInformation($form);
    }

    protected function aboutTheTenants(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'About the tenants',
                ])
                ->make()
                ->toArray()
        );

        // How many
        $howManyStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'How many tenants occupy the property?',
                    'help_text' => '<p>A tenant is an individual or family who is renting the property from the owner (landlord) and residing in it. The number of tenants can vary based on the number of separate rental agreements or families occupying the property. If there is only one rental agreement with one family living in the property, then there is one tenant. However, if there are multiple rental agreements or families residing in different parts of the property, then the number of tenants will be higher accordingly.</p>
                    <p>For instance, if there are two separate rental agreements with two families living in different parts of the property, then there are two tenants. It is essential to accurately determine the number of tenants to ensure proper record-keeping, compliance with rental regulations, and appropriate allocation of responsibilities and costs related to the property\'s maintenance and management.</p>',
                ])
                ->make()
                ->toArray()
        );

        $howManyAnswer = $howManyStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => '1'],
                            ['value' => '2'],
                            ['value' => '3'],
                            ['value' => '4'],
                            ['value' => '5+'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $howManyAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
    }

    protected function rent(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Rent',
                ])
                ->make()
                ->toArray()
        );

        // How to rent
        $howToRentStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Was the requisite \'How to Rent\' provided to the tenant(s) at commencement of the tenancy?',
                    'help_text' => '<p>The "How to Rent" guide is a document provided by the UK government to private residential landlords in England and Wales. It is intended to be given to tenants at the beginning of a new tenancy. This guide outlines important information about the rights and responsibilities of both landlords and tenants, as well as essential information about the rental property and tenancy agreement</p>.
                    <p>Providing the "How to Rent" guide to tenants at the commencement of the tenancy is a legal requirement for landlords in England and Wales. It ensures that tenants have access to essential information about their rights and responsibilities, and it helps promote transparency and clear communication between landlords and tenants throughout the tenancy period.</p>',
                ])
                ->make()
                ->toArray()
        );

        $howToRentAnswer = $howToRentStep->answers()->create(
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

        $howToRentAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $howToRentDetailsAnswer = $howToRentStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the tenancy commenced prior to the introduction of the How to Rent policy',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $howToRentDetailsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $howToRentDetailsAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $howToRentDetailsAnswer->id,
            'answer_id' => $howToRentAnswer->id,
            'selected_value' => 'No',
        ]);

        // Arrears
        $arrearsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are there any arrears of rent?',
                    'help_text' => 'Arrears of rent refer to any unpaid rent that is past due. It is essential to keep track of rental payments and address any arrears promptly to avoid financial issues and potential disputes with tenants. If there are arrears, it\'s advisable to work with the tenant to find a resolution, such as setting up a repayment plan or discussing the reasons behind the missed payments. Maintaining open communication with your tenant can help prevent arrears from escalating and ensure a positive landlord-tenant relationship.',
                ])
                ->make()
                ->toArray()
        );

        $arrearsAnswer = $arrearsStep->answers()->create(
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

        $arrearsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $arrearsDetailsAnswer = $arrearsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'Please provide the amount of rent in arrears and the tenants who are responsible for the arrears. E.g. £1300 owed by Michael Smith.',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $arrearsDetailsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $arrearsDetailsAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $arrearsDetailsAnswer->id,
            'answer_id' => $arrearsAnswer->id,
            'selected_value' => 'Yes',
        ]);

        // Rent time
        $rentTimeStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is rent normally paid on time?',
                    'help_text' => 'E.g. The tenants have consistently experienced challenges in making timely rental payments due to unforeseen circumstances, resulting in a pattern of late or delayed payments.',
                ])
                ->make()
                ->toArray()
        );

        $rentTimeAnswer = $rentTimeStep->answers()->create(
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

        $rentTimeAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $rentTimeDetailsAnswer = $rentTimeStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the tenant in flat 3 is always late with paying rent',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $rentTimeDetailsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $rentTimeDetailsAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $rentTimeDetailsAnswer->id,
            'answer_id' => $rentTimeAnswer->id,
            'selected_value' => 'No',
        ]);

        $rentTimeStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $rentTimeStep->id,
            'answer_id' => $arrearsAnswer->id,
            'selected_value' => 'Yes',
        ]);
    }

    protected function deposit(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Deposit',
                ])
                ->make()
                ->toArray()
        );

        // Deposit
        $depositStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there a tenancy deposit held?',
                    'help_text' => 'A tenancy deposit is a sum of money which a landlord requires a tenant to pay at the start of the tenancy or which the landlord holds over from a previous tenancy with the same tenant. The money is security, in case the tenant does not meet their obligations in connection with the tenancy.',
                ])
                ->make()
                ->toArray()
        );

        $depositAnswer = $depositStep->answers()->create(
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

        $depositAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Deposit protection
        $depositProtectionStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the deposit being held under a Tenancy Deposit Protection scheme?',
                    'help_text' => 'Tenancy Deposit Protection (TDP) schemes are companies approved by the government. They have the authority to govern tenancy deposits and oversee the process of deposit returns, deductions and disputes. All landlords are required to protect any deposits taken for a tenancy.',
                ])
                ->make()
                ->toArray()
        );

        $depositProtectionStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $depositProtectionStep->id,
            'answer_id' => $depositAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $depositProtectionAnswer = $depositProtectionStep->answers()->create(
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

        $depositProtectionAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $depositProtectionNoDetailsAnswer = $depositProtectionStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'Please specific who is holding the deposit if not in a Tenancy Deposit Protection Scheme',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $depositProtectionNoDetailsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $depositProtectionNoDetailsAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $depositProtectionNoDetailsAnswer->id,
            'answer_id' => $depositProtectionAnswer->id,
            'selected_value' => 'No',
        ]);

        $depositProtectionYesDetailsAnswer = $depositProtectionStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'Please specify which Tenancy Deposit scheme the deposit is held under',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $depositProtectionYesDetailsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $depositProtectionYesDetailsAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $depositProtectionYesDetailsAnswer->id,
            'answer_id' => $depositProtectionAnswer->id,
            'selected_value' => 'Yes',
        ]);

        // Deposit timing
        $depositTimingStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm the deposit was deposited within 30 days of the commencement of the tenancy:',
                    'help_text' => 'This is a legal requirement under Tenancy Deposit Protection (TDP) regulations, and it ensures compliance with the deposit protection rules. If the deposit was not deposited within the specified timeframe, it may result in penalties and potential legal issues. Providing proof of timely depositing of the deposit will give the buyer confidence in the property\'s compliance with TDP regulations.',
                ])
                ->make()
                ->toArray()
        );

        $depositTimingStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $depositTimingStep->id,
            'answer_id' => $depositProtectionAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $depositTimingAnswer = $depositTimingStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Confirm'],
                            ['value' => 'Not applicable'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $depositTimingAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $depositTimingDetailsAnswer = $depositTimingStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the deposit is being held privately',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $depositTimingDetailsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $depositTimingDetailsAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $depositTimingDetailsAnswer->id,
            'answer_id' => $depositTimingAnswer->id,
            'selected_value' => 'Not applicable',
        ]);

        // Deposit protection cert
        $depositProtectionCertStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the requisite certificate confirming that the deposit has been protected:',
                    'help_text' => 'This refers to the document that confirms the tenancy deposit has been protected in a government-approved Tenancy Deposit Protection (TDP) scheme. This certificate is provided to the tenant by the landlord or letting agent within 30 days of receiving the deposit. It contains important details about the deposit protection, such as the scheme used, deposit amount, property details, and relevant contact information. The certificate serves as proof that the landlord has complied with legal requirements regarding deposit protection and informs the tenant of their rights and responsibilities.',
                ])
                ->make()
                ->toArray()
        );

        $depositProtectionCertStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $depositProtectionCertStep->id,
            'answer_id' => $depositAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $depositProtectionCertAnswer = $depositProtectionCertStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $depositProtectionCertAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
    }

    protected function disputesAndComplaints(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Disputes and Complaints',
                ])
                ->make()
                ->toArray()
        );

        // Disputes
        $disputesStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are there or have there been any disputes between tenants?',
                    'help_text' => 'These are disputes between landlords and tenants over rent payment, property damage, return of security deposits, repair and maintenance of facilities, etc.',
                ])
                ->make()
                ->toArray()
        );

        $disputesAnswer = $disputesStep->answers()->create(
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

        $disputesAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $disputesDetailsAnswer = $disputesStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. a previous tenant was using another tenants parking space',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $disputesDetailsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $disputesDetailsAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $disputesDetailsAnswer->id,
            'answer_id' => $disputesAnswer->id,
            'selected_value' => 'Yes',
        ]);

        // Complaints
        $complaintsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are there or have there been any complaints made by the tenant(s)?',
                    'help_text' => 'Neighbourhood disputes are never good for a landlord&quot;s reputation. If you have knowledge of a recurring antisocial tenant behaviour, please let us know.',
                ])
                ->make()
                ->toArray()
        );

        $complaintsAnswer = $complaintsStep->answers()->create(
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

        $complaintsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $complaintsDetailsAnswer = $complaintsStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the tenant of the lower floor flat has made noise complaints about the first floor tenant',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $complaintsDetailsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $complaintsDetailsAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $complaintsDetailsAnswer->id,
            'answer_id' => $complaintsAnswer->id,
            'selected_value' => 'Yes',
        ]);
    }

    protected function regulations(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Regulations',
                ])
                ->make()
                ->toArray()
        );

        // Energy
        $energyStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has an Energy Performance Certificate (EPC) been given to the tenant(s) before entering into the tenancy agreement or within 28 days of a renewal of tenancy?',
                    'help_text' => 'This is a requirement for tenancies entered into after 1st October 2015 – Assured Shorthold Tenancy Notices and Prescribed Requirements (England) Regulations 2015.',
                ])
                ->make()
                ->toArray()
        );

        $energyAnswer = $energyStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'No'],
                            ['value' => 'Not known'],
                            ['value' => 'Not required'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $energyAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Gas cert
        $gasCertStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has a Gas Safe Certificate been given to the tenant before entering into the tenancy agreement or within 28 days of a renewal of tenancy?',
                    'help_text' => 'This is a requirement for tenancies entered into after 1st October 2015 &ndash; Assured Shorthold Tenancy Notices and Prescribed Requirements (England) Regulations 2015.',
                ])
                ->make()
                ->toArray()
        );

        $gasCertAnswer = $gasCertStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'No'],
                            ['value' => 'Not known'],
                            ['value' => 'Not required'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $gasCertAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Upholstery
        $upholsteryStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Do all items containing upholstery comply with the fire resistance requirements regulated by the Furniture and Furnishings (Fire Safety) Regulations 1988?',
                    'help_text' => 'Upholstery is the materials—which include fabric, padding, webbing, and springs—that make up the soft coverings of chairs, sofas, and other furniture. The Furniture and Furnishings (Fire Safety) Regulations 1988 (as amended in 1989, 1993 and 2010) set levels of fire resistance for domestic upholstered furniture, furnishings and other products containing upholstery.',
                ])
                ->make()
                ->toArray()
        );

        $upholsteryAnswer = $upholsteryStep->answers()->create(
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

        $upholsteryAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Right to rent
        $rightToRentStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Were the \'Right to Rent\' checks undertaken on the tenant(s) under Section 22 of The Immigration Act 2014?',
                    'help_text' => 'Upholstery is the materials—which include fabric, padding, webbing, and springs—that make up the soft coverings of chairs, sofas, and other furniture. The Furniture and Furnishings (Fire Safety) Regulations 1988 (as amended in 1989, 1993 and 2010) set levels of fire resistance for domestic upholstered furniture, furnishings and other products containing upholstery.',
                ])
                ->make()
                ->toArray()
        );

        $rightToRentAnswer = $rightToRentStep->answers()->create(
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

        $rightToRentAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Smoke alarms
        $smokeAlarmsStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Does each storey of the property have at least one smoke alarm installed?',
                    'help_text' => 'Smoke alarms are required in all rented properties in England. The Smoke and Carbon Monoxide Alarm (England) Regulations 2015 require landlords to fit a smoke alarm on every storey of the property and in every room used as living accommodation. The alarm must be interlinked so that if one sounds, they all sound.',
                ])
                ->make()
                ->toArray()
        );

        $smokeAlarmsAnswer = $smokeAlarmsStep->answers()->create(
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

        $smokeAlarmsAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Legionnaires
        $legionnairesStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has a Legionnaires Disease Risk Assessment been undertaken?',
                    'help_text' => 'The Control of Legionella Bacteria in Water Systems (England) Regulations 2000 require landlords to assess the risk of Legionnaires&quot; disease from water systems in their properties and to take appropriate action to control the risk. This includes the need to carry out a risk assessment and to maintain the water system in a safe condition.',
                ])
                ->make()
                ->toArray()
        );

        $legionnairesAnswer = $legionnairesStep->answers()->create(
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

        $legionnairesAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Legionnaires document
        $legionnairesDocumentStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the Legionnaires Disease Risk Assessment:',
                ])
                ->make()
                ->toArray()
        );

        $legionnairesDocumentStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $legionnairesDocumentStep->id,
            'answer_id' => $legionnairesAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $legionnairesDocumentAnswer = $legionnairesDocumentStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $legionnairesDocumentAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Electrical
        $electricalStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Has the electrical installation been checked within the last five years?',
                    'help_text' => 'The Electrical Equipment (Safety) Regulations 1994 require landlords to ensure that electrical equipment in their properties is safe. This includes the need to carry out a periodic inspection and test of the electrical installation at least every five years.',
                ])
                ->make()
                ->toArray()
        );

        $electricalAnswer = $electricalStep->answers()->create(
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

        $electricalAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // PAT
        $patStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Have the portable electronic devices at the property been PAT tested?',
                    'help_text' => 'The Electrical Equipment (Safety) Regulations 1994 require landlords to ensure that electrical equipment in their properties is safe. This includes the need to carry out a periodic inspection and test of the electrical installation at least every five years.',
                ])
                ->make()
                ->toArray()
        );

        $patAnswer = $patStep->answers()->create(
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

        $patAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
    }

    protected function documents(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Documents',
                ])
                ->make()
                ->toArray()
        );

        // Tenancy agreement
        $tenancyAgreementStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the tenancy agreement:',
                    'help_text' => 'A tenancy agreement is a contract between a landlord and a tenant specifying the terms and conditions of their rental agreement. Tenancy agreements are usually put in place before letting out a property.',
                ])
                ->make()
                ->toArray()
        );

        $tenancyAgreementAnswer = $tenancyAgreementStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $tenancyAgreementAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Original tenancy agreement
        $originalTenancyAgreementStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm the original tenancy agreements will be provided on completion of the sale:',
                ])
                ->make()
                ->toArray()
        );

        $originalTenancyAgreementAnswer = $originalTenancyAgreementStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Confirm'],
                            ['value' => 'Not available'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $originalTenancyAgreementAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Schedule of condition
        $scheduleOfConditionStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there a Schedule of Condition?',
                    'help_text' => 'Upholstery is the materials—which include fabric, padding, webbing, and springs—that make up the soft coverings of chairs, sofas, and other furniture. The Furniture and Furnishings (Fire Safety) Regulations 1988 (as amended in 1989, 1993 and 2010) set levels of fire resistance for domestic upholstered furniture, furnishings and other products containing upholstery. ',
                ])
                ->make()
                ->toArray()
        );

        $scheduleOfConditionAnswer = $scheduleOfConditionStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'No'],
                            ['value' => 'Not available'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $scheduleOfConditionAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $scheduleOfConditionAnswerText = $scheduleOfConditionStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the property is to be sold completely empty',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $scheduleOfConditionAnswerText->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $scheduleOfConditionAnswerText->id,
            'answer_id' => $scheduleOfConditionAnswer->id,
            'selected_value' => 'No',
            'type' => ConditionType::OR,
        ]);

        $scheduleOfConditionAnswerText->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $scheduleOfConditionAnswerText->id,
            'answer_id' => $scheduleOfConditionAnswer->id,
            'selected_value' => 'Not available',
            'type' => ConditionType::OR,
        ]);

        $scheduleOfConditionAnswerText->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Schedule of condition document
        $scheduleOfConditionDocumentStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the Schedule of Condition:',
                    'help_text' => 'If a Schedule of Condition exists for the property, please provide a copy of it to the buyers. The Schedule of Condition is a detailed record of the property\'s condition at the start of the tenancy, outlining any pre-existing damages or issues. It is essential for the buyers to have this document to understand the property\'s condition at the time of sale and have a reference for any potential disputes or claims in the future. Providing the Schedule of Condition ensures transparency and helps the buyers make informed decisions about the property\'s condition and potential responsibilities.',
                ])
                ->make()
                ->toArray()
        );

        $scheduleOfConditionDocumentStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $scheduleOfConditionDocumentStep->id,
            'answer_id' => $scheduleOfConditionAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $scheduleOfConditionDocumentAnswer = $scheduleOfConditionDocumentStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $scheduleOfConditionDocumentAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Inventory
        $inventoryStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there an inventory confirming the items included in the purchase price?',
                    'help_text' => 'An inventory is a detailed list of all the fixtures, fittings, and furnishings that are part of the sale. It helps ensure clarity and agreement between the parties on what is included in the purchase. If an inventory exists, it is important to provide a copy to the buyers to avoid any misunderstandings and disputes regarding the items to be transferred with the property. This will also help the buyers assess the property\'s value and understand what they will be receiving as part of the purchase.',
                ])
                ->make()
                ->toArray()
        );

        $inventoryStep->conditions()->create([
            'answer_id' => $scheduleOfConditionAnswer->id,
            'selected_value' => 'No',
            'type' => ConditionType::OR,
        ]);

        $inventoryStep->conditions()->create([
            'answer_id' => $scheduleOfConditionAnswer->id,
            'selected_value' => 'Not available',
            'type' => ConditionType::OR,
        ]);

        $inventoryAnswer = $inventoryStep->answers()->create(
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

        $inventoryAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $inventoryDetailAnswer = $inventoryStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the property is to be sold completely empty',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $inventoryDetailAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $inventoryDetailAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $inventoryDetailAnswer->id,
            'answer_id' => $inventoryAnswer->id,
            'selected_value' => 'No',
        ]);

        // Inventory document
        $inventoryDocumentStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the inventory:',
                    'help_text' => 'If you have an inventory, it is essential to provide a copy to the buyers during the property transaction process. The inventory should be a detailed list of all fixtures, fittings, and furnishings that are included in the sale. Providing a copy of the inventory will ensure transparency and clarity between both parties and help prevent any potential disputes or misunderstandings regarding the items included in the purchase. If you have the inventory, please make sure to share it with the buyers as part of the property sale process.',
                ])
                ->make()
                ->toArray()
        );

        $inventoryDocumentStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $inventoryDocumentStep->id,
            'answer_id' => $inventoryAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $inventoryDocumentAnswer = $inventoryDocumentStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $inventoryDocumentAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // HMO license
        $hmoLicenseStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there a HMO Licence in place?',
                    'help_text' => '<p>An HMO license is needed if your property is occupied by three or more people from more than one household and they share facilities like a kitchen, bathroom, or toilet.</p>
                    <p>If your property is an HMO, it should have a valid HMO license in place. If you have obtained the license, you need to provide a copy of the HMO license to the buyer to demonstrate that the property is legally allowed to be operated as an HMO.</p>
                    <p>However, if your property is not an HMO, then there is no need for an HMO license. You should ensure that you accurately inform the buyers about the status of the property in relation to HMO requirements during the property sale process.</p>',
                ])
                ->make()
                ->toArray()
        );

        $hmoLicenseAnswer = $hmoLicenseStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'No'],
                            ['value' => 'Not required'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $hmoLicenseAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // HMO license document
        $hmoLicenseDocumentStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the HMO Licence:',
                    'help_text' => 'If your property is an HMO and you have a valid HMO license, you should provide a copy of it to the buyer during the property sale process. Make sure to verify the validity of the license and ensure that it is up to date and compliant with local regulations. If you need to obtain a copy, you may contact the local authority or licensing department responsible for issuing HMO licenses in your area.',
                ])
                ->make()
                ->toArray()
        );

        $hmoLicenseDocumentStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $hmoLicenseDocumentStep->id,
            'answer_id' => $hmoLicenseAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $hmoLicenseDocumentAnswer = $hmoLicenseDocumentStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $hmoLicenseDocumentAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Fire risk assessment
        $fireRiskAssessmentStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the Fire Risk Assessment:',
                    'help_text' => 'A Fire Risk Assessment is a thorough evaluation of a property to identify potential fire hazards, assess the level of risk, and recommend measures to minimise the risk and ensure the safety of occupants. This assessment is often conducted by a qualified professional and is required for certain properties, such as houses in multiple occupation (HMOs) and commercial buildings, to comply with fire safety regulations. The assessment covers various aspects, including the layout of the property, fire escape routes, fire alarms, firefighting equipment, and the storage of flammable materials. It is essential to have a Fire Risk Assessment to maintain the safety and well-being of the property and its occupants.',
                ])
                ->make()
                ->toArray()
        );

        $fireRiskAssessmentStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $fireRiskAssessmentStep->id,
            'answer_id' => $hmoLicenseAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $fireRiskAssessmentAnswer = $fireRiskAssessmentStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $fireRiskAssessmentAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Asbestos report document
        $asbestosReportDocumentStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the asbestos report:',
                    'help_text' => 'An asbestos report is a document that details the presence and condition of asbestos-containing materials (ACMs) within a property. It is typically carried out by a qualified asbestos surveyor who inspects the property to identify any ACMs and assess their condition. The report provides valuable information about the location, type, and condition of asbestos in the property, as well as recommendations for managing or removing the asbestos safely. Asbestos is a hazardous material that can cause serious health risks when disturbed, so having an asbestos report is essential for ensuring the safety of occupants and those involved in any renovation or maintenance work. It helps property owners and buyers to make informed decisions about managing asbestos and complying with legal requirements related to asbestos management.',
                ])
                ->make()
                ->toArray()
        );

        $asbestosReportDocumentStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $asbestosReportDocumentStep->id,
            'answer_id' => $hmoLicenseAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $asbestosReportDocumentAnswer = $asbestosReportDocumentStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $asbestosReportDocumentAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Fire alarm system
        $fireAlarmSystemStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide evidence of the fire alarm system service:',
                    'help_text' => 'Evidence of the fire alarm system service refers to documentation or records that demonstrate that the fire alarm system in the property has been regularly inspected, tested, and maintained by a qualified fire alarm technician. This service ensures that the fire alarm system is in proper working condition, providing early detection and warning in case of a fire emergency. The evidence typically includes service reports, certificates, or logbooks that detail the date of service, the work carried out, any repairs or replacements made, and the overall status of the fire alarm system. Regular servicing is crucial for the safety of occupants and compliance with fire safety regulations. It helps identify any faults or issues with the system, allowing for timely repairs and ensuring that the alarm system operates effectively to protect the property and its occupants in the event of a fire.',
                ])
                ->make()
                ->toArray()
        );

        $fireAlarmSystemStep->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $fireAlarmSystemStep->id,
            'answer_id' => $hmoLicenseAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $fireAlarmSystemAnswer = $fireAlarmSystemStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $fireAlarmSystemAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
    }

    protected function additionalInformation(Form $form)
    {
        // Section
        $section = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Additional Information',
                ])
                ->make()
                ->toArray()
        );

        // Additional information
        $additionalInformationStep = $section->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are you aware of any information regarding the AST or occupiers not covered by these enquiries?',
                    'help_text' => '<p>The Assured Shorthold Tenancy (AST) is a common type of tenancy agreement used in England and Wales for residential tenancies with certain protections for both landlords and tenants. If your property is subject to an AST, it\'s crucial to ensure that the necessary documentation, such as the tenancy agreement and any other relevant information, is provided to the buyer.</p>
                    <p>For other aspects not covered in the enquiries, it\'s important to be transparent with the buyer and address any additional information or concerns they may have. It\'s recommended to provide any relevant documentation, such as certificates, reports, or agreements related to the property, to the buyer to facilitate a smooth and informed transaction.</p>',
                ])
                ->make()
                ->toArray()
        );

        $additionalInformationAnswer = $additionalInformationStep->answers()->create(
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

        $additionalInformationAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $additionalInformationDetailAnswer = $additionalInformationStep->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Please, provide details',
                        'placeholder' => 'e.g. the tenants of number 3 has a dog',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $additionalInformationDetailAnswer->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $additionalInformationDetailAnswer->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $additionalInformationDetailAnswer->id,
            'answer_id' => $additionalInformationAnswer->id,
            'selected_value' => 'Yes',
        ]);
    }
}
