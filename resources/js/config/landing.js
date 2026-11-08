export const SITUS_DESA = 'https://sangketdesa.com';
export const LOGO_DESA = '/images/logo-desa-adat-sangket.png';

// Kontak Bendesa Adat seperti yang tercantum di sangketdesa.com.
export const TELEPON_TAMPIL = '+62 878-6306-1763';

export const MENU_LANDING = [
    { href: '/profil', label: 'Profil' },
    { href: '/#kasukretan-krama', label: 'Kasukretan Krama' },
    { href: '/listarticle', label: 'Berita' },
    { href: '/lpd', label: 'LPD' },
    { href: '/#cek-pembayaran', label: 'Cek pembayaran' },
    { href: '/#pelaporan-tinggal', label: 'Pelaporan' },
];

const FOLDER_FOTO = '/images/landing';

/** Foto dokumentasi dari sangketdesa.com. Lebar dan tinggi dipakai agar tata letak tidak bergeser saat foto dimuat. */
export const FOTO = {
    dudonan: { src: `${FOLDER_FOTO}/dudonan-piodalan.jpg`, alt: 'Krama berbusana adat mengiringi dudonan piodalan dengan tedung kuning', lebar: 800, tinggi: 600 },
    tariJabaPura: { src: `${FOLDER_FOTO}/tari-jaba-pura.jpg`, alt: 'Penari tampil di jaba pura disaksikan krama saat piodalan malam hari', lebar: 1000, tinggi: 999 },
    tariPesraman: { src: `${FOLDER_FOTO}/tari-pesraman.jpg`, alt: 'Penari remaja tampil di panggung pada penutupan Pesraman Remaja Widya Bhakti', lebar: 1000, tinggi: 750 },
    pecaruan: { src: `${FOLDER_FOTO}/iring-iringan-pecaruan.jpg`, alt: 'Iring-iringan krama berjalan menuju upacara pecaruan', lebar: 1100, tinggi: 593 },
    tradisi: { src: `${FOLDER_FOTO}/tradisi-sangket.jpg`, alt: 'Krama berbusana adat berdoa di tepi kolam taman yang dihias', lebar: 1200, tinggi: 675 },
    palinggihMobil: { src: `${FOLDER_FOTO}/palinggih-mobil.jpg`, alt: 'Palinggih Mobil berbentuk kendaraan, ciri khas Desa Adat Sangket', lebar: 640, tinggi: 360 },
};

/** Foto dokumentasi LPD Desa Adat Sangket. */
export const FOTO_LPD = {
    gedung: { src: '/images/lpd/gedung-lpd.jpg', alt: 'Gedung kantor LPD Sangket berwarna biru dengan papan nama dan gerbang besi', lebar: 787, tinggi: 1400 },
    prajuru: { src: '/images/lpd/prajuru-pegawai.jpg', alt: 'Prajuru dan pegawai LPD Desa Adat Sangket berbusana adat di tangga candi bentar pura', lebar: 1200, tinggi: 1188 },
    rapat: { src: '/images/lpd/rat-krama.jpg', alt: 'Krama berbusana adat mengikuti rapat tahunan LPD Desa Adat Sangket', lebar: 1050, tinggi: 1400 },
};

/** Foto suasana Bali dari Unsplash (lisensi Unsplash). Kredit fotografer ditampilkan di footer. */
export const FOTO_BALI = {
    mapeed: {
        src: `${FOLDER_FOTO}/bali/mapeed.jpg`, alt: 'Krama istri membawa gebogan di depan pura berhias penjor dan tedung', lebar: 844, tinggi: 1500,
        fotografer: 'Ruben Hutabarat', tautan: 'https://unsplash.com/photos/VvJ0DL_PLR8',
    },
    penari: {
        src: `${FOLDER_FOTO}/bali/penari-bali.jpg`, alt: 'Penari Bali menari diiringi gamelan pada malam hari', lebar: 1400, tinggi: 931,
        fotografer: 'Didi Paul', tautan: 'https://unsplash.com/photos/fWvv8L9Ann0',
    },
    sawah: {
        src: `${FOLDER_FOTO}/bali/sawah-tegalalang.jpg`, alt: 'Sawah terasering hijau dengan pohon kelapa di Bali', lebar: 1400, tinggi: 933,
        fotografer: 'Niklas Weiss', tautan: 'https://unsplash.com/photos/-2WlTWZLnRc',
    },
    candiBentar: {
        src: `${FOLDER_FOTO}/bali/candi-bentar.jpg`, alt: 'Candi bentar pura Bali dengan bunga di latar depan', lebar: 1400, tinggi: 933,
        fotografer: 'Jeremy Bishop', tautan: 'https://unsplash.com/photos/QUwLZNchflk',
    },
    puraDanau: {
        src: `${FOLDER_FOTO}/bali/pura-ulun-danu.jpg`, alt: 'Pura Ulun Danu Beratan di tepi danau pada pagi hari', lebar: 1400, tinggi: 933,
        fotografer: 'Sebastian Pena Lambarri', tautan: 'https://unsplash.com/photos/U_i6h9Y50wQ',
    },
};

/** Ringkasan Pararem Kasukretan Krama No 02 Tahun 2025, Pasal 1, 5–8, dan 11–14. */
export const JENIS_KRAMA = [
    {
        kunci: 'krama-adat',
        nama: 'Krama Desa Adat',
        definisi: 'Warga Bali beragama Hindu yang mipil dan tercatat di Desa Adat Sangket, serta bertanggung jawab penuh atas Pura Kahyangan Tiga.',
        bidang: { label: 'Penuh', isi: 'Parahyangan, Pawongan, Palemahan' },
        hak: ['Hak penuh atas pelayanan Desa Adat', 'Memanfaatkan fasilitas milik Desa Adat'],
    },
    {
        kunci: 'krama-tamiu',
        nama: 'Krama Tamiu',
        definisi: 'Warga beragama Hindu yang tidak mipil sebagai krama adat, tetapi tercatat di Desa Adat Sangket.',
        bidang: { label: 'Terbatas', isi: 'Parahyangan, Pawongan, Palemahan' },
        hak: ['Pasayuban saat bencana', 'Pelayanan administrasi adat', 'Pemantauan keamanan', 'Mabhakti saat pujawali dan memakai fasilitas desa sesuai aturan'],
    },
    {
        kunci: 'tamiu',
        nama: 'Tamiu',
        definisi: 'Setiap orang selain Krama Desa Adat dan Krama Tamiu, termasuk yang beragama selain Hindu dan warga negara asing, yang tinggal dan tercatat di Desa Adat Sangket.',
        bidang: { label: 'Terbatas', isi: 'Pawongan, Palemahan' },
        hak: ['Pasayuban saat bencana', 'Pelayanan administrasi adat', 'Pemantauan keamanan'],
    },
];

export const LAMA_TINGGAL = [
    { nama: 'Rajeg', isi: 'Menetap, memiliki tanah atau rumah, atau mengontrak 1 tahun atau lebih.' },
    { nama: 'Padunungan', isi: 'Tinggal sementara minimal 7 hari dengan menyewa atau mengontrak kurang dari 1 tahun.' },
    { nama: 'Padgata Kala', isi: 'Tinggal kurang dari 7 hari atau menginap di rumah keluarga kurang dari 35 hari.' },
];

export const KEWAJIBAN_BERSAMA = [
    { judul: 'Mematuhi hukum adat', isi: 'Menaati awig-awig, pararem, dan peraturan Desa Adat Sangket.' },
    { judul: 'Lapor diri 1×24 jam', isi: 'Krama Tamiu dan Tamiu melapor sejak berada di wewidangan Desa Adat, langsung atau melalui pemilik rumah.' },
    { judul: 'Membayar dudukan', isi: 'Kontribusi wajib berupa uang dari Krama Tamiu dan Tamiu kepada Desa Adat.' },
    { judul: 'Menjaga lingkungan', isi: 'Ikut menjaga kebersihan, keamanan, ketertiban, dan kerukunan umat beragama.' },
    { judul: 'Punia dan gotong royong', isi: 'Sukarela sesuai kemampuan dan ketulusan hati.' },
];

/** Peruntukan dana dudukan seperti yang berlaku di Desa Adat Sangket. */
export const PERUNTUKAN_DANA = [
    { persen: 50, judul: 'Desa Adat', isi: 'Baga Parahyangan, Pawongan, dan Palemahan, termasuk pecaruan tawur kesanga dan prosesi penyepian setiap tahun.', warna: '#0369a1' },
    { persen: 30, judul: 'Operasional', isi: 'Transport, peralatan penunjang kegiatan kasukretan, dan konsumsi.', warna: '#38bdf8' },
    { persen: 10, judul: 'Banjar', isi: 'Penataan lingkungan oleh banjar.', warna: '#16a34a' },
    { persen: 10, judul: 'Pecalang', isi: 'Biaya pengamanan oleh pecalang.', warna: '#ea580c' },
];

export const FAQ = [
    {
        tanya: 'Apa itu Kasukretan Krama?',
        jawab: 'Partisipasi krama untuk menjaga ketertiban, keamanan, kenyamanan, kedamaian, ketenteraman, kebersihan, dan kesucian wewidangan Desa Adat, secara sekala maupun niskala.',
    },
    {
        tanya: 'Kapan saya harus melapor?',
        jawab: 'Paling lambat 1×24 jam sejak berada di wewidangan Desa Adat Sangket. Krama Tamiu dan Tamiu Padgata Kala dilaporkan oleh keluarga atau pemilik rumah yang menjadi penanggung jawab.',
    },
    {
        tanya: 'Berapa besar dudukan?',
        jawab: 'Dudukan setara 20 kg beras per tahun, sekitar Rp200.000. Pembayaran boleh dicicil, dan kekurangannya tampil pada tagihan tahun berikutnya.',
    },
    {
        tanya: 'Bagaimana cara mengecek tunggakan?',
        jawab: 'Isi nama lengkap dan NIK di bagian Cek pembayaran. Data tampil jika keduanya cocok dengan data pendaftaran, dan NIK tidak ditampilkan.',
    },
    {
        tanya: 'Apakah surat tanda berusaha adat sama dengan izin usaha?',
        jawab: 'Tidak. Ilikita Pamasih Mautsaha Adat hanya pencatatan dan persetujuan berusaha di wewidangan Desa Adat, bukan izin usaha menurut hukum negara.',
    },
    {
        tanya: 'Bagaimana jika saya keberatan?',
        jawab: 'Ajukan pengaduan tertulis kepada Kerta Desa Adat yang wajib menanggapi dalam 7 hari. Jika tidak ditanggapi, pengaduan dapat diteruskan ke Majelis Desa Adat secara berjenjang.',
    },
];

export const WEWIDANGAN = 'Timur Sungai Padang Bulia, selatan Desa Lumbanan, barat Sungai Sambangan, dan utara Desa Bantang Banua.';
