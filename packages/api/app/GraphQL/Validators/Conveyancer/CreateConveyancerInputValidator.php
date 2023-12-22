<?php

namespace App\GraphQL\Validators\Conveyancer;

use App\Enums\ConveyancerType;
use App\Rules\CompaniesHouseNumber;
use BenSampo\Enum\Rules\EnumValue;
use Nuwave\Lighthouse\Validation\Validator;

final class CreateConveyancerInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'max:255'],
            'type' => ['required', new EnumValue(ConveyancerType::class)],
            'company_number' => ['required', new CompaniesHouseNumber],
            'sra_clc_number' => ['required', 'max:255'],
            'telephone_number' => ['required', 'max:255'],
            'email_address' => ['required', 'email', 'max:255'],
            'trading_name' => ['string', 'max:255'],
            'vat_number' => ['string', 'max:255'],
            'website' => ['string', 'max:255'],
            'location' => ['string', 'max:255'],
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
            'name.required' => 'Name is required',
            'name.max' => 'Name is too long',
            'type.enum_value' => 'Type is invalid',
            'type.required' => 'Type is required',
            'company_number.required' => 'Company number is required',
            'company_number.companies_house_number' => 'Company number is invalid',
            'sra_clc_number.required' => 'SRA CLC number is required',
            'telephone_number.required' => 'Branch telephone number is required',
            'telephone_number.max' => 'Branch telephone number is too long',
            'email_address.required' => 'Email address is required',
            'email_address.email' => 'Email address is invalid',
            'email_address.max' => 'Email address is too long',
            'trading_name.string' => 'Trading name is invalid',
            'trading_name.max' => 'Trading name is too long',
            'vat_number.string' => 'VAT number is invalid',
            'vat_number.max' => 'VAT number is too long',
            'website.string' => 'Website name is invalid',
            'website.max' => 'Website URL is too long',
            'location.string' => 'Location is invalid',
            'location.max' => 'Location is too long',
        ];
    }
}
