<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('periode_iuran', function (Blueprint $table) {
            $table->id();
            $table->year('tahun')->unique();
            $table->decimal('jumlah_dudukan', 12, 0);
            $table->decimal('jumlah_penanjung_batu', 12, 0);
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('periode_iuran');
    }
};
