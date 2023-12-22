<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ConveyancerSeeder::class,
            PropertySeeder::class,
            AddressSeeder::class,
            FormSeeder::class,
            UserSeeder::class,
        ]);
    }
}
