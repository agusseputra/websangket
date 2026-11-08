<?php

namespace Tests\Feature;

use App\Models\Krama;
use App\Models\PeriodeIuran;
use App\Models\Perumahan;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValidasiApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Perumahan $perumahan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->perumahan = Perumahan::create(['nama' => 'RT 1']);
    }

    private function periksaWajib(string $metode, string $url, array $kolom): void
    {
        $respons = $this->actingAs($this->admin)->json($metode, $url, [])->assertUnprocessable();

        $respons->assertJsonValidationErrors($kolom);

        foreach ($kolom as $satu) {
            $pesan = $respons->json("errors.{$satu}.0");
            $this->assertStringNotContainsString('validation.', $pesan, "Pesan {$satu} belum diterjemahkan.");
            $this->assertStringNotContainsString('field', $pesan, "Pesan {$satu} masih bahasa Inggris.");
        }
    }

    public function test_semua_endpoint_menolak_input_wajib_yang_kosong(): void
    {
        $krama = Krama::create(['nama' => 'A']);
        $periode = PeriodeIuran::create(['tahun' => 2027, 'jumlah_dudukan' => 200000, 'jumlah_penanjung_batu' => 1000000]);
        $tagihan = Tagihan::create(['krama_id' => $krama->id, 'periode_iuran_id' => $periode->id, 'jenis' => 'dudukan', 'nominal' => 200000]);

        $daftar = [
            ['POST', '/api/krama', ['nama', 'perumahan_id']],
            ['PUT', "/api/krama/{$krama->id}", ['nama']],
            ['PUT', "/api/krama/{$krama->id}/lokasi", ['latitude', 'longitude', 'perumahan_id']],
            ['POST', "/api/krama/{$krama->id}/bayar", ['transaksi_uuid', 'metode', 'item']],
            ['POST', '/api/perumahan', ['nama']],
            ['PUT', "/api/perumahan/{$this->perumahan->id}", ['nama']],
            ['POST', '/api/users', ['name', 'email', 'password', 'role']],
            ['PUT', "/api/users/{$this->admin->id}", ['name', 'email', 'role']],
            ['POST', '/api/periode', ['tahun', 'jumlah_dudukan', 'jumlah_penanjung_batu']],
            ['PUT', "/api/periode/{$periode->id}", ['tahun', 'jumlah_dudukan', 'jumlah_penanjung_batu']],
            ['POST', '/api/tagihan/validasi', ['ids']],
            ['POST', "/api/tagihan/{$tagihan->id}/koreksi", ['alasan']],
            ['POST', "/api/tagihan/{$tagihan->id}/dispensasi", ['alasan']],
            ['POST', '/api/impor-krama/pratinjau', ['file']],
            ['POST', '/api/impor-krama', ['token']],
            ['POST', '/api/impor-pembayaran/pratinjau', ['file']],
        ];

        foreach ($daftar as [$metode, $url, $kolom]) {
            $this->periksaWajib($metode, $url, $kolom);
        }

        $this->app['auth']->forgetGuards();
        $this->withHeader('Referer', 'http://localhost:8000/login')->postJson('/api/login', [])
            ->assertUnprocessable()->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_format_input_yang_salah_ditolak(): void
    {
        $krama = Krama::create(['nama' => 'A', 'latitude' => -8.1, 'longitude' => 115.1, 'perumahan_id' => $this->perumahan->id]);
        $periode = PeriodeIuran::create(['tahun' => 2027, 'jumlah_dudukan' => 200000, 'jumlah_penanjung_batu' => 1000000]);
        $tagihan = Tagihan::create(['krama_id' => $krama->id, 'periode_iuran_id' => $periode->id, 'jenis' => 'dudukan', 'nominal' => 200000]);
        $this->actingAs($this->admin);

        $this->postJson('/api/krama', ['nama' => 'B', 'perumahan_id' => 9999, 'nik' => '12ab', 'tanggal_mulai_menetap' => 'bukan', 'jumlah_penghuni' => -1])
            ->assertUnprocessable()->assertJsonValidationErrors(['perumahan_id', 'nik', 'tanggal_mulai_menetap', 'jumlah_penghuni']);

        $this->putJson("/api/krama/{$krama->id}/lokasi", ['latitude' => 200, 'longitude' => 'x', 'perumahan_id' => $this->perumahan->id])
            ->assertUnprocessable()->assertJsonValidationErrors(['latitude', 'longitude']);

        $this->postJson("/api/krama/{$krama->id}/bayar", [
            'transaksi_uuid' => 'bukan-uuid', 'metode' => 'kartu',
            'item' => [['tagihan_id' => $tagihan->id, 'nominal_bayar' => 0], ['tagihan_id' => $tagihan->id]],
        ])->assertUnprocessable()->assertJsonValidationErrors(['transaksi_uuid', 'metode', 'item.0.nominal_bayar', 'item.1.nominal_bayar', 'item.0.tagihan_id']);

        $this->postJson('/api/periode', ['tahun' => 1900, 'jumlah_dudukan' => -1, 'jumlah_penanjung_batu' => 'x'])
            ->assertUnprocessable()->assertJsonValidationErrors(['tahun', 'jumlah_dudukan', 'jumlah_penanjung_batu']);

        $this->postJson('/api/users', ['name' => 'X', 'email' => 'bukan', 'password' => 'pendek', 'role' => 'raja'])
            ->assertUnprocessable()->assertJsonValidationErrors(['email', 'password', 'role']);

        $this->postJson('/api/tagihan/validasi', ['ids' => ['x']])->assertUnprocessable()->assertJsonValidationErrors('ids.0');

        $this->getJson('/api/krama?status=raja')->assertUnprocessable();
        $this->getJson('/api/krama?status_data=x')->assertUnprocessable();
        $this->getJson('/api/tagihan?status=x')->assertUnprocessable();
    }

    public function test_tahun_periode_terkunci_setelah_ada_tagihan(): void
    {
        $krama = Krama::create(['nama' => 'A']);
        $kosong = PeriodeIuran::create(['tahun' => 2028, 'jumlah_dudukan' => 200000, 'jumlah_penanjung_batu' => 1000000]);
        $terisi = PeriodeIuran::create(['tahun' => 2027, 'jumlah_dudukan' => 200000, 'jumlah_penanjung_batu' => 1000000]);
        Tagihan::create(['krama_id' => $krama->id, 'periode_iuran_id' => $terisi->id, 'jenis' => 'dudukan', 'nominal' => 200000]);
        $this->actingAs($this->admin);

        $this->putJson("/api/periode/{$terisi->id}", ['tahun' => 2029, 'jumlah_dudukan' => 1, 'jumlah_penanjung_batu' => 1])
            ->assertUnprocessable()->assertJsonValidationErrors('tahun');
        $this->putJson("/api/periode/{$terisi->id}", ['tahun' => 2027, 'jumlah_dudukan' => 250000, 'jumlah_penanjung_batu' => 1])->assertOk();
        $this->putJson("/api/periode/{$kosong->id}", ['tahun' => 2029, 'jumlah_dudukan' => 1, 'jumlah_penanjung_batu' => 1])->assertOk();
    }

    public function test_pagination_krama_tunggakan_dan_tagihan_50_baris(): void
    {
        $periode = PeriodeIuran::create(['tahun' => 2026, 'jumlah_dudukan' => 200000, 'jumlah_penanjung_batu' => 1000000]);

        foreach (range(1, 51) as $nomor) {
            $krama = Krama::create(['nama' => sprintf('Krama %03d', $nomor), 'perumahan_id' => $this->perumahan->id]);
            Tagihan::create(['krama_id' => $krama->id, 'periode_iuran_id' => $periode->id, 'jenis' => 'dudukan', 'nominal' => 200000]);
        }

        $this->actingAs($this->admin);

        foreach (['/api/krama', '/api/tunggakan', '/api/tagihan'] as $url) {
            $this->getJson($url)->assertOk()->assertJsonCount(50, 'data')
                ->assertJsonPath('meta.current_page', 1)
                ->assertJsonPath('meta.last_page', 2)
                ->assertJsonPath('meta.total', 51);

            $this->getJson("{$url}?page=2")->assertOk()->assertJsonCount(1, 'data')
                ->assertJsonPath('meta.current_page', 2);
        }

        $this->getJson('/api/tunggakan?page=2')->assertJsonPath('total', 10200000);
        $this->getJson('/api/krama?q=Krama 05&page=1')->assertJsonPath('meta.total', 2);
    }

    public function test_pagination_tagihan_menghitung_per_pembayaran(): void
    {
        $periode = PeriodeIuran::create(['tahun' => 2027, 'jumlah_dudukan' => 200000, 'jumlah_penanjung_batu' => 1000000]);

        foreach (range(1, 51) as $nomor) {
            $krama = Krama::create(['nama' => "K{$nomor}"]);

            foreach (['dudukan', 'penanjung_batu'] as $jenis) {
                Tagihan::create([
                    'krama_id' => $krama->id, 'periode_iuran_id' => $periode->id, 'jenis' => $jenis, 'nominal' => 200000,
                    'nominal_bayar' => 200000, 'status' => 'dibayar', 'no_struk' => "DS-{$nomor}",
                ]);
            }
        }

        $this->actingAs($this->admin)->getJson('/api/tagihan')
            ->assertJsonCount(50, 'data')->assertJsonPath('meta.total', 51)->assertJsonCount(2, 'data.0.item');
        $this->actingAs($this->admin)->getJson('/api/tagihan?page=2')
            ->assertJsonCount(1, 'data')->assertJsonCount(2, 'data.0.item');

    }
}
