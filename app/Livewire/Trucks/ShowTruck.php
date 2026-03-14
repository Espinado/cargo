<?php

namespace App\Livewire\Trucks;

use App\Models\Trip;
use App\Models\Truck;
use App\Models\TruckOdometerEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Services\Services\MaponService;

class ShowTruck extends Component
{
    public Truck $truck;

    /** Пробег за период */
    public ?string $mileagePeriodFrom = null;
    public ?string $mileagePeriodTo = null;

    // Mapon UI fields (CAN only)
    public ?float $maponCanMileageKm = null;     // CAN odometer (km)
    public ?string $maponUnitName = null;
    public ?string $maponError = null;

    // Stale status (configured)
    public bool $maponCanStale = false;
    public ?int $maponCanDaysAgo = null;

    // Meta
    public ?string $maponLastUpdate = null;      // unit.last_update
    public ?string $maponCanAt = null;           // can.odom.gmt

    public function mount(Truck $truck): void
    {
        $this->truck = $truck;
        $this->loadMaponData();
        if ($this->mileagePeriodFrom === null || $this->mileagePeriodTo === null) {
            $this->mileagePeriodTo = Carbon::now()->toDateString();
            $this->mileagePeriodFrom = Carbon::now()->subDays(30)->toDateString();
        }
    }

    public function setMileagePeriod(int $days): void
    {
        $this->mileagePeriodTo = Carbon::now()->toDateString();
        $this->mileagePeriodFrom = Carbon::now()->subDays($days)->toDateString();
    }

    public function clearMileagePeriod(): void
    {
        $this->mileagePeriodFrom = null;
        $this->mileagePeriodTo = null;
    }

    /**
     * Button handler: clear cache and load fresh data
     */
    public function refreshMaponData(): void
    {
        $unitId = $this->truck->mapon_unit_id ?? null;

        if ($unitId) {
            Cache::forget($this->cacheKey($unitId));
        }

        $this->loadMaponData();
    }

public function loadMaponData(): void
{
    // reset so nothing "sticks"
    $this->maponError = null;
    $this->maponCanMileageKm = null;
    $this->maponUnitName = null;
    $this->maponLastUpdate = null;
    $this->maponCanAt = null;

    $this->maponCanStale = false;
    $this->maponCanDaysAgo = null;

    $unitId = $this->truck->mapon_unit_id ?? null;

    if (!$unitId) {
        $this->maponError = 'mapon_unit_id не задан для данного трака.';
        return;
    }

    // cache учитывает компанию (ключ Mapon разный)
    $companyId = (int) ($this->truck->company_id ?? 0);
    $cacheKey = $this->cacheKey($unitId) . ':company:' . $companyId;

    $result = Cache::remember($cacheKey, now()->addMinutes(5), function () {
        try {
            /** @var MaponService $svc */
            $svc = app(MaponService::class);

            return $svc->getUnitDataForTruck($this->truck, 'can');
        } catch (\Throwable $e) {
            $unitId = $this->truck->mapon_unit_id ?? 'null';
            \Log::warning("MaponService getUnitDataForTruck failed unit_id={$unitId}: " . $e->getMessage());
            return null;
        }
    });

    if (!is_array($result)) {
        $this->maponError = 'Не удалось получить данные из Mapon.';
        return;
    }

    $this->maponUnitName = $result['label']
        ?? $result['number']
        ?? ($result['vehicle_title'] ?? null)
        ?? '—';

    $this->maponLastUpdate = $result['last_update'] ?? null;

    // --------------------------------------
    // ODOMETER: CAN → fallback mileage
    // --------------------------------------
    $canValue = data_get($result, 'can.odom.value');
    $canAt    = data_get($result, 'can.odom.gmt');

    if ($canValue !== null && $canValue !== '') {
        // ✅ CAN odometer (км)
        $this->maponCanMileageKm = round((float) $canValue, 1);

        // timestamp CAN, если нет — берём last_update
        $this->maponCanAt = !empty($canAt)
            ? (string) $canAt
            : ($this->maponLastUpdate ? (string) $this->maponLastUpdate : null);

    } else {
        // ✅ CAN нет → используем mileage
        $rawMileage = data_get($result, 'mileage');

        if ($rawMileage === null || $rawMileage === '') {
            // ❌ вот это уже реальная ошибка
            $this->maponError = 'Mapon не вернул ни CAN odometer, ни mileage.';
            return;
        }

        // mileage обычно в метрах → конвертим в км
        $this->maponCanMileageKm = round(((float) $rawMileage) / 1000, 1);

        // timestamp берём last_update
        $this->maponCanAt = $this->maponLastUpdate ? (string) $this->maponLastUpdate : null;
    }

    // --------------------------------------
    // stale logic via config/mapon.php
    // --------------------------------------
    if ($this->maponCanAt) {
        try {
            $now = now();
            $at  = \Carbon\Carbon::parse($this->maponCanAt);

            $this->maponCanDaysAgo = $at->diffInDays($now);

            $thresholdDays    = (int) config('mapon.can_stale_days', 2);
            $thresholdMinutes = (int) config('mapon.can_stale_minutes', 30);

            $isStaleByDays = $thresholdDays > 0 && $at->diffInDays($now) >= $thresholdDays;
            $isStaleByMin  = $thresholdMinutes > 0 && $at->diffInMinutes($now) >= $thresholdMinutes;

            $this->maponCanStale = $isStaleByDays || $isStaleByMin;
        } catch (\Throwable $e) {
            \Log::warning("Mapon time parse failed: {$this->maponCanAt}. " . $e->getMessage());
        }
    }
}




    protected function cacheKey(int|string $unitId): string
    {
        return "mapon:unit:{$unitId}:data:can";
    }

    public function getTruckMileageStatsProperty(): array
    {
        $from = $this->mileagePeriodFrom ? Carbon::parse($this->mileagePeriodFrom)->startOfDay() : null;
        $to = $this->mileagePeriodTo ? Carbon::parse($this->mileagePeriodTo)->endOfDay() : null;

        $q = Trip::query()
            ->where('truck_id', $this->truck->id)
            ->select('trips.id', 'trips.start_date', 'trips.odo_start_km', 'trips.odo_end_km');

        if ($from) {
            $q->whereDate('trips.start_date', '>=', $from);
        }
        if ($to) {
            $q->whereDate('trips.start_date', '<=', $to);
        }

        $q->addSelect([
            'departure_odometer' => TruckOdometerEvent::query()
                ->selectRaw('COALESCE(NULLIF(odometer_km, 0), trips.odo_start_km)')
                ->whereColumn('trip_id', 'trips.id')
                ->where('type', TruckOdometerEvent::TYPE_DEPARTURE)
                ->orderBy('occurred_at', 'asc')
                ->limit(1),
            'return_odometer' => TruckOdometerEvent::query()
                ->selectRaw('COALESCE(NULLIF(odometer_km, 0), trips.odo_end_km)')
                ->whereColumn('trip_id', 'trips.id')
                ->where('type', TruckOdometerEvent::TYPE_RETURN)
                ->orderBy('occurred_at', 'desc')
                ->limit(1),
        ]);

        $rows = $q->orderBy('trips.start_date', 'desc')->get();

        $totalKm = 0;
        $trips = [];
        foreach ($rows as $t) {
            $dep = (float) ($t->departure_odometer ?? $t->odo_start_km ?? 0);
            $ret = (float) ($t->return_odometer ?? $t->odo_end_km ?? 0);
            $distanceKm = $ret > $dep ? round($ret - $dep, 1) : 0;
            $totalKm += $distanceKm;
            $trips[] = [
                'id' => $t->id,
                'start_date' => $t->start_date?->format('Y-m-d') ?? '',
                'distance_km' => $distanceKm,
            ];
        }

        return [
            'total_km' => round($totalKm, 1),
            'trips_count' => $rows->count(),
            'trips' => $trips,
        ];
    }

    public function destroy()
    {
        if ($this->truck) {
            $this->truck->delete();
            session()->flash('success', __('app.truck.show.deleted_success'));
            return redirect()->route('trucks.index');
        }

        session()->flash('error', __('app.truck.show.deleted_error'));
        return redirect()->route('trucks.index');
    }

    public function render()
    {
        $mileageStats = $this->truckMileageStats;
        return view('livewire.trucks.show-truck', compact('mileageStats'))
            ->layout('layouts.app', [
                'title' => __('app.truck.show.title'),
            ]);
    }
}
