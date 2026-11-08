import { createContext, useContext, useEffect, useMemo, useState } from 'react';

const KUNCI_TEMA = 'dudukan-tema';

const ThemeContext = createContext(null);

function bacaTema() {
    try {
        const tersimpan = localStorage.getItem(KUNCI_TEMA);
        return tersimpan === 'light' || tersimpan === 'dark' ? tersimpan : 'system';
    } catch {
        return 'system';
    }
}

function simpanTema(tema) {
    try {
        if (tema === 'system') {
            localStorage.removeItem(KUNCI_TEMA);
        } else {
            localStorage.setItem(KUNCI_TEMA, tema);
        }
    } catch {
        // Pilihan tetap berlaku sampai halaman ditutup jika penyimpanan browser diblokir.
    }
}

export function ThemeProvider({ children }) {
    const [tema, setTema] = useState(bacaTema);

    useEffect(() => {
        const root = document.documentElement;

        if (tema === 'system') {
            root.removeAttribute('data-theme');
        } else {
            root.setAttribute('data-theme', tema);
        }

        simpanTema(tema);
    }, [tema]);

    const nilai = useMemo(() => ({ tema, setTema }), [tema]);

    return <ThemeContext.Provider value={nilai}>{children}</ThemeContext.Provider>;
}

export function useTheme() {
    return useContext(ThemeContext);
}
