import { rupiah } from './format';

/** Menyusun isi struk dari hasil pembayaran. NIK sengaja tidak dicetak. */
export function buatDataStruk({ hasil, krama, petugas, cetakUlang = false }) {
    const [pertama] = hasil.tagihan;
    const total = hasil.tagihan.reduce((jumlah, baris) => jumlah + baris.nominal_bayar, 0);

    return {
        judul: 'DESA ADAT SANGKET',
        subjudul: cetakUlang ? 'STRUK DUDUKAN - CETAK ULANG' : 'STRUK DUDUKAN',
        baris: [
            ['No. struk', hasil.no_struk],
            ['Tanggal', pertama.tanggal_bayar],
            ['Nama', krama.nama],
            ['Petugas', petugas.name],
            ['Metode', pertama.metode === 'transfer' ? 'Transfer' : 'Tunai'],
            ...(pertama.status === 'pending' ? [['Status', 'Menunggu verifikasi']] : []),
        ],
        rincian: hasil.tagihan.map((baris) => [`${baris.tahun} ${baris.jenis_label}`, rupiah(baris.nominal_bayar)]),
        total: ['TOTAL', rupiah(total)],
        catatan: [['Sisa tunggakan', rupiah(hasil.tunggakan)]],
        penutup: 'Salin catatan ke kartu dudukan',
    };
}
