<?php

namespace Database\Seeders\Forms\Remortgage;

use App\Enums\AnswerType;
use App\Enums\ConditionType;
use App\Enums\FormGroup;
use App\Enums\FormType;
use App\Enums\OverviewPdfField;
use App\Enums\PropertyType;
use App\Enums\StepType;
use App\Models\Answer;
use App\Models\Form;
use App\Models\Step;
use App\Services\StepAnswerGeneration\StepAnswerGeneration;
use Database\Seeders\Forms\Purchase\Helpers\BuyerAttorneySection;
use Database\Seeders\Forms\Purchase\Helpers\BuyerDeputySection;
use Illuminate\Database\Seeder;

class Remortgager_Company extends Seeder
{
    private $globalCompanyRepresentationAnswer;

    private $globalFirstCompanyRepStatusAnswer;

    private $globalSecondCompanyRepStatusAnswer;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stepRemortgagerName = Step::firstWhere('type', StepType::Mortgager);

        // Form
        $form = Form::factory()
            ->state([
                'name' => 'Remortgager Form (Company)',
                'group' => FormGroup::GettingStarted,
                'description' => 'This section aims to gather information about the company and whether the property is being remortgaged by them or on their behalf.',
                'repeatable_answer_id' => $stepRemortgagerName->answers->firstWhere('details.label', 'Remortgager type')->id,
                'ta_form_template' => FormType::Company,
                'order_number' => 2,
                'type' => PropertyType::Remortgage,
            ])
            ->create();

        $this->theRemortgager($form);
        $this->theCompanyRepresentatives($form);

        $this->theCompanyPowerOfAttorney($form, $this->globalFirstCompanyRepStatusAnswer, '0');
        $this->theCompanyDeputyshipOrder($form, $this->globalFirstCompanyRepStatusAnswer, '0');

        $this->theCompanyPowerOfAttorney($form, $this->globalSecondCompanyRepStatusAnswer, '1');
        $this->theCompanyDeputyshipOrder($form, $this->globalSecondCompanyRepStatusAnswer, '1');
    }

    public function theRemortgager(Form $form)
    {
        // 1.0 Buyer Section
        $remortgagerSection = $form->sections()->create([
            'name' => 'Buyer {{COMPANY NAME}}',
        ]);

        // 1.1 Buyer contact details
        $contactDetailsStep = $remortgagerSection->steps()->create([
            'question' => 'Please enter the contact details for the company:',
            'help_text' => 'The conveyancer/solicitor will require basic contact details for the company.',
        ]);

        // Email
        $contactEmailAnswer = $contactDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Email Address',
                'pdfFormFieldName' => OverviewPdfField::Email,
            ],
        ]);

        $contactEmailAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        // Phone number
        $contactPhoneNumberAnswer = $contactDetailsStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Phone number',
                'pdfFormFieldName' => OverviewPdfField::Phone,
            ],
        ]);

        $contactPhoneNumberAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        // Address
        $contactAddressAnswer = $contactDetailsStep->answers()->create([
            'type' => AnswerType::Address,
            'details' => [
                'label' => 'Company registered address',
                'pdfFormFieldName' => OverviewPdfField::Address,
            ],
        ]);

        $contactAddressAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 1.2 Company number
        $companyNumberSection = $remortgagerSection->steps()->create([
            'question' => 'Please enter the company number:',
        ]);

        // Company number
        $companyNumberAnswer = $companyNumberSection->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Company number',
            ],
        ]);

        $companyNumberAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 1.3 Company VAT
        $companyVATStep = $remortgagerSection->steps()->create([
            'question' => 'Is the company VAT registered?',
        ]);

        $companyVATAnswer = $companyVATStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Yes'],
                    ['value' => 'No'],
                ],
                'pdfFormFieldName' => OverviewPdfField::VatRegistered,
            ],
        ]);

        $companyVATAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 1.3a Company VAT number
        $companyVATNumberStep = $remortgagerSection->steps()->create([
            'question' => 'Please enter the company VAT number:',
        ]);

        $companyVATNumberStep->conditions()->create([
            'answer_id' => $companyVATAnswer->id,
            'selected_value' => 'Yes',
        ]);

        $companyVATNumberAnswer = $companyVATNumberStep->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Company VAT number',
                'pdfFormFieldName' => OverviewPdfField::VatNumber,
            ],
        ]);

        $companyVATNumberAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---

        // 1.4 Company representation
        $companyRepresentationStep = $remortgagerSection->steps()->create([
            'question' => 'Please confirm the representation of the company in the property purchase:',
            'help_text' => 'Section 36A(6) of the Companies Act 1985 provides protection for purchasers where a document is executed in the following fashion: “In favour of a purchaser a document shall be deemed to have been duly executed by a company if it purports to be signed by a director and the secretary of the company, or by two directors of the company”.',
        ]);

        $companyRepresentationAnswer = $companyRepresentationStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'One member of the board of directors'], // Continue to 2.1a
                    ['value' => 'Two members of the board of directors'], // Continue to 2.1a, show 2.1b
                    ['value' => 'One such member and the clerk, secretary, deputy or other permanent officer of the corporation'], // Continue to 2.1a, show 2.1b
                ],
                'pdfFormFieldName' => OverviewPdfField::Representation,
            ],
        ]);

        $this->globalCompanyRepresentationAnswer = $companyRepresentationAnswer;

        $companyRepresentationAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
        // End ---
    }

    public function theCompanyRepresentatives(Form $form)
    {
        // 2.0 Company representative section
        $companyRepSection = $form->sections()->create([
            'name' => 'Company Representatives',
        ]);

        // 2.1a Company rep details
        $companyRepresentativeStep = $companyRepSection->steps()->create([
            'question' => 'Please enter the details for your company representative',
            'help_text' => 'Please enter the details of the representatives who will be acting for the company in the sale. This typically should be at least one director. The conveyancer/solicitor will require at least an email address or phone number for each representative.',
            'type' => StepType::CompanyRepresentative,
        ]);

        StepAnswerGeneration::personInformation(
            $companyRepresentativeStep,
            text: 'representative',
            canSelectPreviousPerson: true,
            canSelectSaleAddress: true,
            pdfField: OverviewPdfField::Representative.'0',
            useSaleAddressPdfField: OverviewPdfField::RepresentativeUseSaleAddress.'0',
        );

        // 2.1b Company rep details
        $secondCompanyRepresentativeStep = $companyRepSection->steps()->create([
            'question' => 'Please enter the details for your second company representative',
            'help_text' => 'Please enter the details of the representatives who will be acting for the company in the sale. This typically should be at least one director. The conveyancer/solicitor will require at least an email address or phone number for each representative.',
            'type' => StepType::CompanyRepresentative,
        ]);

        StepAnswerGeneration::personInformation(
            $secondCompanyRepresentativeStep,
            text: 'representative',
            canSelectPreviousPerson: true,
            canSelectSaleAddress: true,
            pdfField: OverviewPdfField::Representative.'1',
            useSaleAddressPdfField: OverviewPdfField::RepresentativeUseSaleAddress.'1',
        );

        $secondCompanyRepresentativeStep->conditions()->create([
            'answer_id' => $this->globalCompanyRepresentationAnswer->id, // 1.4
            'selected_value' => 'Two members of the board of directors',
            'type' => ConditionType::OR,
        ]);

        $secondCompanyRepresentativeStep->conditions()->create([
            'answer_id' => $this->globalCompanyRepresentationAnswer->id, // 1.4
            'selected_value' => 'One such member and the clerk, secretary, deputy or other permanent officer of the corporation',
            'type' => ConditionType::OR,
        ]);

        // 2.2a Company rep status
        $companyRepresentativeStatusStep = $companyRepSection->steps()->create([
            'question' => 'Please select the status of each company representative',
            'help_text' => ''
                .'<p>Company representatives may be selling the property as themselves or in certain circumstances have someone selling on their behalf.</p>'
                .'<p>If the representative is acting for the company as themself as Director, select &quot;Director acting for themselves&quot;</p>'
                .'<p>If the representative is acting for the company as a secretary or other, select &quot;Other acting for themselves&quot;</p>'
                .'<p>If the representative has an attorney dealing with the sale, select &#39;Selling via attorney&quot;</p>',
        ]);

        $companyRepresentativeStatusAnswer = $companyRepresentativeStatusStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Director acting for themselves'], // End form
                    ['value' => 'Purchasing via attorney'], // Show 3.0
                    ['value' => 'Purchasing via deputy'], // Show 4.0
                    ['value' => 'Other acting for themselves'], // End form
                ],
                'pdfFormFieldName' => OverviewPdfField::RepresentativeRepresentation.'0',
            ],
        ]);

        $this->globalFirstCompanyRepStatusAnswer = $companyRepresentativeStatusAnswer;

        $companyRepresentativeStatusAnswer->validationRules()->create([
            'rule' => 'required',
        ]);

        // 2.2b Company rep status
        $secondCompanyRepresentativeStatusStep = $companyRepSection->steps()->create([
            'question' => 'Please select the status of each company representative',
            'help_text' => ''
                .'<p>Company representatives may be selling the property as themselves or in certain circumstances have someone selling on their behalf.</p>'
                .'<p>If the representative is acting for the company as themself as Director, select &quot;Director acting for themselves&quot;</p>'
                .'<p>If the representative is acting for the company as a secretary or other, select &quot;Other acting for themselves&quot;</p>'
                .'<p>If the representative has an attorney dealing with the sale, select &#39;Selling via attorney&quot;</p>',
        ]);

        $secondCompanyRepresentativeStatusStep->conditions()->create([
            'answer_id' => $this->globalCompanyRepresentationAnswer->id, // 1.4
            'selected_value' => 'Two members of the board of directors',
            'type' => ConditionType::OR,
        ]);

        $secondCompanyRepresentativeStatusStep->conditions()->create([
            'answer_id' => $this->globalCompanyRepresentationAnswer->id, // 1.4
            'selected_value' => 'One such member and the clerk, secretary, deputy or other permanent officer of the corporation',
            'type' => ConditionType::OR,
        ]);

        $secondCompanyRepresentativeStatusAnswer = $secondCompanyRepresentativeStatusStep->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Director acting for themselves'], // End form
                    ['value' => 'Purchasing via attorney'], // Show 3.1
                    ['value' => 'Purchasing via deputy'], // Show 4.1
                    ['value' => 'Other acting for themselves'], // End form
                ],
                'pdfFormFieldName' => OverviewPdfField::RepresentativeRepresentation.'1',
            ],
        ]);

        $this->globalSecondCompanyRepStatusAnswer = $secondCompanyRepresentativeStatusAnswer;

        $secondCompanyRepresentativeStatusAnswer->validationRules()->create([
            'rule' => 'required',
        ]);
    }

    public function theCompanyPowerOfAttorney(Form $form, Answer $companyRepresentativeStatus, string $number)
    {
        // 3.0 Company representative power of attorney section
        $companyPowerOfAttorneySection = $form->sections()->create([
            'name' => 'Power of Attorney',
        ]);

        (new BuyerAttorneySection($companyPowerOfAttorneySection, $number))->createSteps();

        $companyPowerOfAttorneySection->conditions()->create([
            'answer_id' => $companyRepresentativeStatus->id, // 2.2a
            'selected_value' => 'Purchasing via attorney',
        ]);
    }

    public function theCompanyDeputyshipOrder(Form $form, Answer $companyRepresentativeStatus, string $number)
    {
        // 4.0 Company representative power of attorney section
        $companyDeputyshipSection = $form->sections()->create([
            'name' => 'Deputyship Order',
        ]);

        (new BuyerDeputySection($companyDeputyshipSection, $number))->createSteps();

        $companyDeputyshipSection->conditions()->create([
            'answer_id' => $companyRepresentativeStatus->id, // 2.2a
            'selected_value' => 'Purchasing via deputy',
        ]);
    }
}
