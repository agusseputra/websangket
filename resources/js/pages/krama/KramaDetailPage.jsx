import { Eye, MapPin, Pencil, Trash2, WalletCards } from 'lucide-react';
import { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import StatusDataBadge from '../../components/krama/StatusDataBadge';
import Alert from '../../components/ui/Alert';
import Button from '../../components/ui/Button';
import Card from '../../components/ui/Card';
import ConfirmDialog from '../../components/ui/ConfirmDialog';
import DefinitionGrid from '../../components/ui/DefinitionGrid';
import PageHeader from '../../components/ui/PageHeader';
import { useAuth } from '../../context/AuthContext';
import api from '../../lib/api';
import { pesanGalat } from '../../lib/galat';

export default function KramaDetailPage() {
    const { id } = useParams();
    const { user } = useAuth();
    const navigate = useNavigate();
    const [krama, setKrama] = useState(null);
    const [nik, setNik] = useState(null);
    const [galat, setGalat] = useState('');
    const [konfirmasiHapus, setKonfirmasiHapus] = useState(false);
    const [menghapus, setMenghapus] = useState(false);

    useEffect(() => {
        api.get(`/krama/${id}`)
            .then((res) => setKrama(res.data.data))
            .catch((error) => setGalat(pesanGalat(error, 'Data krama gagal dimuat.')));
    }, [id]);

    async function tampilkanNik() {
        try {
            const res = await api.get(`/krama/${id}/nik`);
            setNik(res.data.nik);
        } catch (error) {
            setGalat(pesanGalat(error, 'NIK gagal dibuka.'));
        }
    }

    async function hapus() {
        setMenghapus(true);

        try {
            await api.delete(`/krama/${id}`);
            navigate('/krama', { replace: true });
        } catch (error) {
            setGalat(pesanGalat(error, 'Data krama gagal dihapus.'));
            setKonfirmasiHapus(false);
            setMenghapus(false);
        }
    }

    if (!krama) {
        return galat ? <Alert nada="gagal" role="alert">{galat}</Alert> : <p className="text-redup">Memuat...</p>;
    }

    return (
        <>
            <div className="flex flex-wrap items-start justify-between gap-3">
                <PageHeader judul={krama.nama} deskripsi={krama.status_label} />
                <div className="flex flex-wrap gap-2">
                    <Button as={Link} to={`/krama/${id}/bayar`}>
                        <WalletCards className="size-4" aria-hidden="true" />
                        Bayar tagihan
                    </Button>
                    <Button as={Link} varian="garis" to={`/krama/${id}/lokasi`}>
                        <MapPin className="size-4" aria-hidden="true" />
                        Tandai lokasi
                    </Button>
                    <Button as={Link} varian="garis" to={`/krama/${id}/ubah`}>
                        <Pencil className="size-4" aria-hidden="true" />
                        Ubah
                    </Button>
                    {user.role === 'admin' && (
                        <Button varian="garis" onClick={() => setKonfirmasiHapus(true)}>
                            <Trash2 className="size-4" aria-hidden="true" />
                            Hapus
                        </Button>
                    )}
                </div>
            </div>

            {galat && <Alert nada="gagal" role="alert">{galat}</Alert>}

            {krama.status_data === 'pending' && (
                <Alert>Data pending: {krama.alasan_pending.join(', ')}.</Alert>
            )}

            <Card judul="Identitas">
                <div className="flex flex-wrap items-center gap-3">
                    <div className="min-w-0">
                        <p className="text-sm text-redup">NIK</p>
                        <p className="font-mono break-all">{nik ?? krama.nik_tersamar ?? '–'}</p>
                    </div>
                    {krama.nik_tersamar && nik === null && (
                        <Button varian="garis" onClick={tampilkanNik}>
                            <Eye className="size-4" aria-hidden="true" />
                            Tampilkan NIK
                        </Button>
                    )}
                </div>
                <DefinitionGrid
                    items={[
                        { label: 'Status data', isi: <StatusDataBadge status={krama.status_data} /> },
                        { label: 'Asal', isi: krama.asal },
                        { label: 'Pekerjaan', isi: krama.pekerjaan },
                        { label: 'No HP', isi: krama.no_hp },
                    ]}
                />
            </Card>

            <Card judul="Tempat tinggal">
                <DefinitionGrid
                    items={[
                        { label: 'Perumahan', isi: krama.perumahan },
                        { label: 'Nomor rumah', isi: krama.nomor_rumah },
                        { label: 'Jumlah penghuni', isi: krama.jumlah_penghuni },
                        { label: 'Tanggal mulai menetap', isi: krama.tanggal_mulai_menetap },
                        { label: 'Lokasi', isi: krama.ada_lokasi ? 'Sudah ditandai' : 'Belum ditandai' },
                        { label: 'Keaktifan', isi: krama.aktif ? 'Aktif' : 'Nonaktif' },
                        { label: 'Keterangan', isi: krama.keterangan },
                    ]}
                />
            </Card>

            {konfirmasiHapus && (
                <ConfirmDialog
                    judul="Hapus data krama?"
                    pesan={`Data ${krama.nama} tidak lagi tampil di daftar dan tidak ikut Generate tagihan.`}
                    labelYa="Hapus"
                    memproses={menghapus}
                    onYa={hapus}
                    onBatal={() => setKonfirmasiHapus(false)}
                />
            )}
        </>
    );
}
