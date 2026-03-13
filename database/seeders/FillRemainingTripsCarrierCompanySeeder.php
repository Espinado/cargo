<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Trip;

class FillRemainingTripsCarrierCompanySeeder extends Seeder
{
    public function run(): void
    {
        $carrier = Company::orderBy('id')->first();
        if (!$carrier) {
            return;
        }

        Trip::whereNull('carrier_company_id')
            ->update(['carrier_company_id' => $carrier->id]);
    }
}
