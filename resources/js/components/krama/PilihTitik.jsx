import { LocateFixed, X } from 'lucide-react';
import { lazy, Suspense, useState } from 'react';
import Button from '../ui/Button';
import TextField from '../ui/TextField';

// Leaflet cukup besar, jadi hanya dimuat saat peta dibutuhkan.
const PetaLokasi = lazy(() => import('./PetaLokasi'));

const PESAN_GPS = {
    1: 'Izin lokasi ditolak. Izinkan lokasi di browser, atau pilih titik langsung di peta.',
    2: 'Lokasi belum bisa dibaca. Coba di tempat terbuka, atau pilih titik di peta.',
    3: 'GPS terlalu lama merespons. Coba lagi, atau pilih titik di peta.',
};

function titikUntukPeta(titik) {
    const lintang = Number(titik?.latitude);
    const bujur = Number(titik?.longitude);
    const lengkap = titik?.latitude !== '' && titik?.longitude !== '' && titik?.latitude != null && titik?.longitude != null;

    return lengkap && Math.abs(lintang) <= 90 && Math.abs(bujur) <= 180 ? { latitude: lintang, longitude: bujur } : null;
}

export default function PilihTitik({ titik, onUbah, galat, galatBujur, petunjuk, inputManual = false, tinggiPeta }) {
    const [mencariGps, setMencariGps] = useState(false);
    const [galatGps, setGalatGps] = useState('');
    const titikPeta = titikUntukPeta(titik);

    function pilihDiPeta({ latitude, longitude }) {
        onUbah({ latitude: latitude.toFixed(7), longitude: longitude.toFixed(7) });
    }

    function ambilGps() {
        if (!navigator.geolocation) {
            setGalatGps('Browser ini tidak mendukung GPS. Pilih titik langsung di peta.');
            return;
        }

        setGalatGps('');
        setMencariGps(true);

        navigator.geolocation.getCurrentPosition(
            ({ coords }) => {
                pilihDiPeta(coords);
                setMencariGps(false);
            },
            (error) => {
                setGalatGps(PESAN_GPS[error.code] ?? PESAN_GPS[2]);
                setMencariGps(false);
            },
            { enableHighAccuracy: true, timeout: 15000 },
        );
    }

    return (
        <div className="grid min-w-0 gap-2">
            <div className="flex flex-wrap items-center justify-between gap-2">
                <p className="min-w-0 text-sm text-redup">{petunjuk}</p>
                <Button varian="garis" onClick={ambilGps} disabled={mencariGps}>
                    <LocateFixed className="size-4" aria-hidden="true" />
                    {mencariGps ? 'Mencari lokasi...' : 'Gunakan GPS'}
                </Button>
            </div>

            <Suspense fallback={<div className={`grid place-items-center rounded border border-garis text-sm text-redup ${tinggiPeta ?? 'h-80 md:h-[28rem]'}`}>Memuat peta...</div>}>
                <PetaLokasi titik={titikPeta} onPilih={pilihDiPeta} {...(tinggiPeta ? { tinggi: tinggiPeta } : {})} />
            </Suspense>
            <p className="text-xs text-redup">Ketuk peta untuk menaruh pin, lalu geser pin untuk merapikan. Pilih <strong>Satelit</strong> di pojok kanan atas untuk melihat atap rumah, dan tombol di kiri atas untuk memperbesar peta.</p>

            {inputManual ? (
                <div className="grid gap-3 sm:grid-cols-2">
                    <TextField
                        id="titik-latitude"
                        label="Latitude"
                        inputMode="decimal"
                        autoComplete="off"
                        value={titik?.latitude ?? ''}
                        onChange={(e) => onUbah({ latitude: e.target.value, longitude: titik?.longitude ?? '' })}
                        galat={galat}
                    />
                    <TextField
                        id="titik-longitude"
                        label="Longitude"
                        inputMode="decimal"
                        autoComplete="off"
                        value={titik?.longitude ?? ''}
                        onChange={(e) => onUbah({ latitude: titik?.latitude ?? '', longitude: e.target.value })}
                        galat={galatBujur}
                    />
                </div>
            ) : (
                <>
                    <p className="font-mono text-xs break-all text-redup">
                        {titikPeta ? `${titikPeta.latitude.toFixed(7)}, ${titikPeta.longitude.toFixed(7)}` : 'Titik belum dipilih'}
                    </p>
                    {galat && <p className="text-sm text-gagal">{galat}</p>}
                </>
            )}

            {inputManual && titik && (
                <Button varian="hantu" ukuran="kecil" className="justify-self-start" onClick={() => onUbah(null)}>
                    <X className="size-4" aria-hidden="true" />
                    Hapus titik
                </Button>
            )}

            {galatGps && <p role="alert" className="text-sm text-gagal">{galatGps}</p>}
        </div>
    );
}
