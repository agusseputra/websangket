<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('krama', function (Blueprint $table) {
            $table->id();
            $table->text('nik')->nullable();
            $table->char('nik_hash', 64)->nullable()->unique();
            $table->string('nama', 150);
            $table->enum('status', ['pengampel', 'pemirak', 'krama_tamiu', 'tamiu'])->default('krama_tamiu');
            $table->enum('status_data', ['lengkap', 'pending'])->default('pending')->index();
            $table->string('asal', 150)->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->foreignId('perumahan_id')->nullable()->constrained('perumahan')->restrictOnDelete();
            $table->string('nomor_rumah', 30)->nullable();
            $table->unsignedSmallInteger('jumlah_penghuni')->nullable();
            $table->date('tanggal_mulai_menetap')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->foreignId('lokasi_ditandai_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('lokasi_ditandai_at')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('krama');
    }
};
