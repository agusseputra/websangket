import { useCallback, useEffect, useRef, useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import PanelPembayaran from '../components/tagihan/PanelPembayaran';
import Alert from '../components/ui/Alert';
import AlasanDialog from '../components/ui/AlasanDialog';
import Button from '../components/ui/Button';
import ConfirmDialog from '../components/ui/ConfirmDialog';
import PageHeader from '../components/ui/PageHeader';
import Pagination from '../components/ui/Pagination';
import SelectField from '../components/ui/SelectField';
import TextField from '../components/ui/TextField';
import { JENIS_TAGIHAN } from '../config/tagihan';
import { useAuth } from '../context/AuthContext';
import useDebouncedValue from '../hooks/useDebouncedValue';
import usePeriodeTahun from '../hooks/usePeriodeTahun';
import api from '../lib/api';
import { rupiah } from '../lib/format';
import { galatKolom, pesanGalat } from '../lib/galat';

const STATUS = [
    { nilai: 'dibuat', label: 'Belum dibayar' },
    { nilai: 'dibayar', label: 'Dibayar' },
    { nilai: 'pending', label: 'Pending' },
    { nilai: 'valid', label: 'Valid' },
    { nilai: 'dispensasi', label: 'Dispensasi' },
];

const AKSI = {
    koreksi: { judul: 'Koreksi pembayaran', pesan: 'Data bayar dikosongkan dan tagihan kembali belum dibayar.', labelYa: 'Koreksi' },
    dispensasi: { judul: 'Beri dispensasi', pesan: 'Tagihan menjadi Rp0 dan tidak menambah tunggakan.', labelYa: 'Beri dispensasi' },
};

export default function TagihanPage() {
    const { user } = useAuth();
    const admin = user.role === 'admin';
    const [cari, setCari] = useState('');
    const [params] = useSearchParams();
    const [status, setStatus] = useState(params.get('status') ?? (admin ? 'dibayar' : ''));
    const [jenis, setJenis] = useState(params.get('jenis') ?? '');
    const [tahun, setTahun] = useState(params.get('tahun') ?? '');
    const [petugasId, setPetugasId] = useState(params.get('petugas_id') ?? '');
    const [pilihanPetugas, setPilihanPetugas] = useState([]);
    const [tanggalDari, setTanggalDari] = useState('');
    const [tanggalSampai, setTanggalSampai] = useState('');
    const [halaman, setHalaman] = useState(1);
    const [data, setData] = useState(null);
    const [terpilih, setTerpilih] = useState([]);
    const [aksi, setAksi] = useState(null);
    const [galat, setGalat] = useState('');
    const [galatAksi, setGalatAksi] = useState('');
    const [info, setInfo] = useState('');
    const [memproses, setMemproses] = useState(false);
    const [konfirmasiValid, setKonfirmasiValid] = useState(false);
    const { pilihanTahun } = usePeriodeTahun();
    const permintaanRef = useRef(0);
    const cariTertunda = useDebouncedValue(cari);
    const jumlahPembayaranTerpilih = data?.data.filter((pembayaran) => pembayaran.item.some((baris) => terpilih.includes(baris.id))).length ?? 0;

    const totalTerpilih = data?.data
        .flatMap((pembayaran) => pembayaran.item)
        .filter((baris) => terpilih.includes(baris.id))
        .reduce((jumlah, baris) => jumlah + (baris.nominal_bayar ?? 0), 0) ?? 0;

    const muat = useCallback(async () => {
        const nomor = ++permintaanRef.current;

        try {
            const res = await api.get('/tagihan', {
                params: {
                    q: cariTertunda || undefined,
                    status: status || undefined,
                    jenis: jenis || undefined,
                    tahun: tahun || undefined,
                    petugas_id: petugasId || undefined,
                    tanggal_dari: tanggalDari || undefined,
                    tanggal_sampai: tanggalSampai || undefined,
                    page: halaman,
                },
            });

            // Jawaban permintaan lama tidak boleh menimpa hasil filter terbaru.
            if (nomor !== permintaanRef.current) return;

            setGalat('');
            setData(res.data);
            setTerpilih([]);
        } catch (error) {
            setGalat(pesanGalat(error, 'Tagihan gagal dimuat.'));
        }
    }, [cariTertunda, status, jenis, tahun, petugasId, tanggalDari, tanggalSampai, halaman]);

    useEffect(() => {
        muat();
    }, [muat]);

    useEffect(() => {
        setHalaman(1);
    }, [cariTertunda, status, jenis, tahun, petugasId, tanggalDari, tanggalSampai]);

    useEffect(() => {
        api.get('/pilihan-petugas')
            .then((res) => setPilihanPetugas(res.data.map(({ id, name }) => ({ nilai: id, label: name }))))
            .catch(() => setPilihanPetugas([]));
    }, []);

    function centang(ids, aktif) {
        setTerpilih((lama) => (aktif ? [...new Set([...lama, ...ids])] : lama.filter((id) => !ids.includes(id))));
    }

    async function validasi() {
        setGalat('');
        setInfo('');
        setMemproses(true);

        try {
            const res = await api.post('/tagihan/validasi', { ids: terpilih });
            setInfo(`${jumlahPembayaranTerpilih} pembayaran (${res.data.jumlah} rincian) menjadi valid.`);
            await muat();
        } catch (error) {
            setGalat(pesanGalat(error, 'Validasi gagal.'));
        } finally {
            setKonfirmasiValid(false);
            setMemproses(false);
        }
    }

    async function jalankanAksi(alasan) {
        setGalatAksi('');
        setMemproses(true);

        try {
            await api.post(`/tagihan/${aksi.baris.id}/${aksi.jenis}`, { alasan });
            setAksi(null);
            await muat();
        } catch (error) {
            setGalatAksi(galatKolom(error).alasan ?? pesanGalat(error));
        } finally {
            setMemproses(false);
        }
    }

    return (
        <>
            {admin ? (
                <PageHeader judul="Tagihan dan validasi" deskripsi="Centang pembayaran yang sudah dihitung atau dicek, lalu ubah menjadi valid." />
            ) : (
                <PageHeader judul="Tagihan" deskripsi="Daftar tagihan dan pembayaran krama." />
            )}
            <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <TextField id="cari-tagihan" type="search" label="Cari nama" value={cari} onChange={(e) => setCari(e.target.value)} />
                <SelectField id="filter-status-tagihan" label="Status" kosong="Semua" value={status} onChange={(e) => setStatus(e.target.value)} pilihan={STATUS} />
                <SelectField id="filter-jenis" label="Jenis" kosong="Semua" value={jenis} onChange={(e) => setJenis(e.target.value)} pilihan={JENIS_TAGIHAN} />
                <SelectField id="filter-tahun" label="Periode tahun" kosong="Semua" value={tahun} onChange={(e) => setTahun(e.target.value)} pilihan={pilihanTahun} />
                <SelectField id="filter-petugas" label="Petugas" kosong="Semua" value={petugasId} onChange={(e) => setPetugasId(e.target.value)} pilihan={pilihanPetugas} />
                <TextField id="filter-tanggal-dari" type="date" label="Tanggal bayar dari" value={tanggalDari} onChange={(e) => setTanggalDari(e.target.value)} />
                <TextField id="filter-tanggal-sampai" type="date" label="Tanggal bayar sampai" value={tanggalSampai} onChange={(e) => setTanggalSampai(e.target.value)} />
            </div>

            {galat && <Alert nada="gagal" role="alert">{galat}</Alert>}
            {info && <Alert nada="berhasil">{info}</Alert>}

            {terpilih.length > 0 && (
                <div className="sticky bottom-3 z-10 flex flex-wrap items-center justify-between gap-3 rounded border border-garis bg-panel p-3">
                    <p className="text-sm font-medium">{jumlahPembayaranTerpilih} pembayaran dipilih</p>
                    <Button onClick={() => setKonfirmasiValid(true)} disabled={memproses}>Jadikan valid</Button>
                </div>
            )}

            {data && data.data.length === 0 && <p className="rounded border border-garis bg-panel p-6 text-center text-redup">Tidak ada tagihan yang cocok.</p>}

            {data && data.data.length > 0 && (
                <>
                    <ul className="divide-y divide-garis rounded border border-garis bg-panel">
                        {data.data.map((pembayaran) => (
                            <PanelPembayaran
                                key={pembayaran.kunci}
                                pembayaran={pembayaran}
                                admin={admin}
                                terpilih={terpilih}
                                onCentang={centang}
                                onAksi={(jenisAksi, baris) => {
                                    setGalatAksi('');
                                    setAksi({ jenis: jenisAksi, baris });
                                }}
                            />
                        ))}
                    </ul>
                    <Pagination meta={data.meta} onPindah={setHalaman} />
                </>
            )}

            {konfirmasiValid && (
                <ConfirmDialog
                    judul="Jadikan pembayaran valid?"
                    pesan={`${jumlahPembayaranTerpilih} pembayaran dengan total ${rupiah(totalTerpilih)} akan ditandai sudah dihitung atau dicek. Setelah valid, pembayaran tidak bisa dikoreksi.`}
                    labelYa="Ya, jadikan valid"
                    memproses={memproses}
                    onYa={validasi}
                    onBatal={() => setKonfirmasiValid(false)}
                />
            )}

            {aksi && (
                <AlasanDialog
                    {...AKSI[aksi.jenis]}
                    memproses={memproses}
                    galat={galatAksi}
                    onYa={jalankanAksi}
                    onBatal={() => setAksi(null)}
                />
            )}
        </>
    );
}
