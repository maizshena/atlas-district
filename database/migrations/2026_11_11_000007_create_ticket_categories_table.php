<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_categories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedBigInteger('price');
            $table->unsignedInteger('quota');
            $table->unsignedInteger('sold')->default(0);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE ticket_categories ADD CONSTRAINT ticket_categories_sold_check CHECK (sold >= 0 AND sold <= quota)');
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_categories');
    }
};
