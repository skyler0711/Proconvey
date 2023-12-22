<?php

namespace App\GraphQL\Validators\Conveyancer;

use Nuwave\Lighthouse\Validation\Validator;

final class UpdateStripeCodeInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'code' => ['required'],
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
            'code.required' => 'Code is required',
        ];
    }
}
