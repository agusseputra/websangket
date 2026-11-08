<?php

namespace Tests\Feature;

use App\Enums\StatusKrama;
use App\Models\Krama;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class KramaNikTest extends TestCase
{
    use RefreshDatabase;

    private const NIK = '5108010101900001';

    public function test_nik_terenkripsi_di_database_dan_terbaca_lewat_model(): void
    {
        $krama = Krama::create(['nama' => 'Contoh', 'nik' => self::NIK]);

        $baris = DB::table('krama')->find($krama->id);

        $this->assertStringNotContainsString(self::NIK, $baris->nik);
        $this->assertNotSame(self::NIK, $baris->nik_hash);
        $this->assertSame(Krama::hashNik(self::NIK), $baris->nik_hash);
        $this->assertSame(self::NIK, $krama->fresh()->nik);
    }

    public function test_nik_tidak_ikut_keluar_di_json(): void
    {
        $krama = Krama::create(['nama' => 'Contoh', 'nik' => self::NIK]);

        $this->assertArrayNotHasKey('nik', $krama->toArray());
        $this->assertArrayNotHasKey('nik_hash', $krama->toArray());
    }

    public function test_nik_sama_ditolak_tetapi_nik_kosong_boleh_banyak(): void
    {
        Krama::create(['nama' => 'Kosong 1', 'nik' => '']);
        Krama::create(['nama' => 'Kosong 2', 'nik' => null]);
        Krama::create(['nama' => 'Pertama', 'nik' => self::NIK]);

        $this->assertSame(2, Krama::whereNull('nik_hash')->count());

        $this->expectException(QueryException::class);
        Krama::create(['nama' => 'Kedua', 'nik' => self::NIK]);
    }

    public function test_status_krama_baru_default_krama_tamiu(): void
    {
        $krama = Krama::create(['nama' => 'Baru']);

        $this->assertSame(StatusKrama::KramaTamiu, $krama->fresh()->status);
    }
}
