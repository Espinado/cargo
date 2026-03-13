<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Чистка старых/некорректных водительских расходов,
 * которые ломают отчёт Vadītāja notikumi:
 *
 * - amount <= 0       → сумма ноль, неинтересно для статистики;
 * - expense_date NULL → попадают как 01.01.1970;
 * - driver_id NULL    → не привязаны к водителю (в Notikumi не нужны).
 *
 * Удаляем только такие строки, остальные расходы не трогаем.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('trip_expenses')
            ->where(function ($q) {
                $q->whereNull('expense_date')
                  ->orWhere('amount', '<=', 0)
                  ->orWhereNull('trip_id');
            })
            ->delete();
    }

    public function down(): void
    {
        // Обратное восстановление конкретных строк невозможно.
        // Оставляем метод пустым.
    }
};

