import { FOTO_BALI, LOGO_DESA, MENU_LANDING, SITUS_DESA, TELEPON_TAMPIL, WEWIDANGAN } from '../../config/landing';
import ThemeSwitcher from '../ui/ThemeSwitcher';
import PitaPoleng from './PitaPoleng';

// Token warna diganti khusus di footer agar komponen bersama tetap terbaca di latar hitam.
const TOKEN_GELAP = {
    '--warna-teks': '#fafafa',
    '--warna-redup': '#a3a3a3',
    '--warna-garis': '#3f3f46',
    '--warna-kertas': '#27272a',
    '--warna-panel': '#18181b',
    '--warna-utama': '#38bdf8',
    '--warna-di-utama': '#082f49',
    colorScheme: 'dark',
};

export default function LandingFooter() {
    return (
        <footer style={TOKEN_GELAP} className="bg-[#0a0a0a] text-neutral-300">
            <PitaPoleng />
            <div className="mx-auto grid max-w-6xl gap-10 px-4 pt-12 pb-[max(2.5rem,env(safe-area-inset-bottom))] md:grid-cols-[1.6fr_1fr_1fr] lg:px-6">
                <div className="grid content-start gap-4">
                    <div className="flex items-center gap-3">
                        <img src={LOGO_DESA} alt="" className="size-14 object-contain" />
                        <div>
                            <p className="font-display text-lg font-bold text-white">Desa Adat Sangket</p>
                            <p className="text-sm text-neutral-400">Kecamatan Sukasada, Kabupaten Buleleng, Bali</p>
                        </div>
                    </div>
                    <p className="max-w-md text-sm text-neutral-400"><span className="font-semibold text-neutral-200">Wewidangan:</span> {WEWIDANGAN}</p>
                    <p className="text-sm text-neutral-400">Bendesa Adat: <span className="font-medium whitespace-nowrap text-neutral-200">{TELEPON_TAMPIL}</span></p>
                    <p className="text-sm text-neutral-400">Dasar: Pararem Desa Adat Sangket No 02 Tahun 2025 tentang Kasukretan Krama.</p>
                </div>

                <nav aria-label="Tautan footer" className="grid content-start gap-2 text-sm">
                    <p className="font-semibold text-white">Tautan</p>
                    {MENU_LANDING.map(({ href, label }) => <a key={href} href={href} className="text-neutral-400 hover:text-white">{label}</a>)}
                    {/* Pindah halaman penuh agar navbar landing (dari Blade) tidak ikut tampil di halaman login. */}
                    <a href="/login" className="text-neutral-400 hover:text-white">Masuk petugas</a>
                </nav>

                <div className="grid content-start gap-3">
                    <p className="text-sm font-semibold text-white">Tampilan</p>
                    <div className="max-w-64"><ThemeSwitcher /></div>
                </div>
            </div>

            <div className="border-t border-neutral-800 px-4 py-4 text-center text-xs text-neutral-500">
                <p>© {new Date().getFullYear()} Desa Adat Sangket · Foto dokumentasi dari <a href={SITUS_DESA} target="_blank" rel="noreferrer" className="underline underline-offset-2 hover:text-neutral-300">sangketdesa.com</a></p>
                <p className="mt-1">
                    Foto suasana Bali dari Unsplash:{' '}
                    {Object.values(FOTO_BALI).map(({ fotografer, tautan }, indeks, semua) => (
                        <span key={tautan}>
                            <a href={tautan} target="_blank" rel="noreferrer" className="underline underline-offset-2 hover:text-neutral-300">{fotografer}</a>
                            {indeks < semua.length - 1 ? ', ' : ''}
                        </span>
                    ))}
                </p>
            </div>
        </footer>
    );
}
