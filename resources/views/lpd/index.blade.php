{{-- Halaman statis LPD. Sumber: Profil Desa Adat Sangket ed 2026, Awig-awig LPD Desa Adat Sangket 1999, dan blog lpdpakramansangket.blogspot.com. --}}
@php
    $deskripsi = 'LPD Desa Adat Sangket, lembaga keuangan milik desa adat sejak tahun 2000: tabungan, deposito, pinjaman, layanan PPOB, dan dana pembangunan untuk Desa Adat Sangket.';
    $remah = [['Beranda', url('/')], ['Profil Desa Adat', route('profil')], ['LPD Desa Adat Sangket', route('lpd')]];

    $daftarIsi = [
        ['profil-lpd', 'Profil LPD'],
        ['prajuru-lpd', 'Prajuru dan pegawai'],
        ['program-lpd', 'Program dan layanan'],
        ['tampan-lpd', 'Produk unggulan TAMPAN'],
        ['kendaraan-lpd', 'Pembelian kendaraan baru'],
        ['keuntungan-lpd', 'Pembagian keuntungan'],
        ['kegiatan-lpd', 'Kegiatan LPD'],
    ];

    $sorotan = [
        ['2000', 'Mulai beroperasi', 'SK Gubernur Bali No. 438 Tahun 1999'],
        ['9 orang', 'Pengelola', '3 prajuru dan 6 pegawai'],
        ['20%', 'Untuk desa adat', 'Bagian keuntungan bersih tiap tahun'],
    ];

    $tujuan = [
        'Krama memperoleh kredit dengan cepat karena sistemnya praktis.',
        'Memberantas sistem ijon, gadai gelap, dan rentenir.',
        'Meningkatkan daya beli krama dan melancarkan lalu lintas pembayaran di desa.',
        'Membentuk modal krama dan membiasakan menabung.',
    ];

    $perjalanan = [
        ['11 September 1999', 'Pendirian LPD ditetapkan dengan SK Gubernur Kepala Daerah Tingkat I Bali Nomor 438 Tahun 1999.'],
        ['13 Oktober 1999', 'Paruman krama di Pura Dalem menetapkan awig-awig tambahan tentang LPD Desa Adat Sangket.'],
        ['2000', 'LPD mulai beroperasi, dirintis Made Resnika (ketua), Made Budiasa (sekretaris), dan I Gusti Ayu Mas Candrawati (bendahara) dengan 4 pegawai.'],
        ['November 2010', 'Gedung kantor LPD Sangket diresmikan dengan penandatanganan prasasti oleh Bupati Buleleng.'],
    ];

    $prajuru = [
        ['Ketua/Kepala', 'Made Budiasa, S.E.'],
        ['Sekretaris/Tata Usaha', 'Ngurah Putu Arka Rai'],
        ['Bendahara/Kasir', 'I Gusti Ayu Mas Candrawati'],
    ];
    $pegawai = ['Luh Sukreni', 'Putu Laba', 'Ketut Serina', 'Ketut Widiada', 'Made Gede Somadata', 'Ketut Rustini'];
    $pengawas = [
        ['I Nyoman Suarjana, S.E.', 'Bendesa Adat Sangket'],
        ['Nyoman Tomas Dritawan, S.H.', 'Unsur Kelian Banjar Dinas Sangket'],
        ['Dr. Gede Sandiasa, S.Sos., M.Si.', 'Unsur praktisi UMKM'],
    ];

    $layanan = [
        ['Simpanan', 'M12 3v18M17 7H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6', ['Tabungan Sukarela', 'Tabungan Masa Depan', 'Deposito']],
        ['Pinjaman', 'M3 10h18M5 6h14a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2Zm2 9h4', ['Pinjaman Bulanan', 'Pinjaman Musiman', 'Kredit Kendaraan', 'Kredit Usaha']],
        ['Pembayaran (PPOB)', 'M13 2 4 14h7l-1 8 9-12h-7l1-8Z', ['Listrik', 'BPJS', 'Air', 'WiFi', 'Pulsa', 'Paket Data']],
    ];

    // Sumber: brosur Program Tabungan Masa Depan (TAMPAN) LPD Desa Adat Sangket.
    $tampanTentang = [
        'Tabungan Masa Depan (TAMPAN) memberi kesempatan kepada krama untuk menyimpan uang di LPD Desa Adat Sangket secara bertahap, setiap bulan, agar kebutuhan masa depan dan kesejahteraan keluarga lebih terjamin.',
        'Jangka waktunya 1 tahun, 2 tahun, 3 tahun, dan seterusnya sesuai kontrak yang dipilih nasabah, sehingga saat dicairkan dananya bermanfaat tepat pada waktunya.',
        'TAMPAN juga membimbing dan mendorong krama untuk terbiasa menabung, sehingga kepentingan keluarga dapat dijadwalkan sesuai harapan.',
    ];
    $tampanKegunaan = ['Biaya sekolah TK sampai SD', 'SMP dan SMA', 'Kuliah', 'Upacara yadnya'];
    $tampanKeuntungan = [
        ['Bunga yang cukup besar', 'Bunga dihitung dari saldo yang mengendap setiap bulan.'],
        ['Bunga berbunga', 'Bunga langsung menambah saldo dan ikut berbunga kembali pada bulan berikutnya.'],
        ['Setoran dijemput', 'Petugas LPD menjemput setoran setiap bulan atau sesuai permintaan nasabah.'],
        ['Tepat waktu dan tepat guna', 'Jangka waktu yang pasti membuat penggunaan dana mudah direncanakan.'],
        ['Bisa diperpanjang', 'Jika saat jatuh tempo dana belum diperlukan, tabungan dapat diperpanjang dalam kelipatan tahun.'],
        ['Jaminan kredit', 'TAMPAN dapat menjadi jaminan kredit di LPD dengan plafon maksimal 75% dari saldo tabungan.'],
        ['Mudah dicairkan', 'Setelah jatuh tempo, cukup datang ke kantor LPD membawa buku tabungan, atau petugas LPD mengantarkannya ke rumah.'],
    ];
    $tampanSyarat = [
        'Tabungan baru dapat diambil kembali setelah jangka waktunya berakhir.',
        'Perubahan alamat, tanda tangan, dan data lainnya wajib diberitahukan secara tertulis kepada LPD Desa Adat Sangket.',
        'Jika pemilik tabungan meninggal dunia, simpanan dibayarkan kepada ahli waris yang menunjukkan surat keterangan ahli waris yang sah dari pejabat berwenang beserta buku tabungan.',
        'Tabungan diberi bunga 0,5% per bulan dari saldo yang mengendap, tanpa potongan pajak.',
        'Pemegang TAMPAN tunduk pada syarat umum dan ketentuan lain yang ditetapkan LPD Desa Adat Sangket.',
        'Agar jumlah penerimaan sesuai tabel simulasi, setoran dilakukan setiap bulan sesuai tanggal mulai menjadi peserta.',
    ];
    /** @var array<int, array<int, int>> Setoran per bulan => penerimaan setelah 1–5 tahun. */
    $tampanSimulasi = [
        25000 => [308521, 635939, 983551, 1352604, 1744419],
        50000 => [617042, 1271878, 1967103, 2705208, 3488837],
        100000 => [1234084, 2543756, 3934206, 5410415, 6977674],
        150000 => [1851127, 3815634, 5901309, 8115623, 10466511],
        200000 => [2468169, 5087513, 7868412, 10820831, 13955348],
        250000 => [3085211, 6359391, 9835515, 13526038, 17444185],
        300000 => [3702253, 7631269, 11802618, 16231246, 20933022],
        350000 => [4319296, 8903147, 13769721, 18936454, 24421859],
        400000 => [4936338, 10175025, 15736823, 21641661, 27910696],
        500000 => [6170422, 12718782, 19671029, 27052077, 34888371],
    ];
    $rupiah = fn (int $nilai): string => number_format($nilai, 0, ',', '.');
    $tampanTab = [
        ['tentang', 'Tentang TAMPAN'],
        ['keuntungan', 'Keuntungan'],
        ['syarat', 'Syarat umum'],
        ['simulasi', 'Tabel simulasi'],
    ];

    // Sumber: banner kerja sama dealer yang dipasang di kantor LPD Desa Adat Sangket.
    $dealer = [
        [
            'nama' => 'Astra Motor Singaraja',
            'merek' => 'Honda',
            'alamat' => null,
            'isi' => 'Sepeda motor Honda baru, bisa beli langsung maupun indent untuk tipe yang sedang inden.',
            'sorotan' => ['Bisa beli & indent'],
            'foto' => '/images/lpd/dealer-honda.jpg',
            'ukuran' => [768, 2031],
            'alt' => 'Banner kerja sama LPD Sangket dengan Astra Motor Singaraja menampilkan motor Honda PCX dan Vario',
        ],
        [
            'nama' => 'Maha Surya Motor III',
            'merek' => 'Yamaha',
            'alamat' => 'Jalan Ngurah Rai No. 52 A',
            'isi' => 'Sepeda motor Yamaha baru dengan unit siap kirim, serta layanan tukar tambah untuk segala merek motor.',
            'sorotan' => ['Garansi 5 tahun', 'Siap kirim', 'Tukar tambah'],
            'foto' => '/images/lpd/dealer-yamaha.jpg',
            'ukuran' => [711, 1980],
            'alt' => 'Banner kerja sama LPD Desa Sangket dengan Maha Surya Motor III menampilkan motor Yamaha NMAX, Grand Filano, Fazzio, dan Aerox',
        ],
        [
            'nama' => 'Duta Intika',
            'merek' => 'Kawasaki',
            'alamat' => null,
            'isi' => 'Sepeda motor Kawasaki baru, dari trail, sport Ninja, sampai motor klasik, dengan promo menarik dari dealer.',
            'sorotan' => ['Cashback jutaan rupiah', 'DP & bunga rendah'],
            'foto' => '/images/lpd/dealer-kawasaki.jpg',
            'ukuran' => [777, 2010],
            'alt' => 'Banner Best Deal Kawasaki dari dealer Duta Intika menampilkan motor KLX, Ninja, dan W175',
        ],
        [
            'nama' => 'Vespa Udayana',
            'merek' => 'Piaggio, Vespa, dan Aprilia',
            'alamat' => null,
            'isi' => 'Skuter matik terbaru dari Piaggio, Vespa, dan Aprilia kini bisa didapatkan melalui LPD Sangket.',
            'sorotan' => ['Skuter matik terbaru'],
            'foto' => '/images/lpd/dealer-vespa.jpg',
            'ukuran' => [858, 2028],
            'alt' => 'Banner kerja sama LPD Sangket dengan Vespa Udayana menampilkan skuter Piaggio Liberty, Vespa, dan Aprilia SR GT',
        ],
    ];

    $keuntungan = [
        ['Dana cadangan', 40, '#0369a1'],
        ['Desa Adat', 20, '#38bdf8'],
        ['Cadangan umum', 20, '#7dd3fc'],
        ['Jasa produksi', 10, '#16a34a'],
        ['Dana sosial', 5, '#ea580c'],
        ['Dana pembinaan', 5, '#a855f7'],
    ];

    $kegiatan = [
        ['/images/lpd/rat-lpd.jpg', 'Rapat Tahunan LPD', 'Pamucuk LPD dan badan pengawas menyampaikan laporan pertanggungjawaban 2025 serta RK-RAPB 2026 pada 20 Februari 2026.', 'Pengurus LPD, badan pengawas, dan koordinator LPLPD di meja rapat tahunan LPD Desa Adat Sangket'],
        ['/images/lpd/rat-krama.jpg', 'Krama hadir di RAT', 'Krama desa adat hadir langsung, mendengar laporan, dan memberi masukan secara terbuka.', 'Krama Desa Adat Sangket berbusana adat duduk di bawah tenda mengikuti rapat tahunan LPD'],
        ['/images/lpd/hadiah-rat.jpg', 'Hadiah untuk krama', 'Krama yang hadir dalam rapat tahunan berkesempatan membawa pulang hadiah peralatan rumah tangga.', 'Hadiah blender, teko listrik, setrika, dan kipas angin berjajar di depan meja rapat tahunan LPD'],
        ['/images/lpd/piodalan-lpd.jpg', 'Piodalan LPD', 'Prajuru, pegawai, dan krama ngaturang bakti saat piodalan di kantor LPD yang dihias penjor dan umbul-umbul.', 'Prajuru dan krama berbusana putih ngaturang banten saat piodalan di depan gedung LPD Sangket'],
    ];

    $total = array_sum(array_column($keuntungan, 1));
    $seo = [
        'judul' => 'LPD Desa Adat Sangket – Profil, Prajuru, dan Layanan',
        'deskripsi' => $deskripsi,
        'canonical' => route('lpd'),
        'gambar' => url('/images/lpd/piodalan-lpd.jpg'),
        'gambar_alt' => 'Piodalan di depan gedung LPD Desa Adat Sangket',
        'jsonld' => [
            [
                '@context' => 'https://schema.org',
                '@type' => 'FinancialService',
                'name' => 'LPD Desa Adat Sangket',
                'alternateName' => 'Lembaga Perkreditan Desa Adat Sangket',
                'url' => route('lpd'),
                'description' => $deskripsi,
                'image' => url('/images/lpd/gedung-lpd.jpg'),
                'foundingDate' => '2000',
                'parentOrganization' => ['@type' => 'Organization', 'name' => 'Desa Adat Sangket', 'url' => url('/')],
                'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Sukasada', 'addressRegion' => 'Bali', 'addressCountry' => 'ID'],
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => collect($remah)->values()->map(fn ($item, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $item[0], 'item' => $item[1]])->all(),
            ],
        ],
    ];
@endphp
<x-layout-publik :seo="$seo">
    <article aria-labelledby="judul-lpd" data-lightbox>
        <header class="relative -mt-20 overflow-hidden bg-gradient-to-b from-langit-sedang via-langit-muda to-latar pt-24">
            <div aria-hidden="true" class="pointer-events-none absolute -top-40 -right-32 size-[34rem] rounded-full bg-langit/30 blur-3xl"></div>
            <div class="relative mx-auto grid max-w-6xl gap-8 px-4 pt-8 pb-10 lg:grid-cols-[1.15fr_1fr] lg:items-center lg:px-6">
                <div class="grid content-start gap-5">
                    @include('artikel.remah', ['remah' => $remah])
                    <p class="justify-self-start rounded-full bg-panel px-3 py-1 text-sm font-semibold text-langit-tua ring-1 ring-langit-sedang">Lembaga Perkreditan Desa milik Desa Adat Sangket</p>
                    <h1 id="judul-lpd" class="font-display text-4xl leading-[1.05] font-extrabold tracking-tight text-balance sm:text-6xl">LPD Desa Adat <span class="text-langit-tua">Sangket</span></h1>
                    <p class="max-w-xl text-lg text-redup">Menabung dan meminjam di lembaga keuangan milik sendiri. Sebagian keuntungannya kembali untuk pembangunan dan kegiatan adat di Desa Adat Sangket.</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="#program-lpd" class="inline-flex min-h-12 items-center rounded-full bg-langit px-6 font-semibold text-di-langit shadow-lg shadow-langit/40 transition hover:-translate-y-0.5 motion-reduce:transition-none">Lihat layanan LPD</a>
                        <a href="#prajuru-lpd" class="inline-flex min-h-12 items-center rounded-full border border-langit-tua/40 bg-panel px-6 font-semibold text-langit-tua hover:bg-langit-muda">Kenali prajuru</a>
                    </div>
                </div>
                <div class="grid grid-cols-[1fr_1.15fr] gap-4">
                    <figure class="row-span-2 overflow-hidden rounded-[2rem] shadow-xl shadow-langit/10">
                        <img src="/images/lpd/gedung-lpd.jpg" alt="Gedung kantor LPD Sangket berwarna biru dengan papan nama dan gerbang besi" width="787" height="1400" fetchpriority="high" class="h-full max-h-[30rem] w-full object-cover">
                    </figure>
                    <figure class="overflow-hidden rounded-[1.5rem]">
                        <img src="/images/lpd/prajuru-pegawai.jpg" alt="Prajuru dan pegawai LPD Desa Adat Sangket berbusana adat berfoto di tangga candi bentar pura" width="1200" height="1188" class="aspect-square w-full object-cover">
                    </figure>
                    <figure class="overflow-hidden rounded-[1.5rem]">
                        <img src="/images/lpd/piodalan-lpd.jpg" alt="Krama ngaturang banten saat piodalan di kantor LPD" width="1400" height="1050" class="aspect-square w-full object-cover">
                    </figure>
                </div>
            </div>
        </header>

        <div class="mx-auto grid max-w-6xl gap-10 px-4 lg:grid-cols-[minmax(0,1fr)_20rem] lg:px-6">
            <div class="grid min-w-0 content-start gap-14">
                {{-- Profil --}}
                <section id="profil-lpd" aria-labelledby="judul-profil-lpd" class="grid gap-6 scroll-mt-24">
                    <h2 id="judul-profil-lpd" class="font-display text-2xl font-bold sm:text-3xl">Profil LPD</h2>
                    <div class="isi-artikel text-redup">
                        <p class="ql-align-justify">Lembaga Perkreditan Desa (LPD) Desa Adat Sangket adalah lembaga keuangan milik Desa Adat Sangket yang dibina oleh pembina LPD kecamatan, kabupaten, dan provinsi. LPD mendapat dukungan penuh krama dan dikelola secara transparan dengan sistem keuangan terkomputerisasi yang mengacu pada praktik perbankan modern.</p>
                    </div>

                    <dl class="grid gap-4 sm:grid-cols-3">
                        @foreach ($sorotan as [$angka, $label, $rincian])
                            <div class="grid gap-1 rounded-[1.5rem] bg-panel p-5 ring-1 ring-garis">
                                <dt class="order-2 text-sm font-semibold">{{ $label }}</dt>
                                <dd class="order-1 font-display text-3xl font-extrabold text-langit-tua tabular-nums">{{ $angka }}</dd>
                                <dd class="order-3 text-sm text-redup">{{ $rincian }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="grid content-start gap-3 rounded-[2rem] bg-panel p-6 ring-1 ring-garis">
                            <h3 class="font-display text-lg font-bold">Maksud dan tujuan</h3>
                            <ol class="grid gap-2 text-sm text-redup">
                                @foreach ($tujuan as $satu)
                                    <li class="flex gap-3">
                                        <span class="grid size-6 shrink-0 place-items-center rounded-full bg-langit-muda text-xs font-bold text-langit-tua">{{ $loop->iteration }}</span>
                                        {{ $satu }}
                                    </li>
                                @endforeach
                            </ol>
                            <p class="text-xs text-redup">Awig-awig LPD Desa Adat Sangket, Pasal 2.</p>
                        </div>
                        <div class="grid content-start gap-3 rounded-[2rem] bg-panel p-6 ring-1 ring-garis">
                            <h3 class="font-display text-lg font-bold">Perjalanan LPD</h3>
                            <ol class="relative grid gap-4 border-l-2 border-langit-sedang pl-5">
                                @foreach ($perjalanan as [$waktu, $isi])
                                    <li class="relative">
                                        <span class="absolute top-1.5 -left-[1.6rem] size-3 rounded-full bg-langit ring-4 ring-panel" aria-hidden="true"></span>
                                        <p class="text-sm font-semibold text-langit-tua">{{ $waktu }}</p>
                                        <p class="text-sm text-redup">{{ $isi }}</p>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </section>

                {{-- Prajuru dan pegawai --}}
                <section id="prajuru-lpd" aria-labelledby="judul-prajuru-lpd" class="grid gap-6 scroll-mt-24">
                    <h2 id="judul-prajuru-lpd" class="font-display text-2xl font-bold sm:text-3xl">Susunan prajuru dan pegawai</h2>
                    <figure class="grid gap-3">
                        <img src="/images/lpd/prajuru-pegawai.jpg" alt="Prajuru dan pegawai LPD Desa Adat Sangket berbusana adat berfoto di tangga candi bentar pura" width="1200" height="1188" loading="lazy" class="aspect-[16/10] w-full rounded-[2rem] object-cover object-[50%_35%] shadow-xl shadow-langit/10">
                        <figcaption class="text-sm text-redup">Prajuru dan pegawai LPD Desa Adat Sangket.</figcaption>
                    </figure>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="grid content-start gap-3 rounded-[2rem] bg-panel p-6 ring-1 ring-garis md:col-span-2">
                            <h3 class="font-display text-lg font-bold">Prajuru (pengurus)</h3>
                            <ul class="grid gap-3 sm:grid-cols-3">
                                @foreach ($prajuru as [$jabatan, $nama])
                                    <li class="grid gap-1 rounded-[1.25rem] bg-langit-muda p-4">
                                        <span class="text-xs font-semibold tracking-wide text-langit-tua uppercase">{{ $jabatan }}</span>
                                        <span class="font-semibold">{{ $nama }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <h3 class="mt-2 font-display text-lg font-bold">Pegawai</h3>
                            <ul class="flex flex-wrap gap-2">
                                @foreach ($pegawai as $nama)
                                    <li class="rounded-full bg-latar px-3 py-1.5 text-sm font-medium ring-1 ring-garis">{{ $nama }}</li>
                                @endforeach
                            </ul>
                            <p class="text-sm text-redup">Pegawai direkrut melalui tes potensi akademik, kemampuan manajemen keuangan, integritas, dan kepribadian oleh tim yang kompeten.</p>
                        </div>
                        <div class="grid content-start gap-3 rounded-[2rem] bg-gradient-to-br from-langit to-langit-sedang p-6 text-di-langit">
                            <h3 class="font-display text-lg font-bold">Badan pengawas</h3>
                            <ul class="grid gap-3">
                                @foreach ($pengawas as [$nama, $unsur])
                                    <li>
                                        <p class="font-semibold">{{ $nama }}</p>
                                        <p class="text-sm opacity-80">{{ $unsur }}</p>
                                    </li>
                                @endforeach
                            </ul>
                            <p class="text-xs opacity-80">Bendesa Adat karena jabatannya menjadi ketua badan pengawas.</p>
                        </div>
                    </div>
                </section>

                {{-- Program dan layanan --}}
                <section id="program-lpd" aria-labelledby="judul-program-lpd" class="grid gap-6 scroll-mt-24">
                    <div class="grid gap-2">
                        <h2 id="judul-program-lpd" class="font-display text-2xl font-bold sm:text-3xl">Program dan layanan</h2>
                        <p class="text-redup">Setiap krama desa adat dan penduduk Desa Adat Sangket dapat menabung dan meminjam di LPD. Pinjaman memakai jaminan dengan bunga yang ditetapkan, dan tabungan diberi bunga.</p>
                    </div>
                    <div class="grid gap-4 md:grid-cols-3">
                        @foreach ($layanan as [$judul, $ikon, $produk])
                            <div class="muncul grid content-start gap-4 rounded-[2rem] bg-panel p-6 ring-1 ring-garis">
                                <span class="grid size-12 place-items-center rounded-2xl bg-langit-muda text-langit-tua">
                                    <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $ikon }}"/></svg>
                                </span>
                                <h3 class="font-display text-xl font-bold">{{ $judul }}</h3>
                                <ul class="grid gap-1.5 text-sm">
                                    @foreach ($produk as $satu)
                                        <li class="flex gap-2"><span class="mt-2 size-1.5 shrink-0 rounded-full bg-langit" aria-hidden="true"></span>{{ $satu }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-4 rounded-[2rem] bg-panel p-6 ring-1 ring-garis">
                        <div class="grid gap-1">
                            <p class="font-display text-lg font-bold">Ingin membuka tabungan atau mengajukan pinjaman?</p>
                            <p class="text-sm text-redup">Datang langsung ke kantor LPD Desa Adat Sangket di Sukasada pada jam layanan.</p>
                        </div>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ rawurlencode('LPD Desa Adat Sangket Sukasada') }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-12 items-center rounded-full bg-langit px-6 font-semibold text-di-langit hover:brightness-95">Petunjuk arah</a>
                    </div>
                </section>

                {{-- Produk unggulan: TAMPAN. Tanpa JavaScript semua panel tampil berurutan; skrip mengubahnya menjadi tab. --}}
                <section id="tampan-lpd" aria-labelledby="judul-tampan-lpd" class="grid gap-6 scroll-mt-24">
                    <div class="grid gap-2">
                        <p class="inline-flex items-center gap-2 justify-self-start rounded-full bg-langit-muda px-3 py-1 text-sm font-semibold text-langit-tua">
                            <svg class="size-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="m12 2 3 6.5 7 .8-5.2 4.8 1.4 6.9L12 17.5 5.8 21l1.4-6.9L2 9.3l7-.8L12 2Z"/></svg>
                            Produk unggulan
                        </p>
                        <h2 id="judul-tampan-lpd" class="font-display text-2xl font-bold sm:text-3xl">Tabungan Masa Depan (TAMPAN)</h2>
                        <p class="text-redup">Menabung sedikit demi sedikit setiap bulan untuk kebutuhan besar keluarga: sekolah anak, kuliah, sampai upacara yadnya.</p>
                    </div>

                    <dl class="grid gap-3 sm:grid-cols-3">
                        <div class="grid gap-1 rounded-[1.5rem] bg-langit p-5 text-di-langit">
                            <dt class="text-sm font-medium">Bunga per bulan</dt>
                            <dd class="font-display text-3xl font-extrabold">0,5%</dd>
                        </div>
                        <div class="grid gap-1 rounded-[1.5rem] bg-panel p-5 ring-1 ring-garis">
                            <dt class="text-sm text-redup">Setoran mulai</dt>
                            <dd class="font-display text-3xl font-extrabold">Rp25.000</dd>
                        </div>
                        <div class="grid gap-1 rounded-[1.5rem] bg-panel p-5 ring-1 ring-garis">
                            <dt class="text-sm text-redup">Jangka waktu</dt>
                            <dd class="font-display text-3xl font-extrabold">1–5+ tahun</dd>
                        </div>
                    </dl>

                    <div class="grid gap-4 rounded-[2rem] bg-panel p-3 ring-1 ring-garis sm:p-4" data-tab-tampan>
                        <div role="tablist" aria-label="Informasi TAMPAN" class="hidden grid-cols-2 gap-1 rounded-[1.5rem] bg-latar p-1.5 data-[siap]:grid sm:data-[siap]:flex">
                            @foreach ($tampanTab as $urutan => [$kunci, $label])
                                <button type="button" role="tab" id="tab-tampan-{{ $kunci }}" aria-controls="panel-tampan-{{ $kunci }}" aria-selected="{{ $urutan === 0 ? 'true' : 'false' }}" tabindex="{{ $urutan === 0 ? '0' : '-1' }}"
                                    class="min-h-11 shrink-0 rounded-[1.1rem] px-4 text-sm font-semibold whitespace-nowrap text-redup transition-colors hover:text-langit-tua aria-selected:bg-panel aria-selected:text-langit-tua aria-selected:shadow-sm motion-reduce:transition-none">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>

                        <div id="panel-tampan-tentang" role="tabpanel" aria-labelledby="tab-tampan-tentang" tabindex="0" class="grid gap-4 p-3 sm:p-4">
                            <h3 class="font-display text-xl font-bold">Tentang TAMPAN</h3>
                            @foreach ($tampanTentang as $paragraf)
                                <p>{{ $paragraf }}</p>
                            @endforeach
                            <div class="grid gap-2">
                                <p class="text-sm font-semibold text-redup">Cocok untuk merencanakan</p>
                                <ul class="flex flex-wrap gap-2">
                                    @foreach ($tampanKegunaan as $kegunaan)
                                        <li class="rounded-full bg-langit-muda px-3 py-1.5 text-sm font-medium text-langit-tua">{{ $kegunaan }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div id="panel-tampan-keuntungan" role="tabpanel" aria-labelledby="tab-tampan-keuntungan" tabindex="0" class="grid gap-4 p-3 sm:p-4">
                            <h3 class="font-display text-xl font-bold">Keuntungan TAMPAN</h3>
                            <ol class="grid gap-3 sm:grid-cols-2">
                                @foreach ($tampanKeuntungan as $nomor => [$judul, $isi])
                                    <li class="flex gap-3 rounded-[1.25rem] bg-latar p-4">
                                        <span class="grid size-8 shrink-0 place-items-center rounded-full bg-langit font-display text-sm font-bold text-di-langit" aria-hidden="true">{{ $nomor + 1 }}</span>
                                        <div class="grid gap-0.5">
                                            <p class="font-semibold">{{ $judul }}</p>
                                            <p class="text-sm text-redup">{{ $isi }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </div>

                        <div id="panel-tampan-syarat" role="tabpanel" aria-labelledby="tab-tampan-syarat" tabindex="0" class="grid gap-4 p-3 sm:p-4">
                            <h3 class="font-display text-xl font-bold">Syarat umum</h3>
                            <ol class="grid gap-3">
                                @foreach ($tampanSyarat as $nomor => $syarat)
                                    <li class="flex gap-3">
                                        <span class="mt-0.5 grid size-6 shrink-0 place-items-center rounded-full bg-langit-muda text-xs font-bold text-langit-tua" aria-hidden="true">{{ $nomor + 1 }}</span>
                                        <span>{{ $syarat }}</span>
                                    </li>
                                @endforeach
                            </ol>
                        </div>

                        <div id="panel-tampan-simulasi" role="tabpanel" aria-labelledby="tab-tampan-simulasi" tabindex="0" class="grid min-w-0 gap-4 p-3 sm:p-4">
                            <div class="grid gap-1">
                                <h3 class="font-display text-xl font-bold">Tabel simulasi</h3>
                                <p class="text-sm text-redup">Jumlah yang diterima saat jatuh tempo, dengan total setoran di bawahnya. Nilai dalam rupiah, sesuai brosur LPD.</p>
                            </div>
                            <div class="overflow-x-auto rounded-[1.25rem] ring-1 ring-garis" tabindex="0" role="region" aria-label="Tabel simulasi TAMPAN, dapat digulir ke samping">
                                <table class="w-full min-w-[40rem] text-right text-sm tabular-nums">
                                    <caption class="sr-only">Simulasi penerimaan Tabungan Masa Depan berdasarkan setoran per bulan dan jangka waktu</caption>
                                    <thead class="bg-latar text-xs text-redup">
                                        <tr>
                                            <th scope="col" class="sticky left-0 bg-latar px-4 py-3 text-left font-semibold">Setoran/bulan</th>
                                            @for ($tahun = 1; $tahun <= 5; $tahun++)
                                                <th scope="col" class="px-4 py-3 font-semibold">{{ $tahun }} tahun</th>
                                            @endfor
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-garis">
                                        @foreach ($tampanSimulasi as $setoran => $penerimaan)
                                            <tr class="hover:bg-langit-muda/40">
                                                <th scope="row" class="sticky left-0 bg-panel px-4 py-3 text-left font-semibold">{{ $rupiah($setoran) }}</th>
                                                @foreach ($penerimaan as $indeks => $terima)
                                                    <td class="px-4 py-3">
                                                        <span class="block font-semibold">{{ $rupiah($terima) }}</span>
                                                        <span class="block text-xs text-redup">setor {{ $rupiah($setoran * 12 * ($indeks + 1)) }}</span>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-xs text-redup">Setoran di atas Rp500.000 dan jangka waktu lebih dari 5 tahun dapat dihitung bersama petugas LPD.</p>
                        </div>
                    </div>

                    <p class="text-xs text-redup">Sumber: brosur Program Tabungan Masa Depan (TAMPAN) LPD Desa Adat Sangket. Suku bunga dapat berubah; pastikan ketentuan terbaru di kantor LPD.</p>
                </section>

                <script>
                    document.querySelectorAll('[data-tab-tampan]').forEach((wadah) => {
                        const daftar = wadah.querySelector('[role="tablist"]');
                        const tab = [...daftar.querySelectorAll('[role="tab"]')];
                        const pilih = (aktif, fokus = false) => {
                            tab.forEach((satu) => {
                                const terpilih = satu === aktif;
                                satu.setAttribute('aria-selected', String(terpilih));
                                satu.tabIndex = terpilih ? 0 : -1;
                                document.getElementById(satu.getAttribute('aria-controls')).hidden = !terpilih;
                            });
                            if (fokus) aktif.focus();
                        };

                        daftar.dataset.siap = '';
                        pilih(tab[0]);
                        daftar.addEventListener('click', (event) => {
                            const satu = event.target.closest('[role="tab"]');
                            if (satu) pilih(satu);
                        });
                        daftar.addEventListener('keydown', (event) => {
                            const posisi = tab.indexOf(document.activeElement);
                            const tujuan = { ArrowRight: posisi + 1, ArrowLeft: posisi - 1, Home: 0, End: tab.length - 1 }[event.key];
                            if (tujuan === undefined || posisi < 0) return;
                            event.preventDefault();
                            pilih(tab[(tujuan + tab.length) % tab.length], true);
                        });
                    });
                </script>

                {{-- Program pembelian kendaraan baru bersama dealer mitra --}}
                <section id="kendaraan-lpd" aria-labelledby="judul-kendaraan-lpd" class="grid gap-6 scroll-mt-24">
                    <div class="grid gap-2">
                        <h2 id="judul-kendaraan-lpd" class="font-display text-2xl font-bold sm:text-3xl">Program pembelian kendaraan baru</h2>
                        <p class="text-redup">Krama dapat membeli sepeda motor baru melalui Kredit Kendaraan LPD Desa Adat Sangket. LPD bekerja sama dengan dealer resmi terbaik di Singaraja, sehingga pilihan merek dan tipe motor lebih lengkap.</p>
                    </div>

                    <ul class="grid gap-4 sm:grid-cols-2">
                        @foreach ($dealer as $satu)
                            <li class="muncul flex gap-4 overflow-hidden rounded-[1.75rem] bg-panel p-3 ring-1 ring-garis">
                                <a href="{{ $satu['foto'] }}" target="_blank" rel="noopener" class="group relative w-24 shrink-0 self-start overflow-hidden rounded-[1.25rem] bg-latar sm:w-28" aria-label="Lihat banner {{ $satu['nama'] }} ukuran penuh">
                                    <img src="{{ $satu['foto'] }}" alt="{{ $satu['alt'] }}" width="{{ $satu['ukuran'][0] }}" height="{{ $satu['ukuran'][1] }}" loading="lazy" class="h-auto w-full transition-transform duration-500 group-hover:scale-105 motion-reduce:transition-none">
                                </a>
                                <div class="grid min-w-0 content-start gap-2 py-1 pr-1">
                                    <p class="text-xs font-semibold tracking-wide text-langit-tua uppercase">{{ $satu['merek'] }}</p>
                                    <h3 class="font-display text-lg leading-tight font-bold">{{ $satu['nama'] }}</h3>
                                    @if ($satu['alamat'])
                                        <p class="flex gap-1.5 text-sm text-redup">
                                            <svg class="mt-0.5 size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-6.2-7-11.5a7 7 0 0 1 14 0C19 14.8 12 21 12 21Z"/><circle cx="12" cy="9.5" r="2.5"/></svg>
                                            {{ $satu['alamat'] }}, Singaraja
                                        </p>
                                    @endif
                                    <p class="text-sm">{{ $satu['isi'] }}</p>
                                    <ul class="flex flex-wrap gap-1.5" aria-label="Keunggulan {{ $satu['nama'] }}">
                                        @foreach ($satu['sorotan'] as $sorot)
                                            <li class="rounded-full bg-langit-muda px-2.5 py-1 text-xs font-medium text-langit-tua">{{ $sorot }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="flex flex-wrap items-center justify-between gap-4 rounded-[2rem] bg-langit-muda p-6">
                        <div class="grid gap-1">
                            <p class="font-display text-lg font-bold">Tertarik membeli motor baru?</p>
                            <p class="text-sm text-redup">Tanyakan syarat dan simulasi angsuran Kredit Kendaraan di kantor LPD Desa Adat Sangket, Jl. Pratu Ginten No. 1, Sangket.</p>
                        </div>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ rawurlencode('LPD Desa Adat Sangket Sukasada') }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-12 items-center rounded-full bg-langit px-6 font-semibold text-di-langit hover:brightness-95">Petunjuk arah</a>
                    </div>

                    <p class="text-xs text-redup">Promo, harga, dan ketentuan mengikuti masing-masing dealer. Syarat dan ketentuan berlaku.</p>
                </section>

                {{-- Pembagian keuntungan --}}
                <section id="keuntungan-lpd" aria-labelledby="judul-keuntungan-lpd" class="grid gap-6 scroll-mt-24">
                    <div class="grid gap-2">
                        <h2 id="judul-keuntungan-lpd" class="font-display text-2xl font-bold sm:text-3xl">Pembagian keuntungan dan dana pembangunan desa adat</h2>
                        <p class="text-redup">Keuntungan LPD setiap akhir tahun disalurkan sesuai awig-awig. Bagian Desa Adat dipakai untuk pembangunan dan kegiatan di Desa Adat Sangket, dan dana sosial untuk krama di wewidangan desa adat.</p>
                    </div>
                    <div class="grid gap-5 rounded-[2rem] bg-panel p-6 ring-1 ring-garis sm:p-8">
                        <div class="flex h-5 overflow-hidden rounded-full" role="img" aria-label="Pembagian keuntungan LPD: {{ collect($keuntungan)->map(fn ($k) => $k[0].' '.$k[1].'%')->implode(', ') }}">
                            @foreach ($keuntungan as [$label, $persen, $warna])
                                <span style="width: {{ $persen / $total * 100 }}%; background-color: {{ $warna }}" class="h-full border-r-2 border-panel last:border-r-0"></span>
                            @endforeach
                        </div>
                        <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($keuntungan as [$label, $persen, $warna])
                                <li class="flex items-center justify-between gap-3 rounded-2xl bg-latar px-4 py-3 {{ $label === 'Desa Adat' ? 'ring-2 ring-langit' : '' }}">
                                    <span class="flex items-center gap-2 text-sm font-medium"><span class="size-3 rounded-full" style="background-color: {{ $warna }}" aria-hidden="true"></span>{{ $label }}</span>
                                    <span class="font-display text-lg font-extrabold tabular-nums">{{ $persen }}%</span>
                                </li>
                            @endforeach
                        </ul>
                        <p class="text-xs text-redup">Awig-awig LPD Desa Adat Sangket, Pasal 6, ditetapkan dalam paruman 13 Oktober 1999.</p>
                    </div>
                </section>

                {{-- Kegiatan --}}
                <section id="kegiatan-lpd" aria-labelledby="judul-kegiatan-lpd" class="grid gap-6 scroll-mt-24">
                    <div class="grid gap-2">
                        <h2 id="judul-kegiatan-lpd" class="font-display text-2xl font-bold sm:text-3xl">Kegiatan LPD</h2>
                        <p class="text-redup">LPD menerima masukan krama secara terbuka, baik dalam kegiatan sehari-hari maupun melalui rapat tahunan.</p>
                    </div>
                    <ul class="grid gap-4 sm:grid-cols-2">
                        @foreach ($kegiatan as [$foto, $judul, $isi, $alt])
                            <li class="muncul flex flex-col overflow-hidden rounded-[1.75rem] bg-panel ring-1 ring-garis">
                                <img src="{{ $foto }}" alt="{{ $alt }}" loading="lazy" class="aspect-[4/3] w-full object-cover">
                                <div class="grid content-start gap-1.5 p-5">
                                    <h3 class="font-semibold">{{ $judul }}</h3>
                                    <p class="text-sm text-redup">{{ $isi }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>

                <p class="text-sm text-redup">Sumber: Profil Desa Adat Sangket, Awig-awig LPD Desa Adat Sangket, dan dokumentasi LPD.</p>
            </div>

            <x-sidebar-publik label="Navigasi LPD">
                <nav aria-labelledby="judul-daftar-isi-lpd" class="rounded-[1.75rem] bg-panel p-5 ring-1 ring-garis">
                    <h2 id="judul-daftar-isi-lpd" class="font-display text-lg font-bold">Di halaman ini</h2>
                    <ol class="mt-3 grid gap-1 text-sm">
                        @foreach ($daftarIsi as [$id, $label])
                            <li><a href="#{{ $id }}" class="flex min-h-10 items-center rounded-xl px-3 font-medium hover:bg-langit-muda">{{ $label }}</a></li>
                        @endforeach
                    </ol>
                </nav>
            </x-sidebar-publik>
        </div>
    </article>

    @include('publik.lightbox')
</x-layout-publik>
