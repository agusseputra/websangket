import { createContext, useCallback, useContext, useEffect, useMemo, useRef, useState } from 'react';
import { useLocation } from 'react-router-dom';

const BREAKPOINT_DESKTOP = '(min-width: 1024px)';

const SidebarContext = createContext(null);

export function SidebarProvider({ children }) {
    const [terbuka, setTerbuka] = useState(false);
    const pemicuRef = useRef(null);
    const pernahTerbukaRef = useRef(false);
    const { pathname } = useLocation();

    const buka = useCallback(() => setTerbuka(true), []);
    const tutup = useCallback(() => setTerbuka(false), []);

    // Drawer selalu tertutup setelah berpindah halaman.
    useEffect(() => {
        setTerbuka(false);
    }, [pathname]);

    // Di desktop sidebar selalu tampil, jadi status drawer dikembalikan.
    useEffect(() => {
        const desktop = window.matchMedia(BREAKPOINT_DESKTOP);
        const saatBerubah = (event) => {
            if (event.matches) {
                setTerbuka(false);
            }
        };

        desktop.addEventListener('change', saatBerubah);

        return () => desktop.removeEventListener('change', saatBerubah);
    }, []);

    // Fokus dikembalikan setelah render, saat konten utama tidak lagi inert.
    useEffect(() => {
        if (terbuka) {
            pernahTerbukaRef.current = true;
        } else if (pernahTerbukaRef.current) {
            pernahTerbukaRef.current = false;
            pemicuRef.current?.focus();
        }
    }, [terbuka]);

    useEffect(() => {
        if (!terbuka) {
            return undefined;
        }

        const saatTombol = (event) => {
            if (event.key === 'Escape') {
                tutup();
            }
        };

        document.addEventListener('keydown', saatTombol);
        document.body.style.overflow = 'hidden';

        return () => {
            document.removeEventListener('keydown', saatTombol);
            document.body.style.overflow = '';
        };
    }, [terbuka, tutup]);

    const nilai = useMemo(() => ({ terbuka, buka, tutup, pemicuRef }), [terbuka, buka, tutup]);

    return <SidebarContext.Provider value={nilai}>{children}</SidebarContext.Provider>;
}

export function useSidebar() {
    return useContext(SidebarContext);
}
