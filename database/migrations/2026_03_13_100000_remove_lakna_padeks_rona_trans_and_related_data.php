<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Company;

/**
 * Удаление компаний Lakna, Padeks, Rona Trans из конфига и БД.
 * - Создаётся/обновляется одна компания Carrier (slug 1).
 * - Все ссылки на старые компании переназначаются на Carrier.
 * - Удаляются документы (trip_documents, trip_step_documents) и инвойсы по рейсам этих компаний.
 * - Удаляются сами компании.
 */
return new class extends Migration
{
    public function up(): void
    {
        $oldCompanies = Company::where('name', 'like', '%Lakna%')
            ->orWhere('name', 'like', '%Padeks%')
            ->orWhere('name', 'like', '%Padex%')
            ->orWhere('name', 'like', '%Rona Trans%')
            ->get();

        if ($oldCompanies->isEmpty()) {
            return;
        }

        $carrier = Company::updateOrCreate(
            ['slug' => '1'],
            [
                'name'       => 'Carrier',
                'type'      => 'forwarder',
                'reg_nr'    => '40000000000',
                'vat_nr'    => 'LV40000000000',
                'country'   => 'Latvia',
                'city'      => 'Riga',
                'address'   => 'Example Street 1',
                'post_code' => 'LV-1000',
                'email'     => 'carrier@example.lv',
                'phone'     => '+371 00000000',
                'banks_json' => json_encode([
                    1 => ['name' => 'Example Bank', 'iban' => 'LV00BANK0000000000000', 'bic' => 'BICOLV22'],
                ]),
                'is_system' => true,
                'is_active' => true,
            ]
        );

        $toDeleteIds = $oldCompanies->pluck('id')->filter(fn ($id) => (int) $id !== (int) $carrier->id)->values()->all();

        if (empty($toDeleteIds)) {
            return;
        }

        $tripIdsToClean = DB::table('trips')->whereIn('carrier_company_id', $toDeleteIds)->pluck('id');

        if ($tripIdsToClean->isNotEmpty()) {
            DB::table('trip_documents')->whereIn('trip_id', $tripIdsToClean)->delete();
            DB::table('trip_step_documents')->whereIn('trip_id', $tripIdsToClean)->delete();
            DB::table('invoices')->whereIn('trip_id', $tripIdsToClean)->delete();
        }

        DB::table('trips')->whereIn('carrier_company_id', $toDeleteIds)->update(['carrier_company_id' => $carrier->id]);
        DB::table('drivers')->whereIn('company_id', $toDeleteIds)->update(['company_id' => $carrier->id]);
        DB::table('trucks')->whereIn('company_id', $toDeleteIds)->update(['company_id' => $carrier->id]);
        DB::table('trailers')->whereIn('company_id', $toDeleteIds)->update(['company_id' => $carrier->id]);
        DB::table('users')->whereIn('company_id', $toDeleteIds)->update(['company_id' => $carrier->id]);
        DB::table('trip_expenses')->whereIn('supplier_company_id', $toDeleteIds)->update(['supplier_company_id' => $carrier->id]);

        Company::whereIn('id', $toDeleteIds)->delete();
    }

    public function down(): void
    {
        // Не восстанавливаем удалённые компании
    }
};
