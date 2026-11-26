<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// database/migrations/2026_11_25_000003_add_trigram_search_indexes.php indeks pencarian nama event dan nomor pesanan untuk postgres
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        DB::statement('CREATE INDEX events_name_trgm_index ON events USING gin (lower(name) gin_trgm_ops)');
        DB::statement('CREATE INDEX orders_order_number_trgm_index ON orders USING gin (lower(order_number) gin_trgm_ops)');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS events_name_trgm_index');
        DB::statement('DROP INDEX IF EXISTS orders_order_number_trgm_index');
    }
};
