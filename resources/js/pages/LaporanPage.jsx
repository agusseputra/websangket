import { useCallback, useEffect, useRef, useState } from 'react';
import PanelLaporan from '../components/laporan/PanelLaporan';
import Alert from '../components/ui/Alert';
import ConfirmDialog from '../components/ui/ConfirmDialog';
import PageHeader from '../components/ui/PageHeader';
import Pagination from '../components/ui/Pagination';
import SelectField from '../components/ui/SelectField';
import TextField from '../components/ui/TextField';
import useDebouncedValue from '../hooks/useDebouncedValue';
import api from '../lib/api';
import { pesanGalat } from '../lib/galat';

const STATUS = [
    { nilai: 'pending', label: 'Pending' },
    { nilai: 'ditindak', label: 'Ditindak' },
];

const JENIS = [
    { nilai: 'tinggal', label: 'Tinggal' },
    { nilai: 'usaha', label: 'Usaha' },
];

export default function LaporanPage() {
    const [cari, setCari] = useState('');
    const [status, setStatus] = useState('pending');
    const [jenis, setJenis] = useState('');
    const [halaman, setHalaman] = useState(1);
    const [data, setData] = useState(null);
    const [galat, setGalat] = useState('');
    const [konfirmasi, setKonfirmasi] = useState(null);
    const [memproses, setMemproses] = useState(false);
    const permintaanRef = useRef(0);
    const cariTertunda = useDebouncedValue(cari);

    const muat = useCallback(async () => {
        const nomor = ++permintaanRef.current;

        try {
            const res = await api.get('/laporan-warga', {
                params: { q: cariTertunda || undefined, status: status || undefined, jenis: jenis || undefined, page: halaman },
            });

            if (nomor !== permintaanRef.current) return;

            setGalat('');
            setData(res.data);
        } catch (error) {
            setGalat(pesanGalat(error, 'Laporan gagal dimuat.'));
        }
    }, [cariTertunda, status, jenis, halaman]);

    useEffect(() => {
        muat();
    }, [muat]);

    useEffect(() => {
        setHalaman(1);
    }, [cariTertunda, status, jenis]);

    async function ubahStatus() {
        setMemproses(true);

        try {
            await api.put(`/laporan-warga/${konfirmasi.laporan.id}/status`, { status: konfirmasi.status });
            await muat();
        } catch (error) {
            setGalat(pesanGalat(error, 'Status laporan gagal diubah.'));
        } finally {
            setKonfirmasi(null);
            setMemproses(false);
        }
    }

    return (
        <>
            <PageHeader judul="Laporan warga" deskripsi="Laporan tinggal dan usaha dari landing page." />

            <div className="grid gap-3 sm:grid-cols-3">
                <TextField id="cari-laporan" type="search" label="Cari nama" value={cari} onChange={(e) => setCari(e.target.value)} />
                <SelectField id="filter-status-laporan" label="Status" kosong="Semua" value={status} onChange={(e) => setStatus(e.target.value)} pilihan={STATUS} />
                <SelectField id="filter-jenis-laporan" label="Jenis laporan" kosong="Semua" value={jenis} onChange={(e) => setJenis(e.target.value)} pilihan={JENIS} />
            </div>

            {galat && <Alert nada="gagal" role="alert">{galat}</Alert>}

            {data && data.data.length === 0 && <p className="rounded border border-garis bg-panel p-6 text-center text-redup">Tidak ada laporan yang cocok.</p>}

            {data && data.data.length > 0 && (
                <>
                    <ul className="divide-y divide-garis rounded border border-garis bg-panel">
                        {data.data.map((laporan) => (
                            <PanelLaporan
                                key={laporan.id}
                                laporan={laporan}
                                memproses={memproses}
                                onUbahStatus={(satu, statusBaru) => setKonfirmasi({ laporan: satu, status: statusBaru })}
                            />
                        ))}
                    </ul>
                    <Pagination meta={data.meta} onPindah={setHalaman} />
                </>
            )}

            {konfirmasi && (
                <ConfirmDialog
                    judul={konfirmasi.status === 'ditindak' ? 'Tandai laporan ditindak?' : 'Kembalikan laporan ke pending?'}
                    pesan={`Laporan ${konfirmasi.laporan.nama} akan berstatus ${konfirmasi.status === 'ditindak' ? 'ditindak' : 'pending'}.`}
                    labelYa="Ya, ubah"
                    memproses={memproses}
                    onYa={ubahStatus}
                    onBatal={() => setKonfirmasi(null)}
                />
            )}
        </>
    );
}
