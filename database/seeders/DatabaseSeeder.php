<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === Очередность имеет значение ===
        $this->call([
            ClientsTableSeeder::class,
            ClientsFullSeeder::class,
            CompaniesSeeder::class,
            CarrierExpeditorCompaniesSeeder::class,
            TruckSeeder::class,
            TrailerSeeder::class,
            DriverSeeder::class,
            FleetExtraSeeder::class,
            CarrierDriversSeeder::class,
            CarrierTrucksSeeder::class,
            CarrierTrailersSeeder::class,
            AdminUserSeeder::class,
            FortyTripsFullSeeder::class,
            FakeInvoicesSeeder::class,
        ]);
    }
}
