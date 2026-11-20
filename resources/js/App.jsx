import { lazy, Suspense } from 'react';
import { Navigate, Route, Routes } from 'react-router-dom';
import ProtectedRoute from './components/auth/ProtectedRoute';
import AppLayout from './components/layout/AppLayout';
import { berandaUntuk, useAuth } from './context/AuthContext';
import BerandaAdmin from './pages/BerandaAdmin';
import BerandaPetugas from './pages/BerandaPetugas';
import BeritaListPage from './pages/berita/BeritaListPage';
import ImporKramaPage from './pages/ImporKramaPage';
import KategoriBeritaPage from './pages/KategoriBeritaPage';
import BayarPage from './pages/krama/BayarPage';
import KramaDetailPage from './pages/krama/KramaDetailPage';
import KramaFormPage from './pages/krama/KramaFormPage';
import KramaListPage from './pages/krama/KramaListPage';
import KramaLokasiPage from './pages/krama/KramaLokasiPage';
import LandingPage from './pages/LandingPage';
import LaporanPage from './pages/LaporanPage';
import Login from './pages/Login';
import PenggunaPage from './pages/PenggunaPage';
import PetaPage from './pages/PetaPage';
import PeriodePage from './pages/PeriodePage';
import PerumahanPage from './pages/PerumahanPage';
import TagihanPage from './pages/TagihanPage';
import TunggakanPage from './pages/TunggakanPage';
import UjiPrinter from './pages/UjiPrinter';

// Teks editor cukup besar, jadi halaman form berita dimuat terpisah agar halaman lain tetap ringan.
const BeritaFormPage = lazy(() => import('./pages/berita/BeritaFormPage'));

export default function App() {
    const { user, memuat } = useAuth();

    if (memuat) {
        return <p className="p-6 text-redup">Memuat...</p>;
    }

    return (
        <Routes>
            <Route path="/" element={<LandingPage />} />
            <Route path="/login" element={<Login />} />
            <Route element={<ProtectedRoute />}>
                <Route element={<AppLayout />}>
                    <Route element={<ProtectedRoute peran={['admin']} />}>
                        <Route path="/admin" element={<BerandaAdmin />} />
                        <Route path="/perumahan" element={<PerumahanPage />} />
                        <Route path="/impor-krama" element={<ImporKramaPage />} />
                        <Route path="/periode" element={<PeriodePage />} />
                        <Route path="/peta" element={<PetaPage />} />
                        <Route path="/laporan" element={<LaporanPage />} />
                        <Route path="/pengguna" element={<PenggunaPage />} />
                        <Route path="/uji-printer" element={<UjiPrinter />} />
                        <Route path="/berita" element={<BeritaListPage />} />
                        <Route path="/berita/baru" element={<Suspense fallback={<p className="text-redup">Memuat editor...</p>}><BeritaFormPage /></Suspense>} />
                        <Route path="/berita/:id/ubah" element={<Suspense fallback={<p className="text-redup">Memuat editor...</p>}><BeritaFormPage /></Suspense>} />
                        <Route path="/kategori-berita" element={<KategoriBeritaPage />} />
                        <Route path="/petugas" element={<BerandaPetugas />} />
                    </Route>
                    <Route path="/krama" element={<KramaListPage />} />
                    <Route path="/krama/baru" element={<KramaFormPage />} />
                    <Route path="/krama/:id" element={<KramaDetailPage />} />
                    <Route path="/krama/:id/ubah" element={<KramaFormPage />} />
                    <Route path="/krama/:id/lokasi" element={<KramaLokasiPage />} />
                    <Route path="/krama/:id/bayar" element={<BayarPage />} />
                    <Route path="/tunggakan" element={<TunggakanPage />} />
                    <Route path="/tagihan" element={<TagihanPage />} />
                </Route>
            </Route>
            <Route path="*" element={<Navigate to={user ? berandaUntuk(user) : '/login'} replace />} />
        </Routes>
    );
}
