<?php

namespace Tests\Feature;

use App\Models\Krama;
use App\Models\PeriodeIuran;
use App\Models\Tagihan;
use App\Models\User;
use Database\Seeders\ContohKramaSeeder;
use Database\Seeders\PerumahanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContohKramaTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_aman_diulang_dan_perintah_hapus_tidak_menyentuh_data_lain(): void
    {
        $this->seed(PerumahanSeeder::class);
        User::factory()->admin()->create();
        $asli = Krama::create(['nama' => 'Krama Asli']);

        $this->seed(ContohKramaSeeder::class);
        $this->seed(ContohKramaSeeder::class);

        $contoh = Krama::where('keterangan', ContohKramaSeeder::PENANDA);
        $this->assertSame(18, (clone $contoh)->count());
        $this->assertSame(12, (clone $contoh)->where('status_data', 'lengkap')->count());
        $this->assertSame(6, (clone $contoh)->where('status_data', 'pending')->count());
        $this->assertSame(8, (clone $contoh)->whereNotNull('latitude')->count());

        $periode = PeriodeIuran::create(['tahun' => 2027, 'jumlah_dudukan' => 200000, 'jumlah_penanjung_batu' => 1000000]);
        Tagihan::create(['krama_id' => (clone $contoh)->first()->id, 'periode_iuran_id' => $periode->id, 'jenis' => 'dudukan', 'nominal' => 200000]);

        $this->artisan('krama:hapus-contoh')->assertSuccessful();

        $this->assertSame(0, (clone $contoh)->count());
        $this->assertSame(0, Tagihan::count());
        $this->assertTrue(Krama::whereKey($asli->id)->exists());
    }
}
