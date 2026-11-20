import { Pencil, Trash2 } from 'lucide-react';
import { useState } from 'react';
import Alert from '../components/ui/Alert';
import Button from '../components/ui/Button';
import Card from '../components/ui/Card';
import ConfirmDialog from '../components/ui/ConfirmDialog';
import IconButton from '../components/ui/IconButton';
import PilihTitik from '../components/krama/PilihTitik';
import PageHeader from '../components/ui/PageHeader';
import TextField from '../components/ui/TextField';
import usePerumahan from '../hooks/usePerumahan';
import api from '../lib/api';
import { galatKolom, pesanGalat } from '../lib/galat';

export default function PerumahanPage() {
    const { perumahan, memuat, galat, muat } = usePerumahan();
    const [nama, setNama] = useState('');
    const [titik, setTitik] = useState(null);
    const [galatTitik, setGalatTitik] = useState({});
    const [sedangDiubah, setSedangDiubah] = useState(null);
    const [akanDihapus, setAkanDihapus] = useState(null);
    const [galatForm, setGalatForm] = useState('');
    const [galatHapus, setGalatHapus] = useState('');
    const [memproses, setMemproses] = useState(false);

    function reset() {
        setNama('');
        setTitik(null);
        setGalatTitik({});
        setSedangDiubah(null);
        setGalatForm('');
    }

    async function simpan(event) {
        event.preventDefault();
        setMemproses(true);
        setGalatForm('');
        setGalatTitik({});

        const data = {
            nama,
            latitude: titik?.latitude === '' ? null : titik?.latitude ?? null,
            longitude: titik?.longitude === '' ? null : titik?.longitude ?? null,
        };

        try {
            if (sedangDiubah) {
                await api.put(`/perumahan/${sedangDiubah.id}`, data);
            } else {
                await api.post('/perumahan', data);
            }

            reset();
            await muat();
        } catch (error) {
            const kolom = galatKolom(error);

            setGalatTitik(kolom);
            setGalatForm(kolom.nama ?? (kolom.latitude || kolom.longitude ? '' : pesanGalat(error)));
        } finally {
            setMemproses(false);
        }
    }

    async function hapus() {
        setMemproses(true);
        setGalatHapus('');

        try {
            await api.delete(`/perumahan/${akanDihapus.id}`);
            await muat();
        } catch (error) {
            setGalatHapus(pesanGalat(error, 'Perumahan gagal dihapus.'));
        } finally {
            setAkanDihapus(null);
            setMemproses(false);
        }
    }

    return (
        <>
            <PageHeader judul="Perumahan" deskripsi="Daftar pilihan perumahan untuk data krama." />
            {(galat || galatHapus) && <Alert nada="gagal" role="alert">{galat || galatHapus}</Alert>}

            <div className="grid items-start gap-6 xl:grid-cols-[minmax(0,3fr)_minmax(0,2fr)]">
                <Card judul={sedangDiubah ? `Ubah ${sedangDiubah.nama}` : 'Tambah perumahan'}>
                    <form onSubmit={simpan} className="grid gap-3">
                        <TextField id="nama-perumahan" label="Nama perumahan" required value={nama} onChange={(e) => setNama(e.target.value)} galat={galatForm} />
                        <PilihTitik
                            key={sedangDiubah?.id ?? 'baru'}
                            titik={titik}
                            onUbah={setTitik}
                            galat={galatTitik.latitude}
                            galatBujur={galatTitik.longitude}
                            petunjuk="Opsional. Ketik koordinat, gunakan GPS, atau ketuk titik di peta."
                            inputManual
                            tinggiPeta="h-[26rem] md:h-[34rem]"
                        />
                        <div className="flex flex-wrap justify-end gap-2">
                            {sedangDiubah && <Button varian="garis" onClick={reset}>Batal</Button>}
                            <Button type="submit" disabled={memproses}>Simpan</Button>
                        </div>
                    </form>
                </Card>

                <section aria-label="Daftar perumahan" className="min-w-0">
                    {memuat && <p className="text-redup">Memuat...</p>}
                    <ul className="divide-y divide-garis rounded border border-garis bg-panel">
                        {perumahan.map((item) => (
                            <li key={item.id} className="flex items-center justify-between gap-3 py-1 pr-1 pl-4">
                                <div className="min-w-0">
                                    <p className="font-medium break-words">{item.nama}</p>
                                    <p className="text-sm text-redup">{item.krama_count} krama · {item.latitude ? 'Titik tersimpan' : 'Belum ada titik'}</p>
                                </div>
                                <div className="flex shrink-0">
                                    <IconButton
                                        label={`Ubah ${item.nama}`}
                                        ikon={Pencil}
                                        onClick={() => {
                                            setSedangDiubah(item);
                                            setNama(item.nama);
                                            setTitik(item.latitude ? { latitude: item.latitude, longitude: item.longitude } : null);
                                            setGalatTitik({});
                                            window.scrollTo({ top: 0 });
                                            setGalatForm('');
                                        }}
                                    />
                                    <IconButton
                                        label={`Hapus ${item.nama}`}
                                        ikon={Trash2}
                                        onClick={() => {
                                            setGalatHapus('');
                                            setAkanDihapus(item);
                                        }}
                                    />
                                </div>
                            </li>
                        ))}
                    </ul>
                </section>
            </div>

            {akanDihapus && (
                <ConfirmDialog
                    judul={`Hapus ${akanDihapus.nama}?`}
                    pesan="Perumahan yang masih dipakai data krama tidak bisa dihapus."
                    labelYa="Hapus"
                    memproses={memproses}
                    onYa={hapus}
                    onBatal={() => setAkanDihapus(null)}
                />
            )}
        </>
    );
}
