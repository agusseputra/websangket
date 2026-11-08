import { FOTO, FOTO_BALI } from '../../config/landing';
import FotoBudaya from './FotoBudaya';
import JudulSection from './JudulSection';

const NILAI = ['Piodalan', 'Ngayah', 'Tari sakral', 'Gamelan', 'Awig-awig'];

export default function BudayaSection() {
    return (
        <section aria-labelledby="judul-budaya" className="bg-latar">
            <div className="mx-auto grid max-w-6xl gap-10 px-4 py-16 lg:px-6 lg:py-24">
                <div className="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-end">
                    <JudulSection
                        id="judul-budaya"
                        label="Adat yang terus hidup"
                        judul="Dudukan krama menopang upacara dan budaya Sangket"
                        deskripsi="Desa Adat Sangket dikenal dengan Palinggih Mobil di Pura Kahyangan Tiga. Dudukan ikut mendukung piodalan, tari, gamelan, dan pelestarian awig-awig."
                    />
                    <ul className="flex flex-wrap gap-2 lg:max-w-xs lg:justify-end">
                        {NILAI.map((nilai) => (
                            <li key={nilai} className="rounded-full bg-panel px-3 py-1 text-sm font-medium text-langit-tua ring-1 ring-langit-sedang">{nilai}</li>
                        ))}
                    </ul>
                </div>

                <div className="grid grid-cols-2 gap-4 lg:grid-cols-4 lg:grid-rows-2">
                    <FotoBudaya foto={FOTO.palinggihMobil} keterangan="Palinggih Mobil" className="muncul col-span-2 aspect-[16/10] rounded-[2rem] lg:row-span-2 lg:aspect-auto" />
                    <FotoBudaya foto={FOTO.tariJabaPura} keterangan="Pementasan saat piodalan" className="muncul aspect-square rounded-[2rem]" />
                    <FotoBudaya foto={FOTO.pecaruan} keterangan="Menuju pecaruan" className="muncul aspect-square rounded-[2rem]" />
                    <FotoBudaya foto={FOTO_BALI.sawah} keterangan="Palemahan yang asri" className="muncul col-span-2 aspect-[16/7] rounded-[2rem]" />
                </div>

                <div className="flex flex-wrap gap-x-6 gap-y-2 text-sm font-semibold text-langit-tua">
                    <a href="/profil" className="underline underline-offset-4">Kenali profil Desa Adat Sangket</a>
                    <a href="/listarticle" className="underline underline-offset-4">Baca berita kegiatan desa</a>
                </div>
            </div>
        </section>
    );
}
