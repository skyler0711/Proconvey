<?php

namespace App\GraphQL\Validators\Conveyancer;

use Nuwave\Lighthouse\Validation\Validator;

final class CreateSetupIntentInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'address' => ['required'],
            'name' => ['required'],
            'card_number' => ['required', 'boolean'],
            'card_expiry_date' => ['required', 'boolean'],
            'card_cvv' => ['required', 'boolean'],
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
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'name.required' => 'Name is required',
            'address.required' => 'Address is required',
            'card_number.required' => 'Card Number is required',
            'card_expiry_date.required' => 'Expiry date is required',
            'card_cvv.required' => 'Card CVV is required',
        ];
    }
}
