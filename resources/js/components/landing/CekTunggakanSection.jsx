import { Search } from 'lucide-react';
import { useState } from 'react';
import api, { siapkanCsrf } from '../../lib/api';
import { galatKolom, pesanGalat } from '../../lib/galat';
import Alert from '../ui/Alert';
import TextField from '../ui/TextField';
import HasilCekTunggakan from './HasilCekTunggakan';
import KartuDudukanContoh from './KartuDudukanContoh';

function pesanCek(error) {
    if (error.response?.status === 404) return error.response.data.message;
    if (error.response?.status === 429) return 'Terlalu banyak percobaan. Coba lagi dalam satu menit.';

    return pesanGalat(error, 'Pengecekan gagal. Coba lagi.');
}

export default function CekTunggakanSection() {
    const [nama, setNama] = useState('');
    const [nik, setNik] = useState('');
    const [hasil, setHasil] = useState(null);
    const [galat, setGalat] = useState({});
    const [galatUmum, setGalatUmum] = useState('');
    const [memproses, setMemproses] = useState(false);

    async function cek(event) {
        event.preventDefault();
        setGalat({});
        setGalatUmum('');
        setHasil(null);
        setMemproses(true);

        try {
            await siapkanCsrf();
            const res = await api.post('/cek-tunggakan', { nama, nik });
            setHasil(res.data);
            setNik('');
        } catch (error) {
            const kolom = galatKolom(error);

            setGalat(kolom);
            setGalatUmum(Object.keys(kolom).length > 0 ? '' : pesanCek(error));
        } finally {
            setMemproses(false);
        }
    }

    return (
        <section id="cek-pembayaran" className="bg-gradient-to-b from-langit-muda to-latar">
            <div className="mx-auto grid max-w-6xl gap-10 px-4 py-14 lg:grid-cols-[0.85fr_1.15fr] lg:items-start lg:px-8 lg:py-20">
                <div className="grid min-w-0 content-start gap-6">
                    <div className="grid gap-2">
                        <p className="text-sm font-semibold tracking-wide text-langit-tua">Tanpa login</p>
                        <h2 className="font-display text-3xl font-bold text-balance sm:text-4xl">Cek pembayaran dan tunggakan</h2>
                        <p className="text-redup">Masukkan nama dan NIK sesuai data pendaftaran. Data hanya tampil jika keduanya cocok, dan NIK tidak ditampilkan.</p>
                    </div>
                    <div className="hidden lg:block">
                        <KartuDudukanContoh />
                    </div>
                </div>

                <div className="grid min-w-0 content-start gap-5">
                <form onSubmit={cek} className="grid gap-4 rounded-[2rem] bg-panel p-5 shadow-lg shadow-langit/20 md:grid-cols-[1fr_1fr_auto] md:items-start">
                    <TextField id="cek-nama" label="Nama lengkap" required autoComplete="name" value={nama} onChange={(e) => setNama(e.target.value)} galat={galat.nama} />
                    <TextField
                        id="cek-nik"
                        label="NIK"
                        required
                        inputMode="numeric"
                        autoComplete="off"
                        maxLength={20}
                        value={nik}
                        onChange={(e) => setNik(e.target.value.replace(/\D/g, ''))}
                        galat={galat.nik}
                    />
                    <button type="submit" disabled={memproses} className="inline-flex min-h-11 items-center justify-center gap-2 rounded-full bg-langit px-6 font-semibold text-di-langit transition-colors hover:brightness-95 disabled:opacity-60 md:mt-6">
                        <Search className="size-4" aria-hidden="true" />
                        {memproses ? 'Memeriksa...' : 'Cek data'}
                    </button>
                </form>

                {galatUmum && <Alert nada="gagal" role="alert">{galatUmum}</Alert>}
                {hasil && <HasilCekTunggakan hasil={hasil} />}
                </div>
            </div>
        </section>
    );
}
