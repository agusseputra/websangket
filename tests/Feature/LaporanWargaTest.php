<?php

namespace Tests\Feature;

use App\Enums\StatusLaporan;
use App\Models\LaporanWarga;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class LaporanWargaTest extends TestCase
{
    use RefreshDatabase;

    private const NIK = '5108010101900001';

    private bool $captchaBerhasil = true;

    private bool $googleTerhubung = true;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.recaptcha.secret' => 'rahasia-uji']);

        Http::fake(['www.google.com/recaptcha/*' => function () {
            if (! $this->googleTerhubung) {
                throw new ConnectionException('Tidak terhubung');
            }

            return Http::response(['success' => $this->captchaBerhasil]);
        }]);
    }

    private function dataTinggal(array $ubah = []): array
    {
        return [
            'nama' => 'Warga Baru', 'nik' => self::NIK, 'asal' => 'Denpasar', 'jenis' => 'tinggal',
            'alamat' => 'Griya Jro B1', 'tanggal_mulai' => '2026-11-01', 'menetap' => true, 'captcha' => 'token-uji',
            ...$ubah,
        ];
    }

    public function test_warga_melapor_tinggal_tanpa_login_dan_nik_terenkripsi(): void
    {
        $this->postJson('/api/laporan-warga', $this->dataTinggal())->assertCreated();

        $laporan = LaporanWarga::firstOrFail();
        $this->assertSame(self::NIK, $laporan->nik);
        $this->assertSame(StatusLaporan::Pending, $laporan->status);
        $this->assertTrue($laporan->menetap);
        $this->assertNull($laporan->tanggal_selesai);
        $this->assertStringNotContainsString(self::NIK, DB::table('laporan_warga')->value('nik'));

        Http::assertSent(fn ($permintaan) => $permintaan['secret'] === 'rahasia-uji' && $permintaan['response'] === 'token-uji');
    }

    public function test_laporan_usaha_wajib_nama_dan_jenis_usaha(): void
    {
        $this->postJson('/api/laporan-warga', $this->dataTinggal(['jenis' => 'usaha']))
            ->assertUnprocessable()->assertJsonValidationErrors(['nama_usaha', 'jenis_usaha']);

        $this->postJson('/api/laporan-warga', $this->dataTinggal(['jenis' => 'usaha', 'nama_usaha' => 'Warung Sari', 'jenis_usaha' => 'Warung']))
            ->assertCreated();
        $this->assertSame('Warung Sari', LaporanWarga::firstOrFail()->nama_usaha);
    }

    public function test_laporan_tinggal_mengabaikan_isian_usaha(): void
    {
        $this->postJson('/api/laporan-warga', $this->dataTinggal(['nama_usaha' => 'Abaikan', 'jenis_usaha' => 'Abaikan']))->assertCreated();

        $this->assertNull(LaporanWarga::firstOrFail()->nama_usaha);
    }

    public function test_tanggal_selesai_wajib_jika_tidak_menetap_dan_tidak_boleh_mundur(): void
    {
        $this->postJson('/api/laporan-warga', $this->dataTinggal(['menetap' => false]))
            ->assertUnprocessable()->assertJsonValidationErrors('tanggal_selesai');

        $this->postJson('/api/laporan-warga', $this->dataTinggal(['menetap' => false, 'tanggal_selesai' => '2026-10-01']))
            ->assertUnprocessable()->assertJsonValidationErrors('tanggal_selesai');

        $this->postJson('/api/laporan-warga', $this->dataTinggal(['menetap' => false, 'tanggal_selesai' => '2026-12-01']))->assertCreated();
        $this->assertSame('2026-12-01', LaporanWarga::firstOrFail()->tanggal_selesai->toDateString());

        $this->postJson('/api/laporan-warga', $this->dataTinggal(['menetap' => true, 'tanggal_selesai' => '2026-12-01']))->assertCreated();
        $this->assertNull(LaporanWarga::latest('id')->firstOrFail()->tanggal_selesai);
    }

    public function test_input_wajib_divalidasi_dan_status_tidak_bisa_dikirim_warga(): void
    {
        $this->postJson('/api/laporan-warga', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nama', 'nik', 'asal', 'jenis', 'alamat', 'tanggal_mulai', 'menetap', 'captcha']);

        $this->postJson('/api/laporan-warga', $this->dataTinggal(['nik' => '123']))->assertUnprocessable()->assertJsonValidationErrors('nik');
        $this->postJson('/api/laporan-warga', $this->dataTinggal(['jenis' => 'lain']))->assertUnprocessable()->assertJsonValidationErrors('jenis');

        $this->postJson('/api/laporan-warga', $this->dataTinggal(['status' => 'ditindak']))->assertCreated();
        $this->assertSame(StatusLaporan::Pending, LaporanWarga::firstOrFail()->status);
    }

    public function test_captcha_ditolak_jika_gagal_atau_google_tidak_bisa_dihubungi(): void
    {
        $this->captchaBerhasil = false;
        $this->postJson('/api/laporan-warga', $this->dataTinggal())->assertUnprocessable()->assertJsonValidationErrors('captcha');

        $this->googleTerhubung = false;
        $this->postJson('/api/laporan-warga', $this->dataTinggal())->assertUnprocessable()->assertJsonValidationErrors('captcha');

        $this->assertSame(0, LaporanWarga::count());
    }

    public function test_tanpa_kunci_captcha_produksi_menolak_dan_lokal_mengizinkan(): void
    {
        config(['services.recaptcha.secret' => null]);

        $this->app['env'] = 'production';
        $this->postJson('/api/laporan-warga', $this->dataTinggal())->assertUnprocessable()->assertJsonValidationErrors('captcha');
        $this->assertSame(0, LaporanWarga::count());

        $this->app['env'] = 'local';
        $this->postJson('/api/laporan-warga', $this->dataTinggal(['captcha' => null]))->assertCreated();
    }

    public function test_pengiriman_laporan_dibatasi_per_menit(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/laporan-warga', $this->dataTinggal())->assertCreated();
        }

        $this->postJson('/api/laporan-warga', $this->dataTinggal())->assertTooManyRequests();
    }

    public function test_admin_melihat_filter_dan_menindak_laporan(): void
    {
        $admin = User::factory()->admin()->create();
        $tinggal = LaporanWarga::factory()->create(['nama' => 'Warga Tinggal', 'nik' => self::NIK]);
        LaporanWarga::factory()->usaha()->create(['nama' => 'Warga Usaha']);

        $respons = $this->actingAs($admin)->getJson('/api/laporan-warga')->assertOk()->assertJsonCount(2, 'data');
        $this->assertStringNotContainsString(self::NIK, $respons->getContent());

        $this->actingAs($admin)->getJson('/api/laporan-warga?jenis=usaha')->assertJsonCount(1, 'data')->assertJsonPath('data.0.nama', 'Warga Usaha');
        $this->actingAs($admin)->getJson('/api/laporan-warga?q=tinggal')
            ->assertJsonCount(1, 'data')->assertJsonPath('data.0.nik_tersamar', '510801******0001');

        $this->actingAs($admin)->getJson("/api/laporan-warga/{$tinggal->id}/nik")->assertOk()->assertJsonPath('nik', self::NIK);

        $this->actingAs($admin)->putJson("/api/laporan-warga/{$tinggal->id}/status", ['status' => 'ditindak'])
            ->assertOk()->assertJsonPath('data.status', 'ditindak')->assertJsonPath('data.ditindak_oleh', $admin->name);
        $this->assertNotNull($tinggal->fresh()->ditindak_at);
        $this->actingAs($admin)->getJson('/api/laporan-warga?status=pending')->assertJsonCount(1, 'data');

        $this->actingAs($admin)->putJson("/api/laporan-warga/{$tinggal->id}/status", ['status' => 'pending'])->assertOk();
        $this->assertNull($tinggal->fresh()->ditindak_oleh);

        $this->actingAs($admin)->putJson("/api/laporan-warga/{$tinggal->id}/status", [])->assertUnprocessable();
        $this->actingAs($admin)->getJson('/api/admin/ringkasan')->assertJsonPath('laporan_pending', 2);
    }

    public function test_laporan_hanya_untuk_admin_dan_memakai_pagination(): void
    {
        $laporan = LaporanWarga::factory()->count(51)->create()->first();

        $this->getJson('/api/laporan-warga')->assertUnauthorized();

        $petugas = User::factory()->create();
        $this->actingAs($petugas)->getJson('/api/laporan-warga')->assertForbidden();
        $this->actingAs($petugas)->getJson("/api/laporan-warga/{$laporan->id}/nik")->assertForbidden();
        $this->actingAs($petugas)->putJson("/api/laporan-warga/{$laporan->id}/status", ['status' => 'ditindak'])->assertForbidden();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->getJson('/api/laporan-warga')->assertJsonCount(50, 'data')->assertJsonPath('meta.last_page', 2);
        $this->actingAs($admin)->getJson('/api/laporan-warga?page=2')->assertJsonCount(1, 'data');
    }
}
