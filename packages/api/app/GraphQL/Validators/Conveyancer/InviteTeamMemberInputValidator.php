<?php

namespace App\GraphQL\Validators\Conveyancer;

use App\Enums\UserJobRole;
use BenSampo\Enum\Rules\EnumValue;
use Nuwave\Lighthouse\Validation\Validator;

final class InviteTeamMemberInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'job_role' => ['required', new EnumValue(UserJobRole::class)],
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
            'email.required' => 'Email address is required',
            'email.email' => 'This email address is invalid',
            'email.max' => 'This email address is too long',
            'email.unique' => 'This email address is already in use',
            'job_role.required' => 'Job role is required',
            'job_role.enum_value' => 'This job role is invalid',
        ];
    }
}
