<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class KategoriBeritaController extends Controller
{
    public function index(): JsonResponse
    {
        $kategori = Category::withCount('articles')->orderBy('name')->get()->map(fn (Category $satu) => [
            'id' => $satu->id,
            'nama' => $satu->name,
            'slug' => $satu->slug,
            'url' => $satu->url(),
            'jumlah_berita' => $satu->articles_count,
        ]);

        return response()->json($kategori);
    }

    public function store(Request $request): JsonResponse
    {
        $kategori = Category::create(['name' => $this->validasi($request)]);

        return response()->json(['id' => $kategori->id, 'nama' => $kategori->name], 201);
    }

    public function update(Request $request, Category $kategori): JsonResponse
    {
        $kategori->update(['name' => $this->validasi($request, $kategori)]);

        return response()->json(['id' => $kategori->id, 'nama' => $kategori->name]);
    }

    public function destroy(Category $kategori): JsonResponse
    {
        // Menghapus kategori ikut menghapus beritanya (cascade), jadi kategori yang masih dipakai ditolak.
        if ($kategori->articles()->exists()) {
            throw ValidationException::withMessages([
                'kategori' => 'Kategori masih dipakai berita, pindahkan atau hapus beritanya lebih dulu.',
            ]);
        }

        $kategori->delete();

        return response()->json(status: 204);
    }

    private function validasi(Request $request, ?Category $kategori = null): string
    {
        $request->merge(['nama' => Str::squish((string) $request->input('nama'))]);

        $nama = $request->validate([
            'nama' => [
                'required', 'string', 'min:3', 'max:50',
                Rule::unique('categories', 'name')->ignore($kategori),
                // Slug dipakai di URL /listarticle/{slug}, jadi dua nama tidak boleh menghasilkan slug yang sama.
                function (string $attribute, mixed $value, \Closure $fail) use ($kategori) {
                    $slug = Str::slug($value);

                    if ($slug === '') {
                        $fail('Nama kategori harus memuat huruf atau angka.');

                        return;
                    }

                    $bentrok = Category::query()->when($kategori, fn ($query) => $query->whereKeyNot($kategori->id))->get()
                        ->contains(fn (Category $lain) => $lain->slug === $slug);

                    if ($bentrok) {
                        $fail('Nama kategori ini menghasilkan alamat yang sama dengan kategori lain.');
                    }
                },
            ],
        ], [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.min' => 'Nama kategori minimal 3 karakter.',
            'nama.max' => 'Nama kategori maksimal 50 karakter.',
            'nama.unique' => 'Nama kategori ini sudah ada.',
        ])['nama'];

        return $nama;
    }
}
