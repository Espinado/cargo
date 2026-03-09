<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('legal_country')->nullable()->after('post_code');
            $table->string('legal_city')->nullable()->after('legal_country');
            $table->string('legal_address')->nullable()->after('legal_city');
            $table->string('legal_post_code')->nullable()->after('legal_address');

            $table->string('physical_country')->nullable()->after('legal_post_code');
            $table->string('physical_city')->nullable()->after('physical_country');
            $table->string('physical_address')->nullable()->after('physical_city');
            $table->string('physical_post_code')->nullable()->after('physical_address');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'legal_country', 'legal_city', 'legal_address', 'legal_post_code',
                'physical_country', 'physical_city', 'physical_address', 'physical_post_code',
            ]);
        });
    }
};
