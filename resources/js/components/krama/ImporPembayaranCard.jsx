import { useEffect, useState } from 'react';
import api from '../../lib/api';
import { rupiah } from '../../lib/format';
import { pesanGalat } from '../../lib/galat';
import Alert from '../ui/Alert';
import Button from '../ui/Button';
import Card from '../ui/Card';

export default function ImporPembayaranCard() {
    const [sudah, setSudah] = useState(null);
    const [file, setFile] = useState(null);
    const [laporan, setLaporan] = useState(null);
    const [selesai, setSelesai] = useState(false);
    const [galat, setGalat] = useState('');
    const [memproses, setMemproses] = useState(false);

    useEffect(() => {
        api.get('/impor-pembayaran')
            .then((res) => setSudah(res.data.sudah_diimpor))
            .catch((error) => setGalat(pesanGalat(error)));
    }, []);

    async function pratinjau(event) {
        event.preventDefault();
        setGalat('');
        setMemproses(true);

        const data = new FormData();
        data.append('file', file);

        try {
            const res = await api.post('/impor-pembayaran/pratinjau', data);
            setLaporan(res.data);
        } catch (error) {
            setGalat(pesanGalat(error, 'File gagal dibaca.'));
        } finally {
            setMemproses(false);
        }
    }

    async function simpan() {
        setGalat('');
        setMemproses(true);

        try {
            await api.post('/impor-pembayaran', { token: laporan.token });
            setSudah(true);
            setSelesai(true);
            setLaporan(null);
        } catch (error) {
            setGalat(pesanGalat(error, 'Impor gagal. Tidak ada data yang tersimpan.'));
        } finally {
            setMemproses(false);
        }
    }

    async function batal() {
        setGalat('');

        try {
            await api.delete('/impor-pembayaran');
        } catch (error) {
            setGalat(pesanGalat(error));
        }

        setLaporan(null);
        setFile(null);
    }

    return (
        <Card judul="Pembayaran 2026 dari Excel">
            {galat && <Alert nada="gagal" role="alert">{galat}</Alert>}
            {selesai && <Alert nada="berhasil">Pembayaran 2026 berhasil diimpor.</Alert>}
            {sudah && !selesai && <p className="text-redup">Periode 2026 sudah ada, jadi impor pembayaran tidak tersedia lagi.</p>}

            {sudah === false && !laporan && (
                <form onSubmit={pratinjau} className="grid gap-3">
                    <p className="text-sm text-redup">Gunakan file Excel yang sama dengan impor krama. Nominal mengikuti sheet ALL, dan yang kosong tetap belum dibayar.</p>
                    <label htmlFor="file-pembayaran" className="text-sm font-medium">File Excel (.xlsx)</label>
                    <input
                        id="file-pembayaran"
                        type="file"
                        accept=".xlsx"
                        required
                        onChange={(e) => setFile(e.target.files[0] ?? null)}
                        className="block w-full min-w-0 text-sm file:mr-3 file:min-h-11 file:rounded file:border file:border-garis file:bg-kertas file:px-3 file:text-teks"
                    />
                    <Button type="submit" disabled={!file || memproses}>{memproses ? 'Membaca file...' : 'Lihat pratinjau'}</Button>
                </form>
            )}

            {laporan && (
                <>
                    <ul className="divide-y divide-garis text-sm">
                        <li className="flex justify-between gap-4 py-2"><span>Tagihan Dudukan</span><span className="font-semibold tabular-nums">{laporan.krama}</span></li>
                        <li className="flex justify-between gap-4 py-2"><span>Dudukan dibayar ({laporan.dudukan_bayar})</span><span className="font-semibold tabular-nums">{rupiah(laporan.total_dudukan)}</span></li>
                        <li className="flex justify-between gap-4 py-2"><span>Dudukan belum dibayar</span><span className="font-semibold tabular-nums">{laporan.dudukan_belum_bayar}</span></li>
                        <li className="flex justify-between gap-4 py-2"><span>Dispensasi</span><span className="font-semibold tabular-nums">{laporan.dispensasi}</span></li>
                        <li className="flex justify-between gap-4 py-2"><span>Penanjung Batu dibayar ({laporan.penanjung_bayar})</span><span className="font-semibold tabular-nums">{rupiah(laporan.total_penanjung)}</span></li>
                        <li className="flex justify-between gap-4 py-2"><span>Tanggal bayar</span><span className="font-semibold tabular-nums">{laporan.tanggal_bayar}</span></li>
                    </ul>
                    {laporan.tidak_tertaut.length > 0 && <Alert>Baris tanpa data krama: {laporan.tidak_tertaut.join(', ')}.</Alert>}
                    {laporan.nama_berbeda.length > 0 && <Alert>Nama berbeda dengan data krama pada baris: {laporan.nama_berbeda.join(', ')}. Pastikan filenya benar.</Alert>}
                    <div className="flex flex-wrap justify-end gap-2">
                        <Button varian="garis" onClick={batal} disabled={memproses}>Batal</Button>
                        <Button onClick={simpan} disabled={memproses}>{memproses ? 'Menyimpan...' : 'Impor pembayaran'}</Button>
                    </div>
                </>
            )}
        </Card>
    );
}
