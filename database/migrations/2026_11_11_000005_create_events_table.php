<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('venue_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->boolean('is_closed')->default(false);
            $table->string('banner_url')->nullable();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE events ADD CONSTRAINT events_dates_check CHECK (end_date >= start_date)');
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
