import { ChevronDown } from 'lucide-react';
import { FAQ, FOTO_BALI } from '../../config/landing';
import FotoBudaya from './FotoBudaya';
import JudulSection from './JudulSection';

export default function FaqSection() {
    return (
        <section aria-labelledby="judul-faq" className="bg-panel">
            <div className="mx-auto grid max-w-6xl gap-10 px-4 py-16 lg:grid-cols-[1fr_1.3fr] lg:gap-14 lg:px-6 lg:py-24">
                <div className="grid content-start gap-6">
                    <JudulSection id="judul-faq" label="Pertanyaan umum" judul="Yang sering ditanyakan krama" />
                    <FotoBudaya foto={FOTO_BALI.puraDanau} className="hidden aspect-[4/3] rounded-[2rem] lg:block" />
                </div>

                <div className="grid content-start gap-3">
                    {FAQ.map(({ tanya, jawab }) => (
                        <details key={tanya} className="group muncul rounded-[1.5rem] bg-latar ring-1 ring-garis open:ring-langit">
                            <summary className="flex min-h-14 cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 font-semibold [&::-webkit-details-marker]:hidden">
                                {tanya}
                                <ChevronDown className="size-5 shrink-0 transition-transform group-open:rotate-180 motion-reduce:transition-none" aria-hidden="true" />
                            </summary>
                            <p className="px-5 pb-5 text-redup">{jawab}</p>
                        </details>
                    ))}
                </div>
            </div>
        </section>
    );
}
