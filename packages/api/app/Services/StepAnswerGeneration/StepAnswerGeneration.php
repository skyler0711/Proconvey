<?php

namespace App\Services\StepAnswerGeneration;

use App\Enums\AnswerType;
use App\Enums\ConditionType;
use App\Enums\FileTextAnswerTypes;
use App\Enums\OverviewPdfField;
use App\Models\Step;

final class StepAnswerGeneration
{
    /**
     * The function `personInformation` creates a form for entering personal information
     *
     * Always:
     * - Title
     * - First name
     * - Middle name(s)
     * - Surname
     * - Email address
     * - Phone number
     * - Correspondence address
     *
     * Optional:
     * - Previous person
     * - Sale address
     *
     * @param Step
     * @param text
     * @param canSelectPreviousPerson
     * @param canSelectSaleAddress
     * @param pdfField
     * @param useSaleAddressPdfField
     */
    public static function personInformation(
      Step $step,
      ?string $text = 'owner',
      ?bool $canSelectPreviousPerson = false,
      ?bool $canSelectSaleAddress = false,
      ?string $pdfField = null,
      ?string $useSaleAddressPdfField = null,
    ) {
        if ($canSelectPreviousPerson) {
            $answerSelectableDropdown = $step->answers()->create([
                'type' => AnswerType::OwnerDropdown,
                'details' => [
                    'label' => 'Select from a previously added person',
                    'placeholder' => 'Select a person',
                    'pdfFormFieldName' => $pdfField,
                ],
            ]);

            $answerSelectableDropdown->validationRules()->create([
                'rule' => 'required',
            ]);

            $answerShowAttorneyForm = $step->answers()->create([
                'type' => AnswerType::Checkbox,
                'details' => [
                    'label' => 'Add a new person',
                ],
            ]);

            $answerSelectableDropdown->conditions()->create([
                'answer_id' => $answerShowAttorneyForm->id,
                'selected_value' => '0',
            ]);
        }

        $answerAttorneyTitle = $step->answers()->create([
            'type' => AnswerType::Dropdown,
            'details' => [
                'label' => 'Title',
                'options' => [
                    ['value' => 'Mr', 'pdfFormFieldName' => $pdfField],
                    ['value' => 'Mrs', 'pdfFormFieldName' => $pdfField],
                    ['value' => 'Miss', 'pdfFormFieldName' => $pdfField],
                    ['value' => 'Ms', 'pdfFormFieldName' => $pdfField],
                ],
                'pdfFormFieldName' => $pdfField,
            ],
        ]);

        $answerAttorneyTitle->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerAttorneyFirstName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'First name',
                'placeholder' => "Enter $text first name",
                'pdfFormFieldName' => $pdfField,
            ],
        ]);

        $answerAttorneyFirstName->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerAttorneyMiddleName = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Middle name(s)',
                'placeholder' => "Enter $text middle name(s)",
                'pdfFormFieldName' => $pdfField,
            ],
        ]);

        $answerAttorneySurname = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Surname',
                'placeholder' => "Enter $text surname",
                'pdfFormFieldName' => $pdfField,
            ],
        ]);

        $answerAttorneySurname->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerAttorneyEmail = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Email address',
                'placeholder' => 'name@company.com',
                'pdfFormFieldName' => $pdfField,
            ],
        ]);

        $answerAttorneyEmail->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerAttorneyPhone = $step->answers()->create([
            'type' => AnswerType::Text,
            'details' => [
                'label' => 'Phone number',
                'placeholder' => '+44 ---- -- -- --',
                'pdfFormFieldName' => $pdfField,
            ],
        ]);

        $answerAttorneyPhone->validationRules()->create([
            'rule' => 'required',
        ]);

        $answerAttorneyAddress = $step->answers()->create([
            'type' => AnswerType::Address,
            'details' => [
                'label' => 'Correspondence address',
                'pdfFormFieldName' => $pdfField,
            ],
        ]);

        $answerAttorneyAddress->validationRules()->create([
            'rule' => 'required',
        ]);

        if ($canSelectSaleAddress) {
            $answerAddressSameAsSale = $step->answers()->create([
                'type' => AnswerType::Checkbox,
                'details' => [
                    'label' => 'Same as the sale address',
                    'pdfFormFieldName' => $useSaleAddressPdfField ?? $pdfField,
                ],
            ]);

            $answerAddressSameAsSale->validationRules()->create([
                'rule' => 'required',
            ]);

            $answerAttorneyAddress->conditions()->create([
                'answer_id' => $answerAddressSameAsSale->id,
                'selected_value' => '0',
            ]);

            if ($canSelectPreviousPerson) {
                $answerAddressSameAsSale->conditions()->create([
                    'answer_id' => $answerShowAttorneyForm->id,
                    'selected_value' => '1',
                ]);
            }
        }

        if ($canSelectPreviousPerson) {
            $answerAttorneyTitle->conditions()->create([
                'answer_id' => $answerShowAttorneyForm->id,
                'selected_value' => '1',
            ]);
            $answerAttorneyFirstName->conditions()->create([
                'answer_id' => $answerShowAttorneyForm->id,
                'selected_value' => '1',
            ]);
            $answerAttorneyMiddleName->conditions()->create([
                'answer_id' => $answerShowAttorneyForm->id,
                'selected_value' => '1',
            ]);
            $answerAttorneySurname->conditions()->create([
                'answer_id' => $answerShowAttorneyForm->id,
                'selected_value' => '1',
            ]);
            $answerAttorneyEmail->conditions()->create([
                'answer_id' => $answerShowAttorneyForm->id,
                'selected_value' => '1',
            ]);
            $answerAttorneyPhone->conditions()->create([
                'answer_id' => $answerShowAttorneyForm->id,
                'selected_value' => '1',
            ]);
            $answerAttorneyAddress->conditions()->create([
                'answer_id' => $answerShowAttorneyForm->id,
                'selected_value' => '1',
            ]);
        }
    }

    public static function nameChange(
        Step $step,
        ?string $number = '',
    ) {
        $answerNameChange = $step->answers()->create([
            'type' => AnswerType::SingleSelect,
            'details' => [
                'options' => [
                    ['value' => 'Marriage/Civil Partnership'],
                    ['value' => 'Divorce/Dissolved Civil Partnership'],
                    ['value' => 'Change of name deed'],
                    ['value' => 'Not applicable'],
                ],
                'pdfFormFieldName' => OverviewPdfField::RepresentativeRepresentativesNameChangeReason.$number,
            ],
        ]);

        $answerNameChange->validationRules()->create([
            'rule' => 'required',
        ]);

        $uploadAnswerNameChange = $step->answers()->create([
            'type' => AnswerType::File,
            'details' => [
                'pdfFormFieldName' => OverviewPdfField::RepresentativeRepresentativesNameChangeProof.$number,
                'textAnswers' => [
                    FileTextAnswerTypes::Enclosed => 'Enclosed',
                    FileTextAnswerTypes::AddLater => 'To follow',
                    FileTextAnswerTypes::NotApplicable => 'N/A',
                ],
            ],
        ]);

        $uploadAnswerNameChange->conditions()->create([
            'answer_id' => $answerNameChange->id,
            'selected_value' => 'Marriage/Civil Partnership',
            'type' => ConditionType::OR,
        ]);
        $uploadAnswerNameChange->conditions()->create([
            'answer_id' => $answerNameChange->id,
            'selected_value' => 'Divorce/Dissolved Civil Partnership',
            'type' => ConditionType::OR,
        ]);
        $uploadAnswerNameChange->conditions()->create([
            'answer_id' => $answerNameChange->id,
            'selected_value' => 'Change of name deed',
            'type' => ConditionType::OR,
        ]);

        $uploadAnswerNameChange->validationRules()->create([
            'rule' => 'required',
        ]);
    }
}
