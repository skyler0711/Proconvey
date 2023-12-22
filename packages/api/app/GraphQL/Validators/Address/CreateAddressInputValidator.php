<?php

namespace App\GraphQL\Validators\Address;

use Nuwave\Lighthouse\Validation\Validator;

final class CreateAddressInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'line_1' => ['required', 'max:255'],
            'line_2' => ['max:255'],
            'city' => ['required', 'max:255'],
            'postcode' => ['required', 'max:255'],
            'uprn' => ['nullable', 'string', 'max:255'],
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
            'line_1.required' => 'Line 1 is required',
            'line_1.max' => 'Line 1 is too long',
            'line_2.max' => 'Line 2 is too long',
            'city.required' => 'City is required',
            'city.max' => 'City is too long',
            'postcode.required' => 'Postcode is required',
            'postcode.max' => 'Postcode is too long',
            'uprn.string' => 'UPRN must be a string',
        ];
    }
}
