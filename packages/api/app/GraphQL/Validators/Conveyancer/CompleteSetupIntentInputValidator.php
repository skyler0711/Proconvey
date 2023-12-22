<?php

namespace App\GraphQL\Validators\Conveyancer;

use Nuwave\Lighthouse\Validation\Validator;

final class CompleteSetupIntentInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'max:255'],
            'email' => ['required', 'email'],
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
            'payment_method.required' => 'Payment method is required',
            'payment_method.max' => 'Payment method is too long',
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
        ];
    }
}
