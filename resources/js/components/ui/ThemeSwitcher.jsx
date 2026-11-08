import { Monitor, Moon, Sun } from 'lucide-react';
import { useTheme } from '../../context/ThemeContext';

const PILIHAN = [
    { nilai: 'light', label: 'Terang', ikon: Sun },
    { nilai: 'dark', label: 'Gelap', ikon: Moon },
    { nilai: 'system', label: 'Sistem', ikon: Monitor },
];

export default function ThemeSwitcher() {
    const { tema, setTema } = useTheme();

    return (
        <div role="group" aria-label="Pilih tema" className="grid grid-cols-3 gap-1 rounded border border-garis p-1">
            {PILIHAN.map(({ nilai, label, ikon: Ikon }) => (
                <button
                    key={nilai}
                    type="button"
                    aria-pressed={tema === nilai}
                    onClick={() => setTema(nilai)}
                    className={`flex min-h-11 min-w-0 flex-col items-center justify-center gap-0.5 rounded text-xs transition-colors motion-reduce:transition-none ${
                        tema === nilai ? 'bg-utama font-semibold text-di-utama' : 'text-redup hover:bg-kertas hover:text-teks'
                    }`}
                >
                    <Ikon className="size-4" aria-hidden="true" />
                    {label}
                </button>
            ))}
        </div>
    );
}
