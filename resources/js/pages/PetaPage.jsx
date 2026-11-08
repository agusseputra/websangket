import { lazy, Suspense, useEffect, useRef, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import CariKrama from '../components/krama/CariKrama';
import Alert from '../components/ui/Alert';
import PageHeader from '../components/ui/PageHeader';
import { WARNA_PERUMAHAN, WARNA_STATUS } from '../config/peta';
import api from '../lib/api';
import { pesanGalat } from '../lib/galat';

// Leaflet cukup besar, jadi hanya dimuat saat halaman peta dibuka.
const PetaSebaran = lazy(() => import('../components/peta/PetaSebaran'));

function Legenda() {
    return (
        <ul className="flex flex-wrap gap-x-4 gap-y-1 text-sm">
            {WARNA_STATUS.map(({ status, label, warna }) => (
                <li key={status} className="flex items-center gap-2">
                    <span className="size-3 rounded-full" style={{ backgroundColor: warna }} aria-hidden="true" />
                    {label}
                </li>
            ))}
            <li className="flex items-center gap-2">
                <span className="size-3" style={{ backgroundColor: WARNA_PERUMAHAN }} aria-hidden="true" />
                Perumahan
            </li>
        </ul>
    );
}

export default function PetaPage() {
    const navigate = useNavigate();
    const petaRef = useRef(null);
    const [data, setData] = useState(null);
    const [galat, setGalat] = useState('');
    const [info, setInfo] = useState('');

    useEffect(() => {
        api.get('/peta')
            .then((res) => setData(res.data))
            .catch((error) => setGalat(pesanGalat(error, 'Data peta gagal dimuat.')));
    }, []);

    function fokus(krama) {
        const berhasil = petaRef.current?.fokusKrama(krama.id);

        setInfo(berhasil ? '' : `${krama.nama} belum tampil di peta karena belum punya titik lokasi atau tidak aktif.`);
    }

    return (
        <>
            <PageHeader
                judul="Peta krama"
                deskripsi={data ? `${data.krama.length} krama dan ${data.perumahan.length} perumahan punya titik lokasi.` : 'Persebaran krama dan perumahan.'}
            />

            <div className="max-w-xl">
                <CariKrama id="cari-peta" label="Cari krama di peta" onPilih={fokus} />
            </div>

            {galat && <Alert nada="gagal" role="alert">{galat}</Alert>}
            {info && <Alert>{info}</Alert>}

            <Legenda />

            {!data && !galat && <p className="text-redup">Memuat peta...</p>}

            {data && (
                <Suspense fallback={<p className="text-redup">Memuat peta...</p>}>
                    <PetaSebaran ref={petaRef} data={data} onDetail={(id) => navigate(`/krama/${id}`)} />
                </Suspense>
            )}
        </>
    );
}
