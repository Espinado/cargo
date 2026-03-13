<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Arr;

use App\Models\Trip;
use App\Models\TripStep;
use App\Models\TripCargo;
use App\Models\TripCargoItem;
use App\Models\TripDocument;
use App\Models\TripExpense;
use App\Models\Company;
use App\Models\Client;
use App\Models\Driver;
use App\Models\Truck;
use App\Models\Trailer;
use App\Models\VehicleRun;
use App\Models\TruckOdometerEvent;

use App\Enums\TripStatus;
use App\Enums\TripStepStatus;
use App\Enums\TripDocumentType;
use App\Enums\TripExpenseCategory;
use App\Helpers\CalculateTax;

/**
 * 40 рейсов: часть — наши перевозчики (forwarder), часть — экспедиторы (expeditor) через третьих лиц (carrier).
 * В каждом рейсе: 3 клиента, у каждого по 3 груза (9 грузов), шаги погрузки/разгрузки, расходы (enum), документы (~10), часть с TIR.
 * Часть рейсов — полный цикл: выезд из гаража, шаги, заезд в гараж, закрытие. Одометр — каждый следующий шаг не меньше предыдущего.
 * Перегрузка (overload_note) не заполняем. Стоимость фрахта третьим лицам <= сумма с клиента.
 */
class FortyTripsFullSeeder extends Seeder
{
    private string $placeholderPath = '';
    private const DOCS_PER_TRIP = 10;
    private const EXPENSES_PER_TRIP_MIN = 5;
    private const EXPENSES_PER_TRIP_MAX = 10;
    private const TRIPS_WITH_FULL_LIFECYCLE = 40; // рейсы с выездом/шагами/заездом (Notikumi)
    private const TRIPS_WITH_TIR = 15;

    public function run(): void
    {
        $this->ensurePlaceholderFile();

        $forwarders = Company::where('type', 'forwarder')->get();
        $expeditors = Company::where('type', 'expeditor')->get();
        $thirdPartyCarriers = Company::where('type', 'carrier')->where('is_third_party', true)->get();

        if ($forwarders->isEmpty() || $expeditors->isEmpty() || $thirdPartyCarriers->isEmpty()) {
            $this->command->warn('Нужны компании: forwarder, expeditor, carrier (third_party). Запустите CarrierExpeditorCompaniesSeeder.');
            return;
        }

        $clients = Client::orderBy('id')->get();
        if ($clients->count() < 9) {
            $this->command->warn('Нужно минимум 9 клиентов.');
            return;
        }

        $countryIdsWithCities = $this->getCountryIdsWithCities();
        if (empty($countryIdsWithCities)) {
            $this->command->warn('Нет стран с городами в config/cities.');
            return;
        }

        $adminUser = \App\Models\User::where('role', 'admin')->first();
        $uploadedBy = $adminUser?->id;

        $faker = \Faker\Factory::create();

        $customsNames = [
            'Rīgas muitas punkts', 'Liepājas muitas nams', 'Daugavpils muitas vēstniecība',
            'Ventspils brīvosta', 'Tallinn Sadam', 'Klaipėda Customs', 'Warsaw Customs Office',
            'Berlin Zoll', 'Hamburg Hafen', 'Vilnius Customs',
        ];

        for ($tripIndex = 0; $tripIndex < 40; $tripIndex++) {
            $isForwarderTrip = $tripIndex < 20;
            $expeditor = $isForwarderTrip
                ? $forwarders->random()
                : $expeditors->random();
            $carrier = $isForwarderTrip ? $expeditor : $thirdPartyCarriers->random();

            $driver = null;
            $truck = null;
            $trailer = null;
            if ($isForwarderTrip) {
                $driver = Driver::where('company_id', $expeditor->id)->inRandomOrder()->first();
                $truck = Truck::where('company_id', $expeditor->id)->inRandomOrder()->first();
                $trailer = Trailer::where('company_id', $expeditor->id)->inRandomOrder()->first();
                if (!$driver || !$truck || !$trailer) {
                    $this->command->warn("Пропуск рейса {$tripIndex}: нет водителя/тягача/прицепа у forwarder.");
                    continue;
                }
            }

            $hasTir = $tripIndex < self::TRIPS_WITH_TIR;
            $fullLifecycle = $tripIndex < self::TRIPS_WITH_FULL_LIFECYCLE && $isForwarderTrip;

            DB::transaction(function () use (
                $faker,
                $expeditor,
                $carrier,
                $driver,
                $truck,
                $trailer,
                $clients,
                $countryIdsWithCities,
                $isForwarderTrip,
                $hasTir,
                $fullLifecycle,
                $customsNames,
                $uploadedBy,
                $tripIndex
            ) {
                $startDate = Carbon::now()->subDays(rand(60, 10));
                $endDate = $startDate->copy()->addDays(rand(2, 5));

                $banks = is_array($expeditor->banks_json) ? $expeditor->banks_json : (json_decode($expeditor->banks_json ?? '[]', true) ?: []);
                $bank = !empty($banks) ? reset($banks) : null;

                $trip = Trip::create([
                    'expeditor_id'        => $expeditor->id,
                    'expeditor_name'      => $expeditor->name,
                    'expeditor_reg_nr'    => $expeditor->reg_nr,
                    'expeditor_country'   => $expeditor->country,
                    'expeditor_city'      => $expeditor->city,
                    'expeditor_address'   => $expeditor->address,
                    'expeditor_post_code' => $expeditor->post_code,
                    'expeditor_email'     => $expeditor->email,
                    'expeditor_phone'     => $expeditor->phone,
                    'expeditor_bank_id'   => 1,
                    'expeditor_bank'      => $bank['name'] ?? null,
                    'expeditor_iban'      => $bank['iban'] ?? null,
                    'expeditor_bic'       => $bank['bic'] ?? null,
                    'carrier_company_id'  => $carrier->id,
                    'driver_id'           => $driver?->id,
                    'truck_id'            => $truck?->id,
                    'trailer_id'          => $trailer?->id,
                    'start_date'          => $startDate->toDateString(),
                    'end_date'            => $endDate->toDateString(),
                    'currency'            => 'EUR',
                    'status'              => $fullLifecycle ? TripStatus::COMPLETED : TripStatus::PLANNED,
                    'cont_nr'             => 'CT-' . strtoupper(substr(uniqid(), -8)),
                    'seal_nr'             => 'S' . rand(1000, 9999),
                    'customs'             => $hasTir,
                    'customs_address'     => $hasTir ? $faker->randomElement($customsNames) : null,
                    'notes'               => 'FortyTripsFullSeeder',
                ]);

                // База одометра для рейса (целые км, т.к. trips.odo_start_km/odo_end_km — int, статистика привязана сюда)
                $baseOdo = (int) (100000 + $tripIndex * 500);

                $stepCustomers = $clients->random(3)->values();
                // 6 шагов: 3 погрузки, 3 разгрузки. Одометр по шагам (trip_steps) — цепочка не убывает для статистики.
                $steps = [];
                $lastOdo = $baseOdo;
                for ($s = 0; $s < 6; $s++) {
                    $type = $s < 3 ? 'loading' : 'unloading';
                    $countryId = $countryIdsWithCities[array_rand($countryIdsWithCities)];
                    $cities = getCitiesByCountryId($countryId);
                    $cityId = $cities ? (int) array_key_first($cities) : 1;
                    if ($cities && count($cities) > 1) {
                        $keys = array_keys($cities);
                        $cityId = (int) $keys[array_rand($keys)];
                    }
                    $stepDate = $startDate->copy()->addDays((int) ($s / 2))->setTime(8 + ($s % 2) * 6, 0);
                    $segmentKm = rand(80, 200);
                    $odoOnTheWay = $lastOdo + (int) round($segmentKm * 0.3);
                    $odoArrived = $lastOdo + (int) round($segmentKm * 0.6);
                    $odoCompleted = $lastOdo + $segmentKm;
                    $lastOdo = $odoCompleted;
                    $stepCustomer = $stepCustomers[$s % 3] ?? null;
                    $steps[] = TripStep::create([
                        'trip_id'    => $trip->id,
                        'type'       => $type,
                        'client_id'  => $stepCustomer?->id,
                        'country_id' => $countryId,
                        'city_id'    => $cityId,
                        'address'    => ($type === 'loading' ? 'Loading' : 'Unloading') . ' point ' . ($s + 1) . ', ' . $faker->streetAddress(),
                        'date'       => $stepDate->toDateString(),
                        'time'       => $stepDate->format('H:i'),
                        'order'      => $s + 1,
                        'notes'      => $faker->optional(0.3)->sentence(),
                        'status'     => $fullLifecycle ? TripStepStatus::COMPLETED : TripStepStatus::NOT_STARTED,
                        'started_at' => $fullLifecycle ? $stepDate->copy()->subMinutes(30) : null,
                        'completed_at' => $fullLifecycle ? $stepDate->copy()->addMinutes(rand(60, 180)) : null,
                        'odo_on_the_way_km'   => $fullLifecycle ? $odoOnTheWay : null,
                        'odo_arrived_km'       => $fullLifecycle ? $odoArrived : null,
                        'odo_completed_km'     => $fullLifecycle ? $odoCompleted : null,
                        'odo_on_the_way_source' => $fullLifecycle ? 3 : null,
                        'odo_arrived_source'    => $fullLifecycle ? 3 : null,
                        'odo_completed_source' => $fullLifecycle ? 3 : null,
                    ]);
                }

                if ($fullLifecycle) {
                    $trip->update([
                        'started_at'    => $startDate->copy()->setTime(6, 0),
                        'ended_at'      => $endDate->copy()->setTime(20, 0),
                        'odo_start_km'  => $baseOdo,
                        'odo_end_km'    => $lastOdo + rand(30, 80), // после последнего шага — заезд в гараж
                    ]);
                }

                $shippers = $clients->random(3)->values();
                $consignees = $clients->random(3)->values();
                $customers = $clients->random(3)->values();

                $tripTotalWithTax = 0;
                $cargos = [];

                for ($c = 0; $c < 9; $c++) {
                    $customer = $customers[$c % 3];
                    $shipper = $shippers[$c % 3];
                    $consignee = $consignees[$c % 3];
                    $price = (float) rand(400, 1200);
                    $taxPercent = (float) $faker->randomElement([5, 12, 21]);
                    $tax = CalculateTax::calculate($price, $taxPercent);
                    $hasDelay = $faker->boolean(20);
                    $delayDays = $hasDelay ? rand(1, 3) : null;
                    $delayAmount = $hasDelay ? (float) rand(50, 200) : null;

                    $cargo = TripCargo::create([
                        'trip_id'                     => $trip->id,
                        'customer_id'                 => $customer->id,
                        'shipper_id'                  => $shipper->id,
                        'consignee_id'                => $consignee->id,
                        'order_file'                  => $this->placeholderPath,
                        'order_created_at'            => $startDate,
                        'order_nr'                    => 'ORD-' . $trip->id . '-' . ($c + 1),
                        'cmr_file'                    => $this->placeholderPath,
                        'cmr_nr'                      => 'CMR-' . $trip->id . '-' . ($c + 1),
                        'cmr_created_at'              => $startDate,
                        'inv_nr'                      => 'INV-' . $trip->id . '-' . ($c + 1),
                        'inv_file'                    => $this->placeholderPath,
                        'inv_created_at'              => $startDate,
                        'price'                       => $tax['price'],
                        'tax_percent'                 => $taxPercent,
                        'total_tax_amount'            => $tax['tax_amount'],
                        'price_with_tax'              => $tax['price_with_tax'],
                        'currency'                    => 'EUR',
                        'payment_terms'               => $endDate->copy()->addDays(14),
                        'payment_days'                => 14,
                        'payer_type_id'               => 1,
                        'commercial_invoice_nr'       => 'CI-' . $trip->id . '-' . ($c + 1),
                        'commercial_invoice_amount'   => $tax['price_with_tax'],
                        'has_delay'                   => $hasDelay,
                        'delay_days'                  => $delayDays,
                        'delay_amount'                => $delayAmount,
                    ]);
                    $cargos[] = $cargo;
                    $tripTotalWithTax += $tax['price_with_tax'];

                    $loadStepIndex = (int) floor($c / 3);
                    $unloadStepIndex = 3 + $loadStepIndex;
                    $cargo->steps()->attach([
                        $steps[$loadStepIndex]->id => ['role' => 'loading'],
                        $steps[$unloadStepIndex]->id => ['role' => 'unloading'],
                    ]);

                    for ($item = 1; $item <= 3; $item++) {
                        $itemPrice = $price / 3;
                        $itemTax = CalculateTax::calculate($itemPrice, $taxPercent);
                        TripCargoItem::create([
                            'trip_cargo_id'   => $cargo->id,
                            'description'     => $faker->randomElement(['Palletized goods', 'Boxes', 'Machinery parts', 'Electronics', 'Textiles']) . ' ' . $item,
                            'packages'        => rand(5, 25),
                            'pallets'         => rand(0, 6),
                            'units'           => rand(10, 100),
                            'net_weight'      => (float) rand(100, 600),
                            'gross_weight'    => (float) rand(110, 650),
                            'tonnes'          => round(rand(100, 600) / 1000, 3),
                            'volume'          => (float) rand(1, 18),
                            'loading_meters'  => (float) rand(1, 6),
                            'customs_code'    => 'HS' . rand(1000, 9999),
                            'hazmat'          => $faker->optional(0.1)->randomElement(['ADR 3', 'ADR 8']),
                            'temperature'     => $faker->optional(0.15)->randomElement(['+2..+6', 'Ambient', '-18']),
                            'stackable'       => $faker->boolean(0.7),
                            'instructions'    => $faker->optional(0.3)->sentence(),
                            'remarks'         => $faker->optional(0.2)->sentence(),
                            'price'           => $itemTax['price'],
                            'tax_percent'     => $taxPercent,
                            'tax_amount'      => $itemTax['tax_amount'],
                            'price_with_tax'  => $itemTax['price_with_tax'],
                        ]);
                    }
                }

                // Сумма по грузам хранится в trip_cargos.price_with_tax; в trips колонки price может не быть

                // Расходы: несколько категорий, без overload_note. Для экспедитора — SUBCONTRACTOR <= выручка
                if (!$isForwarderTrip && $tripTotalWithTax > 0) {
                    $subcontractAmount = round($tripTotalWithTax * $faker->randomFloat(2, 0.70, 0.95), 2);
                    TripExpense::create([
                        'trip_id'             => $trip->id,
                        'trip_cargo_id'       => null,
                        'supplier_company_id' => $carrier->id,
                        'category'            => TripExpenseCategory::SUBCONTRACTOR,
                        'description'         => 'Fracht third party',
                        'amount'              => $subcontractAmount,
                        'currency'             => 'EUR',
                        'expense_date'        => $startDate->toDateString(),
                        'created_by'           => $uploadedBy,
                        'file_path'            => $this->placeholderPath,
                    ]);
                }

                $categories = TripExpenseCategory::cases();
                $categoriesFiltered = array_values(array_filter($categories, fn ($c) => $c !== TripExpenseCategory::SUBCONTRACTOR));
                $expenseCount = rand(self::EXPENSES_PER_TRIP_MIN, self::EXPENSES_PER_TRIP_MAX);
                for ($e = 0; $e < $expenseCount; $e++) {
                    $cat = $categoriesFiltered[array_rand($categoriesFiltered)];
                    TripExpense::create([
                        'trip_id'       => $trip->id,
                        'trip_cargo_id' => $faker->optional(0.3)->randomElement($cargos)?->id,
                        'category'      => $cat,
                        'description'   => $cat->value . ' expense ' . ($e + 1),
                        'amount'        => (float) rand(15, 250),
                        'currency'      => 'EUR',
                        'expense_date'  => $startDate->copy()->addDays(rand(0, 3))->toDateString(),
                        'created_by'    => $uploadedBy,
                        'file_path'     => $this->placeholderPath,
                        'odometer_km'   => $fullLifecycle ? $baseOdo + rand(100, 800) : null,
                        'liters'        => ($cat === TripExpenseCategory::FUEL || $cat === TripExpenseCategory::ADBLUE) ? (float) rand(20, 150) : null,
                    ]);
                }

                // Документы: ~10 на рейс, фейковые фото
                $docTypes = TripDocumentType::cases();
                for ($d = 0; $d < self::DOCS_PER_TRIP; $d++) {
                    $type = $docTypes[array_rand($docTypes)];
                    TripDocument::create([
                        'trip_id'     => $trip->id,
                        'type'        => $type,
                        'name'        => $type->value . ' T' . $trip->id . '-' . ($d + 1),
                        'file_path'   => $this->placeholderPath,
                        'uploaded_by' => $uploadedBy,
                        'uploaded_at' => now(),
                    ]);
                }

                // Полный цикл: VehicleRun + TruckOdometerEvent (выезд, шаги, заезд)
                if ($fullLifecycle && $truck && $driver) {
                    $runStart = $startDate->copy()->setTime(6, 0);
                    $runEnd = $endDate->copy()->setTime(20, 0);
                    $vehicleRun = VehicleRun::create([
                        'truck_id'           => $truck->id,
                        'driver_id'          => $driver->id,
                        'started_at'         => $runStart,
                        'ended_at'           => $runEnd,
                        'start_can_odom_km'  => $trip->odo_start_km,
                        'end_can_odom_km'    => $trip->odo_end_km,
                        'status'             => 'closed',
                        'close_reason'       => 'manual',
                        'created_by'         => 'manual',
                    ]);
                    $trip->update(['vehicle_run_id' => $vehicleRun->id]);

                    TruckOdometerEvent::create([
                        'truck_id'       => $truck->id,
                        'driver_id'      => $driver->id,
                        'trip_id'       => $trip->id,
                        'type'          => TruckOdometerEvent::TYPE_DEPARTURE,
                        'odometer_km'   => $trip->odo_start_km,
                        'source'        => TruckOdometerEvent::SOURCE_MANUAL,
                        'occurred_at'   => $runStart,
                    ]);
                    foreach ($steps as $step) {
                        if ($step->odo_completed_km) {
                            TruckOdometerEvent::create([
                                'truck_id'       => $truck->id,
                                'driver_id'      => $driver->id,
                                'trip_id'       => $trip->id,
                                'trip_step_id'   => $step->id,
                                'type'          => TruckOdometerEvent::TYPE_STEP,
                                'odometer_km'   => $step->odo_completed_km,
                                'source'        => TruckOdometerEvent::SOURCE_MANUAL,
                                'occurred_at'   => $step->completed_at ?? $runStart,
                                'step_status'   => TripStepStatus::COMPLETED->value,
                            ]);
                        }
                    }
                    TruckOdometerEvent::create([
                        'truck_id'       => $truck->id,
                        'driver_id'      => $driver->id,
                        'trip_id'       => $trip->id,
                        'type'          => TruckOdometerEvent::TYPE_RETURN,
                        'odometer_km'   => $trip->odo_end_km,
                        'source'        => TruckOdometerEvent::SOURCE_MANUAL,
                        'occurred_at'   => $runEnd,
                    ]);
                }
            });
        }

        $this->command->info('FortyTripsFullSeeder: создано 40 рейсов.');
    }

    private function ensurePlaceholderFile(): void
    {
        $dir = 'seeders';
        $path = "{$dir}/placeholder.txt";
        if (!Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, 'Placeholder for trip documents.');
        }
        $this->placeholderPath = $path;
    }

    private function getCountryIdsWithCities(): array
    {
        $countries = config('countries', []);
        $out = [];
        foreach ($countries as $id => $c) {
            $iso = $c['iso'] ?? null;
            if (!$iso) {
                continue;
            }
            $path = config_path('cities/' . strtolower($iso) . '.php');
            if (is_file($path)) {
                $out[] = (int) $id;
            }
        }
        return array_slice($out, 0, 15);
    }
}
