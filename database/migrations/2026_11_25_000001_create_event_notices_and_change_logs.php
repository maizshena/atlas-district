<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// database/migrations/2026_11_25_000001_create_event_notices_and_change_logs.php tabel pengumuman event dan riwayat perubahan
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_notices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('message', 160);
            $table->boolean('is_automatic')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index(['event_id', 'expires_at']);
        });

        Schema::create('event_change_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->json('changes');
            $table->timestamp('created_at')->useCurrent();
            $table->index(['event_id', 'created_at']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_change_logs');
        Schema::dropIfExists('event_notices');
    }
};
