<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\Trip;
use App\Models\TripStep;
use App\Models\TruckOdometerEvent;
use App\Models\TripExpense;
use App\Enums\TripStepStatus;
use App\Enums\TripExpenseCategory;

/**
 * Заполняет раздел «Notikumi» (Vadītāja notikumi): создаёт события выезда/шагов/заезда
 * и при необходимости расходы по рейсам, у которых их ещё нет.
 */
class NotikumiEventsSeeder extends Seeder
{
    public function run(): void
    {
        $trips = Trip::query()
            ->whereNotNull('driver_id')
            ->whereNotNull('truck_id')
            ->whereDoesntHave('odometerEvents')
            ->with(['steps' => fn ($q) => $q->orderBy('order')->orderBy('id')])
            ->limit(60)
            ->get();

        $created = 0;
        foreach ($trips as $trip) {
            $startDate = $trip->start_date ? Carbon::parse($trip->start_date)->setTime(6, 0) : now()->subDays(rand(5, 30));
            $endDate = $trip->end_date ? Carbon::parse($trip->end_date)->setTime(20, 0) : $startDate->copy()->addDays(rand(1, 4))->setTime(18, 0);

            $odoStart = $trip->odo_start_km ?? (100000 + $trip->id * 100);
            $odoEnd = $odoStart + rand(500, 2000);
            if ($trip->odo_start_km === null) {
                $trip->update(['odo_start_km' => $odoStart]);
            }
            if ($trip->odo_end_km === null) {
                $trip->update(['odo_end_km' => $odoEnd]);
            }

            TruckOdometerEvent::create([
                'truck_id'     => $trip->truck_id,
                'driver_id'    => $trip->driver_id,
                'trip_id'      => $trip->id,
                'type'         => TruckOdometerEvent::TYPE_DEPARTURE,
                'odometer_km'  => $odoStart,
                'source'       => TruckOdometerEvent::SOURCE_MANUAL,
                'occurred_at'  => $startDate,
            ]);
            $created++;

            $steps = $trip->steps;
            $lastOdo = $odoStart;
            $stepTime = $startDate->copy();
            foreach ($steps as $step) {
                $segmentKm = rand(50, 200);
                $odoCompleted = $step->odo_completed_km ?? ($lastOdo + $segmentKm);
                $lastOdo = (float) $odoCompleted;
                $stepTime = $stepTime->addHours(rand(2, 8));

                TruckOdometerEvent::create([
                    'truck_id'      => $trip->truck_id,
                    'driver_id'     => $trip->driver_id,
                    'trip_id'       => $trip->id,
                    'trip_step_id'  => $step->id,
                    'type'          => TruckOdometerEvent::TYPE_STEP,
                    'odometer_km'   => $odoCompleted,
                    'source'        => TruckOdometerEvent::SOURCE_MANUAL,
                    'occurred_at'   => $step->completed_at ?? $stepTime,
                    'step_status'   => TripStepStatus::COMPLETED->value,
                ]);
                $created++;

                if ($step->odo_completed_km === null) {
                    $step->update(['odo_completed_km' => $odoCompleted]);
                }
            }

            TruckOdometerEvent::create([
                'truck_id'     => $trip->truck_id,
                'driver_id'    => $trip->driver_id,
                'trip_id'      => $trip->id,
                'type'         => TruckOdometerEvent::TYPE_RETURN,
                'odometer_km'  => $odoEnd,
                'source'       => TruckOdometerEvent::SOURCE_MANUAL,
                'occurred_at'  => $endDate,
            ]);
            $created++;
        }

        $tripsWithExpenses = Trip::query()
            ->whereNotNull('driver_id')
            ->whereHas('odometerEvents')
            ->whereDoesntHave('expenses')
            ->limit(25)
            ->get();

        foreach ($tripsWithExpenses as $trip) {
            $cats = [
                TripExpenseCategory::FUEL,
                TripExpenseCategory::TOLL,
                TripExpenseCategory::PARKING,
            ];
            $cat = $cats[array_rand($cats)];
            $date = $trip->start_date
                ? Carbon::parse($trip->start_date)->addDays(rand(0, 2))
                : now()->subDays(rand(1, 20));
            TripExpense::create([
                'trip_id'       => $trip->id,
                'category'      => $cat->value,
                'expense_date'  => $date,
                'amount'        => round(rand(20, 200) + rand(0, 99) / 100, 2),
                'currency'      => $trip->currency ?? 'EUR',
                'odometer_km'   => $trip->odo_start_km ? $trip->odo_start_km + rand(100, 500) : null,
                'description'   => 'Seeder: ' . $cat->value,
            ]);
        }

        $this->command->info('Notikumi: создано событий выезд/шаг/заезд и расходы по рейсам.');
    }
}
