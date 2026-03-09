<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Рейсы экспедитора (третья сторона) выполняются без нашего тягача/прицепа.
 * Статистика по одометру привязана к trips.odo_start_km / odo_end_km и trip_steps.odo_*_km.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->unsignedBigInteger('truck_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->unsignedBigInteger('truck_id')->nullable(false)->change();
        });
    }
};
