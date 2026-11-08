import { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import api, { siapkanCsrf } from '../lib/api';

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
    const [user, setUser] = useState(null);
    const [memuat, setMemuat] = useState(true);

    useEffect(() => {
        api.get('/user')
            .then((res) => setUser(res.data))
            .catch(() => setUser(null))
            .finally(() => setMemuat(false));
    }, []);

    const login = useCallback(async (email, password) => {
        await siapkanCsrf();
        const res = await api.post('/login', { email, password });
        setUser(res.data);
        return res.data;
    }, []);

    const logout = useCallback(async () => {
        await api.post('/logout');
        setUser(null);
    }, []);

    const nilai = useMemo(() => ({ user, memuat, login, logout }), [user, memuat, login, logout]);

    return <AuthContext.Provider value={nilai}>{children}</AuthContext.Provider>;
}

export function useAuth() {
    return useContext(AuthContext);
}

export function berandaUntuk(user) {
    return user?.role === 'admin' ? '/admin' : '/krama';
}
