const ESC = 0x1b;
const GS = 0x1d;
const LEBAR_58MM = 32;

/** Penyusun perintah ESC/POS untuk printer thermal. */
export class EscPos {
    constructor() {
        this.bytes = [ESC, 0x40];
    }

    teks(isi) {
        for (const char of isi) {
            const kode = char.charCodeAt(0);
            this.bytes.push(kode < 128 ? kode : 0x3f);
        }
        return this;
    }

    baris(isi = '') {
        return this.teks(isi).feed();
    }

    feed(jumlah = 1) {
        this.bytes.push(ESC, 0x64, jumlah);
        return this;
    }

    rata(posisi) {
        this.bytes.push(ESC, 0x61, { kiri: 0, tengah: 1, kanan: 2 }[posisi]);
        return this;
    }

    tebal(aktif) {
        this.bytes.push(ESC, 0x45, aktif ? 1 : 0);
        return this;
    }

    garis() {
        return this.baris('-'.repeat(LEBAR_58MM));
    }

    kolom(kiri, kanan) {
        // Nilai yang terlalu panjang dicetak di baris sendiri agar tidak terpotong printer.
        if (kiri.length + kanan.length + 1 > LEBAR_58MM) {
            this.baris(kiri);
            return this.bungkus(kanan);
        }

        return this.baris(`${kiri}${' '.repeat(LEBAR_58MM - kiri.length - kanan.length)}${kanan}`);
    }

    bungkus(isi) {
        for (let awal = 0; awal < isi.length; awal += LEBAR_58MM) {
            const potongan = isi.slice(awal, awal + LEBAR_58MM);
            this.baris(potongan.padStart(LEBAR_58MM));
        }

        return this;
    }

    potong() {
        this.bytes.push(GS, 0x56, 0x42, 0x00);
        return this;
    }

    hasil() {
        return Uint8Array.from(this.bytes);
    }
}

/** Contoh isi struk untuk uji cetak. Datanya bukan data krama sebenarnya. */
export const CONTOH_STRUK = {
    judul: 'DESA ADAT SANGKET',
    subjudul: 'STRUK UJI CETAK',
    baris: [
        ['No. struk', 'UJI-0001'],
        ['Nama', 'Contoh Krama'],
        ['Tahun', '2026'],
    ],
    rincian: [
        ['Dudukan', 'Rp200.000'],
        ['Penanjung Batu', 'Rp1.000.000'],
    ],
    total: ['TOTAL', 'Rp1.200.000'],
    penutup: 'Salin catatan ke kartu dudukan',
};

export function buatStrukUji(struk = CONTOH_STRUK) {
    const printer = new EscPos();

    printer.rata('tengah').tebal(true).baris(struk.judul).tebal(false).baris(struk.subjudul).rata('kiri').garis();
    struk.baris.forEach(([kiri, kanan]) => printer.kolom(kiri, kanan));
    printer.garis();
    struk.rincian.forEach(([kiri, kanan]) => printer.kolom(kiri, kanan));
    printer.garis().tebal(true).kolom(...struk.total).tebal(false);
    (struk.catatan ?? []).forEach(([kiri, kanan]) => printer.kolom(kiri, kanan));
    printer.rata('tengah').feed().baris(struk.penutup).feed(3).potong();

    return printer.hasil();
}

export function keBase64(bytes) {
    return btoa(String.fromCharCode(...bytes));
}
