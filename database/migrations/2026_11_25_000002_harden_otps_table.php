<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// database/migrations/2026_11_25_000002_harden_otps_table.php kolom kode untuk hash dan penghitung percobaan
return new class extends Migration
{
    public function up(): void
    {
        DB::table('otps')->delete();

        Schema::table('otps', function (Blueprint $table): void {
            $table->string('code', 64)->change();
            $table->unsignedSmallInteger('attempts')->default(0)->after('code');
        });
    }

    public function down(): void
    {
        DB::table('otps')->delete();

        Schema::table('otps', function (Blueprint $table): void {
            $table->dropColumn('attempts');
            $table->string('code', 6)->change();
        });
    }
};
