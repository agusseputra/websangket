import { ArrowRight } from 'lucide-react';
import { useEffect, useState } from 'react';
import { LOGO_DESA } from '../../config/landing';
import api from '../../lib/api';
import JudulSection from './JudulSection';

export default function KabarDesaSection({ onMuat }) {
    const [artikel, setArtikel] = useState([]);

    useEffect(() => {
        api.get('/artikel-terbaru')
            .then((res) => setArtikel(res.data.data))
            .catch(() => setArtikel([]));
    }, []);

    // Kartu berita yang baru dimuat menggeser bagian di bawahnya, jadi posisi gulir dari #bagian dihitung ulang.
    useEffect(() => {
        if (artikel.length > 0) onMuat?.();
    }, [artikel, onMuat]);

    // Bagian disembunyikan bila belum ada artikel atau API gagal, agar landing tetap rapi.
    if (artikel.length === 0) return null;

    return (
        <section aria-labelledby="judul-kabar" className="bg-latar">
            <div className="mx-auto grid max-w-6xl gap-10 px-4 py-16 lg:px-6 lg:py-24">
                <div className="flex flex-wrap items-end justify-between gap-6">
                    <JudulSection id="judul-kabar" label="Berita desa" judul="Berita dan pengumuman terbaru" deskripsi="Ikuti kegiatan adat, paruman, dan program Kasukretan Krama di Desa Adat Sangket." />
                    <a href="/listarticle" className="inline-flex min-h-11 items-center gap-2 rounded-full bg-panel px-5 font-semibold text-langit-tua ring-1 ring-langit-sedang hover:bg-langit-muda">
                        Semua berita
                        <ArrowRight className="size-4" aria-hidden="true" />
                    </a>
                </div>

                <div className="grid gap-5 md:grid-cols-3">
                    {artikel.map((satu) => (
                        <article key={satu.id} className="muncul group relative flex flex-col overflow-hidden rounded-[2rem] bg-panel ring-1 ring-garis transition hover:-translate-y-1 hover:shadow-xl motion-reduce:transition-none">
                            <div className="relative aspect-[16/10] overflow-hidden">
                                {satu.gambar ? (
                                    <img src={satu.gambar} alt="" loading="lazy" className="size-full object-cover" />
                                ) : (
                                    <div className="grid size-full place-items-center bg-gradient-to-br from-langit-sedang via-langit-muda to-panel">
                                        <img src={LOGO_DESA} alt="" className="size-16 object-contain opacity-80" />
                                    </div>
                                )}
                                <span className="absolute top-4 left-4 rounded-full bg-panel/90 px-3 py-1 text-xs font-semibold text-langit-tua">{satu.kategori}</span>
                            </div>
                            <div className="grid flex-1 content-start gap-2 p-6">
                                <time dateTime={satu.tanggal} className="text-sm text-redup">{satu.tanggal_tampil}</time>
                                <h3 className="font-display text-lg font-bold text-balance">
                                    <a href={satu.url} className="after:absolute after:inset-0">{satu.judul}</a>
                                </h3>
                                <p className="line-clamp-3 text-sm text-redup">{satu.ringkasan}</p>
                            </div>
                        </article>
                    ))}
                </div>
            </div>
        </section>
    );
}
