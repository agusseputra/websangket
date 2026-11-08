// Layanan BLE yang umum dipakai printer thermal. Chrome hanya mengizinkan layanan yang disebut di sini.
const LAYANAN_PRINTER = [
    '000018f0-0000-1000-8000-00805f9b34fb',
    'e7810a71-73ae-499d-8c15-faa9aef0c3f2',
    '49535343-fe7d-4ae5-8fa9-9fafd205e455',
    '0000ff00-0000-1000-8000-00805f9b34fb',
    '0000fff0-0000-1000-8000-00805f9b34fb',
    '0000ffe0-0000-1000-8000-00805f9b34fb',
    '0000ae30-0000-1000-8000-00805f9b34fb',
    '0000fee7-0000-1000-8000-00805f9b34fb',
];

const UKURAN_POTONGAN = 20;
const JEDA_POTONGAN_MS = 20;

export function webBluetoothTersedia() {
    return typeof navigator !== 'undefined' && 'bluetooth' in navigator;
}

export function pesanGagalCetak(error) {
    return error.name === 'NotFoundError'
        ? 'Tidak ada printer yang dipilih. Jika printer tidak muncul, kemungkinan printer memakai Bluetooth klasik.'
        : `Gagal mencetak: ${error.message}`;
}

async function cariPenulis(server, catat) {
    const layanan = await server.getPrimaryServices();

    for (const satu of layanan) {
        catat(`Layanan: ${satu.uuid}`);
        const daftar = await satu.getCharacteristics();

        for (const karakteristik of daftar) {
            const { write, writeWithoutResponse } = karakteristik.properties;
            if (write || writeWithoutResponse) {
                catat(`Jalur tulis: ${karakteristik.uuid}`);
                return { karakteristik, tanpaRespons: writeWithoutResponse };
            }
        }
    }

    throw new Error('Printer tersambung, tetapi jalur tulisnya tidak ditemukan.');
}

/** Harus dipanggil dari klik pengguna, karena browser meminta izin lewat dialog. */
export async function cetakViaBluetooth(bytes, catat = () => {}) {
    catat('Meminta izin Bluetooth...');
    const perangkat = await navigator.bluetooth.requestDevice({
        acceptAllDevices: true,
        optionalServices: LAYANAN_PRINTER,
    });
    catat(`Perangkat dipilih: ${perangkat.name ?? 'tanpa nama'}`);

    const server = await perangkat.gatt.connect();

    try {
        const { karakteristik, tanpaRespons } = await cariPenulis(server, catat);

        for (let awal = 0; awal < bytes.length; awal += UKURAN_POTONGAN) {
            const potongan = bytes.slice(awal, awal + UKURAN_POTONGAN);
            if (tanpaRespons) {
                await karakteristik.writeValueWithoutResponse(potongan);
            } else {
                await karakteristik.writeValueWithResponse(potongan);
            }
            await new Promise((selesai) => setTimeout(selesai, JEDA_POTONGAN_MS));
        }

        catat(`Terkirim ${bytes.length} byte.`);
    } finally {
        server.disconnect();
    }
}
