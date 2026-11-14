<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// database/seeders/DatabaseSeeder.php
// creates the verified admin account from env, demo data is opt in
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);

        if (filter_var(env('SEED_DEMO_DATA', false), FILTER_VALIDATE_BOOLEAN)) {
            $this->call(DemoDataSeeder::class);
        }
    }
}
