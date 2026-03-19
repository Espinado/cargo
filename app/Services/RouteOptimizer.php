<?php

namespace App\Services;

/**
 * Эвристика TSP: предлагает порядок точек для минимизации маршрута.
 * First и last точка фиксированы (старт/финиш), средние перебираются по "nearest neighbour".
 * Расстояния — по прямой (приближение), итоговый км считает API маршрутизации.
 *
 * Для сборных рейсов: груз не может быть разгружен раньше погрузки.
 * suggestOrderConstrained + buildPrecedenceFromSteps учитывают связь шагов с грузами (loading/unloading).
 */
class RouteOptimizer
{
    /**
     * Строит массив ограничений "шаг J должен быть после шагов из precedence[J]".
     * Для каждого груза: все шаги погрузки (loading) должны быть раньше всех шагов разгрузки (unloading).
     * $stepsArray — массив шагов (TripStep с загруженной связью cargos, pivot role).
     * Возвращает: [ stepIndex => [ indices of steps that must be before this step ] ]
     */
    public static function buildPrecedenceFromSteps(array $stepsArray): array
    {
        $n = count($stepsArray);
        $precedence = array_fill(0, $n, []);

        for ($i = 0; $i < $n; $i++) {
            $step = $stepsArray[$i];
            if (!is_object($step)) {
                continue;
            }
            $cargos = $step->relationLoaded('cargos') ? $step->cargos : [];
            if (!is_iterable($cargos)) {
                continue;
            }
            foreach ($cargos as $cargo) {
                $pivot = $cargo->pivot ?? null;
                if (!$pivot || ($pivot->role ?? null) !== 'loading') {
                    continue;
                }
                $cargoId = $cargo->id ?? null;
                if ($cargoId === null) {
                    continue;
                }
                // Шаг i — погрузка груза cargoId. Все шаги разгрузки этого груза должны быть после i.
                for ($j = 0; $j < $n; $j++) {
                    if ($j === $i) {
                        continue;
                    }
                    $stepJ = $stepsArray[$j];
                    $cargosJ = $stepJ->relationLoaded('cargos') ? $stepJ->cargos : [];
                    if (!is_iterable($cargosJ)) {
                        continue;
                    }
                    foreach ($cargosJ as $c) {
                        $pivotJ = $c->pivot ?? null;
                        if (!$pivotJ || ($pivotJ->role ?? null) !== 'unloading') {
                            continue;
                        }
                        if (($c->id ?? null) == $cargoId) {
                            if (!in_array($i, $precedence[$j], true)) {
                                $precedence[$j][] = $i;
                            }
                            break;
                        }
                    }
                }
            }
        }

        return $precedence;
    }

    /**
     * Расстояние между двумя точками [lon, lat] в км (приближённо, haversine).
     */
    public static function distanceKm(array $a, array $b): float
    {
        $lat1 = (float) $a[1];
        $lon1 = (float) $a[0];
        $lat2 = (float) $b[1];
        $lon2 = (float) $b[0];

        $r = 6371; // Earth radius km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $x = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $r * 2 * asin(sqrt($x));
    }

    /**
     * Предлагает порядок обхода с учётом ограничений: для каждого груза погрузка до разгрузки.
     * $precedence: [ stepIndex => [ индексы шагов, которые должны быть раньше этого ] ].
     * Используется при сборных рейсах: один клиент — одна погрузка, несколько разгрузок и т.п.
     */
    public static function suggestOrderConstrained(array $coordinates, array $precedence, bool $fixFirstAndLast = true): array
    {
        $n = count($coordinates);
        if ($n <= 2) {
            return range(0, $n - 1);
        }

        if (!$fixFirstAndLast) {
            return self::nearestNeighborFull($coordinates);
        }

        $first = 0;
        $last = $n - 1;
        $middle = range(1, $n - 2);

        if (empty($middle)) {
            return [0, $n - 1];
        }

        $order = [$first];
        $current = $first;
        $remaining = $middle;

        while (!empty($remaining)) {
            $best = null;
            $bestDist = PHP_FLOAT_MAX;
            foreach ($remaining as $i) {
                // Шаг i допустим только если все шаги из precedence[i] уже в маршруте
                $mustBeBefore = $precedence[$i] ?? [];
                $allowed = true;
                foreach ($mustBeBefore as $p) {
                    if (!in_array($p, $order, true)) {
                        $allowed = false;
                        break;
                    }
                }
                if (!$allowed) {
                    continue;
                }
                $d = self::distanceKm($coordinates[$current], $coordinates[$i]);
                if ($d < $bestDist) {
                    $bestDist = $d;
                    $best = $i;
                }
            }
            if ($best === null) {
                // Цикл или неверные ограничения — добиваем оставшиеся в произвольном порядке
                $order = array_merge($order, $remaining);
                break;
            }
            $order[] = $best;
            $current = $best;
            $remaining = array_values(array_filter($remaining, fn ($x) => $x !== $best));
        }

        if (!in_array($last, $order, true)) {
            $order[] = $last;
        }
        return $order;
    }

    /**
     * Предлагает порядок обхода точек (индексы 0..n-1).
     * fixFirstAndLast: true = первый и последний индекс не меняются (старт/финиш).
     */
    public static function suggestOrder(array $coordinates, bool $fixFirstAndLast = true): array
    {
        $n = count($coordinates);
        if ($n <= 2) {
            return range(0, $n - 1);
        }

        if (!$fixFirstAndLast) {
            return self::nearestNeighborFull($coordinates);
        }

        $first = 0;
        $last = $n - 1;
        $middle = range(1, $n - 2);

        if (empty($middle)) {
            return [0, $n - 1];
        }

        $order = [$first];
        $current = $first;
        $remaining = $middle;

        while (!empty($remaining)) {
            $best = null;
            $bestDist = PHP_FLOAT_MAX;
            foreach ($remaining as $i) {
                $d = self::distanceKm($coordinates[$current], $coordinates[$i]);
                if ($d < $bestDist) {
                    $bestDist = $d;
                    $best = $i;
                }
            }
            $order[] = $best;
            $current = $best;
            $remaining = array_values(array_filter($remaining, fn ($x) => $x !== $best));
        }

        $order[] = $last;
        return $order;
    }

    /**
     * Nearest neighbour без фиксированных концов (все точки переставляются).
     */
    private static function nearestNeighborFull(array $coordinates): array
    {
        $n = count($coordinates);
        $order = [0];
        $remaining = range(1, $n - 1);

        $current = 0;
        while (!empty($remaining)) {
            $best = null;
            $bestDist = PHP_FLOAT_MAX;
            foreach ($remaining as $i) {
                $d = self::distanceKm($coordinates[$current], $coordinates[$i]);
                if ($d < $bestDist) {
                    $bestDist = $d;
                    $best = $i;
                }
            }
            $order[] = $best;
            $current = $best;
            $remaining = array_values(array_filter($remaining, fn ($x) => $x !== $best));
        }
        return $order;
    }

    /**
     * Переставить массив по заданному порядку индексов.
     */
    public static function reorderByIndices(array $items, array $indices): array
    {
        $out = [];
        foreach ($indices as $i) {
            $out[] = $items[$i];
        }
        return $out;
    }
}
