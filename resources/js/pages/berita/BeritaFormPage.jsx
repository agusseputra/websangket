import { ExternalLink, ImagePlus, X } from 'lucide-react';
import { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';
import EditorBerita from '../../components/berita/EditorBerita';
import Alert from '../../components/ui/Alert';
import Button from '../../components/ui/Button';
import Card from '../../components/ui/Card';
import PageHeader from '../../components/ui/PageHeader';
import SelectField from '../../components/ui/SelectField';
import TextField from '../../components/ui/TextField';
import useKategoriBerita from '../../hooks/useKategoriBerita';
import api from '../../lib/api';
import { periksaGambar, TIPE_SAMPUL, validasiBerita } from '../../lib/berita';
import { galatKolom, pesanGalat } from '../../lib/galat';

const KOSONG = { judul: '', kategori_id: '', isi: '' };

export default function BeritaFormPage() {
    const { id } = useParams();
    const navigate = useNavigate();
    const { kategori, galat: galatKategori } = useKategoriBerita();
    const [nilai, setNilai] = useState(KOSONG);
    const [berita, setBerita] = useState(null);
    const [siap, setSiap] = useState(!id);
    const [sampul, setSampul] = useState(null);
    const [pratinjau, setPratinjau] = useState('');
    const [hapusSampul, setHapusSampul] = useState(false);
    const [galat, setGalat] = useState({});
    const [galatUmum, setGalatUmum] = useState('');
    const [memproses, setMemproses] = useState(false);

    useEffect(() => {
        if (!id) return;

        api.get(`/berita/${id}`)
            .then((res) => {
                const data = res.data.data;

                setBerita(data);
                setNilai({ judul: data.judul, kategori_id: String(data.kategori_id), isi: data.isi });
                setSiap(true);
            })
            .catch((error) => setGalatUmum(pesanGalat(error, 'Berita gagal dimuat.')));
    }, [id]);

    useEffect(() => () => pratinjau && URL.revokeObjectURL(pratinjau), [pratinjau]);

    function ubah(kunci, isi) {
        setNilai((lama) => ({ ...lama, [kunci]: isi }));
        setGalat((lama) => ({ ...lama, [kunci]: undefined }));
    }

    async function pilihSampul(event) {
        const file = event.target.files?.[0];
        event.target.value = '';

        if (!file) return;

        const masalah = await periksaGambar(file, { lebarMin: 400, tinggiMin: 200, label: 'Foto sampul' });

        if (masalah) {
            setGalat((lama) => ({ ...lama, sampul: masalah }));

            return;
        }

        setGalat((lama) => ({ ...lama, sampul: undefined }));
        setSampul(file);
        setPratinjau(URL.createObjectURL(file));
        setHapusSampul(false);
    }

    function buangSampul() {
        setSampul(null);
        setPratinjau('');
        setHapusSampul(Boolean(berita?.sampul));
    }

    async function simpan(event) {
        event.preventDefault();
        setGalatUmum('');

        const masalah = validasiBerita(nilai);

        if (Object.keys(masalah).length > 0) {
            setGalat(masalah);
            setGalatUmum('Periksa kembali isian yang ditandai.');
            document.getElementById(`berita-${Object.keys(masalah)[0].replace('_id', '')}`)?.focus();

            return;
        }

        const data = new FormData();
        data.append('judul', nilai.judul);
        data.append('kategori_id', nilai.kategori_id);
        data.append('isi', nilai.isi);
        if (sampul) data.append('sampul', sampul);
        if (id) {
            data.append('_method', 'PUT');
            data.append('hapus_sampul', hapusSampul ? '1' : '0');
        }

        setMemproses(true);

        try {
            await api.post(id ? `/berita/${id}` : '/berita', data);
            navigate('/berita', { state: { info: id ? 'Berita diperbarui.' : 'Berita diterbitkan.' } });
        } catch (error) {
            const kolom = galatKolom(error);

            setGalat(kolom);
            setGalatUmum(Object.keys(kolom).length > 0 ? 'Periksa kembali isian yang ditandai.' : pesanGalat(error, 'Berita gagal disimpan.'));
        } finally {
            setMemproses(false);
        }
    }

    const sampulTampil = pratinjau || (!hapusSampul && berita?.sampul) || '';

    return (
        <>
            <div className="flex flex-wrap items-start justify-between gap-3">
                <PageHeader judul={id ? 'Ubah berita' : 'Tulis berita'} deskripsi="Judul menjadi alamat berita, dan 225 karakter pertama isi menjadi deskripsi di Google." />
                {berita && (
                    <Button as="a" varian="garis" href={berita.url} target="_blank" rel="noopener noreferrer">
                        <ExternalLink className="size-5" aria-hidden="true" />
                        Lihat di web
                    </Button>
                )}
            </div>

            {(galatUmum || galatKategori) && <Alert nada="gagal" role="alert">{galatUmum || galatKategori}</Alert>}

            {!siap && !galatUmum && <p className="text-redup">Memuat...</p>}

            {siap && (
                <form onSubmit={simpan} noValidate className="grid items-start gap-6 lg:grid-cols-[1fr_18rem]">
                    <div className="grid min-w-0 gap-4">
                        <TextField
                            id="berita-judul"
                            label="Judul"
                            required
                            maxLength={200}
                            value={nilai.judul}
                            onChange={(e) => ubah('judul', e.target.value)}
                            galat={galat.judul}
                            bantuan={`${nilai.judul.trim().length}/200 karakter, minimal 10.`}
                        />

                        <div className="grid min-w-0 gap-1">
                            <span id="berita-isi-label" className="text-sm font-medium">Isi berita</span>
                            <EditorBerita
                                id="berita-isi"
                                labelId="berita-isi-label"
                                nilaiAwal={nilai.isi}
                                galat={galat.isi}
                                onUbah={(isi) => ubah('isi', isi)}
                                onGalatGambar={(pesan) => setGalat((lama) => ({ ...lama, isi: pesan || undefined }))}
                            />
                            {galat.isi
                                ? <p id="berita-isi-galat" className="text-sm text-gagal">{galat.isi}</p>
                                : <p className="text-sm text-redup">Tombol gambar di toolbar mengunggah gambar (maks. 1 MB) ke dalam isi.</p>}
                        </div>
                    </div>

                    <div className="grid gap-4 lg:sticky lg:top-6">
                        <Card judul="Pengaturan">
                            <SelectField
                                id="berita-kategori"
                                label="Kategori"
                                required
                                kosong="Pilih kategori"
                                pilihan={kategori.map((satu) => ({ nilai: String(satu.id), label: satu.nama }))}
                                value={nilai.kategori_id}
                                onChange={(e) => ubah('kategori_id', e.target.value)}
                                galat={galat.kategori_id}
                            />
                            {kategori.length === 0 && <p className="text-sm text-redup">Belum ada kategori. <Link to="/kategori-berita" className="text-utama underline">Tambah kategori</Link></p>}

                            <div className="grid gap-2">
                                <span className="text-sm font-medium" id="berita-sampul-label">Foto sampul</span>
                                {sampulTampil ? (
                                    <div className="relative">
                                        <img src={sampulTampil} alt="Pratinjau foto sampul" className="aspect-[1200/630] w-full rounded border border-garis object-cover" />
                                        <button type="button" onClick={buangSampul} aria-label="Buang foto sampul" className="absolute top-1 right-1 grid size-9 place-items-center rounded-full bg-black/60 text-white hover:bg-black/80">
                                            <X className="size-5" aria-hidden="true" />
                                        </button>
                                    </div>
                                ) : (
                                    <p className="grid aspect-[1200/630] place-items-center rounded border border-dashed border-garis text-sm text-redup">Belum ada foto sampul</p>
                                )}
                                <label htmlFor="berita-sampul" className="inline-flex min-h-11 cursor-pointer items-center justify-center gap-2 rounded border border-garis px-4 font-medium hover:bg-kertas focus-within:outline-2">
                                    <ImagePlus className="size-5" aria-hidden="true" />
                                    {sampulTampil ? 'Ganti foto' : 'Pilih foto'}
                                    <input
                                        id="berita-sampul"
                                        type="file"
                                        accept={TIPE_SAMPUL.join(',')}
                                        onChange={pilihSampul}
                                        aria-invalid={Boolean(galat.sampul)}
                                        aria-describedby={galat.sampul ? 'berita-sampul-galat' : 'berita-sampul-bantuan'}
                                        className="sr-only"
                                    />
                                </label>
                                {galat.sampul
                                    ? <p id="berita-sampul-galat" className="text-sm text-gagal">{galat.sampul}</p>
                                    : <p id="berita-sampul-bantuan" className="text-sm text-redup">JPG, PNG, atau WebP, maks. 1 MB, minimal 400 × 200 piksel. Ideal 1200 × 630 untuk pratinjau di media sosial.</p>}
                            </div>
                        </Card>

                        <div className="flex flex-wrap justify-end gap-2">
                            <Button as={Link} to="/berita" varian="garis">Batal</Button>
                            <Button type="submit" disabled={memproses}>{memproses ? 'Menyimpan...' : id ? 'Simpan perubahan' : 'Terbitkan'}</Button>
                        </div>
                    </div>
                </form>
            )}
        </>
    );
}
