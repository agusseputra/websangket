<?php

namespace Tests\Feature;

use App\Enums\StatusData;
use App\Enums\StatusKrama;
use App\Models\Krama;
use App\Models\Perumahan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KramaApiTest extends TestCase
{
    use RefreshDatabase;

    private const NIK = '5108010101900001';

    private function perumahan(): Perumahan
    {
        return Perumahan::create(['nama' => 'RT 1']);
    }

    public function test_status_data_dihitung_otomatis(): void
    {
        $perumahan = $this->perumahan();

        $lengkap = Krama::create(['nama' => 'A', 'nik' => self::NIK, 'asal' => 'Bali', 'perumahan_id' => $perumahan->id]);
        $tanpaNik = Krama::create(['nama' => 'B', 'asal' => 'Bali', 'perumahan_id' => $perumahan->id]);
        $nikSalah = Krama::create(['nama' => 'C', 'nik' => '5108019999900001', 'asal' => 'Bali', 'perumahan_id' => $perumahan->id]);
        $tanpaAsal = Krama::create(['nama' => 'D', 'nik' => '5108014101900002', 'perumahan_id' => $perumahan->id]);

        $this->assertSame(StatusData::Lengkap, $lengkap->fresh()->status_data);
        $this->assertSame(['NIK kosong'], $tanpaNik->fresh()->alasanPending());
        $this->assertSame(['NIK tidak valid'], $nikSalah->fresh()->alasanPending());
        $this->assertSame(['Asal kosong'], $tanpaAsal->fresh()->alasanPending());

        $tanpaNik->update(['nik' => '5108010101900003']);
        $this->assertSame(StatusData::Lengkap, $tanpaNik->fresh()->status_data);
    }

    public function test_petugas_menambah_krama_dengan_status_default_walau_mengirim_status_lain(): void
    {
        $perumahan = $this->perumahan();

        $respons = $this->actingAs(User::factory()->create())->postJson('/api/krama', [
            'nama' => 'Baru', 'nik' => self::NIK, 'asal' => 'Bali', 'perumahan_id' => $perumahan->id,
            'status' => 'pengampel', 'jumlah_penghuni' => 4, 'tanggal_mulai_menetap' => '2020-01-15', 'pekerjaan' => 'Guru',
        ]);

        $respons->assertCreated()
            ->assertJsonPath('data.status', 'krama_tamiu')
            ->assertJsonPath('data.status_data', 'lengkap')
            ->assertJsonPath('data.jumlah_penghuni', 4)
            ->assertJsonPath('data.pekerjaan', 'Guru')
            ->assertJsonPath('data.nik_tersamar', '510801******0001')
            ->assertJsonMissingPath('data.nik');
    }

    public function test_krama_baru_wajib_perumahan_dan_nik_16_digit_dan_tidak_duplikat(): void
    {
        $perumahan = $this->perumahan();
        Krama::create(['nama' => 'Ada', 'nik' => self::NIK]);
        $petugas = User::factory()->create();

        $this->actingAs($petugas)->postJson('/api/krama', ['nama' => 'X'])
            ->assertUnprocessable()->assertJsonValidationErrors('perumahan_id');

        $this->actingAs($petugas)->postJson('/api/krama', ['nama' => 'X', 'perumahan_id' => $perumahan->id, 'nik' => '123'])
            ->assertUnprocessable()->assertJsonValidationErrors('nik');

        $this->actingAs($petugas)->postJson('/api/krama', ['nama' => 'X', 'perumahan_id' => $perumahan->id, 'nik' => self::NIK])
            ->assertUnprocessable()->assertJsonValidationErrors('nik');
    }

    public function test_ubah_tanpa_nik_mempertahankan_nik_lama_dan_hanya_admin_mengubah_status(): void
    {
        $krama = Krama::create(['nama' => 'Lama', 'nik' => '123456']);
        $data = ['nama' => 'Baru', 'status' => 'pengampel', 'aktif' => false];

        $this->actingAs(User::factory()->create())->putJson("/api/krama/{$krama->id}", $data)->assertOk();
        $krama->refresh();
        $this->assertSame('Baru', $krama->nama);
        $this->assertSame('123456', $krama->nik);
        $this->assertSame(StatusKrama::KramaTamiu, $krama->status);
        $this->assertTrue($krama->aktif);

        $this->actingAs(User::factory()->admin()->create())->putJson("/api/krama/{$krama->id}", $data)->assertOk();
        $krama->refresh();
        $this->assertSame(StatusKrama::Pengampel, $krama->status);
        $this->assertFalse($krama->aktif);
    }

    public function test_pencarian_dan_filter(): void
    {
        $perumahan = $this->perumahan();
        Krama::create(['nama' => 'Wayan Satu', 'nik' => self::NIK, 'asal' => 'Bali', 'perumahan_id' => $perumahan->id]);
        Krama::create(['nama' => 'Made Dua', 'latitude' => -8.1, 'longitude' => 115.1]);
        $petugas = User::factory()->create();

        $this->actingAs($petugas)->getJson('/api/krama?q=wayan')->assertJsonCount(1, 'data')->assertJsonPath('data.0.nama', 'Wayan Satu');
        $this->actingAs($petugas)->getJson('/api/krama?q='.self::NIK)->assertJsonCount(1, 'data')->assertJsonPath('data.0.nama', 'Wayan Satu');
        $this->actingAs($petugas)->getJson('/api/krama?q=5108010')->assertJsonCount(0, 'data');
        $this->actingAs($petugas)->getJson('/api/krama?status_data=pending')->assertJsonCount(1, 'data')->assertJsonPath('data.0.nama', 'Made Dua');
        $this->actingAs($petugas)->getJson('/api/krama?lokasi=belum')->assertJsonCount(1, 'data')->assertJsonPath('data.0.nama', 'Wayan Satu');
        $this->actingAs($petugas)->getJson("/api/krama?perumahan_id={$perumahan->id}")->assertJsonCount(1, 'data');
        $this->actingAs($petugas)->getJson('/api/krama?status=pengampel')->assertJsonCount(0, 'data');
    }

    public function test_akses_nik_dan_hapus(): void
    {
        $krama = Krama::create(['nama' => 'A', 'nik' => self::NIK]);
        $petugas = User::factory()->create();

        $this->actingAs($petugas)->getJson("/api/krama/{$krama->id}/nik")->assertOk()->assertJsonPath('nik', self::NIK);
        $this->actingAs($petugas)->deleteJson("/api/krama/{$krama->id}")->assertForbidden();

        $this->actingAs(User::factory()->admin()->create())->deleteJson("/api/krama/{$krama->id}")->assertNoContent();
        $this->assertSoftDeleted($krama);
    }

    public function test_tamu_tidak_bisa_membuka_krama(): void
    {
        $this->getJson('/api/krama')->assertUnauthorized();
    }
}
