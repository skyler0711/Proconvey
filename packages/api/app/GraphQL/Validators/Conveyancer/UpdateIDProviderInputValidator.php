<?php

namespace App\GraphQL\Validators\Conveyancer;

use App\Enums\IDProviders;
use BenSampo\Enum\Rules\EnumValue;
use Nuwave\Lighthouse\Validation\Validator;

final class UpdateIDProviderInputValidator extends Validator
{
    /**
     * Return the validation rules.
     *
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'provider' => ['required', new EnumValue(IDProviders::class)],
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
            'provider.required' => 'ID Provider is required',
        ];
    }
}
