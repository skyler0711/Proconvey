<?php

namespace App\GraphQL\Validators\Conveyancer;

use Nuwave\Lighthouse\Validation\Validator;

final class UpdateConveyancerInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'payment_on_account_amount' => ['min:1', 'max:1000000'], // More than £0, less than £10,000
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
            'payment_on_account_amount.min' => 'Payment on account amount must be at least £1',
            'payment_on_account_amount.max' => 'Payment on account amount must be less than £10,000',
        ];
    }
}
