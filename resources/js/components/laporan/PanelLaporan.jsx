import { Eye } from 'lucide-react';
import { useState } from 'react';
import api from '../../lib/api';
import { pesanGalat } from '../../lib/galat';
import Alert from '../ui/Alert';
import Badge from '../ui/Badge';
import Button from '../ui/Button';
import DefinitionGrid from '../ui/DefinitionGrid';

export default function PanelLaporan({ laporan, memproses, onUbahStatus }) {
    const [nik, setNik] = useState(null);
    const [galat, setGalat] = useState('');
    const ditindak = laporan.status === 'ditindak';

    async function tampilkanNik() {
        setGalat('');

        try {
            const res = await api.get(`/laporan-warga/${laporan.id}/nik`);
            setNik(res.data.nik);
        } catch (error) {
            setGalat(pesanGalat(error, 'NIK gagal dibuka.'));
        }
    }

    const masaTinggal = laporan.menetap ? `${laporan.tanggal_mulai}, menetap` : `${laporan.tanggal_mulai} hingga ${laporan.tanggal_selesai}`;

    return (
        <li className="grid gap-3 p-4">
            <div className="flex flex-wrap items-start justify-between gap-3">
                <div className="min-w-0">
                    <p className="font-semibold break-words">{laporan.nama}</p>
                    <p className="text-sm text-redup">Dikirim {laporan.dibuat_at}</p>
                </div>
                <div className="flex flex-wrap items-center gap-2">
                    <Badge>{laporan.jenis_label}</Badge>
                    {ditindak ? <Badge nada="berhasil">Ditindak</Badge> : <Badge nada="peringatan">Pending</Badge>}
                </div>
            </div>

            <div className="flex flex-wrap items-center gap-3">
                <p className="font-mono text-sm break-all">{nik ?? laporan.nik_tersamar}</p>
                {nik === null && (
                    <Button varian="garis" ukuran="kecil" onClick={tampilkanNik} aria-label={`Tampilkan NIK ${laporan.nama}`}>
                        <Eye className="size-4" aria-hidden="true" />
                        Tampilkan NIK
                    </Button>
                )}
            </div>

            <DefinitionGrid
                items={[
                    { label: 'Asal', isi: laporan.asal },
                    { label: 'Alamat di Sangket', isi: laporan.alamat },
                    { label: 'Masa tinggal', isi: masaTinggal },
                    ...(laporan.jenis === 'usaha' ? [
                        { label: 'Nama usaha', isi: laporan.nama_usaha },
                        { label: 'Jenis usaha', isi: laporan.jenis_usaha },
                    ] : []),
                    ...(ditindak ? [{ label: 'Ditindak', isi: `${laporan.ditindak_oleh ?? '-'}, ${laporan.ditindak_at}` }] : []),
                ]}
            />

            {galat && <Alert nada="gagal" role="alert">{galat}</Alert>}

            <div className="flex justify-end">
                <Button varian={ditindak ? 'garis' : 'utama'} disabled={memproses} onClick={() => onUbahStatus(laporan, ditindak ? 'pending' : 'ditindak')}>
                    {ditindak ? 'Kembalikan ke pending' : 'Tandai ditindak'}
                </Button>
            </div>
        </li>
    );
}
