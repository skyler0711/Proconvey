<?php

namespace App\GraphQL\Validators\Conveyancer;

use App\Enums\UserJobRole;
use BenSampo\Enum\Rules\EnumValue;
use Nuwave\Lighthouse\Validation\Validator;

final class InviteTeamMembersInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'team_members' => ['required', 'array', 'min:1'],
            'team_members.*.email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'team_members.*.job_role' => ['required', new EnumValue(UserJobRole::class)],
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
            'team_members.*.email.required' => 'Email is required',
            'team_members.*.email.email' => 'Email is invalid',
            'team_members.*.email.max' => 'Email is too long',
            'team_members.*.email.unique' => 'This email address is already in use',
            'team_members.*.job_role.required' => 'Job role is required',
            'team_members.*.job_role.enum_value' => 'Job role is invalid',
        ];
    }
}
