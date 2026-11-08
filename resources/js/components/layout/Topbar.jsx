import { Menu } from 'lucide-react';
import { useSidebar } from '../../context/SidebarContext';
import IconButton from '../ui/IconButton';

export default function Topbar() {
    const { terbuka, buka, pemicuRef } = useSidebar();

    return (
        <header className="sticky top-0 z-20 flex items-center gap-2 border-b border-garis bg-panel px-2 pt-[env(safe-area-inset-top)] lg:hidden">
            <IconButton
                ref={pemicuRef}
                label="Buka menu"
                ikon={Menu}
                onClick={buka}
                aria-expanded={terbuka}
                aria-controls="sidebar"
            />
            <p className="min-w-0 truncate font-semibold">Dudukan Sangket</p>
        </header>
    );
}
