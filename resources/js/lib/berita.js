export const UKURAN_GAMBAR_MAKS = 1024 * 1024;
export const TIPE_SAMPUL = ['image/jpeg', 'image/png', 'image/webp'];

/**
 * Pemeriksaan gambar di browser sebelum diunggah, sama dengan aturan di server.
 * Mengembalikan pesan galat, atau string kosong jika gambar boleh diunggah.
 */
export async function periksaGambar(file, { tipe = TIPE_SAMPUL, lebarMin = 0, tinggiMin = 0, label = 'Gambar' } = {}) {
    if (!tipe.includes(file.type)) {
        return `${label} harus ${tipe.includes('image/gif') ? 'JPG, PNG, WebP, atau GIF' : 'JPG, PNG, atau WebP'}.`;
    }

    if (file.size > UKURAN_GAMBAR_MAKS) {
        return `Ukuran ${label.toLowerCase()} maksimal 1 MB. Perkecil gambar lebih dulu.`;
    }

    if (lebarMin || tinggiMin) {
        const ukuran = await ukuranGambar(file);

        if (!ukuran) return `${label} tidak bisa dibaca.`;
        if (ukuran.lebar < lebarMin || ukuran.tinggi < tinggiMin) return `${label} minimal ${lebarMin} × ${tinggiMin} piksel.`;
    }

    return '';
}

function ukuranGambar(file) {
    return new Promise((selesai) => {
        const url = URL.createObjectURL(file);
        const gambar = new Image();

        gambar.onload = () => {
            selesai({ lebar: gambar.naturalWidth, tinggi: gambar.naturalHeight });
            URL.revokeObjectURL(url);
        };
        gambar.onerror = () => {
            selesai(null);
            URL.revokeObjectURL(url);
        };
        gambar.src = url;
    });
}

/**
 * Panjang teks polos isi berita, tanpa tag HTML, untuk validasi minimal 30 karakter.
 */
export function panjangTeks(html) {
    const wadah = document.createElement('div');
    wadah.innerHTML = html;

    return (wadah.textContent ?? '').replace(/ /g, ' ').trim().length;
}

export function validasiKategori(nama) {
    const bersih = nama.trim().replace(/\s+/g, ' ');

    if (!bersih) return 'Nama kategori wajib diisi.';
    if (bersih.length < 3) return 'Nama kategori minimal 3 karakter.';
    if (bersih.length > 50) return 'Nama kategori maksimal 50 karakter.';

    return '';
}

export function validasiBerita({ judul, kategori_id: kategoriId, isi }) {
    const galat = {};
    const judulBersih = judul.trim().replace(/\s+/g, ' ');

    if (!judulBersih) galat.judul = 'Judul berita wajib diisi.';
    else if (judulBersih.length < 10) galat.judul = 'Judul berita minimal 10 karakter.';
    else if (judulBersih.length > 200) galat.judul = 'Judul berita maksimal 200 karakter.';

    if (!kategoriId) galat.kategori_id = 'Kategori wajib dipilih.';

    if (!isi) galat.isi = 'Isi berita wajib diisi.';
    else if (panjangTeks(isi) < 30) galat.isi = 'Isi berita minimal 30 karakter teks.';

    return galat;
}
