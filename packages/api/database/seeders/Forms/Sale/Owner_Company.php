<?php

namespace Database\Seeders\Forms\Sale;

use App\Enums\AnswerType;
use App\Enums\ConditionType;
use App\Enums\FileTextAnswerTypes;
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
use App\Services\StepAnswerGeneration\StepAnswerGeneration;
use Illuminate\Database\Seeder;

class Owner_Company extends Seeder
{
    private Answer $answerRepresentation;

    private Answer $answerCompanyRepresentativeStatus;

    private Answer $answerCompanyRepresentativeStatus2;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stepOwnerName = Step::firstWhere('type', StepType::OwnerName);

        // Form
        $form = Form::factory()
            ->state([
                'name' => 'Getting started: The Owners - Company',
                'group' => FormGroup::GettingStarted,
                'description' => 'Details about the owners of the property',
                'template_id' => $stepOwnerName->id,
                'repeatable_answer_id' => $stepOwnerName->answers->firstWhere('details.label', 'Owner type')->id,
                'ta_form_template' => FormType::Company,
                'order_number' => 2,
                'type' => PropertyType::Sale,
            ])
            ->create();

        $this->companyInformation($form);
        $this->companyRepresentatives($form);

        $this->powerOfAttorney($form, $this->answerCompanyRepresentativeStatus, '0');
        $this->deputyshipOrder($form, $this->answerCompanyRepresentativeStatus, '0');
        $this->grantOfProbate($form, $this->answerCompanyRepresentativeStatus, '0');

        $this->powerOfAttorney($form, $this->answerCompanyRepresentativeStatus2, '1');
        $this->deputyshipOrder($form, $this->answerCompanyRepresentativeStatus2, '1');
        $this->grantOfProbate($form, $this->answerCompanyRepresentativeStatus2, '1');
    }

    protected function companyInformation(Form $form)
    {
        $sectionCompanyInformation = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Company Information',
                ])
                ->make()
                ->toArray()
        );

        // Company name different
        $stepCompanyNameDifferent = $sectionCompanyInformation->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the current company name different to how it appears on the Property Title Deeds?',
                    'help_text' => 'The company\'s name may have changed since the Title Deeds were issued. Please click \'Yes\' if any of the company\'s name is not exactly as it is shown on the Title Deeds.',
                ])
                ->make()
                ->toArray()
        );

        $answerCompanyNameDifferent = $stepCompanyNameDifferent->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'No'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::NameChange,
                    ],

                ])
                ->make()
                ->toArray()
        );

        $uploadCompanyNameDifferent = $stepCompanyNameDifferent->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                    'details' => [
                        'pdfFormField' => OverviewPdfField::NameChangeProof,
                        'textAnswers' => [
                            FileTextAnswerTypes::Enclosed => 'Enclosed',
                            FileTextAnswerTypes::AddLater => 'To follow',
                            FileTextAnswerTypes::NotApplicable => 'N/A',
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $uploadCompanyNameDifferent->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $uploadCompanyNameDifferent->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $uploadCompanyNameDifferent->id,
            'answer_id' => $answerCompanyNameDifferent->id,
            'selected_value' => 'Yes',
        ]);

        $answerCompanyNameDifferent->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Company name different

        // Company details
        $stepCompanyDetails = $sectionCompanyInformation->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the contact details for the company',
                    'help_text' => 'Please provide the contact details for the company. Your conveyancer needs this information to establish effective communication and ensure smooth coordination throughout the transaction. By entering the contact details for the company, we can promptly convey important updates, inquiries, and information related to the transaction.',
                ])
                ->make()
                ->toArray()
        );

        $answerEmailDetails = $stepCompanyDetails->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Email Address',
                        'placeholder' => 'Enter Email Address',
                        'pdfFormFieldName' => OverviewPdfField::Email,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerEmailDetails->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerPhoneCompanyDetails = $stepCompanyDetails->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Phone Number',
                        'placeholder' => 'Enter Phone Number',
                        'pdfFormFieldName' => OverviewPdfField::Phone,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerPhoneCompanyDetails->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerAddressCompanyDetails = $stepCompanyDetails->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Address,
                    'details' => [
                        'label' => 'Company registered address',
                        'pdfFormFieldName' => OverviewPdfField::Address,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerAddressCompanyDetails->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Company details

        // Company Number
        $stepCompanyNumber = $sectionCompanyInformation->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the company number',
                    'help_text' => 'Please enter the company number associated with the property. The company number is required for accurate identification and documentation purposes. You can find this information on Companies House. Providing the company number will help ensure compliance with legal requirements and facilitate a smooth transaction process.',
                ])
                ->make()
                ->toArray()
        );

        $answerCompanyNumber = $stepCompanyNumber->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Company Number',
                        'placeholder' => 'Enter Company Number',
                        'pdfFormFieldName' => OverviewPdfField::CompanyNumber,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCompanyNumber->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Company Number

        // Company VAT
        $stepCompanyVAT = $sectionCompanyInformation->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is the company VAT registered?',
                    'help_text' => 'Please select \'Yes\' if the company is registered for Value Added Tax (VAT). This information is relevant for UK transactions and helps us ensure accurate documentation and compliance with VAT regulations.',
                ])
                ->make()
                ->toArray()
        );

        $answerCompanyVAT = $stepCompanyVAT->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'No'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::VatRegistered,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCompanyVAT->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Company VAT

        // Company VAT Number
        $stepCompanyVATNumber = $sectionCompanyInformation->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the company VAT number',
                    'help_text' => 'Please enter the company VAT number associated with the property. If the company is registered for Value Added Tax (VAT), provide the VAT number here. You can find the VAT number on official VAT registration documents issued by HM Revenue and Customs (HMRC). Providing the VAT number is important for accurate documentation and compliance with VAT regulations.',
                ])
                ->make()
                ->toArray()
        );

        $answerCompanyVATNumber = $stepCompanyVATNumber->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::Text,
                    'details' => [
                        'label' => 'Company VAT Number',
                        'placeholder' => 'Enter Company VAT Number',
                        'pdfFormFieldName' => OverviewPdfField::VatNumber,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerCompanyVATNumber->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $stepCompanyVATNumber->conditions()->create([
            'conditionable_type' => 'step',
            'conditionable_id' => $stepCompanyVATNumber->id,
            'answer_id' => $answerCompanyVAT->id,
            'selected_value' => 'Yes',
        ]);
        // End of Company VAT Number

        // Representation of the company
        $stepRepresentation = $sectionCompanyInformation->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm the representation of the company in the property sale:',
                    'help_text' => 'Please indicate the representation method used in the property sale. Section 36A(6) of the Companies Act 1985 provides protection for purchasers where a document is executed in the following fashion: “In favour of a purchaser a document shall be deemed to have been duly executed by a company if it purports to be signed by a director and the secretary of the company, or by two directors of the company”.',
                ])
                ->make()
                ->toArray()
        );

        $this->answerRepresentation = $stepRepresentation->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'One member of the board of directors'],
                            ['value' => 'Two members of the board of directors'],
                            ['value' => 'One such member and the clerk, secretary, deputy or other permanent officer of the corporation'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::Representation,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $this->answerRepresentation->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Representation of the company
    }

    protected function companyRepresentatives(Form $form)
    {
        $sectionCompanyRepresentatives = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Company Representatives',
                ])
                ->make()
                ->toArray()
        );

        // Company Representative details
        $companyRepresentative = $sectionCompanyRepresentatives->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the contact details for the company representative',
                    'help_text' => 'Please enter the details of the first/only representative who will be acting for the company in the sale. This typically should be at least one director. The conveyancer/solicitor will require at least an email address or phone number for each representative.',
                    'type' => StepType::CompanyRepresentative,
                ])
                ->make()
                ->toArray()
        );

        StepAnswerGeneration::personInformation(
            $companyRepresentative,
            text: 'representative',
            canSelectPreviousPerson: true,
            canSelectSaleAddress: true,
            pdfField: OverviewPdfField::Representative.'0',
            useSaleAddressPdfField: OverviewPdfField::RepresentativeUseSaleAddress.'0',
        );

        $companyRepresentativeMultiple = $sectionCompanyRepresentatives->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the contact details for the second company representative',
                    'help_text' => 'Please enter the details of the second representatives who will be acting for the company in the sale. This typically should be at least one director. The conveyancer/solicitor will require at least an email address or phone number for each representative.',
                    'type' => StepType::CompanyRepresentative,
                ])
                ->make()
                ->toArray()
        );

        StepAnswerGeneration::personInformation(
            $companyRepresentativeMultiple,
            text: 'representative',
            canSelectPreviousPerson: true,
            canSelectSaleAddress: true,
            pdfField: OverviewPdfField::Representative.'1',
            useSaleAddressPdfField: OverviewPdfField::RepresentativeUseSaleAddress.'1',
        );

        $companyRepresentativeMultiple->conditions()->create([
            'answer_id' => $this->answerRepresentation->id,
            'selected_value' => 'Two members of the board of directors',
            'type' => ConditionType::OR,
        ]);
        $companyRepresentativeMultiple->conditions()->create([
            'answer_id' => $this->answerRepresentation->id,
            'selected_value' => 'One such member and the clerk, secretary, deputy or other permanent officer of the corporation',
            'type' => ConditionType::OR,
        ]);
        // End of Company Representative details

        // Status of Company Representative
        $stepCompanyRepresentativeStatus = $sectionCompanyRepresentatives->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please select the status of the company representative:',
                    'help_text' => '"Company representatives may be selling the property as themselves or in certain circumstances have someone selling on their behalf. If the representative is acting for the company as themself as Director, select \'Director acting for themselves\' If the representative is acting for the company as a secretary or other, select \'Other acting for themselves\' If the representative has an attorney dealing with the sale, select \'Selling via attorney\' If the representative is deceased AND has an executor dealing with the sale, select \'Selling via executor\'',
                ])
                ->make()
                ->toArray()
        );

        $this->answerCompanyRepresentativeStatus = $stepCompanyRepresentativeStatus->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Director acting for themselves'],
                            ['value' => 'Selling via attorney'],
                            ['value' => 'Selling via deputy'],
                            ['value' => 'Selling via executor (deceased)'],
                            ['value' => 'Other acting for themselves'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::RepresentativeRepresentation.'0',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $this->answerCompanyRepresentativeStatus->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Status of Company Representative

        // Status of Company Representative 2
        $stepCompanyRepresentativeStatus2 = $sectionCompanyRepresentatives->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please select the status of the second company representative:',
                    'help_text' => '"Company representatives may be selling the property as themselves or in certain circumstances have someone selling on their behalf.
                    If the representative is acting for the company as themself as Director, select \'Director acting for themselves\'
                    If the representative is acting for the company as a secretary or other, select \'Other acting for themselves\'
                    If the representative has an attorney dealing with the sale, select \'Selling via attorney\'
                    If the representative is deceased AND has an executor dealing with the sale, select \'Selling via executor\'',
                ])
                ->make()
                ->toArray()
        );

        $this->answerCompanyRepresentativeStatus2 = $stepCompanyRepresentativeStatus2->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Director acting for themselves'],
                            ['value' => 'Selling via attorney'],
                            ['value' => 'Selling via deputy'],
                            ['value' => 'Selling via executor (deceased)'],
                            ['value' => 'Other acting for themselves'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::RepresentativeRepresentation.'1',
                    ],
                ])
                ->make()
                ->toArray()
        );

        $this->answerCompanyRepresentativeStatus2->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $stepCompanyRepresentativeStatus2->conditions()->create([
            'answer_id' => $this->answerRepresentation->id,
            'selected_value' => 'Two members of the board of directors',
            'type' => ConditionType::OR,
        ]);

        $stepCompanyRepresentativeStatus2->conditions()->create([
            'answer_id' => $this->answerRepresentation->id,
            'selected_value' => 'One such member and the clerk, secretary, deputy or other permanent officer of the corporation',
            'type' => ConditionType::OR,
        ]);
        // End of Status of Company Representative 2
    }

    protected function powerOfAttorney(Form $form, Answer $companyRepresentativeStatus, string $number)
    {
        // Section
        $sectionPowerOfAttorney = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Power of Attorney',
                ])
                ->make()
                ->toArray()
        );

        $sectionPowerOfAttorney->conditions()->create([
            'answer_id' => $companyRepresentativeStatus->id,
            'selected_value' => 'Selling via attorney',
        ]);

        // Number of attorneys step
        $stepNumberOfAttorneys = $sectionPowerOfAttorney->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm the number of attorneys acting for representative:',
                ])
                ->make()
                ->toArray()
        );

        $numberOfAttorneys = $stepNumberOfAttorneys->answers()->create(
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
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $numberOfAttorneys->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Jointly or severally step
        $stepJointlyOrSeverally = $sectionPowerOfAttorney->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Will the attorneys be required to act jointly or severally?',
                    'help_text' => 'JOINTLY: All Attorneys are required for any decision. If Jointly is selected we will require the names of all the Attorneys. All Attorneys will then need to be invited to ProConvey to confirm your answers to the following enquiries. SEVERALLY: An Attorney can act alone in making decisions. If Severally is selected you can complete the forms alone and do not need to enter the names of the other Attorneys.',
                ])
                ->make()
                ->toArray()
        );

        $stepJointlyOrSeverally->conditions()->create([
            'answer_id' => $numberOfAttorneys->id,
            'selected_value' => '2',
            'type' => ConditionType::OR,
        ]);
        $stepJointlyOrSeverally->conditions()->create([
            'answer_id' => $numberOfAttorneys->id,
            'selected_value' => '3',
            'type' => ConditionType::OR,
        ]);
        $stepJointlyOrSeverally->conditions()->create([
            'answer_id' => $numberOfAttorneys->id,
            'selected_value' => '4',
            'type' => ConditionType::OR,
        ]);
        $stepJointlyOrSeverally->conditions()->create([
            'answer_id' => $numberOfAttorneys->id,
            'selected_value' => '5',
            'type' => ConditionType::OR,
        ]);

        $jointlyOrSeverally = $stepJointlyOrSeverally->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Jointly'],
                            ['value' => 'Severally'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::RepresentativeAuthority.$number,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $jointlyOrSeverally->validationRules()->create([
            'rule' => 'required',
        ]);
        // End of Jointly or severally step

        // Repeatable Attorney details step
        $detailsOfTheAttorney = $sectionPowerOfAttorney->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the details of each attorney:',
                    'type' => StepType::CompanyFormPowerOfAttorneyRepresentative,
                    'repeatable_answer_id' => $numberOfAttorneys->id,
                ])
                ->make()
                ->toArray()
        );

        StepAnswerGeneration::personInformation(
            $detailsOfTheAttorney,
            text: 'attorney',
            canSelectPreviousPerson: true,
            canSelectSaleAddress: true,
            pdfField: OverviewPdfField::RepresentativeRepresentatives.$number,
            useSaleAddressPdfField: OverviewPdfField::RepresentativeRepresentativesUseSaleAddress.$number,
        );

        $detailsOfTheAttorney->conditions()->create([
            'answer_id' => $jointlyOrSeverally->id,
            'selected_value' => 'Severally',
        ]);
        // End of Repeatable Attorney details step

        // Attorney details step
        $detailsOfTheAttorneyOnBehalf = $sectionPowerOfAttorney->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the details of the attorney who will be completing the sale on behalf of this owner:',
                    'type' => StepType::CompanyFormPowerOfAttorneyRepresentative,
                ])
                ->make()
                ->toArray()
        );

        StepAnswerGeneration::personInformation(
            $detailsOfTheAttorneyOnBehalf,
            text: 'attorney',
            canSelectPreviousPerson: true,
            canSelectSaleAddress: true,
            pdfField: OverviewPdfField::RepresentativeRepresentatives.$number,
            useSaleAddressPdfField: OverviewPdfField::RepresentativeRepresentativesUseSaleAddress.$number,
        );

        $detailsOfTheAttorneyOnBehalf->conditions()->create([
            'answer_id' => $jointlyOrSeverally->id,
            'selected_value' => 'Jointly',
            'type' => ConditionType::OR,
        ]);
        $detailsOfTheAttorneyOnBehalf->conditions()->create([
            'answer_id' => $numberOfAttorneys->id,
            'selected_value' => '1',
            'type' => ConditionType::OR,
        ]);
        // End of Attorney details step

        // Power of attorney in place step
        $stepPowerOfAttorneyInPlace = $sectionPowerOfAttorney->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there a Power of Attorney already in place for this owner?',
                    'help_text' => 'In the vast majority of cases, you\'ll need to obtain a Power of Attorney to act as the attorney of someone\'s estate.',
                ])
                ->make()
                ->toArray()
        );

        $answerPowerOfAttorneyInPlace = $stepPowerOfAttorneyInPlace->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'Ongoing application'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::RepresentativeApplication.$number,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerPowerOfAttorneyInPlace->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerPowerOfAttorneyInPlaceDoc = $stepPowerOfAttorneyInPlace->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $answerPowerOfAttorneyInPlaceDoc->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        $answerPowerOfAttorneyInPlaceDoc->conditions()->create([
            'conditionable_type' => 'answer',
            'conditionable_id' => $answerPowerOfAttorneyInPlaceDoc->id,
            'answer_id' => $answerPowerOfAttorneyInPlace->id,
            'selected_value' => 'Yes',
        ]);

        $nameDifference = $sectionPowerOfAttorney->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are any of the attorneys\' current names different to how they appear (or will appear) on the Power of Attorney?',
                ])
                ->make()
                ->toArray()
        );

        $answerNameDifference = $nameDifference->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'No'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::RepresentativeRepresentativesNameChange.$number,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerNameDifference->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );

        // Power of attorney names step
        $stepPowerOfAttorneyNames = $sectionPowerOfAttorney->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm which names are (or will be) different on the Power of Attorney:',
                    'help_text' => 'Some names may have changed since the Power of Attorney was issued (e.g. marriage, divorce etc.). Please click \'Yes\' if any of the attorneys\' names doesn\'t appear exactly like it\'s shown on the Power of Attorney. They will also need to provide proof of this change.',
                    'repeatable_answer_id' => $numberOfAttorneys->id,
                    'type' => StepType::RepeatableNameChangeAttorney,
                ])
                ->make()
                ->toArray()
        );

        StepAnswerGeneration::nameChange(
            step: $stepPowerOfAttorneyNames,
            number: $number,
        );

        $stepPowerOfAttorneyNames->conditions()->create([
            'answer_id' => $answerNameDifference->id,
            'selected_value' => 'Yes',
        ]);
    }

    protected function deputyshipOrder(Form $form, Answer $companyRepresentativeStatus, string $number)
    {
        $sectionDeputyshipOrder = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Deputyship Order',
                ])
                ->make()
                ->toArray()
        );

        $sectionDeputyshipOrder->conditions()->create([
            'answer_id' => $companyRepresentativeStatus->id,
            'selected_value' => 'Selling via deputy',
        ]);

        // Number of Deputies
        $numberOfDeputies = $sectionDeputyshipOrder->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm the number of deputies acting for representative:',
                    'help_text' => 'Please make sure you select the number of deputies listed on the Deputyship Order, even if you are the only one dealing with the sale of the property.',
                ])
                ->make()
                ->toArray()
        );

        $answerNumberOfDeputies = $numberOfDeputies->answers()->create(
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
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerNumberOfDeputies->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Number of Deputies

        // Deputies jointly or severally
        $deputiesJointlyOrSeverally = $sectionDeputyshipOrder->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are the deputies acting jointly or severally?',
                    'help_text' => 'JOINTLY: all deputies are required for any decision. If jointly is selected we will require the names of all the deputies. All deputies will then be invited to PreConvey to confirm your replies to the forms.
                    SEVERALLY: a deputy can act alone in making decisions. If severally is selected you can complete the forms alone and do not need to enter the names of the other deputies.',
                ])
                ->make()
                ->toArray()
        );

        $deputiesJointlyOrSeverally->conditions()->create([
            'answer_id' => $answerNumberOfDeputies->id,
            'selected_value' => '2',
            'type' => ConditionType::OR,
        ]);
        $deputiesJointlyOrSeverally->conditions()->create([
            'answer_id' => $answerNumberOfDeputies->id,
            'selected_value' => '3',
            'type' => ConditionType::OR,
        ]);
        $deputiesJointlyOrSeverally->conditions()->create([
            'answer_id' => $answerNumberOfDeputies->id,
            'selected_value' => '4',
            'type' => ConditionType::OR,
        ]);
        $deputiesJointlyOrSeverally->conditions()->create([
            'answer_id' => $answerNumberOfDeputies->id,
            'selected_value' => '5',
            'type' => ConditionType::OR,
        ]);

        $answerDeputiesJointlyOrSeverally = $deputiesJointlyOrSeverally->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Jointly'],
                            ['value' => 'Severally'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::RepresentativeAuthority.$number,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerDeputiesJointlyOrSeverally->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Deputies jointly or severally

        // Details of the deputy
        $provideDetailsOfDeputyShipOrder = $sectionDeputyshipOrder->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide the details of the deputy acting for the owner:',
                    'help_text' => 'Please provide the details of the deputy acting for the owner. If there are multiple deputies, please provide the details of the first deputy listed on the Deputyship Order.',
                    'type' => StepType::CompanyFormDeputyshipOrderRepresentative,
                ])
                ->make()
                ->toArray()
        );

        StepAnswerGeneration::personInformation(
            $provideDetailsOfDeputyShipOrder,
            text: 'deputy',
            canSelectPreviousPerson: true,
            canSelectSaleAddress: true,
            pdfField: OverviewPdfField::RepresentativeRepresentatives.$number,
            useSaleAddressPdfField: OverviewPdfField::RepresentativeRepresentativesUseSaleAddress.$number,
        );

        $provideDetailsOfDeputyShipOrder->conditions()->create([
            'answer_id' => $answerDeputiesJointlyOrSeverally->id,
            'selected_value' => 'Jointly',
            'type' => ConditionType::OR,
        ]);
        $provideDetailsOfDeputyShipOrder->conditions()->create([
            'answer_id' => $answerNumberOfDeputies->id,
            'selected_value' => '1',
            'type' => ConditionType::OR,
        ]);
        // End of Details of the deputy

        // Details of each deputy
        $stepDetailsOfEachDeputy = $sectionDeputyshipOrder->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the details of the deputy who will be completing the sale on behalf of the owner:',
                    'help_text' => 'Please enter the full current names of the deputy who will be dealing with the sale on behalf of the owner.',
                    'type' => StepType::CompanyFormDeputyshipOrderRepresentative,
                    'repeatable_answer_id' => $answerNumberOfDeputies->id,
                ])
                ->make()
                ->toArray()
        );

        StepAnswerGeneration::personInformation(
            $stepDetailsOfEachDeputy,
            text: 'deputy',
            canSelectPreviousPerson: true,
            canSelectSaleAddress: true,
            pdfField: OverviewPdfField::RepresentativeRepresentatives.$number,
            useSaleAddressPdfField: OverviewPdfField::RepresentativeRepresentativesUseSaleAddress.$number,
        );

        $stepDetailsOfEachDeputy->conditions()->create([
            'answer_id' => $answerDeputiesJointlyOrSeverally->id,
            'selected_value' => 'Severally',
        ]);
        // End of Details of each deputy

        // Deputyship Order
        $deputyshipOrderAlreadyInPlace = $sectionDeputyshipOrder->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there a Deputyship Order already in place for the owner?',
                    'help_text' => 'In the vast majority of cases, you\'ll need to obtain a Deputyship Order to act as the attorney of someone\'s estate.',
                ])
                ->make()
                ->toArray()
        );

        $answerDeputyOrderAlreadyInPlace = $deputyshipOrderAlreadyInPlace->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'Ongoing application'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::RepresentativeApplication.$number,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $fileUploadDeputyOrderAlreadyInPlace = $deputyshipOrderAlreadyInPlace->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $fileUploadDeputyOrderAlreadyInPlace->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray(),
        );

        $fileUploadDeputyOrderAlreadyInPlace->conditions()->create([
            'answer_id' => $answerDeputyOrderAlreadyInPlace->id,
            'selected_value' => 'Yes',
        ]);
        // End of the deputyship order

        // Are there name changes
        $nameDifference = $sectionDeputyshipOrder->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are any of the deputies\' current names different to how they appear (or will appear) on the Deputyship Order?',
                ])
                ->make()
                ->toArray()
        );

        $answerNameDifference = $nameDifference->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'No'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::RepresentativeRepresentativesNameChange.$number,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerNameDifference->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of are there name changes

        // Name Change
        $stepDeputyNames = $sectionDeputyshipOrder->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm which names are (or will be) different on the Deputyship Order:',
                    'help_text' => 'Some names may have changed since the Deputyship Order was issued (e.g. marriage, divorce etc.). Please click \'Yes\' if any of the deputies\' names doesn\'t appear exactly like it\'s shown on the Deputyship Order. They will also need to provide proof of this change.',
                    'repeatable_answer_id' => $answerNumberOfDeputies->id,
                    'type' => StepType::RepeatableNameChangeDeputy,
                ])
                    ->make()
                    ->toArray()
        );

        StepAnswerGeneration::nameChange(
            step: $stepDeputyNames,
            number: $number,
        );

        $stepDeputyNames->conditions()->create([
            'answer_id' => $answerNameDifference->id,
            'selected_value' => 'Yes',
        ]);
    }

    protected function grantOfProbate(Form $form, Answer $companyRepresentativeStatus, string $number)
    {
        $sectionGrantOfProbate = $form->sections()->create(
            Section::factory()
                ->state([
                    'name' => 'Grant of Probate',
                ])
                ->make()
                ->toArray()
        );

        $sectionGrantOfProbate->conditions()->create([
            'answer_id' => $companyRepresentativeStatus->id,
            'selected_value' => 'Selling via executor (deceased)',
        ]);

        // Death Certificate
        $sectionDeathCertificate = $sectionGrantOfProbate->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please provide a copy of the death certificate of the owner',
                    'help_text' => 'A death certificate is a legal document which states when a person had died. We will require a death certificate for each owner named on the Title Deeds that you have marked as deceased. If you are unable to upload the document, please provide it to your conveyancer/solicitor in due course.',
                ])
                ->make()
                ->toArray()
        );

        $uploadDeathCertificate = $sectionDeathCertificate->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $uploadDeathCertificate->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray(),
        );
        //End of Death Certificate

        // Represented by an executor
        $representedByExecutor = $sectionGrantOfProbate->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Will the owner be represented by an executor via a Grant of Probate? ',
                    'help_text' => 'In the vast majority of cases, you\'ll need to obtain a Grant of Probate to act as the executor of someone\'s estate.',
                ])
                ->make()
                ->toArray()
        );

        $answerRepresentedByExecutor = $representedByExecutor->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'Not required'],
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerRepresentedByExecutor->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray(),
        );
        // End of Represented by an executor

        // Grant in place for the owner
        $grantInPlaceForOwner = $sectionGrantOfProbate->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Is there a Grant of Probate already in place for the owner',
                ])
                ->make()
                ->toArray()
        );

        $grantInPlaceForOwner->conditions()->create([
            'answer_id' => $answerRepresentedByExecutor->id,
            'selected_value' => 'Yes',
        ]);

        $answerGrantInPlaceForOwner = $grantInPlaceForOwner->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'Ongoing application'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::RepresentativeApplication.$number,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $uploadGrantInPlaceForOwner = $grantInPlaceForOwner->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::File,
                ])
                ->make()
                ->toArray()
        );

        $uploadGrantInPlaceForOwner->conditions()->create([
            'answer_id' => $answerGrantInPlaceForOwner->id,
            'selected_value' => 'Yes',
        ]);

        $uploadGrantInPlaceForOwner->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray(),
        );
        // End of Grant in place for the owner

        // Numbers of Executors
        $stepNumberOfExecutors = $sectionGrantOfProbate->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please confirm the number of executors on the Grant of Probate:',
                    'help_text' => 'Please make sure you select the number of executors listed on the Grant of Probate, even if you are the only one dealing with the sale of the property.',
                ])
                ->make()
                ->toArray()
        );

        $stepNumberOfExecutors->conditions()->create([
            'answer_id' => $answerRepresentedByExecutor->id,
            'selected_value' => 'Yes',
        ]);

        $answerNumberOfExecutors = $stepNumberOfExecutors->answers()->create(
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
                        ],
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerNumberOfExecutors->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray(),
        );
        // End of Numbers of Executors

        // Details for the executors
        $stepDetailsOfExecutors = $sectionGrantOfProbate->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the details of each executor:',
                    'help_text' => 'Please enter the full current names of all executors. The conveyancer/solicitor will require at least a phone number or email address for each executor.',
                    'repeatable_answer_id' => $answerNumberOfExecutors->id,
                    'type' => StepType::CompanyFormGrantOfProbateRepresentative,
                ])
                ->make()
                ->toArray()
        );

        StepAnswerGeneration::personInformation(
            $stepDetailsOfExecutors,
            text: 'executor',
            canSelectPreviousPerson: true,
            canSelectSaleAddress: true,
            pdfField: OverviewPdfField::RepresentativeRepresentatives.$number,
            useSaleAddressPdfField: OverviewPdfField::RepresentativeRepresentativesUseSaleAddress.$number,
        );

        $stepDetailsOfExecutors->conditions()->create([
            'answer_id' => $answerRepresentedByExecutor->id,
            'selected_value' => 'Yes',
        ]);
        // End of Details for the executors

        // Any of the names different to the grant of probate
        $nameDifference = $sectionGrantOfProbate->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Are any of the executors\' current names different to how they appear (or will appear) on the Grant of Probate?',
                    'help_text' => 'Some names may have changed since the Grant of Probate was issued (e.g. marriage, divorce etc.). Please click \'Yes\' if any of the executors\' names doesn\'t appear exactly like it\'s shown on the Grant of Probate. They will also need to provide proof of this change.',
                ])
                ->make()
                ->toArray()
        );

        $nameDifference->conditions()->create([
            'answer_id' => $answerRepresentedByExecutor->id,
            'selected_value' => 'Yes',
        ]);

        $answerNameDifference = $nameDifference->answers()->create(
            Answer::factory()
                ->state([
                    'type' => AnswerType::SingleSelect,
                    'details' => [
                        'options' => [
                            ['value' => 'Yes'],
                            ['value' => 'No'],
                        ],
                        'pdfFormFieldName' => OverviewPdfField::RepresentativeRepresentativesNameChange.$number,
                    ],
                ])
                ->make()
                ->toArray()
        );

        $answerNameDifference->validationRules()->create(
            ValidationRule::factory()
                ->state([
                    'rule' => 'required',
                ])
                ->make()
                ->toArray()
        );
        // End of Any of the names different to the grant of probate

        // Names different to the grant of probate
        $stepExecutorsNames = $sectionGrantOfProbate->steps()->create(
            Step::factory()
                ->state([
                    'question' => 'Please enter the details of the executors whose names are different to how they appear (or will appear) on the Grant of Probate:',
                    'help_text' => 'Please provide proof of the name change. If you are unable to upload the document, please provide it to your conveyancer/solicitor in due course.',
                    'repeatable_answer_id' => $answerNumberOfExecutors->id,
                    'type' => StepType::RepeatableNameChangeExecutor,
                ])
                ->make()
                ->toArray()
        );

        StepAnswerGeneration::nameChange(
            step: $stepExecutorsNames,
            number: $number,
        );

        $stepExecutorsNames->conditions()->create([
            'answer_id' => $answerNameDifference->id,
            'selected_value' => 'Yes',
        ]);
    }
}
