<?php

namespace Tests\Feature;

use App\Enums\StatusTagihan;
use App\Models\Krama;
use App\Models\PeriodeIuran;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ImporPembayaranTest extends TestCase
{
    use RefreshDatabase;

    private const HEADER = ['Accept' => 'application/json'];

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        $this->admin = User::factory()->admin()->create();
    }

    private function file(): UploadedFile
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet()->setTitle('ALL');
        $sheet->fromArray([
            ['Judul'], [null],
            ['No', 'Nama', 'Keterangan', 'Jumlah', 'Penanjung Batu'],
            [1, 'Bayar Penuh', null, 200000, null],
            [2, 'Bayar Sebagian', null, 125000, 500000],
            [3, 'Teks Rupiah', null, ' Rp 200,000.00 ', '1,000,000'],
            [4, 'Hanya Penanjung', null, null, 1000000],
            [5, 'Luput', 'Luput', null, null],
            [6, 'Transfer', 'tf', null, null],
            [null, null, null, 725000, 2500000],
        ], null, 'A1');

        $path = tempnam(sys_get_temp_dir(), 'bayar').'.xlsx';
        (new Xlsx($spreadsheet))->save($path);

        return new UploadedFile($path, 'data.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    private function kramaImpor(): void
    {
        foreach (['Bayar Penuh', 'Bayar Sebagian', 'Teks Rupiah', 'Hanya Penanjung', 'Luput', 'Transfer'] as $indeks => $nama) {
            Krama::create(['nama' => $nama, 'status' => 'pengampel', 'impor_baris' => $indeks + 4]);
        }
    }

    public function test_impor_sesuai_nominal_excel_dan_tanggal_1_maret_2026(): void
    {
        $this->kramaImpor();

        $pratinjau = $this->actingAs($this->admin)->post('/api/impor-pembayaran/pratinjau', ['file' => $this->file()], self::HEADER)
            ->assertOk()
            ->assertJson([
                'krama' => 6, 'dudukan_bayar' => 3, 'total_dudukan' => 525000, 'dispensasi' => 1, 'dudukan_belum_bayar' => 2,
                'penanjung_bayar' => 3, 'total_penanjung' => 2500000, 'tidak_tertaut' => [], 'nama_berbeda' => [],
            ]);

        $this->assertSame(0, Tagihan::count());

        $this->actingAs($this->admin)->postJson('/api/impor-pembayaran', ['token' => $pratinjau->json('token')])->assertCreated();

        $periode = PeriodeIuran::where('tahun', 2026)->firstOrFail();
        $this->assertEquals(200000, $periode->jumlah_dudukan);
        $this->assertEquals(1000000, $periode->jumlah_penanjung_batu);
        $this->assertSame(9, Tagihan::count());
        $this->assertEquals(525000, Tagihan::where('jenis', 'dudukan')->where('status', 'valid')->sum('nominal_bayar'));
        $this->assertEquals(2500000, Tagihan::where('jenis', 'penanjung_batu')->where('status', 'valid')->sum('nominal_bayar'));

        $teks = Krama::where('impor_baris', 6)->first()->tagihan;
        $this->assertSame(2, $teks->count());
        $this->assertEquals(200000, $teks->firstWhere('jenis.value', 'dudukan')->nominal_bayar);
        $this->assertSame('2026-03-01', $teks->first()->tanggal_bayar->toDateString());

        $luput = Krama::where('impor_baris', 8)->first();
        $this->assertSame(StatusTagihan::Dispensasi, $luput->tagihan->first()->status);
        $this->assertSame(0, $luput->tunggakan());

        $transfer = Krama::where('impor_baris', 9)->first()->tagihan->first();
        $this->assertSame(StatusTagihan::Dibuat, $transfer->status);
        $this->assertNull($transfer->nominal_bayar);

        $this->assertSame(75000 + 500000, Krama::where('impor_baris', 5)->first()->tunggakan());
        $this->assertSame(200000, Krama::where('impor_baris', 7)->first()->tunggakan());
        Storage::disk('local')->assertMissing("impor-pembayaran/{$pratinjau->json('token')}.xlsx");
    }

    public function test_impor_hanya_sekali_dan_butuh_krama_impor(): void
    {
        $this->actingAs($this->admin)->post('/api/impor-pembayaran/pratinjau', ['file' => $this->file()], self::HEADER)
            ->assertUnprocessable()->assertJsonValidationErrors('file');

        $this->kramaImpor();
        $token = $this->actingAs($this->admin)->post('/api/impor-pembayaran/pratinjau', ['file' => $this->file()], self::HEADER)->json('token');
        $this->actingAs($this->admin)->postJson('/api/impor-pembayaran', ['token' => $token])->assertCreated();

        $this->actingAs($this->admin)->getJson('/api/impor-pembayaran')->assertJsonPath('sudah_diimpor', true);
        $this->actingAs($this->admin)->post('/api/impor-pembayaran/pratinjau', ['file' => $this->file()], self::HEADER)->assertUnprocessable();
        $this->assertSame(9, Tagihan::count());
    }
}
