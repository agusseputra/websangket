<?php

namespace App\Http\Controllers\Api;

use App\Enums\JenisTagihan;
use App\Enums\MetodeBayar;
use App\Enums\StatusTagihan;
use App\Http\Controllers\Controller;
use App\Http\Resources\KramaResource;
use App\Http\Resources\TagihanResource;
use App\Models\Krama;
use App\Models\PeriodeIuran;
use App\Models\Tagihan;
use App\Services\GenerateTagihanService;
use App\Services\PembayaranService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TagihanController extends Controller
{
    public function __construct(
        private PembayaranService $pembayaran,
        private GenerateTagihanService $generate,
    ) {}

    /**
     * Daftar per pembayaran: baris dengan nomor struk yang sama menjadi satu kelompok.
     */
    public function index(Request $request): JsonResponse
    {
        $filter = $request->validate([
            'q' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', Rule::enum(StatusTagihan::class)],
            'jenis' => ['nullable', 'in:dudukan,penanjung_batu'],
            'tahun' => ['nullable', 'integer'],
            'tanggal_dari' => ['nullable', 'date'],
            'tanggal_sampai' => ['nullable', 'date', 'after_or_equal:tanggal_dari'],
            'petugas_id' => ['nullable', 'integer'],
        ], [
            'tanggal_sampai.after_or_equal' => 'Tanggal akhir tidak boleh sebelum tanggal awal.',
        ]);

        $kunci = 'COALESCE(tagihan.no_struk, CAST(tagihan.id AS CHAR))';

        $halaman = Tagihan::query()
            ->selectRaw("{$kunci} as kunci, MAX(tagihan.tanggal_bayar) as tanggal_terakhir, MAX(tagihan.id) as id_terakhir")
            ->when($filter['q'] ?? null, fn ($query, $q) => $query->whereHas(
                'krama',
                fn ($krama) => $krama->where('nama', 'like', '%'.addcslashes($q, '%_\\').'%'),
            ))
            ->when($filter['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
            ->when($filter['jenis'] ?? null, fn ($query, $jenis) => $query->where('jenis', $jenis))
            ->when($filter['tahun'] ?? null, fn ($query, $tahun) => $query->whereHas(
                'periodeIuran',
                fn ($periode) => $periode->where('tahun', $tahun),
            ))
            ->when($filter['petugas_id'] ?? null, fn ($query, $petugasId) => $query->where('petugas_id', $petugasId))
            ->when($filter['tanggal_dari'] ?? null, fn ($query, $tanggal) => $query->whereDate('tanggal_bayar', '>=', $tanggal))
            ->when($filter['tanggal_sampai'] ?? null, fn ($query, $tanggal) => $query->whereDate('tanggal_bayar', '<=', $tanggal))
            ->groupByRaw($kunci)
            ->orderByDesc('tanggal_terakhir')
            ->orderByDesc('id_terakhir')
            ->paginate(config('sangket.per_halaman'))
            ->withQueryString();

        $urutan = $halaman->getCollection()->pluck('kunci');

        $baris = Tagihan::with(['krama', 'periodeIuran', 'petugas'])
            ->whereIn('no_struk', $urutan)
            ->orWhereIn('id', $urutan->filter(fn (string $nilai) => ctype_digit($nilai)))
            ->orderBy('id')
            ->get()
            ->groupBy(fn (Tagihan $satu) => $satu->no_struk ?? (string) $satu->id);

        return response()->json([
            'data' => $urutan->map(function (string $nilai) use ($baris) {
                $item = $baris[$nilai];
                $pertama = $item->first();

                return [
                    'kunci' => $nilai,
                    'no_struk' => $pertama->no_struk,
                    'krama_id' => $pertama->krama_id,
                    'krama' => $pertama->krama?->nama,
                    'tanggal_bayar' => $pertama->tanggal_bayar?->toDateString(),
                    'metode' => $pertama->metode?->value,
                    'petugas' => $pertama->petugas?->name,
                    'item' => TagihanResource::collection($item),
                ];
            })->values(),
            'meta' => [
                'current_page' => $halaman->currentPage(),
                'last_page' => $halaman->lastPage(),
                'total' => $halaman->total(),
            ],
        ]);
    }

    public function milikKrama(Krama $krama): JsonResponse
    {
        return response()->json($this->dataTagihanKrama($krama));
    }

    /**
     * Dipanggil saat halaman bayar dibuka: tagihan periode aktif dibuat bila belum ada.
     */
    public function siapkan(Krama $krama): JsonResponse
    {
        $periode = PeriodeIuran::aktif();
        $dibuat = $periode && $krama->aktif ? $this->generate->untukKrama($krama, $periode) : collect();

        return response()->json([
            ...$this->dataTagihanKrama($krama),
            'periode_aktif' => $periode ? (int) $periode->tahun : null,
            'tagihan_dibuat' => $dibuat->map(fn (Tagihan $baris) => $baris->jenis->label())->values(),
        ]);
    }

    public function bayar(Request $request, Krama $krama): JsonResponse
    {
        $data = $request->validate([
            'transaksi_uuid' => ['required', 'uuid'],
            'metode' => ['required', Rule::enum(MetodeBayar::class)],
            'item' => ['required', 'array', 'min:1'],
            'item.*.tagihan_id' => ['required', 'integer', 'distinct'],
            'item.*.nominal_bayar' => ['required', 'integer', 'min:1', 'max:999999999999'],
        ], [
            'item.required' => 'Centang minimal satu tagihan.',
            'item.*.nominal_bayar.min' => 'Nominal bayar minimal Rp1.',
            'item.*.nominal_bayar.required' => 'Nominal bayar wajib diisi.',
        ]);

        $nominal = collect($data['item'])->pluck('nominal_bayar', 'tagihan_id')->all();
        $statusAwal = $krama->status;

        $tagihan = $this->pembayaran->bayar(
            $krama,
            $nominal,
            MetodeBayar::from($data['metode']),
            $data['transaksi_uuid'],
            $request->user(),
        );

        return response()->json([
            'no_struk' => $tagihan->first()->no_struk,
            'tagihan' => TagihanResource::collection($tagihan->load('periodeIuran')),
            'tunggakan' => $krama->tunggakan(),
            'status_krama_baru' => $krama->refresh()->status !== $statusAwal ? $krama->status->label() : null,
        ], 201);
    }

    public function validasi(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'distinct'],
        ], ['ids.required' => 'Centang minimal satu pembayaran.']);

        return response()->json(['jumlah' => $this->pembayaran->validasi($data['ids'], $request->user())]);
    }

    public function koreksi(Request $request, Tagihan $tagihan): TagihanResource
    {
        $this->pembayaran->koreksi($tagihan, $this->alasan($request));

        return TagihanResource::make($tagihan);
    }

    public function dispensasi(Request $request, Tagihan $tagihan): TagihanResource
    {
        $this->pembayaran->dispensasi($tagihan, $this->alasan($request));

        return TagihanResource::make($tagihan);
    }

    public function tunggakan(Request $request): JsonResponse
    {
        $filter = $request->validate([
            'q' => ['nullable', 'string', 'max:150'],
            'perumahan_id' => ['nullable', 'integer'],
            'jenis' => ['nullable', Rule::enum(JenisTagihan::class)],
            'tahun' => ['nullable', 'integer'],
        ]);

        $jumlah = fn () => Tagihan::selectRaw(Tagihan::SQL_TUNGGAKAN)
            ->whereColumn('tagihan.krama_id', 'krama.id')
            ->when($filter['jenis'] ?? null, fn ($query, $jenis) => $query->where('tagihan.jenis', $jenis))
            ->when($filter['tahun'] ?? null, fn ($query, $tahun) => $query->whereIn(
                'tagihan.periode_iuran_id',
                PeriodeIuran::where('tahun', $tahun)->select('id'),
            ));

        $query = Krama::query()
            ->when($filter['q'] ?? null, fn ($builder, $q) => $builder->where('nama', 'like', '%'.addcslashes($q, '%_\\').'%'))
            ->when($filter['perumahan_id'] ?? null, fn ($builder, $id) => $builder->where('perumahan_id', $id))
            ->where($jumlah(), '>', 0);

        $total = (int) (clone $query)->getQuery()->cloneWithout(['columns', 'orders'])
            ->selectRaw('COALESCE(SUM(('.$jumlah()->toRawSql().')), 0) as total')->value('total');

        $halaman = $query->with('perumahan')
            ->addSelect(['krama.*', 'tunggakan' => $jumlah()])
            ->orderByDesc('tunggakan')
            ->orderBy('nama')
            ->paginate(config('sangket.per_halaman'))
            ->withQueryString();

        return response()->json([
            'total' => $total,
            'data' => $halaman->getCollection()->map(fn (Krama $krama) => [
                'id' => $krama->id,
                'nama' => $krama->nama,
                'perumahan' => $krama->perumahan?->nama,
                'nomor_rumah' => $krama->nomor_rumah,
                'tunggakan' => (int) $krama->tunggakan,
            ]),
            'meta' => [
                'current_page' => $halaman->currentPage(),
                'last_page' => $halaman->lastPage(),
                'total' => $halaman->total(),
            ],
        ]);
    }

    private function dataTagihanKrama(Krama $krama): array
    {
        $tagihan = $krama->tagihan()->with(['periodeIuran', 'petugas'])->get()->sortByDesc(fn (Tagihan $baris) => (int) $baris->periodeIuran->tahun)->values();

        return [
            'krama' => KramaResource::make($krama->load('perumahan')),
            'tunggakan' => $krama->tunggakan(),
            'tagihan' => TagihanResource::collection($tagihan),
        ];
    }

    private function alasan(Request $request): string
    {
        return $request->validate(
            ['alasan' => ['required', 'string', 'max:255']],
            ['alasan.required' => 'Alasan wajib diisi.'],
        )['alasan'];
    }
}
