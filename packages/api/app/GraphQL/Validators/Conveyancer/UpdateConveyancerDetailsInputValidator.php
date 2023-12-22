<?php

namespace App\GraphQL\Validators\Conveyancer;

use App\Rules\CompaniesHouseNumber;
use Nuwave\Lighthouse\Validation\Validator;

final class UpdateConveyancerDetailsInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'company_number' => [new CompaniesHouseNumber, 'sometimes', 'required'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'A name is required',
            'company_number.required' => 'A company number is required',
        ];
    }
}
