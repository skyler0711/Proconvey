<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static getSelectableTypes()
 * @method static static getGeneratedTypes()
 *
 * @value string Address
 * @value string SingleSelect
 * @value string MultiSelect
 * @value string Number
 * @value string Dropdown
 * @value string Text
 * @value string Textarea
 * @value string Checkbox
 * @value string File
 * @value string OwnerDropdown
 * @value string PersonMultiSelect
 * @value string DataTable
 */
final class AnswerType extends Enum
{
    const Address = 'address';

    const SingleSelect = 'single_select';

    const MultiSelect = 'multi_select';

    const Number = 'number';

    const Dropdown = 'dropdown';

    const Text = 'text';

    const Textarea = 'textarea';

    const Checkbox = 'checkbox';

    const File = 'file';

    const OwnerDropdown = 'owner_dropdown';

    const PersonMultiSelect = 'person_multi_select';

    const DataTable = 'data_table';
}

AnswerType::macro('getSelectableTypes', function () {
    return [
        AnswerType::SingleSelect,
        AnswerType::Dropdown,
        AnswerType::Checkbox,
        AnswerType::OwnerDropdown,
        AnswerType::PersonMultiSelect,
        AnswerType::DataTable,
    ];
});

AnswerType::macro('getGeneratedTypes', function () {
    return [
        AnswerType::OwnerDropdown,
        AnswerType::PersonMultiSelect,
    ];
});
