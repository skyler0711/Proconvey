<?php

namespace App\GraphQL\Validators\Form;

use App\Rules\UserRelatedToPropertyRule;
use Nuwave\Lighthouse\Validation\Validator;

final class SaveProvidedAnswerInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            // answer_id is validated in the mutation

            'property_id' => [
                'required',
                new UserRelatedToPropertyRule,
            ],
            'value' => ['sometimes'],
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
            'property_id.required' => 'Property id is required',
        ];
    }
}
