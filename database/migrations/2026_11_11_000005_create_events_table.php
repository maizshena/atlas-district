<?php

// database/migrations/2026_11_11_000005_create_events_table.php
// used for concert events booked on a venue

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('banner_url')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamps();

            $table->index(['venue_id', 'start_date']);
        });

        // db level guard so end date never precedes start date
        DB::statement('ALTER TABLE events ADD CONSTRAINT events_date_range_check CHECK (end_date >= start_date)');
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
