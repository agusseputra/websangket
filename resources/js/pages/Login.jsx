import { useState } from 'react';
import { Navigate } from 'react-router-dom';
import ConnectionBanner from '../components/layout/ConnectionBanner';
import Alert from '../components/ui/Alert';
import Button from '../components/ui/Button';
import TextField from '../components/ui/TextField';
import ThemeSwitcher from '../components/ui/ThemeSwitcher';
import { berandaUntuk, useAuth } from '../context/AuthContext';

function pesanGagal(error) {
    const status = error.response?.status;

    if (status === 422) return error.response.data.errors?.email?.[0] ?? 'Periksa kembali email dan kata sandi.';
    if (status === 429) return 'Terlalu banyak percobaan. Coba lagi dalam satu menit.';
    if (!error.response) return 'Tidak bisa menghubungi server. Periksa sinyal Anda.';

    return 'Login gagal. Coba lagi.';
}

export default function Login() {
    const { user, login } = useAuth();
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [galat, setGalat] = useState('');
    const [mengirim, setMengirim] = useState(false);

    if (user) {
        return <Navigate to={berandaUntuk(user)} replace />;
    }

    async function kirim(event) {
        event.preventDefault();
        setGalat('');
        setMengirim(true);

        try {
            await login(email, password);
        } catch (error) {
            setGalat(pesanGagal(error));
        } finally {
            setMengirim(false);
        }
    }

    return (
        <div className="min-h-dvh">
            <ConnectionBanner />
            <main className="mx-auto grid w-full max-w-sm gap-6 px-4 py-14">
                <div className="grid gap-1">
                    <h1 className="text-2xl font-semibold">Dudukan Sangket</h1>
                    <p className="text-redup">Masuk untuk mencatat krama dan pembayaran.</p>
                </div>
                <form onSubmit={kirim} className="grid gap-4 rounded border border-garis bg-panel p-5">
                    <TextField
                        id="email"
                        label="Email"
                        type="email"
                        autoComplete="username"
                        required
                        value={email}
                        onChange={(e) => setEmail(e.target.value)}
                    />
                    <TextField
                        id="password"
                        label="Kata sandi"
                        type="password"
                        autoComplete="current-password"
                        required
                        value={password}
                        onChange={(e) => setPassword(e.target.value)}
                    />
                    {galat && <Alert nada="gagal" role="alert">{galat}</Alert>}
                    <Button type="submit" disabled={mengirim}>
                        {mengirim ? 'Memproses...' : 'Masuk'}
                    </Button>
                </form>
                <ThemeSwitcher />
            </main>
        </div>
    );
}
