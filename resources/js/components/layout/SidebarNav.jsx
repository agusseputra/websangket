import { NavLink } from 'react-router-dom';

const DASAR = 'flex min-h-11 items-center gap-3 rounded px-3 text-sm transition-colors motion-reduce:transition-none';

export default function SidebarNav({ menu }) {
    return (
        <nav aria-label="Menu utama" className="grid gap-1">
            {menu.map(({ ke, label, ikon: Ikon }) => (
                <NavLink
                    key={ke}
                    to={ke}
                    className={({ isActive }) =>
                        `${DASAR} ${isActive ? 'bg-utama font-semibold text-di-utama' : 'text-teks hover:bg-kertas'}`
                    }
                >
                    <Ikon className="size-5 shrink-0" aria-hidden="true" />
                    <span className="min-w-0 truncate">{label}</span>
                </NavLink>
            ))}
        </nav>
    );
}
