<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_warga', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->text('nik');
            $table->string('asal', 150);
            $table->enum('jenis', ['tinggal', 'usaha'])->index();
            $table->string('nama_usaha', 150)->nullable();
            $table->string('jenis_usaha', 100)->nullable();
            $table->string('alamat', 255);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->boolean('menetap')->default(false);
            $table->enum('status', ['pending', 'ditindak'])->default('pending')->index();
            $table->foreignId('ditindak_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ditindak_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_warga');
    }
};
