<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;

/**
 * Полное удаление рейсов и всех связанных данных для компаний:
 * Lakna, Padeks, Rona Trans и Carrier (замена в UI).
 * Удаляются также водители, грузовики и прицепы этих компаний.
 */
return new class extends Migration
{
    public function up(): void
    {
        $companyIds = Company::where('name', 'like', '%Lakna%')
            ->orWhere('name', 'like', '%Padeks%')
            ->orWhere('name', 'like', '%Padex%')
            ->orWhere('name', 'like', '%Rona Trans%')
            ->orWhere('name', 'Carrier')
            ->pluck('id')
            ->all();

        if (empty($companyIds)) {
            return;
        }

        $tripIdsByCompany = DB::table('trips')
            ->where(function ($q) use ($companyIds) {
                $q->whereIn('carrier_company_id', $companyIds)
                    ->orWhereIn('expeditor_id', $companyIds);
            })
            ->pluck('id');

        $tripIdsByName = DB::table('trips')
            ->where(function ($q) {
                $q->where('expeditor_name', 'like', '%Rona Trans%')
                    ->orWhere('expeditor_name', 'like', '%Lakna%')
                    ->orWhere('expeditor_name', 'like', '%Padeks%')
                    ->orWhere('expeditor_name', 'like', '%Padex%')
                    ->orWhere('expeditor_name', 'like', '%Carrier%');
            })
            ->pluck('id');

        $tripIds = $tripIdsByCompany->merge($tripIdsByName)->unique()->values();

        $cargoIds = $tripIds->isNotEmpty()
            ? DB::table('trip_cargos')->whereIn('trip_id', $tripIds)->pluck('id')
            : collect();

        if ($tripIds->isNotEmpty()) {
            $invoiceIds = DB::table('invoices')->whereIn('trip_id', $tripIds)->pluck('id');

            if ($invoiceIds->isNotEmpty() && Schema::hasTable('invoice_payments')) {
                DB::table('invoice_payments')->whereIn('invoice_id', $invoiceIds)->delete();
            }

            DB::table('invoices')->whereIn('trip_id', $tripIds)->delete();

            if (Schema::hasTable('trip_documents')) {
                DB::table('trip_documents')->whereIn('trip_id', $tripIds)->delete();
            }
            if (Schema::hasTable('trip_step_documents')) {
                DB::table('trip_step_documents')->whereIn('trip_id', $tripIds)->delete();
            }
            DB::table('trip_expenses')->whereIn('trip_id', $tripIds)->delete();

            if (Schema::hasTable('truck_odometer_events')) {
                DB::table('truck_odometer_events')->whereIn('trip_id', $tripIds)->delete();
            }
            if (Schema::hasTable('driver_events')) {
                DB::table('driver_events')->whereIn('trip_id', $tripIds)->delete();
            }

            if ($cargoIds->isNotEmpty()) {
                if (Schema::hasTable('trip_cargo_step')) {
                    DB::table('trip_cargo_step')->whereIn('trip_cargo_id', $cargoIds)->delete();
                }
                if (Schema::hasTable('trip_cargo_items')) {
                    DB::table('trip_cargo_items')->whereIn('trip_cargo_id', $cargoIds)->delete();
                }
            }

            DB::table('trip_cargos')->whereIn('trip_id', $tripIds)->delete();
            DB::table('trip_steps')->whereIn('trip_id', $tripIds)->delete();
            DB::table('trips')->whereIn('id', $tripIds)->delete();
        }

        DB::table('trip_expenses')->whereIn('supplier_company_id', $companyIds)->update(['supplier_company_id' => null]);

        DB::table('drivers')->whereIn('company_id', $companyIds)->delete();
        DB::table('trucks')->whereIn('company_id', $companyIds)->delete();
        DB::table('trailers')->whereIn('company_id', $companyIds)->delete();
        DB::table('users')->whereIn('company_id', $companyIds)->update(['company_id' => null]);

        Company::whereIn('id', $companyIds)->delete();
    }

    public function down(): void
    {
        // Не восстанавливаем
    }
};
