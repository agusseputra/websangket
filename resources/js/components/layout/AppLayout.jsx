import { Outlet } from 'react-router-dom';
import { SidebarProvider, useSidebar } from '../../context/SidebarContext';
import ConnectionBanner from './ConnectionBanner';
import Sidebar from './Sidebar';
import Topbar from './Topbar';

function KontenUtama() {
    const { terbuka } = useSidebar();

    return (
        <div inert={terbuka} className="flex min-h-dvh min-w-0 flex-1 flex-col">
            <ConnectionBanner />
            <Topbar />
            <main className="mx-auto grid w-full max-w-6xl flex-1 content-start gap-6 px-4 py-6 lg:px-8 lg:py-8">
                <Outlet />
            </main>
        </div>
    );
}

export default function AppLayout() {
    return (
        <SidebarProvider>
            <div className="min-h-dvh lg:flex">
                <Sidebar />
                <KontenUtama />
            </div>
        </SidebarProvider>
    );
}
