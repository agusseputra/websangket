import { PERUNTUKAN_DANA } from '../../config/landing';
import JudulSection from './JudulSection';

const KELILING = 100;

function Donat() {
    let awal = 0;

    return (
        <svg viewBox="0 0 42 42" role="img" aria-labelledby="donat-judul" className="mx-auto size-64 -rotate-90 sm:size-72">
            <title id="donat-judul">Peruntukan dana: 50% Desa Adat, 30% operasional, 10% banjar, 10% pecalang</title>
            <circle cx="21" cy="21" r="15.915" fill="none" stroke="var(--warna-kertas)" strokeWidth="6" />
            {PERUNTUKAN_DANA.map(({ persen, judul, warna }) => {
                const segmen = (
                    <circle
                        key={judul}
                        cx="21"
                        cy="21"
                        r="15.915"
                        fill="none"
                        stroke={warna}
                        strokeWidth="6"
                        strokeDasharray={`${persen - 0.6} ${KELILING - persen + 0.6}`}
                        strokeDashoffset={-awal}
                    />
                );

                awal += persen;

                return segmen;
            })}
        </svg>
    );
}

export default function PeruntukanSection() {
    return (
        <section aria-labelledby="judul-peruntukan" className="bg-panel">
            <div className="mx-auto grid max-w-6xl gap-10 px-4 py-16 lg:px-6 lg:py-24">
                <JudulSection
                    id="judul-peruntukan"
                    label="Keterbukaan dana"
                    judul="Ke mana dudukan Anda digunakan"
                    deskripsi="Dana yang terkumpul dibagi untuk kegiatan adat, operasional kasukretan, banjar, dan pecalang, serta dilaporkan kepada paruman Desa Adat."
                />

                <div className="grid gap-8 lg:grid-cols-[auto_1fr] lg:items-center lg:gap-14">
                    <div className="muncul relative grid place-items-center">
                        <Donat />
                        <div className="absolute grid place-items-center text-center">
                            <span className="font-display text-4xl font-extrabold">100%</span>
                            <span className="text-sm text-redup">dudukan krama</span>
                        </div>
                    </div>

                    <ul className="grid gap-3 sm:grid-cols-2">
                        {PERUNTUKAN_DANA.map(({ persen, judul, isi, warna }) => (
                            <li key={judul} className="muncul grid content-start gap-2 rounded-[1.5rem] bg-latar p-5 ring-1 ring-garis">
                                <div className="flex items-center justify-between gap-3">
                                    <p className="flex items-center gap-2 font-semibold">
                                        <span className="size-3 rounded-full" style={{ backgroundColor: warna }} aria-hidden="true" />
                                        {judul}
                                    </p>
                                    <span className="font-display text-2xl font-extrabold tabular-nums">{persen}%</span>
                                </div>
                                <p className="text-sm text-redup">{isi}</p>
                            </li>
                        ))}
                    </ul>
                </div>
            </div>
        </section>
    );
}
