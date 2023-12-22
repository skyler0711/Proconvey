<?php

namespace App\GraphQL\Validators\Auth;

use App\Enums\UserJobRole;
use App\Rules\IsValidPassword;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

final class UpdateUserProfileInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        $user = auth()->user();

        return [
            'first_name' => ['required', 'max:255'],
            'last_name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id, 'id')],
            'phone' => ['required', 'max:255'],

            'title' => ['sometimes', 'required', 'max:255'],
            'suffix' => ['sometimes', 'max:255'],
            'job_role' => ['sometimes', 'required', new EnumValue(UserJobRole::class)],
            'job_bio' => ['sometimes', 'required', 'max:255'],
            'profile_image' => ['nullable', 'array'],

            'newPassword' => [
                new IsValidPassword(),
                'nullable',
                'different:password',
                'required_if:password,filled',
            ],
            'password' => [
                'required_if:newPassword,filled',
                'current_password',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'email.unique' => 'Email is already taken',
            'phone.required' => 'Phone is required',

            'title.required' => 'Title is required',
            'job_role.required' => 'Job role is required',
            'job_role.enum_value' => 'Job role is invalid',
            'job_bio.required' => 'Job bio is required',

            'newPassword.min' => 'The new password must at least 8 characters.',
            'newPassword.different' => 'The new password must be different from the current password.',
        ];
    }
}
