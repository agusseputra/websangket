<?php

namespace Tests\Feature;

use App\Models\Krama;
use App\Models\PeriodeIuran;
use App\Models\Perumahan;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardPetaTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    private function tagihan(Krama $krama, PeriodeIuran $periode, string $jenis, int $nominal, ?int $bayar = null, string $status = 'dibuat'): Tagihan
    {
        return Tagihan::create([
            'krama_id' => $krama->id,
            'periode_iuran_id' => $periode->id,
            'jenis' => $jenis,
            'nominal' => $nominal,
            'nominal_bayar' => $bayar,
            'status' => $status,
        ]);
    }

    public function test_ringkasan_dashboard_per_tahun_dan_per_jenis(): void
    {
        $this->travelTo('2026-11-04');
        $periode2026 = PeriodeIuran::create(['tahun' => 2026, 'jumlah_dudukan' => 200000, 'jumlah_penanjung_batu' => 1000000]);
        $periode2027 = PeriodeIuran::create(['tahun' => 2027, 'jumlah_dudukan' => 200000, 'jumlah_penanjung_batu' => 1000000]);

        $lunas = Krama::create(['nama' => 'Lunas']);
        $sebagian = Krama::create(['nama' => 'Sebagian']);
        $belum = Krama::create(['nama' => 'Belum']);
        $terhapus = Krama::create(['nama' => 'Terhapus']);

        $this->tagihan($lunas, $periode2026, 'dudukan', 200000, 200000, 'valid');
        $this->tagihan($lunas, $periode2026, 'penanjung_batu', 1000000, 1000000, 'valid');
        $this->tagihan($sebagian, $periode2026, 'dudukan', 200000, 125000, 'valid');
        $this->tagihan($belum, $periode2026, 'dudukan', 200000, 200000, 'dibayar');
        $this->tagihan($terhapus, $periode2026, 'dudukan', 200000);
        $terhapus->delete();
        $this->tagihan($lunas, $periode2027, 'dudukan', 200000);

        $this->actingAs($this->admin)->getJson('/api/admin/ringkasan')
            ->assertOk()
            ->assertJsonPath('periode.tahun', 2026)
            ->assertJsonPath('periode.krama_total', 3)
            ->assertJsonPath('periode.krama_bayar_valid', 2)
            ->assertJsonPath('periode.tunggakan_total', 75000)
            ->assertJsonPath('periode.jenis.0.jenis', 'dudukan')
            ->assertJsonPath('periode.jenis.0.valid', 325000)
            ->assertJsonPath('periode.jenis.0.baris_valid', 2)
            ->assertJsonPath('periode.jenis.1.baris_valid', 1)
            ->assertJsonPath('periode.jenis.0.belum_dihitung', 200000)
            ->assertJsonPath('periode.jenis.0.tunggakan', 75000)
            ->assertJsonPath('periode.jenis.1.jenis', 'penanjung_batu')
            ->assertJsonPath('periode.jenis.1.valid', 1000000)
            ->assertJsonPath('periode.jenis.1.tunggakan', 0);

        $this->actingAs($this->admin)->getJson('/api/admin/ringkasan?tahun=2027')
            ->assertJsonPath('periode.tahun', 2027)
            ->assertJsonPath('periode.krama_bayar_valid', 0)
            ->assertJsonPath('periode.tunggakan_total', 200000)
            ->assertJsonPath('periode.jenis.1.valid', 0);

        $this->actingAs($this->admin)->getJson('/api/admin/ringkasan?tahun=1999')->assertOk()->assertJsonPath('periode', null);
        $this->actingAs($this->admin)->getJson('/api/admin/ringkasan?tahun=abc')->assertUnprocessable()->assertJsonValidationErrors('tahun');
    }

    public function test_data_peta_hanya_krama_aktif_berkoordinat_tanpa_nik(): void
    {
        $perumahan = Perumahan::create(['nama' => 'Kanaya', 'latitude' => -8.14, 'longitude' => 115.09]);
        Perumahan::create(['nama' => 'Tanpa Titik']);

        Krama::create([
            'nama' => 'Bertitik', 'nik' => '5108010101900001', 'status' => 'pemirak', 'perumahan_id' => $perumahan->id,
            'nomor_rumah' => 'A1', 'latitude' => -8.1, 'longitude' => 115.1,
        ]);
        Krama::create(['nama' => 'Tanpa Lokasi', 'perumahan_id' => $perumahan->id]);
        Krama::create(['nama' => 'Nonaktif', 'latitude' => -8.1, 'longitude' => 115.1, 'aktif' => false]);
        Krama::create(['nama' => 'Terhapus', 'latitude' => -8.1, 'longitude' => 115.1])->delete();

        $respons = $this->actingAs($this->admin)->getJson('/api/peta')
            ->assertOk()
            ->assertJsonCount(1, 'krama')
            ->assertJsonPath('krama.0.nama', 'Bertitik')
            ->assertJsonPath('krama.0.status', 'pemirak')
            ->assertJsonPath('krama.0.status_label', 'Pemirak')
            ->assertJsonPath('krama.0.perumahan', 'Kanaya')
            ->assertJsonPath('krama.0.nomor_rumah', 'A1')
            ->assertJsonPath('krama.0.latitude', -8.1)
            ->assertJsonCount(1, 'perumahan')
            ->assertJsonPath('perumahan.0.nama', 'Kanaya')
            ->assertJsonPath('perumahan.0.krama_count', 2);

        $this->assertStringNotContainsString('5108010101900001', $respons->getContent());
        $this->assertArrayNotHasKey('nik', $respons->json('krama.0'));
    }

    public function test_data_peta_dan_ringkasan_hanya_untuk_admin(): void
    {
        $this->getJson('/api/peta')->assertUnauthorized();
        $this->actingAs(User::factory()->create())->getJson('/api/peta')->assertForbidden();
        $this->actingAs(User::factory()->create())->getJson('/api/admin/ringkasan')->assertForbidden();
    }
}
