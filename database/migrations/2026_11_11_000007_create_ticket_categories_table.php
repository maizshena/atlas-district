<?php

// database/migrations/2026_11_11_000007_create_ticket_categories_table.php
// used for ticket tiers with price quota and sold counter

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('category_name');
            $table->decimal('price', 15, 2);
            $table->unsignedInteger('quota');
            $table->unsignedInteger('sold')->default(0);
            $table->timestamps();

            $table->unique(['event_id', 'category_name']);
        });

        // db level guard against overselling and negative stock
        DB::statement('ALTER TABLE ticket_categories ADD CONSTRAINT ticket_categories_stock_check CHECK (sold >= 0 AND sold <= quota)');
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_categories');
    }
};