<?php

namespace App\GraphQL\Validators\Conveyancer;

use Illuminate\Validation\Rule;
use Nuwave\Lighthouse\Validation\Validator;

final class UpdateExistingPartyInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->arg('user_id'), 'id')],
            'phone' => ['required', 'max:255'],
            'title' => ['sometimes', 'required', 'max:10'],
            'occupation' => ['sometimes', 'required', 'max:255'],
            'address' => ['required', 'array'],
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
            'first_name.required' => 'First name is required',
            'first_name.string' => 'First name must be a name',
            'first_name.max' => 'First name is too long',
            'middle_name.string' => 'Middle name must be a name',
            'middle_name.max' => 'Middle name is too long',
            'last_name.required' => 'Last name is required',
            'last_name.string' => 'Last name must be a name',
            'last_name.max' => 'Last name is too long',
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'email.unique' => 'Email is already taken',
            'email.max' => 'Email is too long',
            'phone.required' => 'Phone is required',
            'phone.max' => 'Phone is too long',
            'phone.numeric' => 'Main Contact Number must be a number',
            'title.required' => 'Title is required',
            'title.max' => 'Title is too long',
            'occupation.required' => 'Occupation is required',
            'occupation.max' => 'Occupation is too long',
            'address.required' => 'Address is required',
        ];
    }
}
