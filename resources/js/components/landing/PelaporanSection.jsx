import { Clock3, Store } from 'lucide-react';
import { useState } from 'react';
import { TELEPON_TAMPIL } from '../../config/landing';
import LaporanForm from './LaporanForm';

const LAPORAN = [
    {
        jenis: 'tinggal',
        ikon: Clock3,
        judul: 'Baru tinggal di Sangket',
        ringkas: 'Laporkan keberadaan Anda dalam 24 jam.',
        isi: 'Pendatang dan tamu yang menetap diminta melapor kepada tim Kasukretan paling lambat 24 jam sejak tiba.',
        tombol: 'Isi laporan tinggal',
    },
    {
        jenis: 'usaha',
        ikon: Store,
        judul: 'Membuka usaha',
        ringkas: 'Beri tahu tim Kasukretan sebelum atau saat usaha dibuka.',
        isi: 'Warga yang membuka usaha di wilayah desa adat diminta melapor agar usahanya tercatat oleh tim Kasukretan.',
        tombol: 'Isi laporan usaha',
    },
];

export default function PelaporanSection() {
    const [jenis, setJenis] = useState('tinggal');

    function pilihJenis(nilai) {
        setJenis(nilai);
        document.getElementById('form-laporan')?.scrollIntoView();
    }

    return (
        <section id="pelaporan-tinggal" className="bg-langit-muda">
            <div className="mx-auto grid max-w-6xl gap-8 px-4 py-14 lg:px-8 lg:py-20">
                <div className="grid max-w-3xl gap-2">
                    <h2 className="font-display text-3xl font-bold text-balance sm:text-4xl">Mari bantu menjaga ketertiban bersama</h2>
                    <p className="text-redup">
                        Partisipasi warga membantu tim Kasukretan mengenal siapa yang tinggal dan berusaha di Sangket, sehingga
                        pelayanan dan keamanan lingkungan lebih mudah dijaga.
                    </p>
                </div>

                <div className="grid gap-5 md:grid-cols-2">
                    {LAPORAN.map(({ jenis: nilai, ikon: Ikon, judul, ringkas, isi, tombol }) => (
                        <article key={nilai} className="grid content-start gap-3 rounded-[2rem] border border-langit-sedang bg-panel p-6 shadow-sm">
                            <span className="grid size-12 place-items-center rounded-full bg-langit text-di-langit">
                                <Ikon className="size-6" aria-hidden="true" />
                            </span>
                            <h3 className="font-display text-xl font-bold">{judul}</h3>
                            <p className="font-medium text-langit-tua">{ringkas}</p>
                            <p className="text-redup">{isi}</p>
                            <button
                                type="button"
                                onClick={() => pilihJenis(nilai)}
                                className="inline-flex min-h-11 items-center justify-center justify-self-start rounded-full border border-langit-tua px-5 font-semibold text-langit-tua transition-colors hover:bg-langit-muda"
                            >
                                {tombol}
                            </button>
                        </article>
                    ))}
                </div>

                <LaporanForm jenis={jenis} onJenis={setJenis} />

                <p className="text-sm text-redup">Butuh bantuan? Hubungi Bendesa Adat: <span className="font-medium whitespace-nowrap text-teks">{TELEPON_TAMPIL}</span></p>
            </div>
        </section>
    );
}
