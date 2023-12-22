<?php

namespace App\GraphQL\Validators\Auth;

use App\Enums\UserJobRole;
use BenSampo\Enum\Rules\EnumValue;
use Nuwave\Lighthouse\Validation\Validator;

final class UpdateUserDetailsInputValidator extends Validator
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
            'title' => ['required', 'max:255'],
            'suffix' => ['max:255'],
            'job_role' => ['required', new EnumValue(UserJobRole::class)],
            'phone' => ['max:255'],
            'sra_clc_number' => ['string'],
            'job_bio' => ['max:255'],
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
            'last_name.required' => 'Last name is required',
            'title.required' => 'Title is required',
            'suffix.max' => 'Suffix should not exceed 255 characters',
            'job_role.required' => 'Job role is required',
            'job_role.enum_value' => 'Job role is invalid',
            'phone.max' => 'Phone number should not exceed 255 characters',
            'sra_clc_number.string' => 'SRA/CLC number should contain only numeric characters',
            'job_bio.max' => 'Job bio should not exceed 255 characters',
        ];
    }
}
