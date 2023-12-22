<?php

namespace App\GraphQL\Validators\Form;

use Nuwave\Lighthouse\Validation\Validator;

final class SaveProvidedAnswersInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }

    /**
     * Return the validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'provided_answers.required' => 'Answers are required',
            'provided_answers.array' => 'Answers must be an array',
        ];
    }
}
