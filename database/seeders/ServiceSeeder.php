<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            ['service_name' => 'Cellophane Blue', 'base_price' => 15, 'price_unit' => '/piece', 'available_for' => 'Both'],
            ['service_name' => 'Cellophane White', 'base_price' => 10, 'price_unit' => '/piece', 'available_for' => 'Both'],
            ['service_name' => 'Drop Off', 'base_price' => 35, 'price_unit' => '/kilo', 'available_for' => 'Drop Off'],
            ['service_name' => 'Dry', 'base_price' => 35, 'price_unit' => '/20 Mins', 'available_for' => 'Self Service'],
            ['service_name' => 'Sabon', 'base_price' => 10, 'price_unit' => '/piece', 'available_for' => 'Self Service'],
            ['service_name' => 'Self Service', 'base_price' => 65, 'price_unit' => '/load', 'available_for' => 'Self Service'],
        ];

        foreach ($services as $service) {
            $serviceExists = DB::table('services')
                ->where('service_name', $service['service_name'])
                ->exists();

            if (! $serviceExists) {
                DB::table('services')->insert([
                    ...$service,
                    'description' => null,
                    'deleted_at' => null,
                ]);
            }
        }
    }
}
