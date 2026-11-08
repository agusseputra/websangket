import { ArrowLeft } from 'lucide-react';
import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { Link, useParams } from 'react-router-dom';
import LokasiForm from '../../components/krama/LokasiForm';
import CatatanKartu from '../../components/tagihan/CatatanKartu';
import CetakStruk from '../../components/tagihan/CetakStruk';
import RiwayatTagihan from '../../components/tagihan/RiwayatTagihan';
import TagihanPilih from '../../components/tagihan/TagihanPilih';
import Alert from '../../components/ui/Alert';
import Button from '../../components/ui/Button';
import Card from '../../components/ui/Card';
import ConfirmDialog from '../../components/ui/ConfirmDialog';
import PageHeader from '../../components/ui/PageHeader';
import SelectField from '../../components/ui/SelectField';
import { useAuth } from '../../context/AuthContext';
import usePerumahan from '../../hooks/usePerumahan';
import api from '../../lib/api';
import { rupiah } from '../../lib/format';
import { pesanGalat } from '../../lib/galat';

const METODE = [
    { nilai: 'tunai', label: 'Tunai' },
    { nilai: 'transfer', label: 'Transfer' },
];

export default function BayarPage() {
    const { id } = useParams();
    const { user } = useAuth();
    const { perumahan } = usePerumahan();
    const [data, setData] = useState(null);
    const [pilihan, setPilihan] = useState({});
    const [metode, setMetode] = useState('tunai');
    const [galat, setGalat] = useState('');
    const [memproses, setMemproses] = useState(false);
    const [konfirmasi, setKonfirmasi] = useState(false);
    const [hasil, setHasil] = useState(null);
    const [tagihanBaru, setTagihanBaru] = useState(null);
    const uuidRef = useRef(crypto.randomUUID());

    const muat = useCallback(async () => {
        try {
            const res = await api.post(`/krama/${id}/tagihan/siapkan`);
            setData(res.data);

            if (res.data.tagihan_dibuat.length > 0) {
                setTagihanBaru({ tahun: res.data.periode_aktif, jenis: res.data.tagihan_dibuat });
            }
        } catch (error) {
            setGalat(pesanGalat(error, 'Tagihan gagal dimuat.'));
        }
    }, [id]);

    useEffect(() => {
        muat();
    }, [muat]);

    const terbuka = useMemo(() => data?.tagihan.filter((baris) => baris.status === 'dibuat') ?? [], [data]);
    const riwayat = useMemo(() => data?.tagihan.filter((baris) => baris.status !== 'dibuat') ?? [], [data]);
    const total = Object.values(pilihan).reduce((jumlah, nominal) => jumlah + Number(nominal || 0), 0);

    function ubahPilihan(tagihanId, nominal) {
        setPilihan((lama) => {
            const baru = { ...lama };

            if (nominal === undefined) delete baru[tagihanId];
            else baru[tagihanId] = nominal;

            return baru;
        });
    }

    function mintaKonfirmasi(event) {
        event.preventDefault();
        setGalat('');
        setKonfirmasi(true);
    }

    async function bayar() {
        setGalat('');
        setMemproses(true);

        try {
            const res = await api.post(`/krama/${id}/bayar`, {
                transaksi_uuid: uuidRef.current,
                metode,
                item: Object.entries(pilihan).map(([tagihanId, nominal]) => ({
                    tagihan_id: Number(tagihanId),
                    nominal_bayar: Number(nominal),
                })),
            });

            uuidRef.current = crypto.randomUUID();
            setHasil(res.data);
            setPilihan({});
            await muat();
        } catch (error) {
            setGalat(pesanGalat(error, 'Pembayaran gagal disimpan.'));
        } finally {
            setKonfirmasi(false);
            setMemproses(false);
        }
    }

    if (!data) {
        return galat ? <Alert nada="gagal" role="alert">{galat}</Alert> : <p className="text-redup">Memuat tagihan...</p>;
    }

    const krama = data.krama;

    const pemberitahuan = (
        <>
            {tagihanBaru && (
                <Alert nada="berhasil">Tagihan {tagihanBaru.tahun} dibuat otomatis: {tagihanBaru.jenis.join(' dan ')}.</Alert>
            )}
            {data.periode_aktif === null && (
                <Alert>
                    Belum ada periode iuran, jadi tagihan belum bisa dibuat.{' '}
                    {user.role === 'admin' ? <Link to="/periode" className="underline">Buat periode</Link> : 'Hubungi admin untuk membuat periode.'}
                </Alert>
            )}
            {!krama.aktif && <Alert>Krama ini nonaktif, jadi tagihan baru tidak dibuat.</Alert>}
        </>
    );

    if (!krama.lokasi_lengkap) {
        return (
            <>
                <PageHeader judul="Bayar tagihan" deskripsi={krama.nama} />
                {pemberitahuan}
                <Alert nada="peringatan" role="alert">
                    Pembayaran terkunci. Tandai lokasi dan pilih perumahan krama ini terlebih dahulu. Nomor rumah boleh dikosongkan.
                </Alert>
                <Card judul="Lengkapi lokasi">
                    <LokasiForm krama={krama} perumahan={perumahan} labelSimpan="Simpan dan lanjut bayar" onTersimpan={muat} />
                </Card>
            </>
        );
    }

    return (
        <>
            <PageHeader judul="Bayar tagihan" deskripsi={`${krama.nama} · Tunggakan ${rupiah(data.tunggakan)}`} />
            {galat && <Alert nada="gagal" role="alert">{galat}</Alert>}
            {!hasil && pemberitahuan}

            {hasil && (
                <Card judul={`Pembayaran tercatat · ${hasil.no_struk}`}>
                    <p className="text-sm text-redup">Salin catatan ini ke Kartu Dudukan krama.</p>
                    <CatatanKartu tagihan={hasil.tagihan} />
                    <p className="text-sm">
                        {hasil.tagihan[0].status === 'pending' ? 'Transfer menunggu dicek admin. ' : ''}
                        Sisa tunggakan {rupiah(hasil.tunggakan)}.
                    </p>
                    {hasil.status_krama_baru && <Alert nada="berhasil">Status krama berubah menjadi {hasil.status_krama_baru}.</Alert>}
                    <CetakStruk hasil={hasil} krama={krama} petugas={user} />
                    <Button as={Link} varian="garis" to="/tunggakan" className="justify-self-start">
                        <ArrowLeft className="size-4" aria-hidden="true" />
                        Kembali ke tunggakan
                    </Button>
                </Card>
            )}

            {terbuka.length === 0 ? (
                <Card>
                    <p className="text-redup">Tidak ada tagihan yang perlu dibayar.</p>
                    <Button as={Link} varian="garis" to={`/krama/${id}`}>Kembali ke data krama</Button>
                </Card>
            ) : (
                <form onSubmit={mintaKonfirmasi} className="grid gap-4">
                    <Card judul="Centang tagihan yang dibayar">
                        <TagihanPilih tagihan={terbuka} pilihan={pilihan} onUbah={ubahPilihan} />
                        <SelectField id="metode" label="Metode pembayaran" value={metode} onChange={(e) => setMetode(e.target.value)} pilihan={METODE} />
                        <div className="flex flex-wrap items-center justify-between gap-3">
                            <p className="font-semibold">Total {rupiah(total)}</p>
                            <Button type="submit" disabled={memproses || Object.keys(pilihan).length === 0}>
                                {memproses ? 'Menyimpan...' : 'Bayar'}
                            </Button>
                        </div>
                    </Card>
                </form>
            )}

            {konfirmasi && (
                <ConfirmDialog
                    judul="Terima pembayaran ini?"
                    pesan={`${krama.nama} membayar ${rupiah(total)} secara ${metode === 'transfer' ? 'transfer' : 'tunai'}.`}
                    labelYa="Ya, bayar"
                    memproses={memproses}
                    onYa={bayar}
                    onBatal={() => setKonfirmasi(false)}
                >
                    <ul className="divide-y divide-garis rounded border border-garis px-3 text-sm">
                        {terbuka.filter((baris) => pilihan[baris.id] !== undefined).map((baris) => (
                            <li key={baris.id} className="flex justify-between gap-4 py-2">
                                <span className="min-w-0">{baris.tahun} · {baris.jenis_label}</span>
                                <span className="font-medium whitespace-nowrap tabular-nums">{rupiah(Number(pilihan[baris.id]))}</span>
                            </li>
                        ))}
                    </ul>
                </ConfirmDialog>
            )}

            <RiwayatTagihan tagihan={riwayat} krama={krama} tunggakan={data.tunggakan} />
        </>
    );
}
