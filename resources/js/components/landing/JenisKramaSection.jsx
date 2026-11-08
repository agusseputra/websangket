import { House, Landmark, Users } from 'lucide-react';
import { JENIS_KRAMA, LAMA_TINGGAL } from '../../config/landing';
import JudulSection from './JudulSection';

const IKON = { 'krama-adat': Landmark, 'krama-tamiu': House, tamiu: Users };

export default function JenisKramaSection() {
    return (
        <section id="kasukretan-krama" aria-labelledby="judul-jenis-krama" className="bg-latar">
            <div className="mx-auto grid max-w-6xl gap-10 px-4 py-16 lg:px-6 lg:py-24">
                <JudulSection
                    id="judul-jenis-krama"
                    label="Siapa krama di Sangket"
                    judul="Tiga jenis krama, satu tanggung jawab bersama"
                    deskripsi="Setiap orang yang tinggal di wewidangan Desa Adat Sangket tercatat sebagai salah satu dari tiga jenis krama, dengan swadharma dan swadikara masing-masing."
                />

                <div className="grid gap-4 md:grid-cols-3">
                    {JENIS_KRAMA.map(({ kunci, nama, definisi, bidang, hak }) => {
                        const Ikon = IKON[kunci];

                        return (
                            <article key={kunci} className="muncul grid content-start gap-4 rounded-[2rem] bg-panel p-6 ring-1 ring-garis">
                                <span className="grid size-12 place-items-center rounded-2xl bg-langit-muda text-langit-tua">
                                    <Ikon className="size-6" aria-hidden="true" />
                                </span>
                                <h3 className="font-display text-2xl font-bold">{nama}</h3>
                                <p className="text-redup">{definisi}</p>

                                <div className="grid gap-1 rounded-2xl bg-langit-muda p-4">
                                    <p className="text-sm font-semibold text-langit-tua">Swadharma {bidang.label.toLowerCase()}</p>
                                    <p className="text-sm">{bidang.isi}</p>
                                </div>

                                <div className="grid gap-2">
                                    <p className="text-sm font-semibold">Swadikara (hak)</p>
                                    <ul className="grid gap-1.5 text-sm text-redup">
                                        {hak.map((satu) => (
                                            <li key={satu} className="flex gap-2">
                                                <span className="mt-2 size-1.5 shrink-0 rounded-full bg-langit" aria-hidden="true" />
                                                {satu}
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            </article>
                        );
                    })}
                </div>

                <div className="muncul grid gap-4 rounded-[2rem] bg-panel p-6 ring-1 ring-garis md:grid-cols-[14rem_1fr] md:items-center">
                    <p className="font-display text-lg font-bold">Lama tinggal Krama Tamiu dan Tamiu</p>
                    <dl className="grid gap-4 sm:grid-cols-3">
                        {LAMA_TINGGAL.map(({ nama, isi }) => (
                            <div key={nama} className="grid gap-1">
                                <dt className="font-semibold text-langit-tua">{nama}</dt>
                                <dd className="text-sm text-redup">{isi}</dd>
                            </div>
                        ))}
                    </dl>
                </div>
            </div>
        </section>
    );
}
