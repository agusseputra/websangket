import { Plus } from 'lucide-react';
import { useEffect, useState } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import KramaFilter from '../../components/krama/KramaFilter';
import KramaList from '../../components/krama/KramaList';
import Alert from '../../components/ui/Alert';
import Button from '../../components/ui/Button';
import PageHeader from '../../components/ui/PageHeader';
import Pagination from '../../components/ui/Pagination';
import useDebouncedValue from '../../hooks/useDebouncedValue';
import usePerumahan from '../../hooks/usePerumahan';
import api from '../../lib/api';
import { pesanGalat } from '../../lib/galat';

const KUNCI_FILTER = ['status_data', 'status', 'perumahan_id', 'lokasi'];

export default function KramaListPage() {
    const [params, setParams] = useSearchParams();
    const [cari, setCari] = useState(params.get('q') ?? '');
    const [hasil, setHasil] = useState(null);
    const [galat, setGalat] = useState('');
    const [memuat, setMemuat] = useState(true);
    const { perumahan } = usePerumahan();

    const cariTertunda = useDebouncedValue(cari);
    const kueri = params.toString();

    const filter = Object.fromEntries(KUNCI_FILTER.map((kunci) => [kunci, params.get(kunci) ?? '']));

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

    useEffect(() => {
        if ((params.get('q') ?? '') !== cariTertunda) {
            ubahParam({ q: cariTertunda, page: '' });
        }
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [cariTertunda]);

    useEffect(() => {
        let batal = false;

        setMemuat(true);
        setGalat('');

        api.get(`/krama?${kueri}`)
            .then((res) => {
                if (!batal) setHasil(res.data);
            })
            .catch((error) => {
                if (!batal) setGalat(pesanGalat(error, 'Data krama gagal dimuat.'));
            })
            .finally(() => {
                if (!batal) setMemuat(false);
            });

        return () => {
            batal = true;
        };
    }, [kueri]);

    return (
        <>
            <div className="flex flex-wrap items-start justify-between gap-3">
                <PageHeader judul="Data krama" deskripsi="NIK hanya bisa dicari dengan 16 digit lengkap." />
                <Button as={Link} to="/krama/baru">
                    <Plus className="size-5" aria-hidden="true" />
                    Tambah krama
                </Button>
            </div>

            <KramaFilter
                cari={cari}
                onCari={setCari}
                filter={filter}
                onFilter={(kunci, nilai) => ubahParam({ [kunci]: nilai, page: '' })}
                perumahan={perumahan}
            />

            {galat && <Alert nada="gagal" role="alert">{galat}</Alert>}

            {memuat && !hasil && <p className="text-redup">Memuat data krama...</p>}

            {hasil && hasil.data.length === 0 && !memuat && (
                <p className="rounded border border-garis bg-panel p-6 text-center text-redup">
                    Tidak ada krama yang cocok. Ubah pencarian atau tambahkan krama baru.
                </p>
            )}

            {hasil && hasil.data.length > 0 && (
                <div aria-busy={memuat} className={`grid gap-4 ${memuat ? 'opacity-60' : ''}`}>
                    <KramaList data={hasil.data} />
                    <Pagination meta={hasil.meta} onPindah={(halaman) => ubahParam({ page: String(halaman) })} />
                </div>
            )}
        </>
    );
}
