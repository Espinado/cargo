<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class EnsureCargoTransAdmin extends Command
{
    protected $signature = 'cargo-trans:ensure-admin';
    protected $description = 'Create or update Cargo TMS admin (rvr@arguss.lv / 12345)';

    public function handle(): int
    {
        $user = User::updateOrCreate(
            ['email' => 'rvr@arguss.lv'],
            [
                'name' => 'Admin',
                'password' => Hash::make('12345'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->info('Admin user ready: rvr@arguss.lv / 12345');
        return 0;
    }
}
