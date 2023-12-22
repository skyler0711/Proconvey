<?php

namespace App\DTO\AnswerDetails;

final class AnswerDetailsSingleSelect
{
    /** @var array<AnswerDetailsSingleSelectOption> */
    public array $options;

    public ?string $label;

    public ?string $pdfFormFieldName;

    public function __construct(array $options, ?string $label, ?string $pdfFormFieldName)
    {
        $this->options = $options;
        $this->label = $label;
        $this->pdfFormFieldName = $pdfFormFieldName;
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        $options = array_map(
            fn (array $option) => new AnswerDetailsSingleSelectOption($option['value'], optional($option)['pdfFormFieldName'], optional($option)['altText']),
            $data['options'],
        );

        return new self($options, optional($data)['label'], optional($data)['pdfFormFieldName']);
    }
}
