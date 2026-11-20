import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { useEffect, useRef, useState } from 'react';
import { PUSAT_SANGKET } from '../../config/peta';
import { pasangBasemap } from '../../lib/basemap';

const ZOOM_AWAL = 16;
const ZOOM_TITIK = 18;

const SVG_PERBESAR = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 3H5a2 2 0 0 0-2 2v3M21 8V5a2 2 0 0 0-2-2h-3M3 16v3a2 2 0 0 0 2 2h3M16 21h3a2 2 0 0 0 2-2v-3"/></svg>';
const SVG_PERKECIL = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 3v3a2 2 0 0 1-2 2H3M21 8h-3a2 2 0 0 1-2-2V3M3 16h3a2 2 0 0 1 2 2v3M16 21v-3a2 2 0 0 1 2-2h3"/></svg>';

// Penanda pin: ujung bawah pin tepat di titik yang dipilih.
const IKON_PIN = L.divIcon({
    className: 'penanda-pin',
    html: '<svg viewBox="0 0 32 44" width="32" height="44" aria-hidden="true"><path d="M16 1C7.7 1 1 7.6 1 15.8 1 27 16 43 16 43s15-16 15-27.2C31 7.6 24.3 1 16 1Z" fill="#c2410c" stroke="#fff" stroke-width="2"/><circle cx="16" cy="15.5" r="5.5" fill="#fff"/></svg>',
    iconSize: [32, 44],
    iconAnchor: [16, 43],
});

export default function PetaLokasi({ titik, onPilih, tinggi = 'h-80 md:h-[28rem]' }) {
    const wadahRef = useRef(null);
    const petaRef = useRef(null);
    const penandaRef = useRef(null);
    const tombolPerbesarRef = useRef(null);
    const onPilihRef = useRef(onPilih);
    const [layarPenuh, setLayarPenuh] = useState(false);

    useEffect(() => {
        onPilihRef.current = onPilih;
    }, [onPilih]);

    useEffect(() => {
        // Animasi zoom dimatikan agar tidak berjalan setelah peta dilepas saat berpindah halaman.
        const peta = L.map(wadahRef.current, { zoomAnimation: false, maxZoom: 20 }).setView(PUSAT_SANGKET, ZOOM_AWAL);

        pasangBasemap(peta);

        const KontrolPerbesar = L.Control.extend({
            onAdd() {
                const tombol = L.DomUtil.create('button', 'leaflet-bar tombol-peta');
                tombol.type = 'button';
                L.DomEvent.disableClickPropagation(tombol);
                L.DomEvent.on(tombol, 'click', () => setLayarPenuh((lama) => !lama));
                tombolPerbesarRef.current = tombol;

                return tombol;
            },
        });
        new KontrolPerbesar({ position: 'topleft' }).addTo(peta);

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
        const tombol = tombolPerbesarRef.current;

        if (!peta) return undefined;

        if (tombol) {
            tombol.innerHTML = layarPenuh ? SVG_PERKECIL : SVG_PERBESAR;
            tombol.setAttribute('aria-label', layarPenuh ? 'Kecilkan peta' : 'Perbesar peta');
            tombol.title = layarPenuh ? 'Kecilkan peta (Esc)' : 'Perbesar peta';
        }

        // Ukuran wadah berubah, jadi Leaflet perlu menghitung ulang petanya.
        peta.invalidateSize({ animate: false });

        if (penandaRef.current) peta.panTo(penandaRef.current.getLatLng(), { animate: false });

        if (!layarPenuh) return undefined;

        const saatTombol = (event) => {
            if (event.key === 'Escape') setLayarPenuh(false);
        };
        document.addEventListener('keydown', saatTombol);
        document.documentElement.style.overflow = 'hidden';

        return () => {
            document.removeEventListener('keydown', saatTombol);
            document.documentElement.style.overflow = '';
        };
    }, [layarPenuh]);

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
            // Pin bisa digeser untuk merapikan posisi tanpa harus mengetuk ulang.
            penandaRef.current = L.marker(posisi, { icon: IKON_PIN, draggable: true, autoPan: true, title: 'Titik lokasi, geser untuk merapikan', alt: 'Titik lokasi' }).addTo(peta);
            penandaRef.current.on('dragend', (event) => {
                const { lat, lng } = event.target.getLatLng();
                onPilihRef.current({ latitude: lat, longitude: lng });
            });
            peta.setView(posisi, Math.max(peta.getZoom(), ZOOM_TITIK), { animate: false });
        }

        peta.panTo(posisi, { animate: false });
    }, [titik]);

    // Class pembungkus diatur React, sedangkan elemen dalam milik Leaflet agar class-nya tidak tertimpa saat ukuran berubah.
    return (
        <div className={`isolate w-full overflow-hidden border border-garis ${layarPenuh ? 'fixed inset-0 z-[60] h-dvh rounded-none' : `rounded ${tinggi}`}`}>
            <div
                ref={wadahRef}
                className="h-full w-full"
                role="application"
                aria-label="Peta untuk memilih titik lokasi. Ketuk peta atau geser pin untuk menandai."
            />
        </div>
    );
}
