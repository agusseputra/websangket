import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { useEffect, useRef } from 'react';

// Perkiraan pusat Lingkungan Sangket, Sukasada.
const PUSAT_SANGKET = [-8.1405, 115.09];
const ZOOM_AWAL = 16;
const ZOOM_TITIK = 18;

export default function PetaLokasi({ titik, onPilih }) {
    const wadahRef = useRef(null);
    const petaRef = useRef(null);
    const penandaRef = useRef(null);
    const onPilihRef = useRef(onPilih);

    useEffect(() => {
        onPilihRef.current = onPilih;
    }, [onPilih]);

    useEffect(() => {
        // Animasi zoom dimatikan agar tidak berjalan setelah peta dilepas saat berpindah halaman.
        const peta = L.map(wadahRef.current, { zoomAnimation: false }).setView(PUSAT_SANGKET, ZOOM_AWAL);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap',
        }).addTo(peta);

        peta.on('click', (event) => onPilihRef.current({ latitude: event.latlng.lat, longitude: event.latlng.lng }));
        petaRef.current = peta;

        return () => {
            peta.remove();
            petaRef.current = null;
            penandaRef.current = null;
        };
    }, []);

    useEffect(() => {
        const peta = petaRef.current;

        if (!peta) return;

        if (!titik) {
            penandaRef.current?.remove();
            penandaRef.current = null;
            return;
        }

        const posisi = [Number(titik.latitude), Number(titik.longitude)];

        if (penandaRef.current) {
            penandaRef.current.setLatLng(posisi);
        } else {
            penandaRef.current = L.circleMarker(posisi, { radius: 9, color: '#1f5f78', fillColor: '#1f5f78', fillOpacity: 0.85 }).addTo(peta);
            peta.setView(posisi, ZOOM_TITIK, { animate: false });
        }

        peta.panTo(posisi, { animate: false });
    }, [titik]);

    return <div ref={wadahRef} className="isolate h-72 w-full rounded border border-garis md:h-96" role="application" aria-label="Peta untuk memilih titik lokasi" />;
}
