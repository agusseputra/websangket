import { BadgeCheck, Clock3, HandHeart, Scale, Sparkles } from 'lucide-react';
import { FOTO_BALI, KEWAJIBAN_BERSAMA } from '../../config/landing';
import FotoBudaya from './FotoBudaya';
import JudulSection from './JudulSection';

const IKON = [Scale, Clock3, BadgeCheck, Sparkles, HandHeart];

export default function KewajibanSection() {
    return (
        <section aria-labelledby="judul-kewajiban" className="bg-panel">
            <div className="mx-auto grid max-w-6xl gap-10 px-4 py-16 lg:grid-cols-[1fr_1.1fr] lg:items-center lg:gap-14 lg:px-6 lg:py-24">
                <FotoBudaya foto={FOTO_BALI.candiBentar} keterangan="Menjaga kesucian wewidangan" className="muncul aspect-[4/3] rounded-[2rem] lg:aspect-[4/5]" />

                <div className="grid min-w-0 gap-8">
                    <JudulSection
                        id="judul-kewajiban"
                        label="Swadharma"
                        judul="Kewajiban yang dijalankan bersama"
                        deskripsi="Secara umum, setiap krama menjaga Desa Adat dalam bidang Parahyangan, Pawongan, dan Palemahan sesuai jenis kramanya."
                    />
                    <ol className="grid gap-3">
                        {KEWAJIBAN_BERSAMA.map(({ judul, isi }, indeks) => {
                            const Ikon = IKON[indeks];

                            return (
                                <li key={judul} className="muncul flex gap-4 rounded-[1.5rem] bg-latar p-4 ring-1 ring-garis">
                                    <span className="grid size-11 shrink-0 place-items-center rounded-2xl bg-langit text-di-langit">
                                        <Ikon className="size-5" aria-hidden="true" />
                                    </span>
                                    <div className="grid min-w-0 gap-0.5">
                                        <p className="font-semibold">{judul}</p>
                                        <p className="text-sm text-redup">{isi}</p>
                                    </div>
                                </li>
                            );
                        })}
                    </ol>
                </div>
            </div>
        </section>
    );
}
