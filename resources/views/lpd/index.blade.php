{{-- Halaman statis LPD. Sumber: Profil Desa Adat Sangket ed 2026, Awig-awig LPD Desa Adat Sangket 1999, dan blog lpdpakramansangket.blogspot.com. --}}
@php
    $deskripsi = 'LPD Desa Adat Sangket, lembaga keuangan milik desa adat sejak tahun 2000: tabungan, deposito, pinjaman, layanan PPOB, dan dana pembangunan untuk Desa Adat Sangket.';
    $remah = [['Beranda', url('/')], ['Profil Desa Adat', route('profil')], ['LPD Desa Adat Sangket', route('lpd')]];

    $daftarIsi = [
        ['profil-lpd', 'Profil LPD'],
        ['prajuru-lpd', 'Prajuru dan pegawai'],
        ['program-lpd', 'Program dan layanan'],
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
    <article aria-labelledby="judul-lpd">
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
</x-layout-publik>
