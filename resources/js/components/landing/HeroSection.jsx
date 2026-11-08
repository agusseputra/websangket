import { ArrowDown, ArrowRight, ChevronLeft, ChevronRight, Clock3, Landmark, MapPinHouse, PiggyBank, Users, Wallet, Wheat } from 'lucide-react';
import { useCallback, useEffect, useState } from 'react';
import { FOTO, FOTO_BALI, FOTO_LPD } from '../../config/landing';
import FotoBudaya from './FotoBudaya';

const JEDA_SLIDE = 6000;

const SOROTAN_DESA = [
    { ikon: Clock3, nilai: '1×24 jam', isi: 'Batas lapor diri sejak tinggal di Sangket' },
    { ikon: Wheat, nilai: '20 kg beras', isi: 'Dudukan per tahun, sekitar Rp200.000' },
    { ikon: Users, nilai: '3 jenis krama', isi: 'Krama Desa Adat, Krama Tamiu, dan Tamiu' },
];

const SOROTAN_LPD = [
    { ikon: Landmark, nilai: 'Sejak 2000', isi: 'Milik Desa Adat Sangket, SK Gubernur No. 438/1999' },
    { ikon: PiggyBank, nilai: 'Simpan pinjam', isi: 'Tabungan, deposito, pinjaman, dan kredit usaha' },
    { ikon: Wallet, nilai: '20% untuk desa', isi: 'Bagian keuntungan untuk pembangunan desa adat' },
];

const KELAS_KARTU = 'grid gap-6 rounded-[2rem] bg-panel/80 p-6 shadow-sm ring-1 ring-langit-sedang/60 backdrop-blur sm:p-10';
const KELAS_LABEL = 'inline-flex items-center gap-2 justify-self-start rounded-full bg-langit-muda px-3 py-1 text-sm font-semibold text-langit-tua';
const KELAS_JUDUL = 'font-display text-4xl leading-[1.05] font-extrabold tracking-tight text-balance sm:text-6xl';
const KELAS_TOMBOL_UTAMA = 'inline-flex min-h-12 items-center gap-2 rounded-full bg-langit px-6 font-semibold text-di-langit shadow-lg shadow-langit/40 transition hover:-translate-y-0.5 motion-reduce:transition-none';
const KELAS_TOMBOL_KEDUA = 'inline-flex min-h-12 items-center gap-2 rounded-full border border-langit-tua/40 bg-panel px-6 font-semibold text-langit-tua transition-colors hover:bg-langit-muda';

function Sorotan({ daftar }) {
    return (
        <dl className="grid gap-4 sm:grid-cols-3">
            {daftar.map(({ ikon: Ikon, nilai, isi }) => (
                <div key={nilai} className="grid gap-1 rounded-[1.5rem] bg-panel/80 p-5 ring-1 ring-langit-sedang/60">
                    <Ikon className="size-5 text-langit-tua" aria-hidden="true" />
                    <dt className="font-display text-xl font-bold">{nilai}</dt>
                    <dd className="text-sm text-redup">{isi}</dd>
                </div>
            ))}
        </dl>
    );
}

function SlideDesaAdat() {
    return (
        <>
            <div className="grid min-w-0 content-start gap-4">
                <div className={KELAS_KARTU}>
                    <p className={KELAS_LABEL}>
                        <span className="size-2 animate-pulse rounded-full bg-langit motion-reduce:animate-none" aria-hidden="true" />
                        Program prioritas: Kasukretan Krama
                    </p>
                    <h1 className={KELAS_JUDUL}>
                        Desa Adat
                        <span className="block text-langit-tua">Sangket</span>
                    </h1>
                    <p className="max-w-xl text-lg text-redup">
                        Bersama krama menjaga ketertiban, keamanan, kebersihan, dan kesucian wewidangan Desa Adat,
                        secara sekala maupun niskala, melalui Kasukretan Krama.
                    </p>
                    <div className="flex flex-wrap gap-3">
                        <a href="/#cek-pembayaran" className={KELAS_TOMBOL_UTAMA}>
                            Cek pembayaran
                            <ArrowDown className="size-4" aria-hidden="true" />
                        </a>
                        <a href="/#pelaporan-tinggal" className={KELAS_TOMBOL_KEDUA}>
                            <MapPinHouse className="size-4" aria-hidden="true" />
                            Lapor tinggal
                        </a>
                    </div>
                </div>
                <Sorotan daftar={SOROTAN_DESA} />
            </div>

            <div className="grid min-w-0 grid-cols-2 grid-rows-[1fr_1fr] gap-4 lg:min-h-[36rem]">
                <FotoBudaya foto={FOTO_BALI.mapeed} keterangan="Mapeed menuju pura" className="row-span-2 aspect-[3/5] rounded-[2rem] lg:aspect-auto" prioritas />
                <FotoBudaya foto={FOTO.dudonan} keterangan="Piodalan di Sangket" className="aspect-square rounded-[2rem] lg:aspect-auto" prioritas />
                <FotoBudaya foto={FOTO_BALI.penari} keterangan="Tari dan gamelan" className="aspect-square rounded-[2rem] lg:aspect-auto" />
            </div>
        </>
    );
}

function SlideLpd() {
    return (
        <>
            <div className="grid min-w-0 content-start gap-4">
                <div className={KELAS_KARTU}>
                    <p className={KELAS_LABEL}>
                        <Landmark className="size-4" aria-hidden="true" />
                        Lembaga Perkreditan Desa
                    </p>
                    <h2 className={KELAS_JUDUL}>
                        LPD Desa Adat
                        <span className="block text-langit-tua">Sangket</span>
                    </h2>
                    <p className="max-w-xl text-lg text-redup">
                        Menabung dan meminjam di lembaga keuangan milik desa adat sendiri. Keuntungannya kembali untuk
                        pembangunan dan kegiatan adat di Sangket.
                    </p>
                    <div className="flex flex-wrap gap-3">
                        <a href="/lpd" className={KELAS_TOMBOL_UTAMA}>
                            Kenali LPD
                            <ArrowRight className="size-4" aria-hidden="true" />
                        </a>
                        <a href="/lpd#program-lpd" className={KELAS_TOMBOL_KEDUA}>
                            <PiggyBank className="size-4" aria-hidden="true" />
                            Layanan LPD
                        </a>
                    </div>
                </div>
                <Sorotan daftar={SOROTAN_LPD} />
            </div>

            <div className="grid min-w-0 grid-cols-2 grid-rows-[1fr_1fr] gap-4 lg:min-h-[36rem]">
                <FotoBudaya foto={FOTO_LPD.gedung} keterangan="Kantor LPD Sangket" className="row-span-2 aspect-[3/5] rounded-[2rem] lg:aspect-auto" />
                <FotoBudaya foto={FOTO_LPD.prajuru} keterangan="Prajuru dan pegawai LPD" className="aspect-square rounded-[2rem] lg:aspect-auto" />
                <FotoBudaya foto={FOTO_LPD.rapat} keterangan="Rapat tahunan bersama krama" className="aspect-square rounded-[2rem] lg:aspect-auto" />
            </div>
        </>
    );
}

const SLIDE = [
    { kunci: 'desa-adat', label: 'Desa Adat Sangket', Isi: SlideDesaAdat },
    { kunci: 'lpd', label: 'LPD Desa Adat Sangket', Isi: SlideLpd },
];

export default function HeroSection() {
    const [aktif, setAktif] = useState(0);
    const [dijeda, setDijeda] = useState(false);
    const [gerakDikurangi] = useState(() => window.matchMedia?.('(prefers-reduced-motion: reduce)').matches ?? false);

    const pindah = useCallback((arah) => setAktif((lama) => (lama + arah + SLIDE.length) % SLIDE.length), []);

    // Berganti otomatis kecuali sedang disorot, difokus, atau pengguna memilih gerak minimal.
    useEffect(() => {
        if (dijeda || gerakDikurangi) return undefined;

        const pewaktu = window.setInterval(() => pindah(1), JEDA_SLIDE);

        return () => window.clearInterval(pewaktu);
    }, [dijeda, gerakDikurangi, pindah, aktif]);

    return (
        <section
            id="beranda"
            aria-roledescription="carousel"
            aria-label="Sorotan Desa Adat Sangket"
            onMouseEnter={() => setDijeda(true)}
            onMouseLeave={() => setDijeda(false)}
            onFocus={() => setDijeda(true)}
            onBlur={(event) => !event.currentTarget.contains(event.relatedTarget) && setDijeda(false)}
            className="relative -mt-20 overflow-hidden bg-gradient-to-b from-langit-sedang via-langit-muda to-latar pt-24"
        >
            <div aria-hidden="true" className="pointer-events-none absolute -top-40 -left-32 size-[34rem] rounded-full bg-langit/30 blur-3xl" />

            <div className="relative mx-auto grid max-w-6xl px-4 pt-6 lg:px-6">
                {SLIDE.map(({ kunci, label, Isi }, indeks) => {
                    const tampil = indeks === aktif;

                    return (
                        <div
                            key={kunci}
                            role="group"
                            aria-roledescription="slide"
                            aria-label={`${indeks + 1} dari ${SLIDE.length}: ${label}`}
                            aria-hidden={!tampil}
                            inert={!tampil}
                            className={`grid gap-4 transition-opacity duration-700 [grid-area:1/1] motion-reduce:transition-none lg:grid-cols-[1.2fr_1fr] ${tampil ? 'opacity-100' : 'pointer-events-none opacity-0'}`}
                        >
                            <Isi />
                        </div>
                    );
                })}
            </div>

            <div className="relative mx-auto flex max-w-6xl items-center justify-center gap-3 px-4 pt-6 pb-12 lg:justify-start lg:px-6 lg:pb-16">
                <button type="button" onClick={() => pindah(-1)} aria-label="Slide sebelumnya" className="grid size-11 place-items-center rounded-full bg-panel/90 text-langit-tua ring-1 ring-langit-sedang hover:bg-langit-muda">
                    <ChevronLeft className="size-5" aria-hidden="true" />
                </button>
                <div className="flex items-center gap-2">
                    {SLIDE.map(({ kunci, label }, indeks) => (
                        <button
                            key={kunci}
                            type="button"
                            onClick={() => setAktif(indeks)}
                            aria-label={`Tampilkan ${label}`}
                            aria-current={indeks === aktif ? 'true' : undefined}
                            className="grid h-11 place-items-center px-1"
                        >
                            <span className={`block h-2.5 rounded-full transition-all motion-reduce:transition-none ${indeks === aktif ? 'w-8 bg-langit-tua' : 'w-2.5 bg-langit-tua/30'}`} />
                        </button>
                    ))}
                </div>
                <button type="button" onClick={() => pindah(1)} aria-label="Slide berikutnya" className="grid size-11 place-items-center rounded-full bg-panel/90 text-langit-tua ring-1 ring-langit-sedang hover:bg-langit-muda">
                    <ChevronRight className="size-5" aria-hidden="true" />
                </button>
            </div>
        </section>
    );
}
