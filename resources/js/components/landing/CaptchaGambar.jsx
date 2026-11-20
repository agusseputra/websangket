import { RefreshCw } from 'lucide-react';
import { useCallback, useEffect, useImperativeHandle, useRef, useState } from 'react';
import api from '../../lib/api';
import { kelasInput } from '../ui/Field';

/**
 * CAPTCHA gambar dari server Laravel. Kode baru dimuat saat dibuka, saat diminta, dan sebelum kedaluwarsa.
 */
export default function CaptchaGambar({ jawaban, onUbah, galat, ref }) {
    const [captcha, setCaptcha] = useState(null);
    const [memuat, setMemuat] = useState(true);
    const [galatMuat, setGalatMuat] = useState('');
    const pewaktuRef = useRef(null);
    const onUbahRef = useRef(onUbah);

    onUbahRef.current = onUbah;

    const muat = useCallback(async () => {
        setMemuat(true);
        setGalatMuat('');
        window.clearTimeout(pewaktuRef.current);

        try {
            const res = await api.get('/captcha');

            setCaptcha(res.data);
            onUbahRef.current({ kunci: res.data.kunci, jawaban: '' });
            // Kode diganti sedikit sebelum kedaluwarsa agar warga tidak mengetik kode yang sudah tidak berlaku.
            pewaktuRef.current = window.setTimeout(muat, Math.max(30, res.data.berlaku_detik - 30) * 1000);
        } catch (error) {
            setGalatMuat(error.response?.status === 429 ? 'Terlalu sering memuat kode. Tunggu sebentar.' : 'Kode CAPTCHA gagal dimuat. Periksa sinyal, lalu muat ulang.');
        } finally {
            setMemuat(false);
        }
    }, []);

    useImperativeHandle(ref, () => ({ muatUlang: muat }), [muat]);

    useEffect(() => {
        muat();

        return () => window.clearTimeout(pewaktuRef.current);
    }, [muat]);

    return (
        <div className="grid gap-2">
            <label htmlFor="laporan-captcha" className="text-sm font-medium">Kode keamanan</label>
            <div className="flex flex-wrap items-center gap-2">
                <div className="grid h-16 w-[200px] place-items-center overflow-hidden rounded-xl border border-garis bg-white">
                    {captcha && !galatMuat
                        ? <img src={captcha.gambar} width="200" height="64" alt="Gambar kode keamanan berisi 5 huruf dan angka" className={memuat ? 'opacity-40' : ''} />
                        : <span className="text-sm text-redup">{memuat ? 'Memuat...' : 'Tidak tersedia'}</span>}
                </div>
                <button
                    type="button"
                    onClick={muat}
                    disabled={memuat}
                    aria-label="Ganti kode keamanan"
                    title="Ganti kode"
                    className="inline-flex size-11 items-center justify-center rounded-full border border-garis hover:bg-kertas disabled:opacity-50"
                >
                    <RefreshCw className={`size-5 ${memuat ? 'animate-spin motion-reduce:animate-none' : ''}`} aria-hidden="true" />
                </button>
            </div>
            <input
                id="laporan-captcha"
                required
                value={jawaban}
                onChange={(e) => onUbah({ kunci: captcha?.kunci ?? '', jawaban: e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 5) })}
                maxLength={5}
                autoComplete="off"
                autoCapitalize="characters"
                spellCheck={false}
                aria-invalid={Boolean(galat)}
                aria-describedby={galat ? 'laporan-captcha-galat' : 'laporan-captcha-bantuan'}
                className={`${kelasInput} max-w-[200px] font-mono tracking-[0.3em] uppercase`}
            />
            {galat || galatMuat
                ? <p id="laporan-captcha-galat" className="text-sm text-gagal">{galat || galatMuat}</p>
                : <p id="laporan-captcha-bantuan" className="text-sm text-redup">Ketik 5 huruf dan angka pada gambar. Huruf besar atau kecil sama saja.</p>}
        </div>
    );
}
