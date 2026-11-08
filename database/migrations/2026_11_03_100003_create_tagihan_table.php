<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('krama_id')->constrained('krama')->restrictOnDelete();
            $table->foreignId('periode_iuran_id')->constrained('periode_iuran')->restrictOnDelete();
            $table->enum('jenis', ['dudukan', 'penanjung_batu']);
            $table->decimal('nominal', 12, 0);
            $table->decimal('kekurangan_sebelumnya', 12, 0)->default(0);
            $table->decimal('total_tagihan', 12, 0)->storedAs('nominal + kekurangan_sebelumnya');
            $table->decimal('nominal_bayar', 12, 0)->nullable();
            $table->date('tanggal_bayar')->nullable();
            $table->enum('metode', ['tunai', 'transfer'])->nullable();
            $table->enum('status', ['dibuat', 'dibayar', 'pending', 'valid', 'dispensasi'])->default('dibuat')->index();
            $table->foreignId('sisa_dipindah_ke_id')->nullable()->constrained('tagihan')->nullOnDelete();
            $table->string('no_struk', 30)->nullable()->index();
            $table->char('transaksi_uuid', 36)->nullable()->index();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('divalidasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('divalidasi_at')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['krama_id', 'periode_iuran_id', 'jenis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihan');
    }
};
