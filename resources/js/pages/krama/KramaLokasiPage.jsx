import { useEffect, useState } from 'react';
import { useNavigate, useParams, useSearchParams } from 'react-router-dom';
import LokasiForm from '../../components/krama/LokasiForm';
import Alert from '../../components/ui/Alert';
import Card from '../../components/ui/Card';
import PageHeader from '../../components/ui/PageHeader';
import usePerumahan from '../../hooks/usePerumahan';
import api from '../../lib/api';
import { pesanGalat } from '../../lib/galat';

export default function KramaLokasiPage() {
    const { id } = useParams();
    const navigate = useNavigate();
    const [params] = useSearchParams();
    const lanjutBayar = params.get('lanjut') === 'bayar';
    const { perumahan, memuat } = usePerumahan();
    const [krama, setKrama] = useState(null);
    const [galat, setGalat] = useState('');

    useEffect(() => {
        api.get(`/krama/${id}`)
            .then((res) => setKrama(res.data.data))
            .catch((error) => setGalat(pesanGalat(error, 'Data krama gagal dimuat.')));
    }, [id]);

    return (
        <>
            <PageHeader judul="Tandai lokasi" deskripsi={krama?.nama} />
            {lanjutBayar && <Alert nada="berhasil">Krama tersimpan. Tandai lokasinya untuk lanjut ke pembayaran.</Alert>}
            {galat && <Alert nada="gagal" role="alert">{galat}</Alert>}
            {!galat && (!krama || memuat) && <p className="text-redup">Memuat...</p>}
            {krama && !memuat && (
                <Card>
                    <LokasiForm
                        krama={krama}
                        perumahan={perumahan}
                        labelSimpan={lanjutBayar ? 'Simpan dan lanjut bayar' : 'Simpan lokasi'}
                        onTersimpan={() => navigate(lanjutBayar ? `/krama/${id}/bayar` : `/krama/${id}`, { replace: true })}
                    />
                </Card>
            )}
        </>
    );
}
