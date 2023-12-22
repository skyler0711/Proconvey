<?php

namespace App\GraphQL\Validators\Client;

use App\Rules\IsValidPassword;
use Nuwave\Lighthouse\Validation\Validator;

final class RegisterClientInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', new IsValidPassword()],
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
            'email.required' => 'This email is required',
            'email.email' => 'This email is invalid',
            'email.max' => 'Email is too long',
            'password.required' => 'The password must be at least 8 characters, and be a mix of upper and lower case characters.',
        ];
    }
}
