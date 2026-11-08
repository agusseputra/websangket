import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import ImporPembayaranCard from '../components/krama/ImporPembayaranCard';
import Alert from '../components/ui/Alert';
import Button from '../components/ui/Button';
import Card from '../components/ui/Card';
import PageHeader from '../components/ui/PageHeader';
import StatCard from '../components/ui/StatCard';
import StatGrid from '../components/ui/StatGrid';
import api from '../lib/api';
import { pesanGalat } from '../lib/galat';

const RINCIAN = [
    { kunci: 'nik_kosong', label: 'NIK kosong' },
    { kunci: 'nik_tidak_valid', label: 'NIK tidak valid' },
    { kunci: 'asal_kosong', label: 'Asal kosong' },
    { kunci: 'perumahan_kosong', label: 'Perumahan kosong' },
    { kunci: 'perumahan_tidak_cocok', label: 'Perumahan tidak ada di Referensi' },
    { kunci: 'perumahan_dikoreksi', label: 'Perumahan dikoreksi' },
    { kunci: 'status_kosong', label: 'Status kosong, diisi Krama Tamiu' },
];

function LaporanMasalah({ masalah }) {
    return (
        <details className="rounded border border-garis bg-panel">
            <summary className="min-h-11 cursor-pointer px-4 py-3 font-semibold">Rincian per baris ({masalah.length} catatan)</summary>
            <div className="max-h-96 overflow-auto border-t border-garis">
                <table className="w-full text-left text-sm">
                    <thead className="sticky top-0 bg-kertas">
                        <tr>
                            <th scope="col" className="px-4 py-2 font-semibold">Baris</th>
                            <th scope="col" className="px-4 py-2 font-semibold">Nama</th>
                            <th scope="col" className="px-4 py-2 font-semibold">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        {masalah.map((item) => (
                            <tr key={`${item.baris}-${item.masalah}`} className="border-t border-garis">
                                <td className="px-4 py-2 tabular-nums">{item.baris}</td>
                                <td className="px-4 py-2">{item.nama}</td>
                                <td className="px-4 py-2">{item.masalah}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </details>
    );
}

export default function ImporKramaPage() {
    const [sudahDiimpor, setSudahDiimpor] = useState(null);
    const [file, setFile] = useState(null);
    const [laporan, setLaporan] = useState(null);
    const [hasil, setHasil] = useState(null);
    const [galat, setGalat] = useState('');
    const [memproses, setMemproses] = useState(false);

    useEffect(() => {
        api.get('/impor-krama')
            .then((res) => setSudahDiimpor(res.data.sudah_diimpor))
            .catch((error) => setGalat(pesanGalat(error)));
    }, []);

    async function pratinjau(event) {
        event.preventDefault();
        setGalat('');
        setMemproses(true);

        const data = new FormData();
        data.append('file', file);

        try {
            const res = await api.post('/impor-krama/pratinjau', data);
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
            const res = await api.post('/impor-krama', { token: laporan.token });
            setHasil(res.data);
            setLaporan(null);
            setSudahDiimpor(true);
        } catch (error) {
            setGalat(pesanGalat(error, 'Impor gagal. Tidak ada data yang tersimpan.'));
        } finally {
            setMemproses(false);
        }
    }

    async function batal() {
        setGalat('');

        try {
            await api.delete('/impor-krama');
        } catch (error) {
            setGalat(pesanGalat(error));
        }

        setLaporan(null);
        setFile(null);
    }

    return (
        <>
            <PageHeader judul="Impor Excel" deskripsi="Impor data krama lebih dulu, lalu pembayaran 2026 dari sheet ALL." />
            {galat && <Alert nada="gagal" role="alert">{galat}</Alert>}

            {hasil && (
                <Alert nada="berhasil">
                    Impor selesai: {hasil.total} krama tersimpan, {hasil.lengkap} lengkap dan {hasil.pending} pending.
                </Alert>
            )}

            {sudahDiimpor && (
                <Card judul="Data Excel sudah diimpor">
                    <p className="text-redup">Impor hanya bisa dilakukan sekali agar data krama tidak tergandakan.</p>
                    <Button as={Link} varian="garis" to="/krama?status_data=pending">Lihat data pending</Button>
                </Card>
            )}

            {sudahDiimpor && <ImporPembayaranCard />}

            {sudahDiimpor === false && !laporan && (
                <Card judul="Pilih file">
                    <form onSubmit={pratinjau} className="grid gap-3">
                        <label htmlFor="file-excel" className="text-sm font-medium">File Excel (.xlsx, maksimal 10 MB)</label>
                        <input
                            id="file-excel"
                            type="file"
                            accept=".xlsx"
                            required
                            onChange={(e) => setFile(e.target.files[0] ?? null)}
                            className="block w-full min-w-0 text-sm file:mr-3 file:min-h-11 file:rounded file:border file:border-garis file:bg-kertas file:px-3 file:text-teks"
                        />
                        <p className="text-sm text-redup">Data belum disimpan sampai Anda mengonfirmasi laporan pratinjau.</p>
                        <Button type="submit" disabled={!file || memproses}>{memproses ? 'Membaca file...' : 'Lihat pratinjau'}</Button>
                    </form>
                </Card>
            )}

            {laporan && (
                <>
                    <StatGrid>
                        <StatCard label="Total krama" nilai={laporan.ringkasan.total} />
                        <StatCard label="Lengkap" nilai={laporan.ringkasan.lengkap} />
                        <StatCard label="Pending" nilai={laporan.ringkasan.pending} />
                    </StatGrid>
                    <Card judul="Hasil pencocokan">
                        <ul className="divide-y divide-garis">
                            {RINCIAN.map(({ kunci, label }) => (
                                <li key={kunci} className="flex justify-between gap-4 py-2 text-sm">
                                    <span className="min-w-0">{label}</span>
                                    <span className="font-semibold tabular-nums">{laporan.ringkasan[kunci]}</span>
                                </li>
                            ))}
                        </ul>
                    </Card>
                    <LaporanMasalah masalah={laporan.masalah} />
                    <div className="flex flex-wrap justify-end gap-2">
                        <Button varian="garis" onClick={batal} disabled={memproses}>Batal</Button>
                        <Button onClick={simpan} disabled={memproses}>
                            {memproses ? 'Menyimpan...' : `Impor ${laporan.ringkasan.total} krama`}
                        </Button>
                    </div>
                </>
            )}
        </>
    );
}
