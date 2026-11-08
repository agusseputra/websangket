import { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import KramaForm from '../../components/krama/KramaForm';
import Alert from '../../components/ui/Alert';
import PageHeader from '../../components/ui/PageHeader';
import usePerumahan from '../../hooks/usePerumahan';
import api from '../../lib/api';
import { pesanGalat } from '../../lib/galat';

export default function KramaFormPage() {
    const { id } = useParams();
    const navigate = useNavigate();
    const { perumahan, memuat: memuatPerumahan, galat: galatPerumahan } = usePerumahan();
    const [krama, setKrama] = useState(null);
    const [galat, setGalat] = useState('');

    useEffect(() => {
        if (!id) return;

        api.get(`/krama/${id}`)
            .then((res) => setKrama(res.data.data))
            .catch((error) => setGalat(pesanGalat(error, 'Data krama gagal dimuat.')));
    }, [id]);

    async function simpan(data) {
        const res = id ? await api.put(`/krama/${id}`, data) : await api.post('/krama', data);
        // Krama baru wajib ditandai lokasinya, lalu langsung lanjut ke pembayaran.
        navigate(id ? `/krama/${id}` : `/krama/${res.data.data.id}/lokasi?lanjut=bayar`, { replace: true });
    }

    const pesan = galat || galatPerumahan;
    const siap = !memuatPerumahan && (!id || krama);

    return (
        <>
            <PageHeader judul={id ? 'Ubah data krama' : 'Tambah krama'} />
            {pesan && <Alert nada="gagal" role="alert">{pesan}</Alert>}
            {!pesan && !siap && <p className="text-redup">Memuat...</p>}
            {!pesan && siap && (
                <KramaForm krama={krama} perumahan={perumahan} onSimpan={simpan} onBatal={() => navigate(-1)} />
            )}
        </>
    );
}
