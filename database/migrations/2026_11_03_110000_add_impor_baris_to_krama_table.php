<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('krama', function (Blueprint $table) {
            // Nomor baris di sheet ALL, dipakai untuk menautkan pembayaran Excel pada fase tagihan.
            $table->unsignedSmallInteger('impor_baris')->nullable()->unique()->after('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('krama', function (Blueprint $table) {
            $table->dropColumn('impor_baris');
        });
    }
};
