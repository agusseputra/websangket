import { useCallback, useEffect, useState } from 'react';
import Alert from '../components/ui/Alert';
import Badge from '../components/ui/Badge';
import Button from '../components/ui/Button';
import Card from '../components/ui/Card';
import PageHeader from '../components/ui/PageHeader';
import SelectField from '../components/ui/SelectField';
import TextField from '../components/ui/TextField';
import api from '../lib/api';
import { galatKolom, pesanGalat } from '../lib/galat';

const KOSONG = { name: '', email: '', password: '', role: 'petugas', aktif: true };
const PERAN = [
    { nilai: 'petugas', label: 'Petugas' },
    { nilai: 'admin', label: 'Admin' },
];

export default function PenggunaPage() {
    const [pengguna, setPengguna] = useState([]);
    const [nilai, setNilai] = useState(KOSONG);
    const [idUbah, setIdUbah] = useState(null);
    const [galat, setGalat] = useState({});
    const [galatUmum, setGalatUmum] = useState('');
    const [info, setInfo] = useState('');
    const [memproses, setMemproses] = useState(false);

    const muat = useCallback(async () => {
        try {
            const res = await api.get('/users');
            setPengguna(res.data);
        } catch (error) {
            setGalatUmum(pesanGalat(error, 'Daftar pengguna gagal dimuat.'));
        }
    }, []);

    useEffect(() => {
        muat();
    }, [muat]);

    const ikat = (kunci) => ({
        value: nilai[kunci],
        onChange: (e) => setNilai((lama) => ({ ...lama, [kunci]: e.target.value })),
        galat: galat[kunci],
    });

    function reset() {
        setNilai(KOSONG);
        setIdUbah(null);
        setGalat({});
        setGalatUmum('');
    }

    async function simpan(event) {
        event.preventDefault();
        setGalat({});
        setGalatUmum('');
        setInfo('');
        setMemproses(true);

        try {
            if (idUbah) await api.put(`/users/${idUbah}`, nilai);
            else await api.post('/users', nilai);

            setInfo(idUbah ? 'Pengguna diperbarui.' : 'Pengguna ditambahkan.');
            reset();
            await muat();
        } catch (error) {
            const kolom = galatKolom(error);

            setGalat(kolom);
            setGalatUmum(kolom.aktif ?? (Object.keys(kolom).length > 0 ? '' : pesanGalat(error, 'Pengguna gagal disimpan.')));
        } finally {
            setMemproses(false);
        }
    }

    return (
        <>
            <PageHeader judul="Pengguna" deskripsi="Kelola akun admin dan petugas." />
            {galatUmum && <Alert nada="gagal" role="alert">{galatUmum}</Alert>}
            {info && <Alert nada="berhasil">{info}</Alert>}

            <div className="grid items-start gap-6 lg:grid-cols-[22rem_1fr]">
                <Card judul={idUbah ? 'Ubah pengguna' : 'Tambah pengguna'}>
                    <form onSubmit={simpan} className="grid gap-3">
                        <TextField id="pengguna-nama" label="Nama" required {...ikat('name')} />
                        <TextField id="pengguna-email" label="Email" type="email" autoComplete="off" required {...ikat('email')} />
                        <TextField
                            id="pengguna-sandi"
                            label="Kata sandi"
                            type="password"
                            autoComplete="new-password"
                            required={!idUbah}
                            bantuan={idUbah ? 'Kosongkan jika tidak diubah.' : 'Minimal 8 karakter.'}
                            {...ikat('password')}
                        />
                        <SelectField id="pengguna-peran" label="Peran" pilihan={PERAN} {...ikat('role')} />
                        {idUbah && (
                            <label className="flex min-h-11 items-center gap-3 text-sm font-medium">
                                <input
                                    id="pengguna-aktif"
                                    type="checkbox"
                                    className="size-5"
                                    checked={nilai.aktif}
                                    onChange={(e) => setNilai((lama) => ({ ...lama, aktif: e.target.checked }))}
                                />
                                Akun aktif
                            </label>
                        )}
                        <div className="flex flex-wrap justify-end gap-2">
                            {idUbah && <Button varian="garis" onClick={reset}>Batal</Button>}
                            <Button type="submit" disabled={memproses}>Simpan</Button>
                        </div>
                    </form>
                </Card>

                <ul className="min-w-0 divide-y divide-garis rounded border border-garis bg-panel">
                    {pengguna.map((satu) => (
                        <li key={satu.id} className="flex flex-wrap items-center justify-between gap-3 p-3 pl-4">
                            <div className="min-w-0">
                                <p className="font-medium break-words">{satu.name}</p>
                                <p className="text-sm break-all text-redup">{satu.email}</p>
                                <div className="mt-1 flex flex-wrap gap-2">
                                    <Badge>{satu.role === 'admin' ? 'Admin' : 'Petugas'}</Badge>
                                    {!satu.aktif && <Badge nada="peringatan">Nonaktif</Badge>}
                                </div>
                            </div>
                            <Button
                                varian="garis"
                                onClick={() => {
                                    setIdUbah(satu.id);
                                    setNilai({ name: satu.name, email: satu.email, password: '', role: satu.role, aktif: satu.aktif });
                                    setGalat({});
                                    setGalatUmum('');
                                    setInfo('');
                                }}
                            >
                                Ubah
                            </Button>
                        </li>
                    ))}
                </ul>
            </div>
        </>
    );
}
