<?php

namespace Tests\Feature;

use App\Models\Krama;
use App\Models\PeriodeIuran;
use App\Models\Perumahan;
use App\Models\Tagihan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CekTunggakanTest extends TestCase
{
    use RefreshDatabase;

    private const NIK = '5108010101900001';

    private function kramaDenganTagihan(): Krama
    {
        $perumahan = Perumahan::create(['nama' => 'Kanaya']);
        $krama = Krama::create([
            'nama' => 'Made Contoh', 'nik' => self::NIK, 'status' => 'krama_tamiu', 'perumahan_id' => $perumahan->id,
            'nomor_rumah' => 'A1', 'no_hp' => '08123456789', 'latitude' => -8.1, 'longitude' => 115.1,
        ]);
        $lama = PeriodeIuran::create(['tahun' => 2026, 'jumlah_dudukan' => 200000, 'jumlah_penanjung_batu' => 1000000]);
        $baru = PeriodeIuran::create(['tahun' => 2027, 'jumlah_dudukan' => 200000, 'jumlah_penanjung_batu' => 1000000]);

        Tagihan::create([
            'krama_id' => $krama->id, 'periode_iuran_id' => $lama->id, 'jenis' => 'dudukan', 'nominal' => 200000,
            'nominal_bayar' => 200000, 'tanggal_bayar' => '2026-03-01', 'status' => 'valid',
        ]);
        Tagihan::create(['krama_id' => $krama->id, 'periode_iuran_id' => $baru->id, 'jenis' => 'dudukan', 'nominal' => 200000]);

        return $krama;
    }

    public function test_nama_dan_nik_cocok_menampilkan_identitas_tunggakan_dan_riwayat_tanpa_login(): void
    {
        $this->kramaDenganTagihan();

        $respons = $this->postJson('/api/cek-tunggakan', ['nama' => '  made   CONTOH ', 'nik' => self::NIK])
            ->assertOk()
            ->assertJsonPath('nama', 'Made Contoh')
            ->assertJsonPath('status', 'Krama Tamiu')
            ->assertJsonPath('perumahan', 'Kanaya')
            ->assertJsonPath('nomor_rumah', 'A1')
            ->assertJsonPath('tunggakan', 200000)
            ->assertJsonCount(2, 'riwayat')
            ->assertJsonPath('riwayat.0.tahun', 2027)
            ->assertJsonPath('riwayat.0.status', 'dibuat')
            ->assertJsonPath('riwayat.1.tanggal_bayar', '2026-03-01');

        foreach ([self::NIK, '08123456789', '115.1', 'latitude', 'krama_id', 'petugas'] as $rahasia) {
            $this->assertStringNotContainsString($rahasia, $respons->getContent());
        }
    }

    public function test_nama_atau_nik_salah_mendapat_jawaban_yang_sama(): void
    {
        $this->kramaDenganTagihan();

        $namaSalah = $this->postJson('/api/cek-tunggakan', ['nama' => 'Orang Lain', 'nik' => self::NIK])->assertNotFound();
        $nikSalah = $this->postJson('/api/cek-tunggakan', ['nama' => 'Made Contoh', 'nik' => '5108010101900002'])->assertNotFound();

        $this->assertSame($namaSalah->json('message'), $nikSalah->json('message'));
        $namaSalah->assertJsonMissingPath('nama');
    }

    public function test_krama_nonaktif_dan_terhapus_tidak_ditemukan(): void
    {
        $krama = $this->kramaDenganTagihan();

        $krama->update(['aktif' => false]);
        $this->postJson('/api/cek-tunggakan', ['nama' => 'Made Contoh', 'nik' => self::NIK])->assertNotFound();

        $krama->update(['aktif' => true]);
        $krama->delete();
        $this->postJson('/api/cek-tunggakan', ['nama' => 'Made Contoh', 'nik' => self::NIK])->assertNotFound();
    }

    public function test_input_wajib_dan_format_nik_divalidasi(): void
    {
        $this->postJson('/api/cek-tunggakan', [])->assertUnprocessable()->assertJsonValidationErrors(['nama', 'nik']);
        $this->postJson('/api/cek-tunggakan', ['nama' => 'A', 'nik' => '12ab'])->assertUnprocessable()->assertJsonValidationErrors('nik');
    }

    public function test_percobaan_dibatasi_per_menit(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->postJson('/api/cek-tunggakan', ['nama' => 'A', 'nik' => '5108010101900002'])->assertNotFound();
        }

        $this->postJson('/api/cek-tunggakan', ['nama' => 'A', 'nik' => '5108010101900002'])->assertTooManyRequests();
    }
}
