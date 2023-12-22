<?php

namespace App\GraphQL\Mutations\Auth;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

final class UpdateConveyancerDetails
{
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\User */
        $user = Auth::user();

        $user->conveyancer->update([
            'name' => Arr::get($args, 'name'),
            'company_number' => Arr::get($args, 'company_number'),
            'sra_clc_number' => Arr::get($args, 'sra_clc_number'),
            'trading_name' => $args['trading_name'],
            'vat_number' => $args['vat_number'],
            'website' => $args['website'],
            'location' => $args['location'],
            'telephone_number' => $args['telephone_number'],
            'email_address' => $args['email_address'],
            'address' => $args['address'],
        ]);

        $user->conveyancer->address->update([
            'line_1' => $args['address']['line_1'],
            'line_2' => optional($args['address'])['line_2'],
            'city' => $args['address']['city'],
            'postcode' => $args['address']['postcode'],
        ]);

        if (isset($args['logo_image'])) {
            if ($user->conveyancer->logo_image) {
                $user->conveyancer->logo_image->delete();
            }
            $user->conveyancer
              ->addMediaFromDisk($args['logo_image']['key'])
              ->usingFileName(explode('/', $args['logo_image']['key'])[1].'.'.$args['logo_image']['extension'])
              ->toMediaCollection('logo_image');
        } else {
            if ($user->conveyancer->logo_image && $args['logo_image'] === null) {
                $user->conveyancer->logo_image->delete();
            }
        }

        return $user->conveyancer;
    }
}
