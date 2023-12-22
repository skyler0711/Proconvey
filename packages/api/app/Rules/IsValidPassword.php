<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class IsValidPassword implements Rule
{
    public $minLength = 8;

    /**
     * Determine if the Length Validation Rule passes.
     *
     * @var bool
     */
    public $lengthPasses = true;

    /**
     * Determine if the Uppercase Validation Rule passes.
     *
     * @var bool
     */
    public $mixedCasePasses = true;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->lengthPasses = (Str::length($value) >= $this->minLength);
        $this->mixedCasePasses = (preg_match('/^(?=.*[a-z])(?=.*[A-Z])/', $value));

        return $this->lengthPasses && $this->mixedCasePasses;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The password must be at least $this->minLength characters, and be a mix of upper and lower case characters.";
    }
}
