<?php

namespace Tests\Feature;

use App\Models\Krama;
use App\Models\PeriodeIuran;
use App\Models\Perumahan;
use App\Models\Tagihan;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkemaTagihanTest extends TestCase
{
    use RefreshDatabase;

    public function test_total_tagihan_dihitung_dari_nominal_dan_kekurangan(): void
    {
        $tagihan = $this->buatTagihan(['nominal' => 200000, 'kekurangan_sebelumnya' => 75000]);

        $this->assertEquals(275000, $tagihan->fresh()->total_tagihan);
    }

    public function test_tagihan_tidak_boleh_ganda_per_krama_periode_jenis(): void
    {
        $tagihan = $this->buatTagihan(['nominal' => 200000]);

        $this->expectException(QueryException::class);
        Tagihan::create([
            'krama_id' => $tagihan->krama_id,
            'periode_iuran_id' => $tagihan->periode_iuran_id,
            'jenis' => 'dudukan',
            'nominal' => 200000,
        ]);
    }

    public function test_seeder_mengisi_perumahan_dan_admin_tanpa_menimpa(): void
    {
        config(['sangket.admin.email' => 'admin@contoh.test', 'sangket.admin.password' => 'rahasia123']);

        $this->seed(DatabaseSeeder::class);
        $sandiAwal = User::where('email', 'admin@contoh.test')->value('password');

        config(['sangket.admin.password' => 'sandi-lain']);
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(16, Perumahan::count());
        $this->assertSame(1, User::where('role', 'admin')->count());
        $this->assertSame($sandiAwal, User::where('email', 'admin@contoh.test')->value('password'));
    }

    private function buatTagihan(array $data): Tagihan
    {
        $krama = Krama::create(['nama' => 'Contoh']);
        $periode = PeriodeIuran::create(['tahun' => 2026, 'jumlah_dudukan' => 200000, 'jumlah_penanjung_batu' => 1000000]);

        return Tagihan::create([
            'krama_id' => $krama->id,
            'periode_iuran_id' => $periode->id,
            'jenis' => 'dudukan',
            ...$data,
        ]);
    }
}
