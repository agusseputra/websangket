import { useEffect, useState } from 'react';

export default function ConnectionBanner() {
    const [online, setOnline] = useState(navigator.onLine);

    useEffect(() => {
        const perbarui = () => setOnline(navigator.onLine);
        window.addEventListener('online', perbarui);
        window.addEventListener('offline', perbarui);

        return () => {
            window.removeEventListener('online', perbarui);
            window.removeEventListener('offline', perbarui);
        };
    }, []);

    if (online) {
        return null;
    }

    return (
        <div role="status" className="bg-latar-peringatan px-4 py-2 text-center text-sm text-peringatan">
            Tidak ada sinyal. Data belum bisa disimpan sampai koneksi kembali.
        </div>
    );
}
