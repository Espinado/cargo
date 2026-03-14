<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Refreshing database...');
        Artisan::call('migrate:fresh', ['--force' => true]);

        $this->command->info('Seeding...');
        // Очередность как в Fleet Manager: компании → клиенты → техника → водители → админы → рейсы
        $this->call([
            CompaniesSeeder::class,
            ClientsTableSeeder::class,
            TruckSeeder::class,
            TrailerSeeder::class,
            DriverSeeder::class,
            AdminUserSeeder::class,
            TripsFullSeeder::class,
        ]);
    }
}
