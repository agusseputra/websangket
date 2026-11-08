import { useState } from 'react';
import api from '../../lib/api';
import { galatKolom, pesanGalat } from '../../lib/galat';
import Alert from '../ui/Alert';
import Button from '../ui/Button';
import SelectField from '../ui/SelectField';
import TextField from '../ui/TextField';
import PilihTitik from './PilihTitik';

export default function LokasiForm({ krama, perumahan, labelSimpan = 'Simpan lokasi', onTersimpan }) {
    const [titik, setTitik] = useState(krama.latitude ? { latitude: krama.latitude, longitude: krama.longitude } : null);
    const [perumahanId, setPerumahanId] = useState(krama.perumahan_id ?? '');
    const [nomorRumah, setNomorRumah] = useState(krama.nomor_rumah ?? '');
    const [galat, setGalat] = useState({});
    const [galatUmum, setGalatUmum] = useState('');
    const [menyimpan, setMenyimpan] = useState(false);

    async function simpan(event) {
        event.preventDefault();
        setGalat({});
        setGalatUmum('');

        if (!titik) {
            setGalat({ latitude: 'Tandai titik lokasi terlebih dahulu.' });
            return;
        }

        setMenyimpan(true);

        try {
            const res = await api.put(`/krama/${krama.id}/lokasi`, {
                latitude: titik.latitude,
                longitude: titik.longitude,
                perumahan_id: perumahanId || null,
                nomor_rumah: nomorRumah || null,
            });

            await onTersimpan(res.data.data);
        } catch (error) {
            const kolom = galatKolom(error);

            setGalat(kolom);
            setGalatUmum(Object.keys(kolom).length > 0 ? '' : pesanGalat(error, 'Lokasi gagal disimpan.'));
            setMenyimpan(false);
        }
    }

    return (
        <form onSubmit={simpan} className="grid gap-4">
            {galatUmum && <Alert nada="gagal" role="alert">{galatUmum}</Alert>}

            <PilihTitik titik={titik} onUbah={setTitik} galat={galat.latitude} petunjuk="Gunakan GPS saat berada di rumah krama, atau ketuk titik rumahnya di peta." />

            <div className="grid gap-4 md:grid-cols-2">
                <SelectField
                    id="lokasi-perumahan"
                    label="Perumahan"
                    kosong="Pilih perumahan"
                    required
                    value={perumahanId}
                    onChange={(e) => setPerumahanId(e.target.value)}
                    galat={galat.perumahan_id}
                    pilihan={perumahan.map(({ id, nama }) => ({ nilai: id, label: nama }))}
                />
                <TextField id="lokasi-nomor" label="Nomor rumah (opsional)" value={nomorRumah} onChange={(e) => setNomorRumah(e.target.value)} galat={galat.nomor_rumah} />
            </div>

            <div className="flex justify-end">
                <Button type="submit" disabled={menyimpan}>{menyimpan ? 'Menyimpan...' : labelSimpan}</Button>
            </div>
        </form>
    );
}
