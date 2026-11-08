import { useEffect, useState } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import Alert from '../components/ui/Alert';
import PageHeader from '../components/ui/PageHeader';
import Pagination from '../components/ui/Pagination';
import SelectField from '../components/ui/SelectField';
import TextField from '../components/ui/TextField';
import { JENIS_TAGIHAN } from '../config/tagihan';
import useDebouncedValue from '../hooks/useDebouncedValue';
import usePeriodeTahun from '../hooks/usePeriodeTahun';
import api from '../lib/api';
import { rupiah } from '../lib/format';
import { pesanGalat } from '../lib/galat';

export default function TunggakanPage() {
    const [cari, setCari] = useState('');
    const [params] = useSearchParams();
    const [jenis, setJenis] = useState(params.get('jenis') ?? '');
    const [tahun, setTahun] = useState(params.get('tahun') ?? '');
    const [halaman, setHalaman] = useState(1);
    const [data, setData] = useState(null);
    const [galat, setGalat] = useState('');
    const cariTertunda = useDebouncedValue(cari);
    const { pilihanTahun } = usePeriodeTahun();

    useEffect(() => {
        setHalaman(1);
    }, [cariTertunda, jenis, tahun]);

    useEffect(() => {
        let batal = false;

        api.get('/tunggakan', { params: { q: cariTertunda || undefined, jenis: jenis || undefined, tahun: tahun || undefined, page: halaman } })
            .then((res) => {
                if (!batal) {
                    setGalat('');
                    setData(res.data);
                }
            })
            .catch((error) => {
                if (!batal) setGalat(pesanGalat(error, 'Tunggakan gagal dimuat.'));
            });

        return () => {
            batal = true;
        };
    }, [cariTertunda, jenis, tahun, halaman]);

    return (
        <>
            <PageHeader judul="Tunggakan" deskripsi={data ? `Total tunggakan ${rupiah(data.total)}` : 'Krama yang masih punya kewajiban.'} />
            <div className="grid gap-3 sm:grid-cols-3">
                <TextField id="cari-tunggakan" type="search" label="Cari nama" value={cari} onChange={(e) => setCari(e.target.value)} />
                <SelectField id="filter-jenis-tunggakan" label="Jenis tunggakan" kosong="Semua" value={jenis} onChange={(e) => setJenis(e.target.value)} pilihan={JENIS_TAGIHAN} />
                <SelectField id="filter-tahun-tunggakan" label="Periode tahun" kosong="Semua" value={tahun} onChange={(e) => setTahun(e.target.value)} pilihan={pilihanTahun} />
            </div>
            {galat && <Alert nada="gagal" role="alert">{galat}</Alert>}
            {data && data.data.length === 0 && <p className="rounded border border-garis bg-panel p-6 text-center text-redup">Tidak ada tunggakan.</p>}
            {data && data.data.length > 0 && (
                <>
                    <ul className="divide-y divide-garis rounded border border-garis bg-panel">
                        {data.data.map((krama) => (
                            <li key={krama.id}>
                                <Link to={`/krama/${krama.id}/bayar`} className="flex min-h-14 flex-wrap items-center justify-between gap-x-4 gap-y-1 px-4 py-3 hover:bg-kertas">
                                    <span className="min-w-0">
                                        <span className="block font-medium break-words">{krama.nama}</span>
                                        <span className="block text-sm text-redup">
                                            {[krama.perumahan, krama.nomor_rumah].filter(Boolean).join(' · ') || 'Perumahan belum diisi'}
                                        </span>
                                    </span>
                                    <span className="font-semibold tabular-nums">{rupiah(krama.tunggakan)}</span>
                                </Link>
                            </li>
                        ))}
                    </ul>
                    <Pagination meta={data.meta} onPindah={setHalaman} />
                </>
            )}
        </>
    );
}
