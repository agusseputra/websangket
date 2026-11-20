import { ExternalLink, ImageOff, Pencil, Plus, Trash2 } from 'lucide-react';
import { useEffect, useState } from 'react';
import { Link, useLocation, useNavigate, useSearchParams } from 'react-router-dom';
import Alert from '../../components/ui/Alert';
import Button from '../../components/ui/Button';
import ConfirmDialog from '../../components/ui/ConfirmDialog';
import IconButton from '../../components/ui/IconButton';
import PageHeader from '../../components/ui/PageHeader';
import Pagination from '../../components/ui/Pagination';
import SelectField from '../../components/ui/SelectField';
import TextField from '../../components/ui/TextField';
import useDebouncedValue from '../../hooks/useDebouncedValue';
import useKategoriBerita from '../../hooks/useKategoriBerita';
import api from '../../lib/api';
import { pesanGalat } from '../../lib/galat';

const formatTanggal = new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

export default function BeritaListPage() {
    const [params, setParams] = useSearchParams();
    const lokasi = useLocation();
    const navigate = useNavigate();
    const [cari, setCari] = useState(params.get('q') ?? '');
    const [hasil, setHasil] = useState(null);
    const [galat, setGalat] = useState('');
    const [memuat, setMemuat] = useState(true);
    const [akanDihapus, setAkanDihapus] = useState(null);
    const [memproses, setMemproses] = useState(false);
    const [info, setInfo] = useState(lokasi.state?.info ?? '');
    const [muatUlang, setMuatUlang] = useState(0);
    const { kategori } = useKategoriBerita();

    const cariTertunda = useDebouncedValue(cari);
    const kueri = params.toString();

    function ubahParam(perubahan) {
        setParams((lama) => {
            const baru = new URLSearchParams(lama);

            Object.entries(perubahan).forEach(([kunci, nilai]) => {
                if (nilai === '' || nilai === null) baru.delete(kunci);
                else baru.set(kunci, nilai);
            });

            return baru;
        }, { replace: true });
    }

    // Pesan sukses dari halaman form cukup tampil sekali.
    useEffect(() => {
        if (lokasi.state?.info) navigate(`${lokasi.pathname}${lokasi.search}`, { replace: true, state: null });
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    useEffect(() => {
        if ((params.get('q') ?? '') !== cariTertunda) ubahParam({ q: cariTertunda, page: '' });
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [cariTertunda]);

    useEffect(() => {
        let batal = false;

        setMemuat(true);
        setGalat('');

        api.get(`/berita?${kueri}`)
            .then((res) => {
                if (!batal) setHasil(res.data);
            })
            .catch((error) => {
                if (!batal) setGalat(pesanGalat(error, 'Daftar berita gagal dimuat.'));
            })
            .finally(() => {
                if (!batal) setMemuat(false);
            });

        return () => {
            batal = true;
        };
    }, [kueri, muatUlang]);

    async function hapus() {
        setMemproses(true);
        setInfo('');

        try {
            await api.delete(`/berita/${akanDihapus.id}`);
            setInfo(`Berita "${akanDihapus.judul}" dihapus.`);
            setMuatUlang((n) => n + 1);
        } catch (error) {
            setGalat(pesanGalat(error, 'Berita gagal dihapus.'));
        } finally {
            setAkanDihapus(null);
            setMemproses(false);
        }
    }

    const berita = hasil?.data ?? [];

    return (
        <>
            <div className="flex flex-wrap items-start justify-between gap-3">
                <PageHeader judul="Berita" deskripsi="Berita tampil di halaman publik /listarticle dan beranda." />
                <Button as={Link} to="/berita/baru">
                    <Plus className="size-5" aria-hidden="true" />
                    Tulis berita
                </Button>
            </div>

            {galat && <Alert nada="gagal" role="alert">{galat}</Alert>}
            {info && <Alert nada="berhasil">{info}</Alert>}

            <div className="grid gap-3 sm:grid-cols-[1fr_16rem]">
                <TextField id="cari-berita" type="search" label="Cari judul" value={cari} onChange={(e) => setCari(e.target.value)} maxLength={100} />
                <SelectField
                    id="filter-kategori"
                    label="Kategori"
                    kosong="Semua kategori"
                    pilihan={kategori.map((satu) => ({ nilai: String(satu.id), label: satu.nama }))}
                    value={params.get('kategori_id') ?? ''}
                    onChange={(e) => ubahParam({ kategori_id: e.target.value, page: '' })}
                />
            </div>

            <section aria-label="Daftar berita" aria-busy={memuat} className="grid gap-3">
                {memuat && !hasil && <p className="text-redup">Memuat...</p>}
                {hasil && berita.length === 0 && <p className="rounded border border-garis bg-panel p-4 text-redup">Tidak ada berita yang cocok.</p>}

                <ul className="divide-y divide-garis rounded border border-garis bg-panel">
                    {berita.map((item) => (
                        <li key={item.id} className="flex gap-3 p-3">
                            {item.sampul ? (
                                <img src={item.sampul} alt="" loading="lazy" className="aspect-[4/3] w-20 shrink-0 rounded object-cover sm:w-28" />
                            ) : (
                                <span className="grid aspect-[4/3] w-20 shrink-0 place-items-center rounded bg-kertas text-redup sm:w-28" title="Tanpa foto sampul">
                                    <ImageOff className="size-5" aria-hidden="true" />
                                </span>
                            )}
                            <div className="grid min-w-0 flex-1 content-start gap-1">
                                <p className="text-xs font-semibold text-utama uppercase">{item.kategori}</p>
                                <Link to={`/berita/${item.id}/ubah`} className="font-semibold break-words hover:underline">{item.judul}</Link>
                                <p className="line-clamp-2 text-sm text-redup">{item.ringkasan}</p>
                                <p className="text-xs text-redup">
                                    Dibuat {formatTanggal.format(new Date(item.dibuat))}
                                    {item.diubah !== item.dibuat && ` · diubah ${formatTanggal.format(new Date(item.diubah))}`}
                                </p>
                            </div>
                            <div className="flex shrink-0 flex-col sm:flex-row">
                                <a href={item.url} target="_blank" rel="noopener noreferrer" aria-label={`Lihat ${item.judul} di web`} className="inline-flex size-11 items-center justify-center rounded hover:bg-kertas">
                                    <ExternalLink className="size-5" aria-hidden="true" />
                                </a>
                                <IconButton label={`Ubah ${item.judul}`} ikon={Pencil} onClick={() => navigate(`/berita/${item.id}/ubah`)} />
                                <IconButton label={`Hapus ${item.judul}`} ikon={Trash2} onClick={() => setAkanDihapus(item)} />
                            </div>
                        </li>
                    ))}
                </ul>

                <Pagination meta={hasil?.meta} onPindah={(halaman) => ubahParam({ page: String(halaman) })} />
            </section>

            {akanDihapus && (
                <ConfirmDialog
                    judul="Hapus berita?"
                    pesan={`"${akanDihapus.judul}" dan foto sampulnya akan dihapus permanen, dan alamatnya di web tidak bisa dibuka lagi.`}
                    labelYa="Hapus"
                    memproses={memproses}
                    onYa={hapus}
                    onBatal={() => setAkanDihapus(null)}
                />
            )}
        </>
    );
}
