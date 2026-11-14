<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

// database/seeders/AdminUserSeeder.php
// admin credentials come from ADMIN_EMAIL and ADMIN_PASSWORD
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! is_string($email) || $email === '' || ! is_string($password) || $password === '') {
            throw new RuntimeException('Set ADMIN_EMAIL and ADMIN_PASSWORD in .env before seeding.');
        }

        $user = User::query()->firstOrNew(['email' => $email]);
        $user->fill(['name' => 'Administrator', 'password' => $password]);
        $user->forceFill(['role' => 'ADMIN', 'is_verified' => true])->save();
    }
}
