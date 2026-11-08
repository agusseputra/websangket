<?php

namespace Tests\Feature;

use App\Enums\StatusKrama;
use App\Models\Krama;
use App\Models\User;
use Database\Seeders\PerumahanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ImporKramaTest extends TestCase
{
    use RefreshDatabase;

    private const JUDUL = ['No', 'Cacakan', 'Nama', 'Status', 'NIK', 'ASal', 'Perumahan', 'Nomor', 'No HP', 'Keterangan', 'Jumlah', 'Penanjung Batu', null];

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->seed(PerumahanSeeder::class);
    }

    private function fileExcel(array $baris, array $judul = self::JUDUL, string $namaSheet = 'ALL'): UploadedFile
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet()->setTitle($namaSheet);
        $sheet->fromArray([['Judul laporan'], [null], $judul, ...$baris], null, 'A1');

        $path = tempnam(sys_get_temp_dir(), 'impor').'.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        return new UploadedFile($path, 'data.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    private function barisContoh(): array
    {
        return [
            [1, 'A', 'Lengkap Satu', 'Pengampel', '5108010101900001', 'Bali', 'RT 2', 'B1', '0812', 'Swasta', 200000, null, null],
            [2, 'A', 'Tanpa Nik', 'Pemirak', null, 'Bali', 'RT 3', 3.0, null, 'Luput', null, null, null],
            [3, 'A', 'Nik Pendek', 'Tamiu', '510801', 'Bali', 'Bukti Awangga', null, null, null, null, null, null],
            [4, 'A', 'Status Kosong', null, '5108010101900004', 'Bali', ' Griya Adi Jaya ', 'A8', null, null, null, null, null],
            [5, 'A', 'Default Krisna', 'Krama Tamiu', '5108010101900005', 'Bali', 'Krisna Graha', 'C8', null, null, null, null, 'sudah ditarik'],
            [6, 'A', 'Tidak Cocok', 'Krama Tamiu', '5108010101900006', 'Bali', 'Gudang', null, null, null, null, null, null],
            [7, 'A', 'Tanpa Asal', 'Krama Tamiu', '5108010101900007', null, null, null, null, null, null, null, null],
            [null, null, null, null, null, null, null, null, null, null, 53125000, 4500000, null],
        ];
    }

    public function test_pratinjau_tidak_menulis_database_dan_laporannya_benar(): void
    {
        $respons = $this->actingAs(User::factory()->admin()->create())
            ->post('/api/impor-krama/pratinjau', ['file' => $this->fileExcel($this->barisContoh())], ['Accept' => 'application/json']);

        $respons->assertOk()
            ->assertJsonPath('ringkasan.total', 7)
            ->assertJsonPath('ringkasan.lengkap', 3)
            ->assertJsonPath('ringkasan.pending', 4)
            ->assertJsonPath('ringkasan.nik_kosong', 1)
            ->assertJsonPath('ringkasan.nik_tidak_valid', 1)
            ->assertJsonPath('ringkasan.asal_kosong', 1)
            ->assertJsonPath('ringkasan.perumahan_kosong', 1)
            ->assertJsonPath('ringkasan.perumahan_tidak_cocok', 1)
            ->assertJsonPath('ringkasan.perumahan_dikoreksi', 3)
            ->assertJsonPath('ringkasan.status_kosong', 1);

        $this->assertStringNotContainsString('5108010101900001', $respons->getContent());
        $this->assertSame(0, Krama::count());
    }

    public function test_impor_menyimpan_data_dengan_aturan_pencocokan_lalu_menghapus_file(): void
    {
        $admin = User::factory()->admin()->create();

        $token = $this->actingAs($admin)
            ->post('/api/impor-krama/pratinjau', ['file' => $this->fileExcel($this->barisContoh())], ['Accept' => 'application/json'])
            ->json('token');

        Storage::disk('local')->assertExists("impor/{$token}.xlsx");

        $this->actingAs($admin)->postJson('/api/impor-krama', ['token' => $token])
            ->assertCreated()
            ->assertJson(['total' => 7, 'lengkap' => 3, 'pending' => 4]);

        Storage::disk('local')->assertMissing("impor/{$token}.xlsx");

        $satu = Krama::where('impor_baris', 4)->first();
        $this->assertSame('Swasta', $satu->pekerjaan);
        $this->assertNull($satu->keterangan);
        $this->assertSame(StatusKrama::Pengampel, $satu->status);
        $this->assertSame('5108010101900001', $satu->nik);
        $this->assertSame('RT 2', $satu->perumahan->nama);

        $kedua = Krama::where('impor_baris', 5)->first();
        $this->assertSame('Luput', $kedua->keterangan);
        $this->assertSame('3', $kedua->nomor_rumah);

        $this->assertSame('Bukit Awangga', Krama::where('impor_baris', 6)->first()->perumahan->nama);
        $this->assertSame('510801', Krama::where('impor_baris', 6)->first()->nik);

        $statusKosong = Krama::where('impor_baris', 7)->first();
        $this->assertSame(StatusKrama::KramaTamiu, $statusKosong->status);
        $this->assertSame('Griya Adi Jaya 1', $statusKosong->perumahan->nama);

        $krisna = Krama::where('impor_baris', 8)->first();
        $this->assertSame('Krisna Graha 1', $krisna->perumahan->nama);
        $this->assertSame('sudah ditarik', $krisna->keterangan);

        $gudang = Krama::where('impor_baris', 9)->first();
        $this->assertNull($gudang->perumahan_id);
        $this->assertSame('Perumahan di Excel: Gudang', $gudang->keterangan);
    }

    public function test_impor_hanya_bisa_sekali(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $this->actingAs($admin)
            ->post('/api/impor-krama/pratinjau', ['file' => $this->fileExcel($this->barisContoh())], ['Accept' => 'application/json'])
            ->json('token');

        $this->actingAs($admin)->postJson('/api/impor-krama', ['token' => $token])->assertCreated();
        $this->actingAs($admin)->getJson('/api/impor-krama')->assertJsonPath('sudah_diimpor', true);

        $this->actingAs($admin)
            ->post('/api/impor-krama/pratinjau', ['file' => $this->fileExcel($this->barisContoh())], ['Accept' => 'application/json'])
            ->assertUnprocessable();
    }

    public function test_file_yang_salah_ditolak_dan_tidak_tersimpan(): void
    {
        $admin = User::factory()->admin()->create();
        $header = ['Accept' => 'application/json'];

        $this->actingAs($admin)->post('/api/impor-krama/pratinjau', ['file' => $this->fileExcel([['x']], self::JUDUL, 'Lain')], $header)
            ->assertUnprocessable()->assertJsonValidationErrors('file');

        $this->actingAs($admin)->post('/api/impor-krama/pratinjau', ['file' => $this->fileExcel([['x']], ['No', 'Nama'])], $header)
            ->assertUnprocessable()->assertJsonValidationErrors('file');

        $this->actingAs($admin)->post('/api/impor-krama/pratinjau', ['file' => UploadedFile::fake()->create('data.pdf', 10, 'application/pdf')], $header)
            ->assertUnprocessable();

        $this->assertSame([], Storage::disk('local')->allFiles('impor'));
    }

    public function test_batal_menghapus_file_dan_petugas_ditolak(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $this->actingAs($admin)
            ->post('/api/impor-krama/pratinjau', ['file' => $this->fileExcel($this->barisContoh())], ['Accept' => 'application/json'])
            ->json('token');

        $this->actingAs($admin)->deleteJson('/api/impor-krama')->assertNoContent();
        Storage::disk('local')->assertMissing("impor/{$token}.xlsx");

        $this->actingAs($admin)->postJson('/api/impor-krama', ['token' => $token])->assertUnprocessable();
        $this->actingAs(User::factory()->create())->getJson('/api/impor-krama')->assertForbidden();
    }
}
