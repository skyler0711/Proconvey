<?php

namespace App\GraphQL\Validators\Conveyancer;

use Nuwave\Lighthouse\Validation\Validator;

final class AddNewPartyInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required_if:owner_type,company', 'string', 'max:255'],
            'last_name' => ['required_if:owner_type,company', 'string', 'max:255'],
            'email' => ['required_if:owner_type,company', 'email', 'max:255', 'unique:users,email'],
            'owner_first_name' => ['required_if:owner_type,individual', 'string', 'max:255'],
            'owner_last_name' => ['required_if:owner_type,individual', 'string', 'max:255'],
            'owner_email' => ['required_if:owner_type,individual', 'email', 'max:255', 'unique:users,email'],
            'representative_first_name' => ['required_if:representation,attorney', 'required_if:representation,deputy', 'required_if:representation,executor', 'required_if:party_type,attorney', 'required_if:party_type,deputy', 'required_if:party_type,executor', 'string', 'max:255'],
            'representative_last_name' => ['required_if:representation,attorney', 'required_if:representation,deputy', 'required_if:representation,executor', 'required_if:party_type,attorney', 'required_if:party_type,deputy', 'required_if:party_type,executor', 'string', 'max:255'],
            'representative_email' => ['required_if:representation,attorney', 'required_if:representation,deputy', 'required_if:representation,executor', 'required_if:party_type,attorney', 'required_if:party_type,deputy', 'required_if:party_type,executor', 'email', 'max:255', 'unique:users,email'],
            'id_check' => ['required', 'boolean'],
            'representation' => ['required_if:owner_type,individual', 'string', 'max:255'],
            'party_type' => ['
            required', 'string', 'max:255'],
            'owner_type' => [
                'required_if:party_type,owner',
                'string', 'max:255',
            ],
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
            'first_name.required_if' => 'First name is required',
            'first_name.string' => 'First name must be a name',
            'first_name.max' => 'First name is too long',
            'last_name.required_if' => 'Last name is required',
            'last_name.string' => 'Last name must be a name',
            'last_name.max' => 'Last name is too long',
            'email.required_if' => 'Email is required',
            'email.email' => 'Email is invalid',
            'email.unique' => 'Email is already in use',
            'email.max' => 'Email is too long',
            'owner_first_name.required_if' => 'First name is required',
            'owner_first_name.string' => 'First name must be a name',
            'owner_first_name.max' => 'First name is too long',
            'owner_last_name.required_if' => 'Last name is required',
            'owner_last_name.string' => 'Last name must be a name',
            'owner_last_name.max' => 'Last name is too long',
            'owner_email.required_if' => 'Email is required',
            'owner_email.email' => 'Email is invalid',
            'owner_email.unique' => 'Email is already in use',
            'owner_email.max' => 'Email is too long',
            'representative_first_name.required_if' => 'First name is required',
            'representative_first_name.string' => 'First name must be a name',
            'representative_first_name.max' => 'First name is too long',
            'representative_last_name.required_if' => 'Last name is required',
            'representative_last_name.string' => 'Last name must be a name',
            'representative_last_name.max' => 'Last name is too long',
            'representative_email.required_if' => 'Email is required',
            'representative_email.email' => 'Email is invalid',
            'representative_email.unique' => 'Email is already in use',
            'representative_email.max' => 'Email is too long',
            'party_type.required_if' => 'Please select a party type',
            'owner_type.required_if' => 'Please select an owner type',
            'representation.required_if' => 'Please select who is representing',
            'id_check.required' => 'Please select if ID check is required',
            'client_care_letter.required' => 'Please select if client care letter is required',
        ];
    }
}
