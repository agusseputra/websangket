import { LogOut } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import Button from '../ui/Button';
import ThemeSwitcher from '../ui/ThemeSwitcher';

const NAMA_PERAN = { admin: 'Admin', petugas: 'Petugas' };

export default function UserPanel() {
    const { user, logout } = useAuth();
    const navigate = useNavigate();

    async function keluar() {
        await logout();
        navigate('/login', { replace: true });
    }

    return (
        <div className="grid gap-2 border-t border-garis pt-3">
            <ThemeSwitcher />
            <div className="min-w-0 px-3">
                <p className="truncate text-sm font-semibold">{user.name}</p>
                <p className="truncate text-sm text-redup">{NAMA_PERAN[user.role]}</p>
            </div>
            <Button varian="hantu" onClick={keluar} className="justify-start px-3">
                <LogOut className="size-5" aria-hidden="true" />
                Keluar
            </Button>
        </div>
    );
}
