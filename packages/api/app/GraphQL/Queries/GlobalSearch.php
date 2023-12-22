<?php

namespace App\GraphQL\Queries;

use App\Models\Property;
use Illuminate\Support\Facades\DB;

final class GlobalSearch
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        $search = $args['input']['search'];
        $conveyancer_id = auth()->user()->conveyancer_id;

        $properties = Property::query()
            ->join('addresses', 'properties.id', '=', 'addresses.addressable_id')
            ->leftJoin('property_user', 'properties.id', '=', 'property_user.property_id')
            ->leftJoin('users', 'property_user.user_id', '=', 'users.id')
            ->where(function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where(DB::raw("CONCAT_WS(' ', line_1, line_2, city, postcode)"), 'LIKE', "%$search%")
                        ->orWhere(DB::raw("CONCAT_WS(' ', line_1, postcode)"), 'LIKE', "%$search%")
                        ->orWhere(DB::raw("CONCAT_WS(' ', line_1, city)"), 'LIKE', "%$search%")
                        ->orWhere(DB::raw("CONCAT_WS(' ', line_2, city)"), 'LIKE', "%$search%")
                        ->orWhere(DB::raw("CONCAT_WS(' ', line_2, postcode)"), 'LIKE', "%$search%");
                })->orWhere(function ($query) use ($search) {
                    $query->where(DB::raw("CONCAT_WS(' ', users.first_name, users.last_name)"), 'LIKE', "%$search%")
                    ->orWhere('users.email', 'LIKE', "%$search%")
                    ->orWhere('users.phone', 'LIKE', "%$search%")
                    ->orWhere('users.job_role', 'LIKE', "%$search%");
                })->orWhere(function ($query) use ($search) {
                    $query->where('properties.case_reference', 'LIKE', "%$search%");
                });
            })
            ->where('addresses.addressable_type', '=', 'property')
            ->where('properties.conveyancer_id', '=', $conveyancer_id)
            ->distinct()
            ->get(['properties.*']);

        return $properties->map(function ($property) {
            return [
                'type' => class_basename($property),
                'line_1' => $property->address->line_1,
                'display_text' => $property->address->singleLine,
                'id' => $property->id,
                'users' => $property->users,
            ];
        });
    }
}
