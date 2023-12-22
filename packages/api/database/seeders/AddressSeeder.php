<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Conveyancer;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Address::factory()->create([
            'addressable_id' => Conveyancer::where('id', 1)->first()->id,
            'addressable_type' => 'conveyancer',
        ]);
    }
}
