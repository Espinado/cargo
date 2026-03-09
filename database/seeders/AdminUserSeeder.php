<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Cargo Trans admin: rvr@arguss.lv / 12345
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'rvr@arguss.lv'],
            [
                'name' => 'Admin',
                'password' => Hash::make('12345'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
