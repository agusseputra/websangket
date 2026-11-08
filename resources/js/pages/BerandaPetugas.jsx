import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import Alert from '../components/ui/Alert';
import Button from '../components/ui/Button';
import PageHeader from '../components/ui/PageHeader';
import StatCard from '../components/ui/StatCard';
import StatGrid from '../components/ui/StatGrid';
import api from '../lib/api';

export default function BerandaPetugas() {
    const [data, setData] = useState({});
    const [galat, setGalat] = useState(false);

    useEffect(() => {
        api.get('/petugas/ringkasan')
            .then((res) => setData(res.data))
            .catch(() => setGalat(true));
    }, []);

    return (
        <>
            <PageHeader judul="Tugas petugas" deskripsi="Tandai lokasi krama yang belum punya titik, dan tambahkan krama yang belum terdata." />
            {galat && <Alert nada="gagal" role="alert">Ringkasan gagal dimuat. Muat ulang halaman ini.</Alert>}
            <StatGrid>
                <StatCard label="Belum ada lokasi" nilai={data.krama_tanpa_lokasi} />
                <StatCard label="Total krama" nilai={data.krama} />
            </StatGrid>
            <div className="flex flex-wrap gap-2">
                <Button as={Link} to="/krama/baru">Tambah krama</Button>
                <Button as={Link} varian="garis" to="/krama?lokasi=belum">Lihat yang belum ada lokasi</Button>
            </div>
        </>
    );
}
