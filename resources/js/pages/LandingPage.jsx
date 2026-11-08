import { useEffect } from 'react';
import BudayaSection from '../components/landing/BudayaSection';
import CekTunggakanSection from '../components/landing/CekTunggakanSection';
import FaqSection from '../components/landing/FaqSection';
import HeroSection from '../components/landing/HeroSection';
import KabarDesaSection from '../components/landing/KabarDesaSection';
import JenisKramaSection from '../components/landing/JenisKramaSection';
import KewajibanSection from '../components/landing/KewajibanSection';
import LandingFooter from '../components/landing/LandingFooter';
import PelaporanSection from '../components/landing/PelaporanSection';
import PeruntukanSection from '../components/landing/PeruntukanSection';
import ConnectionBanner from '../components/layout/ConnectionBanner';

// Halaman ini dirender React setelah dimuat, sehingga browser tidak sempat menggulir ke #bagian dari tautan halaman lain.
function gulirKeHash() {
    if (!window.location.hash) return;

    document.getElementById(decodeURIComponent(window.location.hash.slice(1)))?.scrollIntoView({ behavior: 'instant' });
}

export default function LandingPage() {
    useEffect(() => {
        gulirKeHash();
    }, []);

    return (
        <div className="min-h-dvh bg-latar">
            <ConnectionBanner />
            <main>
                <HeroSection />
                <JenisKramaSection />
                <KewajibanSection />
                <BudayaSection />
                <KabarDesaSection onMuat={gulirKeHash} />
                <CekTunggakanSection />
                <PeruntukanSection />
                <PelaporanSection />
                <FaqSection />
            </main>
            <LandingFooter />
        </div>
    );
}
