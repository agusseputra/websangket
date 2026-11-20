import { ChevronDown } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import { matchPath, NavLink, useLocation } from 'react-router-dom';

const DASAR = 'flex min-h-11 items-center gap-3 rounded px-3 text-sm transition-colors motion-reduce:transition-none';
const KUNCI_SIMPAN = 'sidebar-grup-tertutup';

function TautanMenu({ ke, label, ikon: Ikon, menjorok = false }) {
    return (
        <NavLink
            to={ke}
            className={({ isActive }) =>
                `${DASAR} ${menjorok ? 'pl-5' : ''} ${isActive ? 'bg-utama font-semibold text-di-utama' : 'text-teks hover:bg-kertas'}`
            }
        >
            <Ikon className="size-5 shrink-0" aria-hidden="true" />
            <span className="min-w-0 truncate">{label}</span>
        </NavLink>
    );
}

function bacaTertutup() {
    try {
        return JSON.parse(localStorage.getItem(KUNCI_SIMPAN) ?? '[]');
    } catch {
        return [];
    }
}

function berisiHalaman(item, pathname) {
    return item.some(({ ke }) => matchPath({ path: ke, end: false }, pathname));
}

function GrupMenu({ grup, item, tertutup, onAlih }) {
    const terbuka = !tertutup;
    const idPanel = `grup-menu-${grup.toLowerCase()}`;

    return (
        <li className="grid gap-1">
            <button
                type="button"
                aria-expanded={terbuka}
                aria-controls={idPanel}
                onClick={onAlih}
                className="flex min-h-10 items-center justify-between gap-2 rounded px-3 text-xs font-semibold tracking-wide text-redup uppercase hover:bg-kertas hover:text-teks"
            >
                {grup}
                <ChevronDown className={`size-4 shrink-0 transition-transform motion-reduce:transition-none ${terbuka ? 'rotate-180' : ''}`} aria-hidden="true" />
            </button>
            <ul id={idPanel} hidden={!terbuka} className="grid gap-1">
                {item.map((satu) => (
                    <li key={satu.ke}>
                        <TautanMenu {...satu} menjorok />
                    </li>
                ))}
            </ul>
        </li>
    );
}

export default function SidebarNav({ menu }) {
    const [tertutup, setTertutup] = useState(bacaTertutup);
    const { pathname } = useLocation();
    const navRef = useRef(null);

    // Menu aktif digulir ke area yang terlihat, karena daftar menu bisa lebih panjang dari sidebar.
    useEffect(() => {
        navRef.current?.querySelector('a[aria-current="page"]')?.scrollIntoView({ block: 'nearest' });
    }, [pathname]);

    // Grup yang berisi halaman aktif selalu dibuka saat berpindah halaman.
    useEffect(() => {
        const grupAktif = menu.find((entri) => entri.grup && berisiHalaman(entri.item, pathname))?.grup;

        if (grupAktif) setTertutup((lama) => (lama.includes(grupAktif) ? lama.filter((satu) => satu !== grupAktif) : lama));
    }, [menu, pathname]);

    useEffect(() => {
        try {
            localStorage.setItem(KUNCI_SIMPAN, JSON.stringify(tertutup));
        } catch {
            // Penyimpanan browser tidak tersedia; status grup cukup diingat selama halaman terbuka.
        }
    }, [tertutup]);

    function alih(grup) {
        setTertutup((lama) => (lama.includes(grup) ? lama.filter((satu) => satu !== grup) : [...lama, grup]));
    }

    return (
        <nav ref={navRef} aria-label="Menu utama">
            <ul className="grid gap-2">
                {menu.map((entri) => entri.grup ? (
                    <GrupMenu key={entri.grup} {...entri} tertutup={tertutup.includes(entri.grup)} onAlih={() => alih(entri.grup)} />
                ) : (
                    <li key={entri.ke}>
                        <TautanMenu {...entri} />
                    </li>
                ))}
            </ul>
        </nav>
    );
}
