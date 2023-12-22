<?php

namespace App\GraphQL\Validators\Conveyancer;

use App\Enums\PropertyType;
use BenSampo\Enum\Rules\EnumValue;
use Nuwave\Lighthouse\Validation\Validator;

final class InviteNewClientInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'max:255'],
            'last_name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'case_reference' => ['required', 'max:255'],
            'type' => ['required', new EnumValue(PropertyType::class)],
            'sale_price' => ['required_if:letters_required,true', 'numeric', 'nullable'],
            'conveyancing_fee' => ['required_if:letters_required,true', 'numeric', 'nullable'],
            'fee_earner_id' => ['required_if:letters_required,true', 'exists:users,id'],
            'letters_required' => ['required', 'boolean'],
            'id_check_required' => ['required', 'boolean'],
            'payment_amount' => ['required_if:payment_required,true', 'numeric', 'min:300', 'nullable'],
            'payment_required' => ['required', 'boolean'],
            'sof_check_required' => ['required', 'boolean'],
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
            'first_name.required' => 'First name is required',
            'first_name.max' => 'First name is too long',
            'last_name.required' => 'Last name is required',
            'last_name.max' => 'Last name is too long',
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'email.max' => 'Email is too long',
            'case_reference.required' => 'Case reference is required',
            'case_reference.max' => 'Case reference is too long',
            'sale_price.numeric' => 'Sale price must be a number',
            'sale_price.required_if' => 'Sale price is required',
            'conveyancing_fee.numeric' => 'Conveyancing fee must be a number',
            'conveyancing_fee.required_if' => 'Conveyancing fee is required',
            'fee_earner_id.required_if' => 'Fee earner is required',
            'fee_earner_id.exists' => 'Fee earner is invalid',
            'letters_required.required' => 'Please select if letters are required',
            'id_check_required.required' => 'Please select if ID check is required',
            'payment_required.required' => 'Please select if payment is required',
            'payment_amount.numeric' => 'Payment amount must be a number',
            'payment_amount.required_if' => 'Payment amount is required',
            'sof_check_required.required' => 'Please select if a source of funds check is required',
            'payment_amount.min' => 'Payment amount must be at least £3',
        ];
    }
}
