<?php

namespace App\Rules;

use App\Services\CompaniesHouseService\CompaniesHouseService;
use Illuminate\Contracts\Validation\Rule;

class CompaniesHouseNumber implements Rule
{
    protected $companiesHouseService;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->companiesHouseService = app()->make(CompaniesHouseService::class);
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
        return $this->companiesHouseService->validateCompany($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid company number';
    }
}
