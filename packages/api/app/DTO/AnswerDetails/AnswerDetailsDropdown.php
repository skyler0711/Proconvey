<?php

namespace App\DTO\AnswerDetails;

final class AnswerDetailsDropdown
{
    public ?string $label;

    public ?string $pdfFormFieldName;

    /** @var array<AnswerDetailsDropdownOption> */
    public array $options;

    public function __construct(?string $label, array $options, ?string $pdfFormFieldName)
    {
        $this->label = $label;
        $this->options = $options;
        $this->pdfFormFieldName = $pdfFormFieldName;
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        $options = array_map(fn (array $option) => new AnswerDetailsDropdownOption($option['value'], optional($option)['pdfFormFieldName']), $data['options']);

        return new self(optional($data)['label'], $options, optional($data)['pdfFormFieldName']);
    }
}
