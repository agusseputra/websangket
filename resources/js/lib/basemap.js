import L from 'leaflet';

const KUNCI_SIMPAN = 'peta-basemap';

/**
 * Pilihan peta dasar: jalan (OpenStreetMap) dan satelit (citra Esri) dengan label nama tempat dan jalan.
 */
export function buatBasemap() {
    const peta = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 20,
        maxNativeZoom: 19,
        attribution: '&copy; OpenStreetMap',
    });

    const satelit = L.layerGroup([
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 20,
            maxNativeZoom: 19,
            attribution: 'Citra &copy; Esri, Maxar, Earthstar Geographics',
        }),
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Transportation/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 20,
            maxNativeZoom: 19,
            opacity: 0.8,
        }),
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 20,
            maxNativeZoom: 19,
        }),
    ]);

    return { Peta: peta, Satelit: satelit };
}

/**
 * Memasang peta dasar terakhir yang dipilih pengguna beserta tombol pilihannya.
 */
export function pasangBasemap(peta, { posisi = 'topright' } = {}) {
    const basemap = buatBasemap();
    let pilihan = 'Peta';

    try {
        pilihan = basemap[localStorage.getItem(KUNCI_SIMPAN)] ? localStorage.getItem(KUNCI_SIMPAN) : 'Peta';
    } catch {
        // Penyimpanan browser tidak tersedia; pakai peta jalan.
    }

    basemap[pilihan].addTo(peta);
    L.control.layers(basemap, null, { position: posisi, collapsed: false }).addTo(peta);

    peta.on('baselayerchange', (event) => {
        try {
            localStorage.setItem(KUNCI_SIMPAN, event.name);
        } catch {
            // Pilihan cukup berlaku selama halaman terbuka.
        }
    });

    return basemap;
}
