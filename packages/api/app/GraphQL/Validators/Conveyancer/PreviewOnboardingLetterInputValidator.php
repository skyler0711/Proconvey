<?php

namespace App\GraphQL\Validators\Conveyancer;

use Nuwave\Lighthouse\Validation\Validator;

final class PreviewOnboardingLetterInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'content' => ['required'],
            'header' => ['nullable'],
            'footer' => ['nullable'],
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
            'content.required' => 'Content is required',
        ];
    }
}
