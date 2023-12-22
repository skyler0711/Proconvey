<?php

namespace App\GraphQL\Queries;

use Illuminate\Support\Facades\Http;

final class GetAddressFromOS2API
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $searchAddress = $args['address'];
        $searchAddress = urlencode($searchAddress);
        $apiKey = config('services.os_dh.key');
        $url = "https://api.os.uk/search/places/v1/find?maxresults=1&query={$searchAddress}&key={$apiKey}";

        $response = Http::get($url);
        $data = $response->json()['results'][0]['DPA'];

        $addressLine1 = ($data['BUILDING_NAME'] ?? $data['BUILDING_NUMBER']);
        $addressLine2 = ($data['DEPENDENT_LOCALITY'] ?? $data['THOROUGHFARE_NAME']);

        $address = [
            'line_1' => $addressLine1,
            'line_2' => $addressLine2,
            'city' => !empty($data['POST_TOWN']) ? $data['POST_TOWN'] : (!empty($data['TOWN_NAME']) ? $data['TOWN_NAME'] : "N/A"),
            'postcode' => !empty($data['POSTCODE']) ? $data['POSTCODE'] : (!empty($data['POSTAL_COUNTY']) ? $data['POSTAL_COUNTY'] : "N/A"),
            'uprn' => $data['UPRN'],
        ];

        return $address;
    }
}
