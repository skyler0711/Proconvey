<?php

namespace App\GraphQL\Validators;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

final class UploadSofCheckDocumentsInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'documents' => ['required', 'array'],
            'documents.*.extension' => ['required', Rule::in('pdf')],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'Documents must have unique names',
            'documents.required' => 'At least one document is required',
            'documents.array' => 'At least one document is required',
            'documents.*.extension.in' => 'Document must be a PDF',
        ];
    }
}
