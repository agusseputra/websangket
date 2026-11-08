import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { useEffect, useImperativeHandle, useRef } from 'react';
import { WARNA_PERUMAHAN, WARNA_STATUS } from '../../config/peta';

// Perkiraan pusat Lingkungan Sangket, Sukasada.
const PUSAT_SANGKET = [-8.1405, 115.09];
const ZOOM_AWAL = 15;
const ZOOM_FOKUS = 19;

function labelLayer(warna, teks, jumlah, bentuk = '50%') {
    return `<span style="display:inline-block;width:10px;height:10px;border-radius:${bentuk};background:${warna};margin-right:4px"></span>${teks} (${jumlah})`;
}

// Isi popup dibuat lewat DOM agar nama dari pengguna tidak dibaca sebagai HTML.
function isiPopup(judul, baris, tombol) {
    const wadah = document.createElement('div');
    const elemenJudul = document.createElement('strong');

    elemenJudul.textContent = judul;
    wadah.append(elemenJudul);

    baris.forEach((teks) => {
        const elemen = document.createElement('div');
        elemen.textContent = teks;
        wadah.append(elemen);
    });

    if (tombol) {
        const elemen = document.createElement('button');
        elemen.type = 'button';
        elemen.textContent = tombol.label;
        elemen.style.cssText = 'margin-top:6px;padding:0;background:none;border:0;color:#1f5f78;text-decoration:underline;cursor:pointer';
        elemen.addEventListener('click', tombol.onClick);
        wadah.append(elemen);
    }

    return wadah;
}

export default function PetaSebaran({ data, onDetail, ref }) {
    const wadahRef = useRef(null);
    const petaRef = useRef(null);
    const penandaRef = useRef(new Map());
    const onDetailRef = useRef(onDetail);

    useEffect(() => {
        onDetailRef.current = onDetail;
    }, [onDetail]);

    useImperativeHandle(ref, () => ({
        fokusKrama(id) {
            const entri = penandaRef.current.get(id);

            if (!entri) return false;

            entri.grup.addTo(petaRef.current);
            petaRef.current.setView(entri.penanda.getLatLng(), ZOOM_FOKUS, { animate: false });
            entri.penanda.openPopup();

            return true;
        },
    }), []);

    useEffect(() => {
        // Animasi zoom dimatikan agar tidak berjalan setelah peta dilepas saat berpindah halaman.
        const peta = L.map(wadahRef.current, { zoomAnimation: false }).setView(PUSAT_SANGKET, ZOOM_AWAL);
        const penanda = penandaRef.current;

        const openStreetMap = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap',
        }).addTo(peta);

        const satelit = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: 'Citra &copy; Esri',
        });

        const overlay = {};
        const semuaTitik = [];

        const grupPerumahan = L.layerGroup().addTo(peta);
        const ikonPerumahan = L.divIcon({
            className: '',
            html: `<span style="display:block;width:16px;height:16px;background:${WARNA_PERUMAHAN};border:2px solid #fff;box-shadow:0 0 2px #000"></span>`,
            iconSize: [16, 16],
            iconAnchor: [8, 8],
        });

        data.perumahan.forEach((perumahan) => {
            const posisi = [perumahan.latitude, perumahan.longitude];

            L.marker(posisi, { icon: ikonPerumahan, title: perumahan.nama })
                .bindPopup(isiPopup(perumahan.nama, ['Perumahan', `${perumahan.krama_count} krama`]))
                .addTo(grupPerumahan);
            semuaTitik.push(posisi);
        });

        overlay[labelLayer(WARNA_PERUMAHAN, 'Perumahan', data.perumahan.length, '0')] = grupPerumahan;

        WARNA_STATUS.forEach(({ status, label, warna }) => {
            const grup = L.layerGroup().addTo(peta);
            const daftar = data.krama.filter((krama) => krama.status === status);

            daftar.forEach((krama) => {
                const posisi = [krama.latitude, krama.longitude];
                const titik = L.circleMarker(posisi, { radius: 7, color: '#ffffff', weight: 2, fillColor: warna, fillOpacity: 0.95 })
                    .bindPopup(isiPopup(
                        krama.nama,
                        [krama.status_label, `Perumahan: ${krama.perumahan ?? '–'}`, `Nomor rumah: ${krama.nomor_rumah ?? '–'}`],
                        { label: 'Lihat detail', onClick: () => onDetailRef.current(krama.id) },
                    ))
                    .addTo(grup);

                penanda.set(krama.id, { penanda: titik, grup });
                semuaTitik.push(posisi);
            });

            overlay[labelLayer(warna, label, daftar.length)] = grup;
        });

        L.control.layers({ OpenStreetMap: openStreetMap, Satelit: satelit }, overlay, { collapsed: window.innerWidth < 768 }).addTo(peta);

        if (semuaTitik.length > 0) {
            peta.fitBounds(semuaTitik, { padding: [30, 30], maxZoom: 18, animate: false });
        }

        petaRef.current = peta;

        return () => {
            peta.remove();
            penanda.clear();
            petaRef.current = null;
        };
    }, [data]);

    return <div ref={wadahRef} className="isolate h-[70dvh] min-h-96 w-full rounded border border-garis" role="application" aria-label="Peta persebaran krama" />;
}
