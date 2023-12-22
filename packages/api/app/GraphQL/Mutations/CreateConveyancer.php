<?php

namespace App\GraphQL\Mutations;

use App\Models\Conveyancer;
use Illuminate\Support\Arr;

final class CreateConveyancer
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        $conveyancer = Conveyancer::create([
            'name' => $args['name'],
            'company_number' => Arr::get($args, 'company_number'),
            'sra_clc_number' => $args['sra_clc_number'],
            'type' => $args['type'],
            'payment_on_account_amount' => 25000, // Default £250
            'trading_name' => $args['trading_name'],
            'vat_number' => $args['vat_number'],
            'website' => $args['website'],
            'location' => $args['location'],
            'telephone_number' => $args['telephone_number'],
            'email_address' => $args['email_address'],
            'address' => $args['address'],
        ]);

        $user->conveyancer()->associate($conveyancer)->save();

        if (isset($args['logo_image'])) {
            if ($conveyancer->logo_image) {
                $conveyancer->logo_image->delete();
            }
            $conveyancer
            ->addMediaFromDisk($args['logo_image']['key'])
            ->usingFileName(explode('/', $args['logo_image']['key'])[1].'.'.$args['logo_image']['extension'])
            ->toMediaCollection('logo_image');
        } else {
            if ($conveyancer->logo_image && $args['logo_image'] === null) {
                $conveyancer->logo_image->delete();
            }
        }

        if (isset($args['address'])) {
            $conveyancer->address()->create($args['address']);
        }

        return $conveyancer;
    }
}
