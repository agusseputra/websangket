<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('krama', function (Blueprint $table) {
            // Dipakai untuk memulihkan status jika pembayaran Penanjung Batu dikoreksi.
            $table->enum('status_sebelum_pengampel', ['krama_tamiu', 'tamiu'])->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('krama', function (Blueprint $table) {
            $table->dropColumn('status_sebelum_pengampel');
        });
    }
};
