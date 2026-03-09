<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            if (!Schema::hasColumn('trips', 'carrier_company_id')) {
                $table->unsignedBigInteger('carrier_company_id')->nullable()->after('id');
                $table->index('carrier_company_id');
            }
        });

        $fkExists = DB::selectOne("
            SELECT 1 FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'trips'
            AND CONSTRAINT_NAME = 'trips_carrier_company_id_foreign' AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");
        if (!$fkExists) {
            Schema::table('trips', function (Blueprint $table) {
                $table->foreign('carrier_company_id', 'trips_carrier_company_id_foreign')
                    ->references('id')
                    ->on('companies')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropForeign('trips_carrier_company_id_foreign');
        });
        Schema::table('trips', function (Blueprint $table) {
            if (Schema::hasColumn('trips', 'carrier_company_id')) {
                $table->dropColumn('carrier_company_id');
            }
        });
    }
};
