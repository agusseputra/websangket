<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BeritaAdminTest extends TestCase
{
    use RefreshDatabase;

    private const ISI = '<p>Krama Desa Adat Sangket melaksanakan gotong royong membersihkan pura.</p>';

    private string $folderPublik;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Folder publik sementara agar file unggahan uji tidak masuk ke public/ proyek.
        $this->folderPublik = sys_get_temp_dir().'/publik-berita-'.uniqid();
        File::ensureDirectoryExists($this->folderPublik);
        $this->app->usePublicPath($this->folderPublik);
        $this->admin = User::factory()->admin()->create();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->folderPublik);

        parent::tearDown();
    }

    public function test_petugas_dan_tamu_tidak_bisa_mengelola_berita(): void
    {
        $petugas = User::factory()->create();

        $this->getJson('/api/berita')->assertUnauthorized();
        $this->actingAs($petugas)->getJson('/api/berita')->assertForbidden();
        $this->actingAs($petugas)->postJson('/api/kategori-berita', ['nama' => 'Kegiatan'])->assertForbidden();
    }

    public function test_admin_mengelola_kategori_dan_nama_serta_slug_harus_unik(): void
    {
        $id = $this->actingAs($this->admin)->postJson('/api/kategori-berita', ['nama' => '  Kegiatan   Desa '])
            ->assertCreated()->assertJsonPath('nama', 'Kegiatan Desa')->json('id');

        $this->actingAs($this->admin)->postJson('/api/kategori-berita', ['nama' => 'kegiatan-desa'])
            ->assertUnprocessable()->assertJsonValidationErrors(['nama' => 'alamat yang sama']);
        $this->actingAs($this->admin)->postJson('/api/kategori-berita', ['nama' => ''])
            ->assertUnprocessable()->assertJsonValidationErrors(['nama' => 'Nama kategori wajib diisi.']);

        $this->actingAs($this->admin)->putJson("/api/kategori-berita/{$id}", ['nama' => 'Kegiatan Desa'])->assertOk();
        $this->actingAs($this->admin)->putJson("/api/kategori-berita/{$id}", ['nama' => 'Upacara'])->assertOk();
        $this->assertDatabaseHas('categories', ['id' => $id, 'name' => 'Upacara']);

        $this->actingAs($this->admin)->getJson('/api/kategori-berita')
            ->assertJsonPath('0.nama', 'Upacara')->assertJsonPath('0.slug', 'upacara')->assertJsonPath('0.jumlah_berita', 0);

        $this->actingAs($this->admin)->deleteJson("/api/kategori-berita/{$id}")->assertNoContent();
        $this->assertDatabaseMissing('categories', ['id' => $id]);
    }

    public function test_kategori_yang_masih_dipakai_berita_tidak_bisa_dihapus(): void
    {
        $berita = Article::factory()->create();

        $this->actingAs($this->admin)->deleteJson("/api/kategori-berita/{$berita->category_id}")
            ->assertUnprocessable()->assertJsonValidationErrors(['kategori' => 'Kategori masih dipakai berita']);
        $this->assertModelExists($berita);
    }

    public function test_admin_membuat_berita_dengan_sampul_dan_gambar_tempelan_menjadi_file(): void
    {
        $kategori = Category::factory()->create();
        $gambarTempel = base64_encode(UploadedFile::fake()->image('tempel.png', 20, 20)->getContent());

        $respons = $this->actingAs($this->admin)->post('/api/berita', [
            'judul' => 'Gotong royong di Pura Desa',
            'kategori_id' => $kategori->id,
            'isi' => self::ISI.'<p><img src="data:image/png;base64,'.$gambarTempel.'"></p>',
            'sampul' => UploadedFile::fake()->image('sampul.jpg', 800, 450),
        ], ['Accept' => 'application/json'])->assertCreated()->assertJsonPath('data.judul', 'Gotong royong di Pura Desa');

        $berita = Article::findOrFail($respons->json('data.id'));
        $this->assertMatchesRegularExpression('#^article/[A-Za-z0-9]{40}\.jpg$#', $berita->image);
        $this->assertFileExists(public_path('assets/'.$berita->image));
        $this->assertStringNotContainsString('data:image', $berita->content);
        $this->assertMatchesRegularExpression('#src="/assets/article/konten/[A-Za-z0-9]{32}\.png"#', $berita->content);
        $respons->assertJsonPath('data.url', $berita->url());
    }

    public function test_validasi_berita_menolak_input_kosong_dan_tidak_sah(): void
    {
        Article::factory()->create(['title' => 'Judul yang sudah dipakai berita']);

        $this->actingAs($this->admin)->postJson('/api/berita', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'judul' => 'Judul berita wajib diisi.',
                'kategori_id' => 'Kategori wajib dipilih.',
                'isi' => 'Isi berita wajib diisi.',
            ]);

        $this->actingAs($this->admin)->post('/api/berita', [
            'judul' => 'Judul yang sudah dipakai berita',
            'kategori_id' => 999,
            'isi' => '<p><br></p>',
            'sampul' => UploadedFile::fake()->create('dokumen.pdf', 10, 'application/pdf'),
        ], ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'judul' => 'Judul ini sudah dipakai berita lain.',
                'kategori_id' => 'Kategori tidak ditemukan.',
                'isi' => 'Isi berita minimal 30 karakter teks.',
                'sampul' => 'Foto sampul harus berupa gambar.',
            ]);

        $this->actingAs($this->admin)->post('/api/berita', [
            'judul' => 'Berita dengan sampul besar',
            'kategori_id' => Category::factory()->create()->id,
            'isi' => self::ISI,
            'sampul' => UploadedFile::fake()->image('besar.jpg', 800, 450)->size(1025),
        ], ['Accept' => 'application/json'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['sampul' => 'Ukuran foto sampul maksimal 1 MB.']);
    }

    public function test_mengubah_berita_mengganti_lalu_menghapus_sampul_lama(): void
    {
        $berita = Article::factory()->create();
        $data = ['judul' => $berita->title, 'kategori_id' => $berita->category_id, 'isi' => self::ISI];

        $this->actingAs($this->admin)->post("/api/berita/{$berita->id}", [...$data, '_method' => 'PUT', 'sampul' => UploadedFile::fake()->image('a.jpg', 800, 450)], ['Accept' => 'application/json'])->assertOk();
        $sampulPertama = $berita->fresh()->image;

        $this->actingAs($this->admin)->post("/api/berita/{$berita->id}", [...$data, '_method' => 'PUT', 'sampul' => UploadedFile::fake()->image('b.jpg', 800, 450)], ['Accept' => 'application/json'])->assertOk();
        $sampulKedua = $berita->fresh()->image;
        $this->assertFileDoesNotExist(public_path('assets/'.$sampulPertama));
        $this->assertFileExists(public_path('assets/'.$sampulKedua));

        $this->actingAs($this->admin)->putJson("/api/berita/{$berita->id}", [...$data, 'judul' => 'Judul baru untuk berita ini', 'hapus_sampul' => true])
            ->assertOk()->assertJsonPath('data.sampul', null);
        $this->assertDatabaseHas('articles', ['id' => $berita->id, 'title' => 'Judul baru untuk berita ini', 'image' => null]);
        $this->assertFileDoesNotExist(public_path('assets/'.$sampulKedua));
    }

    public function test_daftar_berita_bisa_dicari_dan_detail_memuat_isi(): void
    {
        $dicari = Article::factory()->create(['title' => 'Melasti ke Segara Penimbangan']);
        Article::factory()->create(['title' => 'Rapat prajuru desa adat']);

        $this->actingAs($this->admin)->getJson('/api/berita?q=melasti')
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $dicari->id)->assertJsonMissingPath('data.0.isi');
        $this->actingAs($this->admin)->getJson("/api/berita?kategori_id={$dicari->category_id}")->assertJsonCount(1, 'data');
        $this->actingAs($this->admin)->getJson("/api/berita/{$dicari->id}")->assertOk()->assertJsonPath('data.isi', $dicari->content);
    }

    public function test_menghapus_berita_ikut_menghapus_file_sampul(): void
    {
        File::ensureDirectoryExists(public_path('assets/article'));
        File::put(public_path('assets/article/sampul-uji.jpg'), 'x');
        $berita = Article::factory()->create(['image' => 'article/sampul-uji.jpg']);

        $this->actingAs($this->admin)->deleteJson("/api/berita/{$berita->id}")->assertNoContent();

        $this->assertModelMissing($berita);
        $this->assertFileDoesNotExist(public_path('assets/article/sampul-uji.jpg'));
    }

    public function test_unggah_gambar_editor_mengembalikan_url_dan_menolak_bukan_gambar(): void
    {
        $url = $this->actingAs($this->admin)->post('/api/berita/gambar', ['gambar' => UploadedFile::fake()->image('isi.png', 300, 200)], ['Accept' => 'application/json'])
            ->assertCreated()->json('url');

        $this->assertStringStartsWith('/assets/article/konten/', $url);
        $this->assertFileExists(public_path(ltrim($url, '/')));

        $this->actingAs($this->admin)->post('/api/berita/gambar', ['gambar' => UploadedFile::fake()->create('skrip.php', 1, 'text/x-php')], ['Accept' => 'application/json'])
            ->assertUnprocessable()->assertJsonValidationErrors(['gambar' => 'File harus berupa gambar.']);

        $this->actingAs($this->admin)->post('/api/berita/gambar', ['gambar' => UploadedFile::fake()->image('besar.png', 300, 200)->size(1025)], ['Accept' => 'application/json'])
            ->assertUnprocessable()->assertJsonValidationErrors(['gambar' => 'Ukuran gambar maksimal 1 MB.']);
    }

    public function test_gambar_tempelan_di_isi_berita_lebih_dari_satu_mb_ditolak(): void
    {
        $data = ['judul' => 'Berita dengan gambar tempelan', 'kategori_id' => Category::factory()->create()->id];
        $base64Besar = str_repeat('A', (int) ceil(1024 * 1024 * 4 / 3) + 8);

        $this->actingAs($this->admin)->postJson('/api/berita', [...$data, 'isi' => self::ISI.'<p><img src="data:image/png;base64,'.$base64Besar.'"></p>'])
            ->assertUnprocessable()->assertJsonValidationErrors(['isi' => 'Gambar di dalam isi berita maksimal 1 MB per gambar.']);

        $this->assertSame(0, Article::count());
    }
}
