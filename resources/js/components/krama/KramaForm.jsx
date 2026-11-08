import { useState } from 'react';
import { STATUS_KRAMA } from '../../config/krama';
import { useAuth } from '../../context/AuthContext';
import { galatKolom, pesanGalat } from '../../lib/galat';
import Alert from '../ui/Alert';
import Button from '../ui/Button';
import Card from '../ui/Card';
import SelectField from '../ui/SelectField';
import TextAreaField from '../ui/TextAreaField';
import TextField from '../ui/TextField';

const NILAI_KOSONG = {
    nama: '', asal: '', pekerjaan: '', perumahan_id: '', nomor_rumah: '', jumlah_penghuni: '',
    tanggal_mulai_menetap: '', no_hp: '', keterangan: '', status: 'krama_tamiu', aktif: true,
};

function nilaiAwal(krama) {
    if (!krama) return NILAI_KOSONG;

    return Object.fromEntries(Object.keys(NILAI_KOSONG).map((kunci) => [kunci, krama[kunci] ?? NILAI_KOSONG[kunci]]));
}

export default function KramaForm({ krama, perumahan, onSimpan, onBatal }) {
    const { user } = useAuth();
    const [nilai, setNilai] = useState(() => nilaiAwal(krama));
    const [nik, setNik] = useState('');
    const [galat, setGalat] = useState({});
    const [galatUmum, setGalatUmum] = useState('');
    const [menyimpan, setMenyimpan] = useState(false);

    const admin = user.role === 'admin';

    const ikat = (kunci) => ({
        value: nilai[kunci],
        onChange: (e) => setNilai((lama) => ({ ...lama, [kunci]: e.target.value })),
        galat: galat[kunci],
    });

    async function kirim(event) {
        event.preventDefault();
        setGalat({});
        setGalatUmum('');
        setMenyimpan(true);

        const data = Object.fromEntries(Object.entries(nilai).map(([k, v]) => [k, v === '' ? null : v]));

        if (nik !== '') {
            data.nik = nik;
        }

        if (!admin) {
            delete data.status;
            delete data.aktif;
        }

        try {
            await onSimpan(data);
        } catch (error) {
            const kolom = galatKolom(error);

            setGalat(kolom);
            setGalatUmum(Object.keys(kolom).length > 0 ? 'Periksa kembali isian yang ditandai.' : pesanGalat(error, 'Data gagal disimpan.'));
            setMenyimpan(false);
        }
    }

    return (
        <form onSubmit={kirim} className="grid gap-6">
            {galatUmum && <Alert nada="gagal" role="alert">{galatUmum}</Alert>}

            <Card judul="Identitas">
                <div className="grid gap-4 md:grid-cols-2">
                    <TextField id="nama" label="Nama" required {...ikat('nama')} />
                    <TextField
                        id="nik"
                        label="NIK"
                        inputMode="numeric"
                        autoComplete="off"
                        maxLength={16}
                        value={nik}
                        onChange={(e) => setNik(e.target.value.replace(/\D/g, ''))}
                        galat={galat.nik}
                        bantuan={krama ? 'Kosongkan jika NIK tidak diubah.' : '16 digit. Boleh dikosongkan, data menjadi pending.'}
                    />
                    <TextField id="asal" label="Asal" {...ikat('asal')} />
                    <TextField id="pekerjaan" label="Pekerjaan" {...ikat('pekerjaan')} />
                    <TextField id="no_hp" label="No HP" type="tel" {...ikat('no_hp')} />
                    {admin ? (
                        <SelectField id="status" label="Status krama" pilihan={STATUS_KRAMA} {...ikat('status')} />
                    ) : (
                        !krama && <p className="self-end text-sm text-redup">Status krama baru otomatis Krama Tamiu.</p>
                    )}
                </div>
            </Card>

            <Card judul="Tempat tinggal">
                <div className="grid gap-4 md:grid-cols-2">
                    <SelectField
                        id="perumahan_id"
                        label="Perumahan"
                        kosong="Pilih perumahan"
                        required={!krama}
                        pilihan={perumahan.map(({ id, nama }) => ({ nilai: id, label: nama }))}
                        {...ikat('perumahan_id')}
                    />
                    <TextField id="nomor_rumah" label="Nomor rumah (opsional)" {...ikat('nomor_rumah')} />
                    <TextField id="jumlah_penghuni" label="Jumlah penghuni" type="number" min="0" {...ikat('jumlah_penghuni')} />
                    <TextField id="tanggal_mulai_menetap" label="Tanggal mulai menetap" type="date" {...ikat('tanggal_mulai_menetap')} />
                </div>
                <TextAreaField id="keterangan" label="Keterangan" {...ikat('keterangan')} />
                {admin && krama && (
                    <label className="flex min-h-11 items-center gap-3 text-sm font-medium">
                        <input
                            id="aktif"
                            type="checkbox"
                            className="size-5"
                            checked={nilai.aktif}
                            onChange={(e) => setNilai((lama) => ({ ...lama, aktif: e.target.checked }))}
                        />
                        Krama aktif dan ikut Generate tagihan
                    </label>
                )}
            </Card>

            <div className="flex flex-wrap justify-end gap-2">
                <Button varian="garis" onClick={onBatal} disabled={menyimpan}>Batal</Button>
                <Button type="submit" disabled={menyimpan}>{menyimpan ? 'Menyimpan...' : 'Simpan'}</Button>
            </div>
        </form>
    );
}
