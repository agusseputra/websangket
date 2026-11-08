{{-- Halaman statis profil. Sumber isi: "Sistematika Profil Desa Adat Sangket ed 2026" (data krama tahun 2024). --}}
@php
    $deskripsi = 'Profil Desa Adat Sangket, Sukasada, Buleleng: sejarah, prajuru adat, Sabha dan Kertha Desa, pecalang, sekeha gong, yowana, LPD, serta kegiatan Parahyangan, Pawongan, dan Palemahan.';
    $remah = [['Beranda', url('/')], ['Profil Desa Adat', route('profil')]];

    $daftarIsi = [
        ['tentang', 'Tentang desa adat'],
        ['sejarah', 'Sejarah singkat'],
        ['tradisi', 'Tradisi dan potensi'],
        ['prajuru', 'Struktur prajuru adat'],
        ['lembaga', 'Lembaga adat'],
        ['kegiatan', 'Kegiatan rutin'],
        ['hukum', 'Awig-awig dan pararem'],
    ];

    $identitas = [
        ['Kode desa adat', '0067'],
        ['Nomor register', '01.003.0067'],
        ['Kategori wilayah', 'Tepat satu desa dinas'],
        ['Kecamatan', 'Sukasada, Kabupaten Buleleng'],
        ['Sekretariat', 'Wantilan Desa Adat Sangket'],
    ];

    $batas = [
        ['Utara', 'Batang Banua, Desa Adat Sukasada'],
        ['Timur', 'Desa Adat Runuh'],
        ['Selatan', 'Desa Adat Lumbanan'],
        ['Barat', 'Desa Adat Sambangan'],
    ];

    $krama = [
        ['584', 'Krama desa adat (mipil)', '292 lanang, 292 istri'],
        ['568', 'Krama tamiu', '284 lanang, 284 istri'],
        ['10 KK', 'Tamiu', 'Hasil pendataan 2024'],
    ];

    $kahyangan = [
        ['Pura Desa', 'Wrespati Dungulan', 'Setiap 6 bulan'],
        ['Pura Bukit Sekar Kencana', 'Buda Julungwangi', 'Setiap 6 bulan'],
        ['Pura Dalem', 'Saniscara Kuningan', 'Setiap 6 bulan'],
        ['Pura Mangening', 'Purnama Kedasa', 'Setiap tahun'],
        ['Padmasana ring Wantilan', 'Rahina Saraswati', 'Setiap 6 bulan'],
    ];

    $prajuru = [
        'bandesa' => ['Bandesa Adat', 'I Nyoman Suarjana, SE'],
        'inti' => [
            ['Petajuh', 'Ir. Ketut Agus Seputra, S.ST., M.T'],
            ['Penyarikan', 'I Gede Mulyawan, S.Ag., M.Pd'],
            ['Patengen', 'Made Darma Semadi, SH'],
        ],
        'banjar' => [
            ['Kelian Banjar Adat Dauh Margi', 'Made Sudarma'],
            ['Kelian Banjar Adat Dangin Margi', 'Komang Suryawan'],
        ],
        'parahyangan' => ['Drs. Gede Tinggen', 'Drs. Nyoman Wijana'],
    ];

    $tugasPrajuru = [
        'Menyusun rencana strategis, program pembangunan, dan anggaran desa adat.',
        'Melaksanakan program melalui kegiatan Parahyangan, Pawongan, dan Palemahan.',
        'Melaksanakan awig-awig dan pararem desa adat.',
        'Menyelesaikan perkara adat (wicara) di wewidangan desa adat.',
        'Mengatur kegiatan sosial dan keagamaan sesuai susastra agama dan tradisi.',
        'Melaporkan hasil pelaksanaan program dalam Paruman Desa Adat.',
    ];

    $lembaga = [
        [
            'id' => 'kertha-desa',
            'nama' => 'Kertha Desa',
            'ikon' => 'M12 3v18M5 7h14M7 7l-3 7a3 3 0 0 0 6 0L7 7Zm10 0-3 7a3 3 0 0 0 6 0l-3-7ZM8 21h8',
            'deskripsi' => 'Lembaga mitra kerja prajuru yang menjalankan fungsi penyelesaian perkara adat (wicara) berdasarkan awig-awig Desa Adat Sangket.',
            'fungsi' => [
                'Menerima, memeriksa, dan menyelesaikan perkara adat di wewidangan desa adat.',
                'Menjadi tempat keberatan krama atas keputusan dalam pelaksanaan pararem.',
                'Memberi pertimbangan hukum adat dalam penyusunan dan penerapan pararem.',
            ],
            'pengurus' => ['Ketua' => 'Dr. I Nyoman Gede Remaja, S.H., M.H.'],
            'anggota' => ['I Nyoman Suarjana, S.E.', 'Ketut Ariawan, S.H., M.Pd.H.', 'Nyoman Tomas Dritawan, S.H.', 'Putu Mariada'],
            'dasar' => 'Keputusan Bandesa Nomor 04/DAS/III/2021',
        ],
        [
            'id' => 'sabha-desa',
            'nama' => 'Sabha Desa',
            'ikon' => 'M17 20v-1a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v1M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm11 9v-1a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8',
            'deskripsi' => 'Lembaga mitra kerja prajuru yang menjalankan fungsi pertimbangan dalam pengelolaan desa adat.',
            'fungsi' => [
                'Memberi pertimbangan atas rencana program dan anggaran desa adat.',
                'Menampung dan menyalurkan aspirasi krama kepada prajuru.',
                'Ikut membahas rancangan pararem sebelum ditetapkan dalam paruman.',
            ],
            'pengurus' => ['Ketua' => 'Drs. Nyoman Wijana'],
            'anggota' => ['Ketut Sedana', 'Komang Ginanta', 'Made Artana', 'Gede Budarma', 'Nyoman Darmada', 'Made Amerta Yasa', 'Ketut Putra Yasa', 'Nyoman Legawa', 'Ketut Gelgel', 'Ketut Merta', 'Gede Widiada', 'Made Merta', 'Made Sumarma', 'I Made Teja, S.Sos', 'Made Tirta Tisna', 'Nyoman Tinggen', 'Dr. Gede Sandiasa, S.Sos., M.Si.'],
            'dasar' => 'Keputusan Bandesa Nomor 04/DAS/III/2021',
        ],
        [
            'id' => 'pecalang',
            'nama' => 'Pecalang',
            'ikon' => 'M12 3 4 6v6c0 5 3.4 8.4 8 9 4.6-.6 8-4 8-9V6l-8-3Zm-3 9 2 2 4-4',
            'deskripsi' => 'Paiketan Pacalang menjaga keamanan dan ketertiban tradisional, termasuk menangani masalah di wewidangan Desa Adat Sangket.',
            'fungsi' => [
                'Mengamankan jalannya piodalan, pecaruan, dan Hari Suci Nyepi.',
                'Melaksanakan pendataan krama tamiu dan tamiu bersama tim kasukretan.',
                'Menangani gangguan keamanan dan ketertiban di wewidangan desa adat.',
            ],
            'pengurus' => ['Ketua' => 'Gede Sami Mulata', 'Wakil Ketua' => 'Putu Gede Suharta Yasa', 'Sekretaris' => 'Putu Edy Mastika', 'Bendahara' => 'Gede Agus Suandana', 'Juru Arah' => 'Gede Buda Arta', 'Perlengkapan' => 'Nyoman Yastara'],
            'dasar' => 'Keputusan Bandesa Nomor 06/DAS/III/2021',
            'foto' => ['/images/profil/pecalang.jpg', 'Pecalang Desa Adat Sangket berbusana kamen poleng berfoto di Monumen Bajra Sandhi'],
        ],
        [
            'id' => 'sekeha-gong',
            'nama' => 'Sekeha Gong',
            'ikon' => 'M9 18V5l12-2v13M9 18a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm12-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
            'deskripsi' => 'Wadah krama mengembangkan kreativitas seni, adat, budaya, dan tabuh yang bermuatan kearifan lokal.',
            'fungsi' => [
                'Mengampu kewajiban nabuh setiap piodalan di pura kahyangan desa.',
                'Mengiringi tari wali dan pementasan seni saat upacara.',
                'Membina generasi muda dalam seni tabuh gamelan Bali.',
            ],
            'pengurus' => ['Ketua' => 'Nyoman Tinggen', 'Wakil Ketua' => 'Made Mertada', 'Sekretaris' => 'Ketut Aryawan Sudarsana', 'Bendahara' => 'Ketut Karya'],
            'dasar' => 'Keputusan Bandesa Nomor 08/DAS/III/2021',
            'foto' => ['/images/profil/sekeha-gong.jpg', 'Sekeha Gong Desa Adat Sangket berbusana adat berfoto di depan candi bentar pura'],
        ],
        [
            'id' => 'yowana',
            'nama' => 'Yowana',
            'ikon' => 'M12 2l2.4 4.9 5.4.8-3.9 3.8.9 5.4L12 14.3l-4.8 2.6.9-5.4-3.9-3.8 5.4-.8L12 2ZM5 22h14',
            'deskripsi' => 'Paiketan Yowana Manik Hasta Gina menghimpun pemuda adat dalam bidang agama, tradisi, seni budaya, pendidikan, olahraga, ekonomi, dan kesehatan.',
            'fungsi' => [
                'Ngayah dan berperan aktif dalam setiap upacara upakara.',
                'Menyelenggarakan kreasi ogoh-ogoh menjelang Nyepi.',
                'Menggerakkan kegiatan seni, olahraga, dan kepemudaan adat.',
            ],
            'pengurus' => ['Kelian' => 'Kadek Setia Kusuma Darma Yoga', 'Wakil' => 'Kadek Wahyu Tresna', 'Sekretaris' => 'Putu Wiwin Arianti', 'Bendahara' => 'Komang Winda Wedayanti'],
            'dasar' => 'Keputusan Bandesa Nomor 4/DAS/I/2024',
            'foto' => ['/images/profil/festival-ogoh-ogoh.jpg', 'Anggota Yowana Manik Hasta Gina berfoto bersama prajuru di depan ogoh-ogoh'],
        ],
        [
            'id' => 'lpd',
            'nama' => 'LPD Desa Adat Sangket',
            'ikon' => 'M3 21h18M5 21V10m4 11V10m6 11V10m4 11V10M2 10l10-6 10 6H2Z',
            'deskripsi' => 'Lembaga Perkreditan Desa yang berdiri sejak tahun 2000 untuk menunjang perekonomian krama, dikelola secara transparan dengan sistem keuangan terkomputerisasi.',
            'fungsi' => [
                'Melayani simpanan dan pinjaman krama desa adat.',
                'Mendukung pendanaan kegiatan dan pembangunan desa adat.',
                'Menerima masukan krama secara terbuka, termasuk melalui rapat tahunan.',
            ],
            'pengurus' => ['Ketua' => 'Made Budiasa, S.E.', 'Sekretaris' => 'Ngurah Putu Arka Rai', 'Bendahara' => 'I Gusti Ayu Mas Candrawati'],
            'anggota' => ['Pegawai: Luh Sukreni, Putu Laba, Ketut Serina, Ketut Widiada, Made Gede Somadata, Ketut Rustini', 'Pengawas: I Nyoman Suarjana, S.E.; Nyoman Tomas Dritawan, S.H.; Dr. Gede Sandiasa, S.Sos., M.Si.'],
            'dasar' => 'Berdiri tahun 2000',
            'foto' => ['/images/lpd/rat-meja-pengurus.jpg', 'Rapat tahunan LPD Desa Adat Sangket 20 Februari 2026 di wantilan'],
        ],
        [
            'id' => 'paiketan-sarati',
            'nama' => 'Paiketan Sarati',
            'ikon' => 'M12 21c-4-3-7-6.5-7-10a7 7 0 0 1 14 0c0 3.5-3 7-7 10Zm0-7a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z',
            'deskripsi' => 'Menuntun dan memberi petunjuk dalam melengkapi sarana prasarana upakara Panca Yadnya di Desa Adat Sangket.',
            'fungsi' => [
                'Menyiapkan dan menuntun pembuatan banten untuk upacara desa.',
                'Membina krama istri dalam keterampilan upakara.',
            ],
            'pengurus' => ['Ketua' => 'Luh Sedani', 'Wakil Ketua' => 'Luh Budarini', 'Sekretaris' => 'Made Anggreni'],
            'dasar' => 'Keputusan Bandesa Nomor 05/DAS/III/2021',
        ],
    ];

    $kegiatan = [
        [
            'baga' => 'Parahyangan',
            'arti' => 'Hubungan harmonis krama dengan Ida Sang Hyang Widhi Wasa',
            'warna' => 'bg-sky-700',
            'isi' => [
                ['Piodalan di empat pura kahyangan desa', 'Pura Desa, Pura Bukit Sekar Kencana, Pura Dalem, dan Pura Mangening diupacarai sesuai pawukon dan sasih, didukung ngayah krama dan dudukan.', '/images/landing/dudonan-piodalan.jpg', 'Krama berbusana adat mengiringi dudonan piodalan dengan tedung kuning'],
                ['Pecaruan tawur kesanga', 'Menjelang Nyepi, krama melaksanakan pecaruan untuk menyeimbangkan bhuana agung dan bhuana alit, diamankan oleh pecalang.', '/images/profil/tawur-kesanga.jpg', 'Krama berbusana putih mengikuti upacara tawur kesanga di depan pura'],
            ],
        ],
        [
            'baga' => 'Pawongan',
            'arti' => 'Hubungan harmonis antarsesama krama',
            'warna' => 'bg-sky-500',
            'isi' => [
                ['Bulan Bahasa Bali', 'Setiap Februari, anak-anak dan yowana diajak menghidupkan bahasa, aksara, dan sastra Bali melalui lomba dan pentas.', '/images/profil/bulan-bahasa-bali.jpg', 'Para juara lomba Bulan Bahasa Bali VIII Desa Adat Sangket 2026 berfoto bersama prajuru'],
                ['Pesraman', 'Pesraman Remaja Widya Bhakti membina remaja dalam ajaran agama, tari, tabuh, dan upakara.', '/images/profil/pesraman-remaja.jpg', 'Peserta Pasraman Remaja Widya Bhakti berbusana tari berfoto di panggung'],
                ['Pembinaan prajuru dan lembaga adat', 'Sosialisasi dan paruman untuk menyamakan pemahaman awig-awig, pararem, dan program desa adat.', '/images/profil/paruman-prajuru.jpg', 'Prajuru dan lembaga adat duduk bersila dalam paruman malam hari di bale pura'],
                ['Festival ogoh-ogoh', 'Yowana berkreasi membuat ogoh-ogoh dan mengaraknya pada malam pengerupukan.', '/images/profil/festival-ogoh-ogoh.jpg', 'Yowana Manik Hasta Gina berfoto di depan ogoh-ogoh saat festival ogoh-ogoh'],
            ],
        ],
        [
            'baga' => 'Palemahan',
            'arti' => 'Hubungan harmonis krama dengan lingkungan',
            'warna' => 'bg-green-600',
            'isi' => [
                ['Gotong royong pembersihan pura dan wewidangan', 'Krama ngayah membersihkan pura kahyangan desa, setra, wantilan, dan lingkungan, sejalan dengan Pararem Pengelolaan Sampah Berbasis Sumber.', null, null],
            ],
        ],
    ];

    $pararem = [
        'Pararem Pengele Nomor 01 Tahun 2020',
        'Pararem Pencegahan Narkoba',
        'Pararem Rabies',
        'Pararem Pengelolaan Sampah Berbasis Sumber Nomor 01 Tahun 2025',
        'Pararem Kasukretan Krama Nomor 02 Tahun 2025',
    ];

    $seo = [
        'judul' => 'Profil Desa Adat Sangket – Sejarah, Prajuru, dan Lembaga Adat',
        'deskripsi' => $deskripsi,
        'canonical' => route('profil'),
        'gambar' => url('/images/profil/melasti.jpg'),
        'gambar_alt' => 'Krama Desa Adat Sangket mengikuti upacara melasti',
        'jsonld' => [
            [
                '@context' => 'https://schema.org',
                '@type' => 'AboutPage',
                'name' => 'Profil Desa Adat Sangket',
                'url' => route('profil'),
                'description' => $deskripsi,
                'inLanguage' => 'id-ID',
                'about' => [
                    '@type' => 'Organization',
                    'name' => 'Desa Adat Sangket',
                    'url' => url('/'),
                    'logo' => url('/images/logo-desa-adat-sangket.png'),
                    'address' => ['@type' => 'PostalAddress', 'streetAddress' => 'Wantilan Desa Adat Sangket', 'addressLocality' => 'Sukasada', 'addressRegion' => 'Bali', 'addressCountry' => 'ID'],
                    'location' => ['@type' => 'Place', 'geo' => ['@type' => 'GeoCoordinates', 'latitude' => -8.148488, 'longitude' => 115.108454]],
                ],
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
    <article aria-labelledby="judul-profil">
        <header class="relative -mt-20 overflow-hidden bg-gradient-to-b from-langit-sedang via-langit-muda to-latar pt-24">
            <div aria-hidden="true" class="pointer-events-none absolute -top-40 -left-32 size-[34rem] rounded-full bg-langit/30 blur-3xl"></div>
            <div class="relative mx-auto grid max-w-6xl gap-8 px-4 pt-8 pb-10 lg:grid-cols-[1.15fr_1fr] lg:items-center lg:px-6">
                <div class="grid content-start gap-5">
                    @include('artikel.remah', ['remah' => $remah])
                    <p class="justify-self-start rounded-full bg-panel px-3 py-1 text-sm font-semibold text-langit-tua ring-1 ring-langit-sedang">Kecamatan Sukasada, Kabupaten Buleleng, Bali</p>
                    <h1 id="judul-profil" class="font-display text-4xl leading-[1.05] font-extrabold tracking-tight text-balance sm:text-6xl">Profil Desa Adat <span class="text-langit-tua">Sangket</span></h1>
                    <p class="max-w-xl text-lg text-redup">Desa adat tua yang berdiri dengan konsep Kahyangan Tiga Mpu Kuturan, dijiwai filosofi Tri Hita Karana dan kearifan lokal Sad Kerthi.</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <figure class="col-span-2 overflow-hidden rounded-[2rem] shadow-xl shadow-langit/10">
                        <img src="/images/profil/melasti.jpg" alt="Krama Desa Adat Sangket berbusana putih mengikuti upacara melasti dengan tedung kuning" width="1400" height="787" fetchpriority="high" class="aspect-[16/9] w-full object-cover">
                    </figure>
                    <figure class="overflow-hidden rounded-[1.5rem]">
                        <img src="/images/landing/palinggih-mobil.jpg" alt="Palinggih Mobil, ciri khas Desa Adat Sangket" width="640" height="360" class="aspect-[4/3] w-full object-cover">
                    </figure>
                    <figure class="overflow-hidden rounded-[1.5rem]">
                        <img src="/images/profil/paruman-desa.jpg" alt="Paruman krama di Wantilan Desa Adat Sangket" width="1000" height="991" class="aspect-[4/3] w-full object-cover">
                    </figure>
                </div>
            </div>
        </header>

        <div class="mx-auto grid max-w-6xl gap-10 px-4 lg:grid-cols-[minmax(0,1fr)_20rem] lg:px-6">
            <div class="grid min-w-0 content-start gap-14">
                {{-- Tentang --}}
                <section id="tentang" aria-labelledby="judul-tentang" class="grid gap-6 scroll-mt-24">
                    <h2 id="judul-tentang" class="font-display text-2xl font-bold sm:text-3xl">Tentang Desa Adat Sangket</h2>
                    <div class="isi-artikel grid gap-4 text-redup">
                        <p class="ql-align-justify">Desa Adat Sangket adalah kesatuan masyarakat hukum adat di Kelurahan Sukasada yang memiliki hak asal usul, hak tradisional, dan hak otonomi asli untuk mengatur rumah tangganya sendiri, sebagaimana diakui Peraturan Daerah Provinsi Bali Nomor 4 Tahun 2019 tentang Desa Adat di Bali.</p>
                        <p class="ql-align-justify">Profil ini disusun sebagai catatan sejarah, sumber daya, kekayaan, dan kearifan lokal desa adat agar tidak tergerus perubahan zaman, sekaligus menjadi acuan perencanaan pembangunan dan pemberdayaan desa adat.</p>
                    </div>

                    <dl class="grid gap-4 sm:grid-cols-3">
                        @foreach ($krama as [$angka, $label, $rincian])
                            <div class="grid gap-1 rounded-[1.5rem] bg-panel p-5 ring-1 ring-garis">
                                <dt class="order-2 text-sm font-semibold">{{ $label }}</dt>
                                <dd class="order-1 font-display text-3xl font-extrabold text-langit-tua tabular-nums">{{ $angka }}</dd>
                                <dd class="order-3 text-sm text-redup">{{ $rincian }}</dd>
                            </div>
                        @endforeach
                    </dl>
                    <p class="-mt-3 text-xs text-redup">Data krama tahun 2024.</p>

                    <div class="grid gap-6 rounded-[2rem] bg-panel p-6 ring-1 ring-garis sm:p-8 md:grid-cols-2">
                        <div class="grid content-start gap-3">
                            <h3 class="font-display text-lg font-bold">Identitas</h3>
                            <dl class="grid gap-2 text-sm">
                                @foreach ($identitas as [$label, $nilai])
                                    <div class="grid grid-cols-[9rem_1fr] gap-2">
                                        <dt class="text-redup">{{ $label }}</dt>
                                        <dd class="font-medium">{{ $nilai }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                        <div class="grid content-start gap-3">
                            <h3 class="font-display text-lg font-bold">Wates wewidangan</h3>
                            <dl class="grid grid-cols-2 gap-3 text-sm">
                                @foreach ($batas as [$arah, $nilai])
                                    <div class="rounded-2xl bg-langit-muda p-3">
                                        <dt class="text-xs font-semibold tracking-wide text-langit-tua uppercase">{{ $arah }}</dt>
                                        <dd class="mt-0.5 font-medium">{{ $nilai }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    </div>

                    <figure class="grid gap-3">
                        <img src="/images/profil/peta-wewidangan.jpg" alt="Citra satelit wewidangan Desa Adat Sangket dengan batas wilayah bergaris kuning dan letak Pura Desa, Pura Bukit Sekar Kencana, Pura Dalem, Pura Mangening, setra, serta wantilan" width="1600" height="825" loading="lazy" class="w-full rounded-[2rem] object-cover shadow-xl shadow-langit/10">
                        <figcaption class="text-sm text-redup">Peta wewidangan Desa Adat Sangket. Kantor desa adat berada di koordinat -8.148488, 115.108454.</figcaption>
                    </figure>
                </section>

                {{-- Sejarah --}}
                <section id="sejarah" aria-labelledby="judul-sejarah" class="grid gap-6 scroll-mt-24">
                    <h2 id="judul-sejarah" class="font-display text-2xl font-bold sm:text-3xl">Sejarah singkat</h2>
                    <div class="isi-artikel rounded-[2rem] bg-panel p-6 ring-1 ring-garis sm:p-8">
                        <p class="ql-align-justify">Prasasti maupun sumber sastra tentang berdirinya Desa Adat Sangket belum ditemukan. Namun peninggalan pura dan sejumlah informasi pendukung memberi petunjuk tentang usianya:</p>
                        <ul>
                            <li>Tidak ada palinggih Padmasana di Pura Desa maupun pura Kahyangan Tiga lainnya. Ini menunjukkan desa adat berdiri dengan konsep Kahyangan Tiga Mpu Kuturan, jauh sebelum kedatangan Dang Hyang Nirartha.</li>
                            <li>Buku <em>I Gusti Anglurah Panji Sakti, Raja Buleleng 1599–1780</em> karya Dr. Soegianto Sastrodiwiryo menyebut perjanjian Panji Sakti dengan Sang Brahmana Peranda Sakti Ngurah di tempat yang kini bernama Sangket.</li>
                            <li>Sejumlah tempat di Sukasada memakai nama Sangket, seperti Geria Sangket, Peken Sangket, dan Terminal Sangket.</li>
                        </ul>
                        <blockquote>
                            <p>Krama bersepakat bahwa nama Sangket berasal dari <strong>Taru Wali atau Kayu Selipet</strong>. <em>Kayu</em> bermakna <em>pakayunan</em> (pikiran) dan <em>selipet</em> bermakna kembali, yaitu kembalinya umat Hindu dari berbagai sekte menjadi satu dalam Tri Murti sejak kehadiran Mpu Kuturan.</p>
                        </blockquote>
                        <p class="ql-align-justify">Dengan demikian Desa Adat Sangket diyakini sudah ada jauh sebelum Kerajaan Buleleng berdiri, sejak Mpu Kuturan membentuk desa pakraman dengan Kahyangan Tiganya.</p>
                    </div>
                </section>

                {{-- Tradisi dan potensi --}}
                <section id="tradisi" aria-labelledby="judul-tradisi" class="grid gap-6 scroll-mt-24">
                    <h2 id="judul-tradisi" class="font-display text-2xl font-bold sm:text-3xl">Tradisi dan potensi</h2>
                    <p class="text-redup">Krama menjaga, melestarikan, dan melaksanakan upacara di pura kahyangan desa sesuai awig-awig. Pura ibu, sanggah kemulan, pura subak, dan pura keluarga menjadi tanggung jawab pengempon masing-masing.</p>

                    <div class="overflow-hidden rounded-[2rem] bg-panel ring-1 ring-garis">
                        <table class="w-full text-left text-sm">
                            <caption class="sr-only">Jadwal piodalan pura kahyangan desa</caption>
                            <thead class="bg-langit-muda text-langit-tua">
                                <tr>
                                    <th scope="col" class="px-5 py-3 font-semibold">Pura</th>
                                    <th scope="col" class="px-5 py-3 font-semibold">Piodalan</th>
                                    <th scope="col" class="hidden px-5 py-3 font-semibold sm:table-cell">Siklus</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-garis">
                                @foreach ($kahyangan as [$pura, $piodalan, $siklus])
                                    <tr>
                                        <th scope="row" class="px-5 py-3 font-semibold">{{ $pura }}</th>
                                        <td class="px-5 py-3">{{ $piodalan }}<span class="block text-xs text-redup sm:hidden">{{ $siklus }}</span></td>
                                        <td class="hidden px-5 py-3 text-redup sm:table-cell">{{ $siklus }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="grid gap-4 md:grid-cols-3">
                        <div class="grid content-start gap-2 rounded-[1.5rem] bg-panel p-5 ring-1 ring-garis">
                            <h3 class="font-display text-lg font-bold">Seni dan budaya</h3>
                            <p class="text-sm text-redup">Palinggih Mobil yang khas, tabuh sekeha gong, serta tari dan pesraman remaja menjadi kekuatan budaya Sangket.</p>
                        </div>
                        <div class="grid content-start gap-2 rounded-[1.5rem] bg-panel p-5 ring-1 ring-garis">
                            <h3 class="font-display text-lg font-bold">Padruwen desa</h3>
                            <p class="text-sm text-redup">Selain pura kahyangan, desa adat memiliki satu setra dan Wantilan Desa Adat yang menjadi pusat paruman dan kegiatan.</p>
                        </div>
                        <div class="grid content-start gap-2 rounded-[1.5rem] bg-panel p-5 ring-1 ring-garis">
                            <h3 class="font-display text-lg font-bold">Ekonomi krama</h3>
                            <p class="text-sm text-redup">LPD Desa Adat Sangket sejak tahun 2000 menopang perekonomian krama. Wilayah ini belum memiliki daya tarik wisata alam.</p>
                        </div>
                    </div>
                </section>

                {{-- Struktur prajuru --}}
                <section id="prajuru" aria-labelledby="judul-prajuru" class="grid gap-6 scroll-mt-24">
                    <h2 id="judul-prajuru" class="font-display text-2xl font-bold sm:text-3xl">Struktur prajuru adat</h2>
                    <p class="text-redup">Prajuru adalah pengurus desa adat. Sesuai Perda Bali Nomor 4 Tahun 2019 Pasal 28, prajuru paling sedikit terdiri atas bandesa, patajuh, panyarikan, dan patengen.</p>

                    <figure class="grid gap-4 rounded-[2rem] bg-panel p-6 ring-1 ring-garis sm:p-8" aria-label="Bagan struktur prajuru Desa Adat Sangket">
                        {{-- Bandesa di tengah; Baga Parahyangan sejajar di sampingnya dengan garis putus-putus sebagai penasihat. --}}
                        <div class="grid items-center justify-items-center gap-0 md:grid-cols-[1fr_minmax(0,15rem)_1fr]">
                            <div class="grid w-full max-w-xs justify-items-center gap-1 rounded-[1.5rem] bg-gradient-to-br from-langit to-langit-sedang p-5 text-center text-di-langit md:col-start-2">
                                <span class="text-xs font-semibold tracking-wide uppercase">{{ $prajuru['bandesa'][0] }}</span>
                                <span class="font-display text-lg font-bold">{{ $prajuru['bandesa'][1] }}</span>
                            </div>
                            <div class="flex w-full flex-col items-center md:col-start-3 md:flex-row">
                                <span aria-hidden="true" class="h-8 border-l-2 border-dashed border-langit-tua md:h-0 md:w-8 md:shrink-0 md:border-t-2 md:border-l-0"></span>
                                <div class="grid w-full max-w-xs justify-items-center gap-1 rounded-[1.25rem] border-2 border-dashed border-langit-tua/50 bg-white px-3 py-4 text-center text-[#1d2a30] shadow-sm">
                                    <span class="text-xs font-semibold tracking-wide text-[#0369a1] uppercase">Baga Parahyangan</span>
                                    @foreach ($prajuru['parahyangan'] as $nama)
                                        <span class="text-sm font-semibold md:whitespace-nowrap">{{ $nama }}</span>
                                    @endforeach
                                    <span class="text-xs text-[#5a6b73]">Penasihat</span>
                                </div>
                            </div>
                        </div>
                        <div aria-hidden="true" class="mx-auto h-5 w-px bg-garis"></div>
                        <ul class="grid gap-3 sm:grid-cols-3">
                            @foreach ($prajuru['inti'] as [$jabatan, $nama])
                                <li class="grid justify-items-center gap-1 rounded-[1.25rem] bg-langit-muda p-4 text-center">
                                    <span class="text-xs font-semibold tracking-wide text-langit-tua uppercase">{{ $jabatan }}</span>
                                    <span class="font-semibold">{{ $nama }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <div aria-hidden="true" class="mx-auto h-5 w-px bg-garis"></div>
                        <ul class="grid gap-3 sm:grid-cols-2">
                            @foreach ($prajuru['banjar'] as [$jabatan, $nama])
                                <li class="grid justify-items-center gap-1 rounded-[1.25rem] p-4 text-center ring-1 ring-garis">
                                    <span class="text-xs font-semibold tracking-wide text-redup uppercase">{{ $jabatan }}</span>
                                    <span class="font-semibold">{{ $nama }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <figcaption class="flex flex-wrap items-center justify-center gap-x-5 gap-y-1 text-xs text-redup">
                            <span class="flex items-center gap-2"><span aria-hidden="true" class="inline-block h-0 w-6 border-t-2 border-garis"></span>Garis komando</span>
                            <span class="flex items-center gap-2"><span aria-hidden="true" class="inline-block h-0 w-6 border-t-2 border-dashed border-langit-tua/60"></span>Garis koordinasi/penasihat</span>
                        </figcaption>
                    </figure>

                    <div class="rounded-[2rem] bg-panel p-6 ring-1 ring-garis sm:p-8">
                        <h3 class="font-display text-lg font-bold">Tugas dan kewajiban prajuru</h3>
                        <ol class="mt-3 grid gap-2 text-sm text-redup sm:grid-cols-2">
                            @foreach ($tugasPrajuru as $tugas)
                                <li class="flex gap-3">
                                    <span class="grid size-6 shrink-0 place-items-center rounded-full bg-langit-muda text-xs font-bold text-langit-tua">{{ $loop->iteration }}</span>
                                    {{ $tugas }}
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </section>

                {{-- Lembaga adat --}}
                <section id="lembaga" aria-labelledby="judul-lembaga" class="grid gap-6 scroll-mt-24">
                    <h2 id="judul-lembaga" class="font-display text-2xl font-bold sm:text-3xl">Lembaga adat</h2>
                    <p class="text-redup">Lembaga mitra prajuru yang menggerakkan kegiatan adat sesuai bidangnya masing-masing.</p>

                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ($lembaga as $satu)
                            <article id="{{ $satu['id'] }}" aria-labelledby="judul-{{ $satu['id'] }}" class="muncul grid content-start gap-4 rounded-[2rem] bg-panel p-6 ring-1 ring-garis scroll-mt-24">
                                @isset($satu['foto'])
                                    <img src="{{ $satu['foto'][0] }}" alt="{{ $satu['foto'][1] }}" loading="lazy" class="-mx-6 -mt-6 aspect-[16/9] w-[calc(100%+3rem)] max-w-none rounded-t-[2rem] object-cover">
                                @endisset
                                <header class="flex items-center gap-3">
                                    <span class="grid size-12 shrink-0 place-items-center rounded-2xl bg-langit-muda text-langit-tua">
                                        <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $satu['ikon'] }}"/></svg>
                                    </span>
                                    <div>
                                        <h3 id="judul-{{ $satu['id'] }}" class="font-display text-xl font-bold">{{ $satu['nama'] }}</h3>
                                        <p class="text-xs text-redup">{{ $satu['dasar'] }}</p>
                                    </div>
                                </header>
                                <p class="text-sm text-redup">{{ $satu['deskripsi'] }}</p>
                                <div class="grid gap-2">
                                    <h4 class="text-sm font-semibold">Fungsi dalam kegiatan adat</h4>
                                    <ul class="grid gap-1.5 text-sm">
                                        @foreach ($satu['fungsi'] as $fungsi)
                                            <li class="flex gap-2"><span class="mt-2 size-1.5 shrink-0 rounded-full bg-langit" aria-hidden="true"></span>{{ $fungsi }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <dl class="grid gap-1 rounded-2xl bg-latar p-4 text-sm">
                                    @foreach ($satu['pengurus'] as $jabatan => $nama)
                                        <div class="flex flex-wrap justify-between gap-x-3">
                                            <dt class="text-redup">{{ $jabatan }}</dt>
                                            <dd class="font-medium">{{ $nama }}</dd>
                                        </div>
                                    @endforeach
                                </dl>
                                @if ($satu['id'] === 'lpd')
                                    <a href="{{ route('lpd') }}" class="inline-flex min-h-11 items-center justify-center rounded-full bg-langit px-5 text-sm font-semibold text-di-langit hover:brightness-95">Selengkapnya tentang LPD</a>
                                @endif
                                @if (! empty($satu['anggota']))
                                    <details class="group text-sm">
                                        <summary class="cursor-pointer font-semibold text-langit-tua">Lihat anggota ({{ count($satu['anggota']) }})</summary>
                                        <ul class="mt-2 grid gap-1 text-redup">
                                            @foreach ($satu['anggota'] as $anggota)
                                                <li>{{ $anggota }}</li>
                                            @endforeach
                                        </ul>
                                    </details>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </section>

                {{-- Kegiatan rutin --}}
                <section id="kegiatan" aria-labelledby="judul-kegiatan" class="grid gap-8 scroll-mt-24">
                    <div class="grid gap-2">
                        <h2 id="judul-kegiatan" class="font-display text-2xl font-bold sm:text-3xl">Kegiatan rutin</h2>
                        <p class="text-redup">Program desa adat dijalankan dalam tiga baga sesuai filosofi Tri Hita Karana.</p>
                    </div>

                    @foreach ($kegiatan as $baga)
                        <section aria-labelledby="baga-{{ $loop->index }}" class="grid gap-4">
                            <header class="flex items-center gap-3">
                                <span class="h-8 w-1.5 rounded-full {{ $baga['warna'] }}" aria-hidden="true"></span>
                                <div>
                                    <h3 id="baga-{{ $loop->index }}" class="font-display text-xl font-bold">{{ $baga['baga'] }}</h3>
                                    <p class="text-sm text-redup">{{ $baga['arti'] }}</p>
                                </div>
                            </header>
                            <ul class="grid gap-4 {{ count($baga['isi']) > 1 ? 'sm:grid-cols-2' : '' }}">
                                @foreach ($baga['isi'] as [$judul, $isi, $foto, $altFoto])
                                    <li class="muncul flex flex-col overflow-hidden rounded-[1.75rem] bg-panel ring-1 ring-garis {{ count($baga['isi']) === 1 ? 'sm:flex-row' : '' }}">
                                        @if ($foto)
                                            <img src="{{ $foto }}" alt="{{ $altFoto }}" loading="lazy" class="aspect-[16/10] w-full object-cover {{ count($baga['isi']) === 1 ? 'sm:w-1/2' : '' }}">
                                        @endif
                                        <div class="grid content-start gap-1.5 p-5">
                                            <h4 class="font-semibold">{{ $judul }}</h4>
                                            <p class="text-sm text-redup">{{ $isi }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endforeach
                </section>

                {{-- Hukum adat --}}
                <section id="hukum" aria-labelledby="judul-hukum" class="grid gap-6 scroll-mt-24">
                    <h2 id="judul-hukum" class="font-display text-2xl font-bold sm:text-3xl">Awig-awig dan pararem</h2>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="grid content-start gap-2 rounded-[1.75rem] bg-panel p-6 ring-1 ring-garis">
                            <h3 class="font-display text-lg font-bold">Awig-awig</h3>
                            <p class="text-sm text-redup">Aturan desa adat yang berlaku bagi krama desa adat, krama tamiu, dan tamiu, sesuai desa, kala, dan patra. Awig-awig Desa Adat Sangket disahkan pada 6 Desember 1995 dengan nomor registrasi 03 Tahun 1995.</p>
                        </div>
                        <div class="grid content-start gap-2 rounded-[1.75rem] bg-panel p-6 ring-1 ring-garis">
                            <h3 class="font-display text-lg font-bold">Pararem</h3>
                            <p class="text-sm text-redup">Keputusan Paruman Desa Adat untuk melaksanakan awig-awig, mengatur hal baru, atau menyelesaikan perkara adat.</p>
                            <ul class="mt-1 grid gap-1.5 text-sm">
                                @foreach ($pararem as $satu)
                                    <li class="flex gap-2"><span class="mt-2 size-1.5 shrink-0 rounded-full bg-langit" aria-hidden="true"></span>{{ $satu }}</li>
                                @endforeach
                            </ul>
                            <a href="{{ route('artikel.daftar', 'perarem') }}" class="mt-2 text-sm font-semibold text-langit-tua underline underline-offset-4">Baca artikel perarem</a>
                        </div>
                    </div>
                </section>

                <p class="text-sm text-redup">Sumber: Profil Desa Adat Sangket, disusun prajuru bersama Sabha Desa dan Kertha Desa.</p>
            </div>

            <x-sidebar-publik label="Navigasi profil">
                <nav aria-labelledby="judul-daftar-isi" class="rounded-[1.75rem] bg-panel p-5 ring-1 ring-garis">
                    <h2 id="judul-daftar-isi" class="font-display text-lg font-bold">Di halaman ini</h2>
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
