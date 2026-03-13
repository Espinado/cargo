<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\TripCargo;

/**
 * Заполняет таблицу invoices фейковыми инвойсами по существующим trip_cargos.
 * Для каждого cargo без инвойса создаётся запись с случайными суммами и датами.
 */
class FakeInvoicesSeeder extends Seeder
{
    public function run(): void
    {
        $cargos = TripCargo::query()
            ->whereNotIn('id', Invoice::query()->select('trip_cargo_id'))
            ->with('trip')
            ->limit(80)
            ->get();

        if ($cargos->isEmpty()) {
            $this->command->warn('Нет trip_cargos без инвойса. Пропуск.');
            return;
        }

        $taxRates = [0, 5, 10, 21];
        $prefix = 'INV-' . date('Y') . '-';
        $startNr = (int) Invoice::max('id') + 1;

        foreach ($cargos as $i => $cargo) {
            $subtotal = round(mt_rand(500, 15000) + mt_rand(0, 99) / 100, 2);
            $taxPercent = $taxRates[array_rand($taxRates)];
            $taxTotal = round($subtotal * $taxPercent / 100, 2);
            $total = round($subtotal + $taxTotal, 2);

            $issuedAt = now()->subDays(mt_rand(1, 90));
            $dueDate = $issuedAt->copy()->addDays(mt_rand(7, 30));

            Invoice::create([
                'trip_id'         => $cargo->trip_id,
                'trip_cargo_id'   => $cargo->id,
                'invoice_no'      => $prefix . str_pad((string) ($startNr + $i), 5, '0', STR_PAD_LEFT),
                'issued_at'       => $issuedAt,
                'due_date'        => $dueDate,
                'payer_type_id'   => mt_rand(1, 3),
                'payer_client_id' => $cargo->customer_id,
                'currency'        => $cargo->trip->currency ?? 'EUR',
                'subtotal'        => $subtotal,
                'tax_percent'     => $taxPercent,
                'tax_total'       => $taxTotal,
                'total'           => $total,
                'pdf_file'        => null,
            ]);
        }

        $this->command->info('Создано фейковых инвойсов: ' . $cargos->count());
    }
}
