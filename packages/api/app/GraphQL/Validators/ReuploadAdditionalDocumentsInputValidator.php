<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

final class ReuploadAdditionalDocumentsInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', Rule::unique('media', 'name')->ignore($this->arg('file_id'), 'id')],
            'uploaded_document.extension' => ['required', Rule::in('pdf')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Documents must have unique names',
            'uploaded_document.extension.in' => 'Document must be a PDF',
        ];
    }
}
