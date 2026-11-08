import { Navigate, Outlet } from 'react-router-dom';
import { berandaUntuk, useAuth } from '../../context/AuthContext';

export default function ProtectedRoute({ peran }) {
    const { user } = useAuth();

    if (!user) {
        return <Navigate to="/login" replace />;
    }

    if (peran && !peran.includes(user.role)) {
        return <Navigate to={berandaUntuk(user)} replace />;
    }

    return <Outlet />;
}
