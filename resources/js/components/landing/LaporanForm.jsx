import { Send } from 'lucide-react';
import { useRef, useState } from 'react';
import api, { siapkanCsrf } from '../../lib/api';
import { galatKolom, pesanGalat } from '../../lib/galat';
import Alert from '../ui/Alert';
import SelectField from '../ui/SelectField';
import TextField from '../ui/TextField';
import CaptchaGambar from './CaptchaGambar';

const KOSONG = { nama: '', nik: '', asal: '', nama_usaha: '', jenis_usaha: '', alamat: '', tanggal_mulai: '', tanggal_selesai: '' };

const JENIS = [
    { nilai: 'tinggal', label: 'Tinggal' },
    { nilai: 'usaha', label: 'Usaha' },
];

export default function LaporanForm({ jenis, onJenis }) {
    const [nilai, setNilai] = useState(KOSONG);
    const [menetap, setMenetap] = useState(false);
    const [captcha, setCaptcha] = useState({ kunci: '', jawaban: '' });
    const [galat, setGalat] = useState({});
    const [galatUmum, setGalatUmum] = useState('');
    const [berhasil, setBerhasil] = useState('');
    const [memproses, setMemproses] = useState(false);
    const captchaRef = useRef(null);

    const ikat = (kunci) => ({
        value: nilai[kunci],
        onChange: (e) => setNilai((lama) => ({ ...lama, [kunci]: e.target.value })),
        galat: galat[kunci],
    });

    async function kirim(event) {
        event.preventDefault();
        setGalat({});
        setGalatUmum('');
        setBerhasil('');

        // Pemeriksaan di browser sama dengan server, agar warga tidak perlu menunggu jawaban server.
        if (captcha.jawaban.length < 5) {
            setGalat({ captcha: 'Ketik 5 huruf dan angka pada gambar.' });
            setGalatUmum('Periksa kembali isian yang ditandai.');
            document.getElementById('laporan-captcha')?.focus();

            return;
        }

        setMemproses(true);

        try {
            await siapkanCsrf();
            const res = await api.post('/laporan-warga', {
                ...nilai,
                jenis,
                menetap,
                tanggal_selesai: menetap ? null : nilai.tanggal_selesai || null,
                captcha_kunci: captcha.kunci,
                captcha: captcha.jawaban,
            });

            setBerhasil(res.data.message);
            setNilai(KOSONG);
            setMenetap(false);
            captchaRef.current?.muatUlang();
        } catch (error) {
            const kolom = galatKolom(error);

            setGalat(kolom);

            // Kode yang sudah diperiksa server hangus, jadi diganti dengan kode baru.
            if (kolom.captcha || kolom.captcha_kunci) {
                setGalat((lama) => ({ ...lama, captcha: kolom.captcha ?? kolom.captcha_kunci }));
                captchaRef.current?.muatUlang();
            }

            if (error.response?.status === 429) {
                setGalatUmum('Terlalu banyak laporan. Coba lagi dalam satu menit.');
            } else {
                setGalatUmum(Object.keys(kolom).length > 0 ? 'Periksa kembali isian yang ditandai.' : pesanGalat(error, 'Laporan gagal dikirim.'));
            }
        } finally {
            setMemproses(false);
        }
    }

    return (
        <form id="form-laporan" onSubmit={kirim} className="grid scroll-mt-24 gap-5 rounded-[2rem] border border-langit-sedang bg-panel p-5 shadow-lg shadow-langit/20 md:p-6">
            <div className="grid gap-1">
                <h3 className="font-display text-xl font-bold">Form laporan</h3>
                <p className="text-sm text-redup">NIK disimpan terenkripsi dan hanya dipakai oleh tim Kasukretan.</p>
            </div>

            <div className="grid gap-4 md:grid-cols-2">
                <SelectField id="laporan-jenis" label="Jenis laporan" value={jenis} onChange={(e) => onJenis(e.target.value)} galat={galat.jenis} pilihan={JENIS} />
                <TextField id="laporan-nama" label="Nama" required autoComplete="name" {...ikat('nama')} />
                <TextField
                    id="laporan-nik"
                    label="NIK"
                    required
                    inputMode="numeric"
                    autoComplete="off"
                    maxLength={16}
                    value={nilai.nik}
                    onChange={(e) => setNilai((lama) => ({ ...lama, nik: e.target.value.replace(/\D/g, '') }))}
                    galat={galat.nik}
                />
                <TextField id="laporan-asal" label="Asal" required {...ikat('asal')} />

                {jenis === 'usaha' && (
                    <>
                        <TextField id="laporan-nama-usaha" label="Nama usaha" required {...ikat('nama_usaha')} />
                        <TextField id="laporan-jenis-usaha" label="Jenis usaha" required placeholder="Contoh: warung, laundry" {...ikat('jenis_usaha')} />
                    </>
                )}

                <div className="md:col-span-2">
                    <TextField id="laporan-alamat" label="Alamat tinggal saat ini di Sangket" required {...ikat('alamat')} />
                </div>

                <TextField id="laporan-tanggal-mulai" label="Tanggal mulai tinggal" type="date" required {...ikat('tanggal_mulai')} />

                <div className="grid content-start gap-2">
                    {!menetap && (
                        <TextField id="laporan-tanggal-selesai" label="Hingga tanggal" type="date" required min={nilai.tanggal_mulai || undefined} {...ikat('tanggal_selesai')} />
                    )}
                    <label htmlFor="laporan-menetap" className="flex min-h-11 items-center gap-3 text-sm font-medium">
                        <input id="laporan-menetap" type="checkbox" className="size-5" checked={menetap} onChange={(e) => setMenetap(e.target.checked)} />
                        Menetap, tanpa tanggal selesai
                    </label>
                </div>
            </div>

            <CaptchaGambar
                ref={captchaRef}
                jawaban={captcha.jawaban}
                onUbah={(baru) => {
                    setCaptcha(baru);
                    if (galat.captcha && baru.jawaban) setGalat((lama) => ({ ...lama, captcha: undefined }));
                }}
                galat={galat.captcha}
            />

            {galatUmum && <Alert nada="gagal" role="alert">{galatUmum}</Alert>}
            {berhasil && <Alert nada="berhasil">{berhasil}</Alert>}

            <button type="submit" disabled={memproses} className="inline-flex min-h-12 items-center justify-center gap-2 justify-self-start rounded-full bg-langit px-6 font-semibold text-di-langit transition-colors hover:brightness-95 disabled:opacity-60">
                <Send className="size-4" aria-hidden="true" />
                {memproses ? 'Mengirim...' : 'Kirim laporan'}
            </button>
        </form>
    );
}
