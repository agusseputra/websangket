import { X } from 'lucide-react';
import { useEffect, useRef } from 'react';
import { menuUntuk } from '../../config/menu';
import { useAuth } from '../../context/AuthContext';
import { useSidebar } from '../../context/SidebarContext';
import IconButton from '../ui/IconButton';
import SidebarNav from './SidebarNav';
import UserPanel from './UserPanel';

export default function Sidebar() {
    const { user } = useAuth();
    const { terbuka, tutup } = useSidebar();
    const tombolTutupRef = useRef(null);

    useEffect(() => {
        if (terbuka) {
            tombolTutupRef.current?.focus();
        }
    }, [terbuka]);

    return (
        <>
            {terbuka && <div aria-hidden="true" onClick={tutup} className="fixed inset-0 z-30 bg-black/50 lg:hidden" />}
            <aside
                id="sidebar"
                aria-label="Navigasi"
                className={`fixed inset-y-0 left-0 z-40 flex w-72 max-w-[85vw] flex-col gap-4 border-r border-garis bg-panel px-3 pt-[max(0.75rem,env(safe-area-inset-top))] pb-[max(0.75rem,env(safe-area-inset-bottom))] transition-transform duration-200 motion-reduce:transition-none lg:sticky lg:top-0 lg:visible lg:h-dvh lg:w-64 lg:shrink-0 lg:translate-x-0 ${
                    terbuka ? 'visible translate-x-0' : 'invisible -translate-x-full'
                }`}
            >
                <div className="flex min-h-11 items-center justify-between gap-2 pl-3">
                    <p className="min-w-0 truncate font-semibold">Dudukan Sangket</p>
                    <IconButton ref={tombolTutupRef} label="Tutup menu" ikon={X} onClick={tutup} className="lg:hidden" />
                </div>
                <div className="min-h-0 flex-1 overflow-y-auto">
                    <SidebarNav menu={menuUntuk(user.role)} />
                </div>
                <UserPanel />
            </aside>
        </>
    );
}
