import { ExternalLink, Pencil, Trash2 } from 'lucide-react';
import { useState } from 'react';
import Alert from '../components/ui/Alert';
import Button from '../components/ui/Button';
import Card from '../components/ui/Card';
import ConfirmDialog from '../components/ui/ConfirmDialog';
import IconButton from '../components/ui/IconButton';
import PageHeader from '../components/ui/PageHeader';
import TextField from '../components/ui/TextField';
import useKategoriBerita from '../hooks/useKategoriBerita';
import api from '../lib/api';
import { validasiKategori } from '../lib/berita';
import { galatKolom, pesanGalat } from '../lib/galat';

export default function KategoriBeritaPage() {
    const { kategori, memuat, galat, muat } = useKategoriBerita();
    const [nama, setNama] = useState('');
    const [sedangDiubah, setSedangDiubah] = useState(null);
    const [akanDihapus, setAkanDihapus] = useState(null);
    const [galatForm, setGalatForm] = useState('');
    const [galatHapus, setGalatHapus] = useState('');
    const [info, setInfo] = useState('');
    const [memproses, setMemproses] = useState(false);

    function reset() {
        setNama('');
        setSedangDiubah(null);
        setGalatForm('');
    }

    async function simpan(event) {
        event.preventDefault();
        setInfo('');

        const masalah = validasiKategori(nama);

        if (masalah) {
            setGalatForm(masalah);

            return;
        }

        setMemproses(true);
        setGalatForm('');

        try {
            if (sedangDiubah) {
                await api.put(`/kategori-berita/${sedangDiubah.id}`, { nama });
            } else {
                await api.post('/kategori-berita', { nama });
            }

            setInfo(sedangDiubah ? 'Kategori diperbarui.' : 'Kategori ditambahkan.');
            reset();
            await muat();
        } catch (error) {
            setGalatForm(galatKolom(error).nama ?? pesanGalat(error, 'Kategori gagal disimpan.'));
        } finally {
            setMemproses(false);
        }
    }

    async function hapus() {
        setMemproses(true);
        setGalatHapus('');
        setInfo('');

        try {
            await api.delete(`/kategori-berita/${akanDihapus.id}`);
            setInfo(`Kategori ${akanDihapus.nama} dihapus.`);
            await muat();
        } catch (error) {
            setGalatHapus(pesanGalat(error, 'Kategori gagal dihapus.'));
        } finally {
            setAkanDihapus(null);
            setMemproses(false);
        }
    }

    return (
        <>
            <PageHeader judul="Kategori berita" deskripsi="Kategori menentukan alamat daftar berita, misalnya /listarticle/pengumuman." />
            {(galat || galatHapus) && <Alert nada="gagal" role="alert">{galat || galatHapus}</Alert>}
            {info && <Alert nada="berhasil">{info}</Alert>}

            <div className="grid items-start gap-6 lg:grid-cols-[22rem_1fr]">
                <Card judul={sedangDiubah ? `Ubah ${sedangDiubah.nama}` : 'Tambah kategori'}>
                    <form onSubmit={simpan} noValidate className="grid gap-3">
                        <TextField
                            id="nama-kategori"
                            label="Nama kategori"
                            required
                            maxLength={50}
                            value={nama}
                            onChange={(e) => {
                                setNama(e.target.value);
                                if (galatForm) setGalatForm('');
                            }}
                            galat={galatForm}
                            bantuan="3–50 karakter, harus unik."
                        />
                        <div className="flex flex-wrap justify-end gap-2">
                            {sedangDiubah && <Button varian="garis" onClick={reset}>Batal</Button>}
                            <Button type="submit" disabled={memproses}>{memproses ? 'Menyimpan...' : 'Simpan'}</Button>
                        </div>
                    </form>
                </Card>

                <section aria-label="Daftar kategori" className="min-w-0">
                    {memuat && <p className="text-redup">Memuat...</p>}
                    {!memuat && kategori.length === 0 && <p className="text-redup">Belum ada kategori.</p>}
                    <ul className="divide-y divide-garis rounded border border-garis bg-panel">
                        {kategori.map((item) => (
                            <li key={item.id} className="flex items-center justify-between gap-3 py-1 pr-1 pl-4">
                                <div className="min-w-0">
                                    <p className="font-medium break-words">{item.nama}</p>
                                    <p className="text-sm text-redup">{item.jumlah_berita} berita · /listarticle/{item.slug}</p>
                                </div>
                                <div className="flex shrink-0">
                                    <a href={item.url} target="_blank" rel="noopener noreferrer" aria-label={`Buka halaman kategori ${item.nama}`} className="inline-flex size-11 items-center justify-center rounded hover:bg-kertas">
                                        <ExternalLink className="size-5" aria-hidden="true" />
                                    </a>
                                    <IconButton
                                        label={`Ubah ${item.nama}`}
                                        ikon={Pencil}
                                        onClick={() => {
                                            setSedangDiubah(item);
                                            setNama(item.nama);
                                            setGalatForm('');
                                            setInfo('');
                                            window.scrollTo({ top: 0 });
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
                    pesan={akanDihapus.jumlah_berita > 0
                        ? `Kategori ini masih dipakai ${akanDihapus.jumlah_berita} berita, jadi tidak bisa dihapus. Pindahkan beritanya ke kategori lain lebih dulu.`
                        : 'Kategori yang tidak dipakai berita akan dihapus permanen.'}
                    labelYa="Hapus"
                    memproses={memproses}
                    onYa={hapus}
                    onBatal={() => setAkanDihapus(null)}
                />
            )}
        </>
    );
}
