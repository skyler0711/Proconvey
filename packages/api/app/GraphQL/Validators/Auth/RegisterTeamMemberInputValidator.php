<?php

namespace App\GraphQL\Validators\Auth;

use App\Rules\IsValidPassword;
use Nuwave\Lighthouse\Validation\Validator;

final class RegisterTeamMemberInputValidator extends Validator
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
            'password' => ['required', new IsValidPassword()],
            'title' => ['required', 'max:255'],
            'suffix' => ['max:255'],
            'phone' => ['required', 'max:255'],
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
            'title.required' => 'Title is required',
            'title.max' => 'Title is too long',
            'suffix.max' => 'Suffix is too long',
            'phone.required' => 'Phone is required',
            'phone.max' => 'Phone is too long',
            'password.required' => 'The password must be at least 8 characters, and be a mix of upper and lower case characters.',
        ];
    }
}
