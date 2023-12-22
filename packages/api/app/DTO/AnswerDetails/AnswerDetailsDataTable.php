<?php

namespace App\DTO\AnswerDetails;

final class AnswerDetailsDataTable
{
    public ?string $label;

    public array $rows;

    public array $columns;

    public bool $allowsAddMore;

    public ?string $addMoreLabel;

    public function __construct(?string $label, array $rows, array $columns, bool $allowsAddMore, ?string $addMoreLabel)
    {
        $this->label = $label;
        $this->rows = $rows;
        $this->columns = $columns;
        $this->allowsAddMore = $allowsAddMore;
        $this->addMoreLabel = $addMoreLabel;
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);

        $rows = array_map(fn (array $row) => new AnswerDetailsDataTableRow($row['name'], $row['pdfFieldPrefix']), $data['rows']);

        $columns = array_map(fn (array $column) => new AnswerDetailsDataTableColumn($column['name'], $column['type'], $column['placeholder'] ?? null, $column['pdfFieldSuffix']), $data['columns']);

        $allowsAddMore = $data['allowsAddMore'] ?? false;

        $addMoreLabel = $data['addMoreLabel'] ?? null;

        return new self(optional($data)['label'], $rows, $columns, $allowsAddMore, $addMoreLabel);
    }
}
