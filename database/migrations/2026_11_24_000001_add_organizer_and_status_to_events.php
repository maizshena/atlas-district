<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/2026_11_24_000001_add_organizer_and_status_to_events.php kepemilikan organizer dan status tayang event
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->foreignId('organizer_id')->nullable()->after('venue_id')->constrained('users')->restrictOnDelete();
            $table->string('status')->default('PUBLISHED')->after('is_closed');
            $table->index('organizer_id');
            $table->index(['status', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->dropIndex(['status', 'end_date']);
            $table->dropIndex(['organizer_id']);
            $table->dropConstrainedForeignId('organizer_id');
            $table->dropColumn('status');
        });
    }
};