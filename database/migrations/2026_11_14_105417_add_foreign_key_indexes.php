<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Foreign key lookups and list filters were doing sequential scans because
     * the referencing columns never had an index of their own.
     *
     * @var array<string, array<int, string>>
     */
    private const INDEXES = [
        'tickets' => ['order_id', 'is_used'],
        'orders' => ['user_id'],
        'order_items' => ['order_id', 'ticket_category_id'],
        'ticket_categories' => ['event_id'],
        'events' => ['venue_id', 'end_date'],
        'event_artist' => ['artist_id'],
        'otps' => ['expires_at'],
    ];

    public function up(): void
    {
        foreach (self::INDEXES as $table => $columns) {
            Schema::table($table, function (Blueprint $blueprint) use ($table, $columns): void {
                foreach ($columns as $column) {
                    $blueprint->index($column, "{$table}_{$column}_index");
                }
            });
        }
    }

    public function down(): void
    {
        foreach (self::INDEXES as $table => $columns) {
            Schema::table($table, function (Blueprint $blueprint) use ($table, $columns): void {
                foreach ($columns as $column) {
                    $blueprint->dropIndex("{$table}_{$column}_index");
                }
            });
        }
    }
};
