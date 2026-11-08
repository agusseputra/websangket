<?php

namespace Tests\Feature;

use App\Enums\JenisTagihan;
use App\Enums\StatusKrama;
use App\Enums\StatusTagihan;
use App\Models\Krama;
use App\Models\PeriodeIuran;
use App\Models\Perumahan;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class TagihanTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $petugas;

    private Perumahan $perumahan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->petugas = User::factory()->create();
        $this->perumahan = Perumahan::create(['nama' => 'RT 1']);
    }

    private function krama(string $status = 'krama_tamiu', array $data = []): Krama
    {
        return Krama::create([
            'nama' => 'Krama '.Str::random(4), 'status' => $status, 'perumahan_id' => $this->perumahan->id,
            'latitude' => -8.1, 'longitude' => 115.1, ...$data,
        ]);
    }

    private function periode(int $tahun, int $dudukan = 200000, int $penanjung = 1000000): PeriodeIuran
    {
        return PeriodeIuran::create(['tahun' => $tahun, 'jumlah_dudukan' => $dudukan, 'jumlah_penanjung_batu' => $penanjung]);
    }

    private function generate(PeriodeIuran $periode): void
    {
        $this->actingAs($this->admin)->postJson("/api/periode/{$periode->id}/generate")->assertOk();
    }

    private function bayar(Krama $krama, array $item, string $metode = 'tunai', ?string $uuid = null)
    {
        return $this->actingAs($this->petugas)->postJson("/api/krama/{$krama->id}/bayar", [
            'transaksi_uuid' => $uuid ?? (string) Str::uuid(),
            'metode' => $metode,
            'item' => collect($item)->map(fn ($nominal, $id) => ['tagihan_id' => $id, 'nominal_bayar' => $nominal])->values()->all(),
        ]);
    }

    private function dudukan(Krama $krama, PeriodeIuran $periode): Tagihan
    {
        return Tagihan::where(['krama_id' => $krama->id, 'periode_iuran_id' => $periode->id, 'jenis' => 'dudukan'])->firstOrFail();
    }

    private function penanjung(Krama $krama, PeriodeIuran $periode): ?Tagihan
    {
        return Tagihan::where(['krama_id' => $krama->id, 'periode_iuran_id' => $periode->id, 'jenis' => 'penanjung_batu'])->first();
    }

    public function test_generate_sesuai_status_dan_aman_diulang(): void
    {
        $tamiu = $this->krama('tamiu');
        $kramaTamiu = $this->krama('krama_tamiu');
        $pengampel = $this->krama('pengampel');
        $pemirak = $this->krama('pemirak');
        $nonaktif = $this->krama('tamiu', ['aktif' => false]);
        $periode = $this->periode(2027, 250000);

        $this->actingAs($this->admin)->getJson("/api/periode/{$periode->id}/pratinjau")
            ->assertJson(['krama' => 4, 'dudukan' => 4, 'penanjung_batu' => 2, 'total_dudukan' => 1000000, 'total_penanjung_batu' => 2000000]);
        $this->assertSame(0, Tagihan::count());

        $this->generate($periode);
        $this->generate($periode);

        $this->assertSame(6, Tagihan::count());
        $this->assertEquals(250000, $this->dudukan($pemirak, $periode)->nominal);
        $this->assertNotNull($this->penanjung($tamiu, $periode));
        $this->assertNotNull($this->penanjung($kramaTamiu, $periode));
        $this->assertNull($this->penanjung($pengampel, $periode));
        $this->assertNull($this->penanjung($pemirak, $periode));
        $this->assertSame(0, $nonaktif->tagihan()->count());

        $baru = $this->krama();
        $this->generate($periode);
        $this->assertSame(2, $baru->tagihan()->count());
        $this->assertNotNull($periode->fresh()->generated_at);
    }

    public function test_penanjung_batu_belum_ditagih_sebelum_2027_dan_hanya_sekali(): void
    {
        $krama = $this->krama();
        $this->generate($periode2026 = $this->periode(2026));
        $this->assertNull($this->penanjung($krama, $periode2026));

        $this->generate($periode2027 = $this->periode(2027));
        $this->assertNotNull($this->penanjung($krama, $periode2027));

        $this->generate($periode2028 = $this->periode(2028));
        $this->assertNull($this->penanjung($krama, $periode2028));
        $this->assertSame(1, $krama->tagihan()->where('jenis', 'penanjung_batu')->count());
    }

    public function test_kurang_bayar_pindah_ke_tagihan_tahun_berikutnya(): void
    {
        $krama = $this->krama('pengampel');
        $this->generate($periode2026 = $this->periode(2026));
        $baris2026 = $this->dudukan($krama, $periode2026);

        $this->bayar($krama, [$baris2026->id => 125000])->assertCreated();
        $this->assertSame(75000, $krama->tunggakan());

        $this->generate($periode2027 = $this->periode(2027));
        $baris2027 = $this->dudukan($krama, $periode2027);

        $this->assertEquals(75000, $baris2027->kekurangan_sebelumnya);
        $this->assertEquals(275000, $baris2027->total_tagihan);
        $this->assertSame($baris2027->id, $baris2026->fresh()->sisa_dipindah_ke_id);
        $this->assertSame(275000, $krama->fresh()->tunggakan());

        $this->generate($periode2027);
        $this->assertEquals(75000, $baris2027->fresh()->kekurangan_sebelumnya);
    }

    public function test_tahun_tidak_dibayar_tetap_menjadi_baris_sendiri_dan_bisa_dipilih(): void
    {
        $krama = $this->krama('pengampel');
        $this->generate($periode2026 = $this->periode(2026));
        $this->generate($periode2027 = $this->periode(2027));

        $baris2026 = $this->dudukan($krama, $periode2026);
        $baris2027 = $this->dudukan($krama, $periode2027);

        $this->assertEquals(0, $baris2027->kekurangan_sebelumnya);
        $this->assertSame(400000, $krama->tunggakan());

        $this->bayar($krama, [$baris2027->id => 200000])->assertCreated()->assertJsonPath('tunggakan', 200000);
        $this->assertSame(StatusTagihan::Dibuat, $baris2026->fresh()->status);

        $this->bayar($krama, [$baris2026->id => 200000])->assertCreated()->assertJsonPath('tunggakan', 0);
    }

    public function test_bayar_dua_baris_sekaligus_menghasilkan_dua_catatan_satu_struk(): void
    {
        $krama = $this->krama();
        $this->generate($periode = $this->periode(2027));

        $respons = $this->bayar($krama, [
            $this->dudukan($krama, $periode)->id => 200000,
            $this->penanjung($krama, $periode)->id => 1000000,
        ])->assertCreated()->assertJsonCount(2, 'tagihan');

        $noStruk = $respons->json('no_struk');
        $this->assertSame(2, Tagihan::where('no_struk', $noStruk)->count());
    }

    public function test_tunai_dibayar_transfer_pending_dan_pending_tidak_mengurangi_tunggakan(): void
    {
        $tunai = $this->krama('pengampel');
        $transfer = $this->krama('pengampel');
        $this->generate($periode = $this->periode(2026));

        $this->bayar($tunai, [$this->dudukan($tunai, $periode)->id => 200000])->assertJsonPath('tunggakan', 0);
        $this->assertSame(StatusTagihan::Dibayar, $this->dudukan($tunai, $periode)->status);
        $this->assertSame($this->petugas->id, $this->dudukan($tunai, $periode)->petugas_id);
        $this->assertSame(now()->toDateString(), $this->dudukan($tunai, $periode)->tanggal_bayar->toDateString());

        $bayarTransfer = $this->dudukan($transfer, $periode);
        $this->bayar($transfer, [$bayarTransfer->id => 200000], 'transfer')->assertJsonPath('tunggakan', 200000);
        $this->assertSame(StatusTagihan::Pending, $bayarTransfer->fresh()->status);

        $this->actingAs($this->admin)->postJson('/api/tagihan/validasi', ['ids' => [$bayarTransfer->id]])->assertJsonPath('jumlah', 1);
        $this->assertSame(StatusTagihan::Valid, $bayarTransfer->fresh()->status);
        $this->assertSame(0, $transfer->tunggakan());
    }

    public function test_pembayaran_terkunci_tanpa_lokasi_atau_perumahan(): void
    {
        $tanpaLokasi = $this->krama('pengampel', ['latitude' => null, 'longitude' => null]);
        $tanpaPerumahan = $this->krama('pengampel', ['perumahan_id' => null]);
        $this->generate($periode = $this->periode(2026));

        $this->bayar($tanpaLokasi, [$this->dudukan($tanpaLokasi, $periode)->id => 200000])
            ->assertUnprocessable()->assertJsonValidationErrors('lokasi');
        $this->bayar($tanpaPerumahan, [$this->dudukan($tanpaPerumahan, $periode)->id => 200000])
            ->assertUnprocessable()->assertJsonValidationErrors('lokasi');

        $this->actingAs($this->petugas)->putJson("/api/krama/{$tanpaLokasi->id}/lokasi", ['latitude' => -8.1, 'longitude' => 115.1])
            ->assertUnprocessable()->assertJsonValidationErrors('perumahan_id');

        $this->actingAs($this->petugas)->putJson("/api/krama/{$tanpaLokasi->id}/lokasi", [
            'latitude' => -8.1, 'longitude' => 115.1, 'perumahan_id' => $this->perumahan->id,
        ])->assertOk()->assertJsonPath('data.lokasi_lengkap', true);

        $this->assertSame($this->petugas->id, $tanpaLokasi->fresh()->lokasi_ditandai_oleh);
        $this->bayar($tanpaLokasi->fresh(), [$this->dudukan($tanpaLokasi, $periode)->id => 200000])->assertCreated();
    }

    public function test_aturan_nominal_kepemilikan_dan_pembayaran_ganda(): void
    {
        $krama = $this->krama('pengampel');
        $lain = $this->krama('pengampel');
        $this->generate($periode = $this->periode(2026));
        $baris = $this->dudukan($krama, $periode);

        $this->bayar($krama, [$baris->id => 200001])->assertUnprocessable();
        $this->bayar($krama, [$baris->id => 0])->assertUnprocessable();
        $this->bayar($krama, [$this->dudukan($lain, $periode)->id => 200000])->assertUnprocessable();

        $uuid = (string) Str::uuid();
        $pertama = $this->bayar($krama, [$baris->id => 200000], 'tunai', $uuid)->assertCreated();
        $ulang = $this->bayar($krama, [$baris->id => 200000], 'tunai', $uuid)->assertCreated();
        $this->assertSame($pertama->json('no_struk'), $ulang->json('no_struk'));

        $this->bayar($krama, [$baris->id => 200000])->assertUnprocessable();
    }

    public function test_penanjung_batu_lunas_tunai_langsung_mengubah_status_menjadi_pengampel(): void
    {
        $krama = $this->krama('tamiu');
        $this->generate($periode = $this->periode(2027));

        $this->bayar($krama, [$this->penanjung($krama, $periode)->id => 1000000])
            ->assertCreated()
            ->assertJsonPath('status_krama_baru', 'Pengampel');

        $this->assertSame(StatusKrama::Pengampel, $krama->fresh()->status);
        $this->assertSame(StatusTagihan::Dibayar, $this->penanjung($krama, $periode)->status);
    }

    public function test_transfer_pending_baru_mengubah_status_setelah_valid(): void
    {
        $krama = $this->krama('krama_tamiu');
        $this->generate($periode = $this->periode(2027));
        $baris = $this->penanjung($krama, $periode);

        $this->bayar($krama, [$baris->id => 1000000], 'transfer')->assertJsonPath('status_krama_baru', null);
        $this->assertSame(StatusKrama::KramaTamiu, $krama->fresh()->status);

        $this->actingAs($this->admin)->postJson('/api/tagihan/validasi', ['ids' => [$baris->id]])->assertOk();
        $this->assertSame(StatusKrama::Pengampel, $krama->fresh()->status);
    }

    public function test_penanjung_batu_kurang_bayar_baru_mengubah_status_setelah_lunas(): void
    {
        $krama = $this->krama('tamiu');
        $this->generate($periode2027 = $this->periode(2027));
        $baris = $this->penanjung($krama, $periode2027);

        $this->bayar($krama, [$baris->id => 600000])->assertJsonPath('status_krama_baru', null);
        $this->assertSame(StatusKrama::Tamiu, $krama->fresh()->status);

        $this->generate($periode2028 = $this->periode(2028));
        $sisa = $this->penanjung($krama, $periode2028);
        $this->assertEquals(400000, $sisa->nominal);
        $this->assertSame($sisa->id, $baris->fresh()->sisa_dipindah_ke_id);

        $this->bayar($krama, [$sisa->id => 400000])->assertJsonPath('status_krama_baru', 'Pengampel');
        $this->assertSame(StatusKrama::Pengampel, $krama->fresh()->status);
    }

    public function test_koreksi_penanjung_batu_memulihkan_status_awal(): void
    {
        $krama = $this->krama('krama_tamiu');
        $this->generate($periode = $this->periode(2027));
        $baris = $this->penanjung($krama, $periode);

        $this->bayar($krama, [$baris->id => 1000000])->assertCreated();
        $this->assertSame(StatusKrama::Pengampel, $krama->fresh()->status);

        $this->actingAs($this->admin)->postJson("/api/tagihan/{$baris->id}/koreksi", ['alasan' => 'Salah input'])->assertOk();

        $krama->refresh();
        $this->assertSame(StatusKrama::KramaTamiu, $krama->status);
        $this->assertNull($krama->status_sebelum_pengampel);
    }

    public function test_koreksi_dudukan_dan_pengampel_lama_tidak_mengubah_status(): void
    {
        $krama = $this->krama('pengampel');
        $this->generate($periode = $this->periode(2027));
        $baris = $this->dudukan($krama, $periode);

        $this->bayar($krama, [$baris->id => 200000])->assertJsonPath('status_krama_baru', null);
        $this->actingAs($this->admin)->postJson("/api/tagihan/{$baris->id}/koreksi", ['alasan' => 'Salah input'])->assertOk();

        $this->assertSame(StatusKrama::Pengampel, $krama->fresh()->status);
    }

    public function test_koreksi_dan_dispensasi(): void
    {
        $krama = $this->krama('pengampel');
        $this->generate($periode = $this->periode(2026));
        $baris = $this->dudukan($krama, $periode);

        $this->actingAs($this->petugas)->postJson("/api/tagihan/{$baris->id}/dispensasi", ['alasan' => 'x'])->assertForbidden();

        $this->bayar($krama, [$baris->id => 200000])->assertCreated();
        $this->actingAs($this->admin)->postJson("/api/tagihan/{$baris->id}/dispensasi", ['alasan' => 'x'])->assertUnprocessable();
        $this->actingAs($this->admin)->postJson("/api/tagihan/{$baris->id}/koreksi", [])->assertUnprocessable();
        $this->actingAs($this->admin)->postJson("/api/tagihan/{$baris->id}/koreksi", ['alasan' => 'Salah krama'])->assertOk();

        $baris->refresh();
        $this->assertSame(StatusTagihan::Dibuat, $baris->status);
        $this->assertNull($baris->nominal_bayar);
        $this->assertSame('Koreksi: Salah krama', $baris->keterangan);
        $this->assertSame(200000, $krama->tunggakan());

        $this->actingAs($this->admin)->postJson("/api/tagihan/{$baris->id}/dispensasi", ['alasan' => 'Luput'])->assertOk();
        $this->assertSame(StatusTagihan::Dispensasi, $baris->fresh()->status);
        $this->assertSame(0, $krama->tunggakan());

        $this->generate($periode2027 = $this->periode(2027));
        $this->assertEquals(0, $this->dudukan($krama, $periode2027)->kekurangan_sebelumnya);
    }

    public function test_daftar_tunggakan_dan_tagihan_krama(): void
    {
        $besar = $this->krama('tamiu', ['nama' => 'Besar']);
        $kecil = $this->krama('pengampel', ['nama' => 'Kecil']);
        $lunas = $this->krama('pengampel', ['nama' => 'Lunas']);
        $this->generate($periode = $this->periode(2027));
        $this->bayar($lunas, [$this->dudukan($lunas, $periode)->id => 200000])->assertCreated();

        $this->actingAs($this->petugas)->getJson('/api/tunggakan')
            ->assertOk()
            ->assertJsonPath('total', 1400000)
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.nama', 'Besar')
            ->assertJsonPath('data.0.tunggakan', 1200000)
            ->assertJsonPath('data.1.nama', 'Kecil');

        $this->actingAs($this->petugas)->getJson('/api/tunggakan?q=kecil')->assertJsonCount(1, 'data')->assertJsonPath('total', 200000);

        $this->actingAs($this->petugas)->getJson("/api/krama/{$besar->id}/tagihan")
            ->assertOk()->assertJsonPath('tunggakan', 1200000)->assertJsonCount(2, 'tagihan');

        $this->actingAs($this->petugas)->getJson('/api/tagihan?status=dibayar')->assertOk()->assertJsonCount(1, 'data');
        $this->actingAs($this->admin)->getJson('/api/tagihan?status=dibayar')->assertJsonCount(1, 'data');
        $this->actingAs($this->admin)->getJson('/api/periode')->assertJsonPath('data.0.tahun', 2027);
    }

    public function test_kelola_pengguna(): void
    {
        $this->actingAs($this->petugas)->getJson('/api/users')->assertForbidden();

        $id = $this->actingAs($this->admin)->postJson('/api/users', [
            'name' => 'Petugas Baru', 'email' => 'baru@contoh.test', 'password' => 'rahasia123', 'role' => 'petugas',
        ])->assertCreated()->assertJsonMissingPath('password')->json('id');

        $this->actingAs($this->admin)->postJson('/api/users', ['name' => 'X', 'email' => 'baru@contoh.test', 'password' => 'pendek', 'role' => 'petugas'])
            ->assertUnprocessable()->assertJsonValidationErrors(['email', 'password']);

        $sandi = User::find($id)->password;
        $this->actingAs($this->admin)->putJson("/api/users/{$id}", ['name' => 'Ganti', 'email' => 'baru@contoh.test', 'password' => '', 'role' => 'petugas', 'aktif' => false])->assertOk();
        $this->assertSame($sandi, User::find($id)->password);
        $this->assertFalse(User::find($id)->aktif);

        $this->actingAs($this->admin)->putJson("/api/users/{$this->admin->id}", ['name' => 'A', 'email' => $this->admin->email, 'role' => 'admin', 'aktif' => false])->assertUnprocessable();
        $this->actingAs($this->admin)->putJson("/api/users/{$this->admin->id}", ['name' => 'A', 'email' => $this->admin->email, 'role' => 'petugas'])->assertUnprocessable();
    }

    public function test_membuka_pembayaran_membuat_tagihan_periode_aktif_dan_aman_diulang(): void
    {
        $this->travelTo('2026-11-03');
        $this->periode(2027);
        $periode = $this->periode(2026, 250000);
        $krama = $this->krama('pengampel');

        $this->actingAs($this->petugas)->postJson("/api/krama/{$krama->id}/tagihan/siapkan")
            ->assertOk()
            ->assertJsonPath('periode_aktif', 2026)
            ->assertJsonPath('tagihan_dibuat', ['Dudukan'])
            ->assertJsonCount(1, 'tagihan')
            ->assertJsonPath('tunggakan', 250000);

        $this->actingAs($this->petugas)->postJson("/api/krama/{$krama->id}/tagihan/siapkan")
            ->assertJsonPath('tagihan_dibuat', [])
            ->assertJsonCount(1, 'tagihan');

        $this->assertEquals(250000, $this->dudukan($krama, $periode)->nominal);
        $this->bayar($krama, [$this->dudukan($krama, $periode)->id => 250000])->assertCreated();
    }

    public function test_tagihan_otomatis_mengikuti_aturan_generate(): void
    {
        $this->travelTo('2027-02-01');
        $periode2026 = $this->periode(2026);
        $periode2027 = $this->periode(2027);

        $tamiu = $this->krama('tamiu');
        $this->actingAs($this->petugas)->postJson("/api/krama/{$tamiu->id}/tagihan/siapkan")
            ->assertJsonPath('tagihan_dibuat', ['Dudukan', 'Penanjung Batu']);

        $pemirak = $this->krama('pemirak');
        $this->actingAs($this->petugas)->postJson("/api/krama/{$pemirak->id}/tagihan/siapkan")
            ->assertJsonPath('tagihan_dibuat', ['Dudukan']);

        $kurang = $this->krama('pengampel');
        $lama = Tagihan::create([
            'krama_id' => $kurang->id, 'periode_iuran_id' => $periode2026->id, 'jenis' => 'dudukan', 'nominal' => 200000,
            'nominal_bayar' => 125000, 'status' => 'valid',
        ]);

        $this->actingAs($this->petugas)->postJson("/api/krama/{$kurang->id}/tagihan/siapkan")->assertOk();
        $baru = $this->dudukan($kurang, $periode2027);
        $this->assertEquals(275000, $baru->total_tagihan);
        $this->assertSame($baru->id, $lama->fresh()->sisa_dipindah_ke_id);
    }

    public function test_tanpa_tagihan_baru_jika_belum_ada_periode_atau_krama_nonaktif(): void
    {
        $krama = $this->krama('pengampel');

        $this->actingAs($this->petugas)->postJson("/api/krama/{$krama->id}/tagihan/siapkan")
            ->assertOk()->assertJsonPath('periode_aktif', null)->assertJsonPath('tagihan_dibuat', []);
        $this->assertSame(0, PeriodeIuran::count());

        $this->periode(2026);
        $nonaktif = $this->krama('pengampel', ['aktif' => false]);
        $this->actingAs($this->petugas)->postJson("/api/krama/{$nonaktif->id}/tagihan/siapkan")->assertJsonPath('tagihan_dibuat', []);
        $this->assertSame(0, Tagihan::count());
    }

    public function test_krama_pending_tetap_mendapat_tagihan_dan_bisa_dilengkapi(): void
    {
        $this->travelTo('2026-11-03');
        $this->periode(2026);
        $krama = $this->krama('pengampel', ['asal' => null, 'nik' => null]);
        $this->assertSame('pending', $krama->fresh()->status_data->value);

        $this->actingAs($this->petugas)->putJson("/api/krama/{$krama->id}", [
            'nama' => $krama->nama, 'nik' => '5108010101900001', 'asal' => 'Bali', 'perumahan_id' => $this->perumahan->id,
        ])->assertOk()->assertJsonPath('data.status_data', 'lengkap');

        $this->actingAs($this->petugas)->postJson("/api/krama/{$krama->id}/tagihan/siapkan")
            ->assertJsonPath('krama.status_data', 'lengkap')
            ->assertJsonPath('tagihan_dibuat', ['Dudukan']);
    }

    public function test_petugas_melihat_tagihan_tetapi_tidak_bisa_validasi_koreksi_atau_dispensasi(): void
    {
        $krama = $this->krama('pengampel');
        $this->generate($periode = $this->periode(2026));
        $baris = $this->dudukan($krama, $periode);

        $this->actingAs($this->petugas)->getJson('/api/tagihan')->assertOk()->assertJsonCount(1, 'data');
        $this->bayar($krama, [$baris->id => 200000])->assertCreated();

        $this->actingAs($this->petugas)->postJson('/api/tagihan/validasi', ['ids' => [$baris->id]])->assertForbidden();
        $this->actingAs($this->petugas)->postJson("/api/tagihan/{$baris->id}/koreksi", ['alasan' => 'x'])->assertForbidden();
        $this->actingAs($this->petugas)->postJson("/api/tagihan/{$baris->id}/dispensasi", ['alasan' => 'x'])->assertForbidden();

        $this->assertSame(StatusTagihan::Dibayar, $baris->fresh()->status);
    }

    public function test_krama_baru_langsung_punya_tagihan_periode_aktif_dan_bisa_dibayar_setelah_tagging(): void
    {
        $this->travelTo('2026-11-03');
        $periode = $this->periode(2027);

        $id = $this->actingAs($this->petugas)->postJson('/api/krama', ['nama' => 'Baru', 'perumahan_id' => $this->perumahan->id])
            ->assertCreated()->json('data.id');

        $krama = Krama::findOrFail($id);
        $dudukan = $this->dudukan($krama, $periode);
        $this->assertEquals(200000, $dudukan->nominal);
        $this->assertNotNull($this->penanjung($krama, $periode));

        $this->bayar($krama, [$dudukan->id => 200000])->assertUnprocessable()->assertJsonValidationErrors('lokasi');

        $this->actingAs($this->petugas)->putJson("/api/krama/{$id}/lokasi", [
            'latitude' => -8.1, 'longitude' => 115.1, 'perumahan_id' => $this->perumahan->id,
        ])->assertOk();

        $this->bayar($krama->fresh(), [$dudukan->id => 200000])->assertCreated();
        $this->actingAs($this->petugas)->postJson("/api/krama/{$id}/tagihan/siapkan")->assertJsonPath('tagihan_dibuat', []);
    }

    public function test_riwayat_krama_memuat_nomor_struk_dan_nama_petugas(): void
    {
        $krama = $this->krama('tamiu');
        $this->generate($periode = $this->periode(2027));
        $noStruk = $this->bayar($krama, [$this->dudukan($krama, $periode)->id => 200000])->json('no_struk');

        $riwayat = collect($this->actingAs($this->petugas)->getJson("/api/krama/{$krama->id}/tagihan")->assertOk()->json('tagihan'))
            ->firstWhere('no_struk', $noStruk);

        $this->assertSame($this->petugas->name, $riwayat['petugas']);
        $this->assertSame(2027, $riwayat['tahun']);
    }

    public function test_daftar_tagihan_satu_baris_per_pembayaran(): void
    {
        $krama = $this->krama('tamiu', ['nama' => 'Dua Tagihan']);
        $lain = $this->krama('pengampel', ['nama' => 'Terpisah']);
        $this->generate($periode = $this->periode(2027));

        $noStruk = $this->bayar($krama, [
            $this->dudukan($krama, $periode)->id => 200000,
            $this->penanjung($krama, $periode)->id => 1000000,
        ])->json('no_struk');
        $this->bayar($lain, [$this->dudukan($lain, $periode)->id => 150000])->assertCreated();

        $respons = $this->actingAs($this->admin)->getJson('/api/tagihan?status=dibayar')->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.total', 2);

        $pembayaran = collect($respons->json('data'))->firstWhere('no_struk', $noStruk);
        $this->assertSame('Dua Tagihan', $pembayaran['krama']);
        $this->assertSame($this->petugas->name, $pembayaran['petugas']);
        $this->assertSame(['dudukan', 'penanjung_batu'], array_column($pembayaran['item'], 'jenis'));
        $this->assertSame(2027, $pembayaran['item'][0]['tahun']);

        // Filter jenis tetap menampilkan seluruh rincian pembayaran yang cocok.
        $this->actingAs($this->admin)->getJson('/api/tagihan?jenis=penanjung_batu&q=Dua')
            ->assertJsonCount(1, 'data')
            ->assertJsonCount(2, 'data.0.item');
    }

    public function test_tagihan_belum_dibayar_tetap_baris_sendiri_dan_halaman_tidak_memecah_pembayaran(): void
    {
        $krama = $this->krama('tamiu');
        $this->generate($periode = $this->periode(2027));

        $this->actingAs($this->admin)->getJson('/api/tagihan')->assertJsonCount(2, 'data')->assertJsonCount(1, 'data.0.item');

        $this->bayar($krama, [$this->dudukan($krama, $periode)->id => 200000, $this->penanjung($krama, $periode)->id => 1000000])->assertCreated();
        $this->actingAs($this->admin)->getJson('/api/tagihan')->assertJsonCount(1, 'data')->assertJsonPath('meta.total', 1);
    }

    public function test_filter_tunggakan_berdasarkan_jenis_dan_tahun(): void
    {
        $tamiu = $this->krama('tamiu', ['nama' => 'Tamiu']);
        $this->krama('pengampel', ['nama' => 'Pengampel']);
        $this->generate($this->periode(2026));
        $this->generate($this->periode(2027));

        $this->actingAs($this->petugas)->getJson('/api/tunggakan')
            ->assertJsonPath('total', 1800000)->assertJsonCount(2, 'data');

        $this->actingAs($this->petugas)->getJson('/api/tunggakan?jenis=penanjung_batu')
            ->assertJsonPath('total', 1000000)->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $tamiu->id)->assertJsonPath('data.0.tunggakan', 1000000);

        $this->actingAs($this->petugas)->getJson('/api/tunggakan?jenis=dudukan')
            ->assertJsonPath('total', 800000)->assertJsonPath('data.0.tunggakan', 400000);

        $this->actingAs($this->petugas)->getJson('/api/tunggakan?tahun=2026')
            ->assertJsonPath('total', 400000)->assertJsonPath('data.0.tunggakan', 200000);

        $this->actingAs($this->petugas)->getJson('/api/tunggakan?tahun=2026&jenis=penanjung_batu')
            ->assertJsonPath('total', 0)->assertJsonCount(0, 'data');

        $this->actingAs($this->petugas)->getJson('/api/tunggakan?jenis=lain')
            ->assertUnprocessable()->assertJsonValidationErrors('jenis');

    }

    public function test_filter_tagihan_berdasarkan_tanggal_bayar_dan_tahun(): void
    {
        $krama = $this->krama('tamiu');
        $this->generate($periode2026 = $this->periode(2026));
        $this->generate($periode2027 = $this->periode(2027));

        $this->travelTo('2026-03-01');
        $noMaret = $this->bayar($krama, [$this->dudukan($krama, $periode2026)->id => 200000])->json('no_struk');

        $this->travelTo('2026-05-15');
        $noMei = $this->bayar($krama, [
            $this->dudukan($krama, $periode2027)->id => 200000,
            $this->penanjung($krama, $periode2027)->id => 1000000,
        ])->json('no_struk');

        $this->actingAs($this->petugas)->getJson('/api/tagihan?tanggal_dari=2026-03-01&tanggal_sampai=2026-03-01')
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.no_struk', $noMaret);

        $this->actingAs($this->petugas)->getJson('/api/tagihan?tanggal_dari=2026-03-02')
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.no_struk', $noMei)->assertJsonCount(2, 'data.0.item');

        $this->actingAs($this->petugas)->getJson('/api/tagihan?tanggal_sampai=2026-05-15')->assertJsonCount(2, 'data');

        $this->actingAs($this->petugas)->getJson('/api/tagihan?tanggal_dari=2026-06-01')->assertJsonCount(0, 'data');

        $this->actingAs($this->petugas)->getJson('/api/tagihan?tahun=2026')
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.no_struk', $noMaret);

        $this->actingAs($this->petugas)->getJson('/api/tagihan?tanggal_dari=2026-05-01&tanggal_sampai=2026-03-01')
            ->assertUnprocessable()->assertJsonValidationErrors('tanggal_sampai');
        $this->actingAs($this->petugas)->getJson('/api/tagihan?tanggal_dari=bukan')
            ->assertUnprocessable()->assertJsonValidationErrors('tanggal_dari');
    }

    public function test_filter_tagihan_berdasarkan_petugas(): void
    {
        $kramaSatu = $this->krama('pengampel');
        $kramaDua = $this->krama('pengampel');
        $this->generate($periode = $this->periode(2026));

        $noPetugas = $this->bayar($kramaSatu, [$this->dudukan($kramaSatu, $periode)->id => 200000])->json('no_struk');
        $noAdmin = $this->actingAs($this->admin)->postJson("/api/krama/{$kramaDua->id}/bayar", [
            'transaksi_uuid' => (string) Str::uuid(),
            'metode' => 'tunai',
            'item' => [['tagihan_id' => $this->dudukan($kramaDua, $periode)->id, 'nominal_bayar' => 200000]],
        ])->json('no_struk');

        $this->actingAs($this->petugas)->getJson("/api/tagihan?petugas_id={$this->petugas->id}")
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.no_struk', $noPetugas);
        $this->actingAs($this->petugas)->getJson("/api/tagihan?petugas_id={$this->admin->id}")
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.no_struk', $noAdmin);
        $this->actingAs($this->petugas)->getJson('/api/tagihan?petugas_id=abc')
            ->assertUnprocessable()->assertJsonValidationErrors('petugas_id');
    }

    public function test_pilihan_petugas_hanya_berisi_nama_dan_butuh_login(): void
    {
        $this->getJson('/api/pilihan-petugas')->assertUnauthorized();

        $respons = $this->actingAs($this->petugas)->getJson('/api/pilihan-petugas')->assertOk()->assertJsonCount(2);

        $this->assertSame(['id', 'name'], array_keys($respons->json('0')));
    }

    public function test_daftar_tahun_periode_bisa_dibaca_petugas(): void
    {
        $this->periode(2026);
        $this->periode(2027);

        $this->getJson('/api/periode-tahun')->assertUnauthorized();
        $this->actingAs($this->petugas)->getJson('/api/periode-tahun')->assertOk()->assertExactJson([2027, 2026]);
    }

    public function test_krama_baru_tanpa_periode_tetap_tersimpan(): void
    {
        $this->actingAs($this->petugas)->postJson('/api/krama', ['nama' => 'Baru', 'perumahan_id' => $this->perumahan->id])->assertCreated();

        $this->assertSame(1, Krama::count());
        $this->assertSame(0, Tagihan::count());
    }

    public function test_jenis_tagihan_punya_label(): void
    {
        $this->assertSame('Penanjung Batu', JenisTagihan::PenanjungBatu->label());
    }
}
