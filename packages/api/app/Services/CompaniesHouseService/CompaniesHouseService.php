<?php

namespace App\Services\CompaniesHouseService;

use Illuminate\Support\Facades\Http;

class CompaniesHouseService
{
    protected $http;

    public function __construct()
    {
        $this->http = Http::baseUrl(
            config('services.companies_house.sandbox')
                ? 'https://api-sandbox.company-information.service.gov.uk'
                : 'https://api.company-information.service.gov.uk',
        )
            ->withBasicAuth(config('services.companies_house.key'), '')
            ->withHeaders([
                'Accept' => 'application/json',
            ]);
    }

    /**
     * Validate a company from their company number
     */
    public function validateCompany(string $companyNumber): bool
    {
        $response = $this->http->get("/company/{$companyNumber}");

        if (in_array($response->status(), [400, 401, 403])) {
            throw new CompaniesHouseException("Unable to query Companies House. Response: {$response->body()}");
        }

        return $response->status() === 200;
    }
}
