<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('trailers', 'company_id')) {
            return;
        }

        Schema::table('trailers', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->after('id');
        });

        if (Schema::hasColumn('trailers', 'company')) {
            DB::table('trailers')->update(['company_id' => DB::raw('`company`')]);
            Schema::table('trailers', function (Blueprint $table) {
                $table->dropColumn('company');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('trailers', 'company_id')) {
            return;
        }
        Schema::table('trailers', function (Blueprint $table) {
            $table->unsignedInteger('company')->nullable()->after('id');
        });
        DB::table('trailers')->update(['company' => DB::raw('company_id')]);
        Schema::table('trailers', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }
};
