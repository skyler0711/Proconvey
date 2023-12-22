<?php

namespace App\Rules;

use App\Models\Property;
use Illuminate\Contracts\Validation\Rule;

class FileAnswer implements Rule
{
    protected Property $property;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Property $property)
    {
        $this->property = $property;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (is_string($value) && ($value === 'Not applicable' || $value === 'Add later')) {
            return true;
        }

        if (is_array($value) && array_key_exists('key', $value) && array_key_exists('extension', $value)) {
            return true;
        }

        if (! is_array($value) && $this->property->providedAnswers()->whereHas('media', fn ($query) => $query->where('id', $value))->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Please submit a valid file.';
    }
}
