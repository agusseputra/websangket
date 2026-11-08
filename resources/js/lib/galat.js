export function pesanGalat(error, cadangan = 'Terjadi kesalahan. Coba lagi.') {
    if (!error.response) return 'Tidak bisa menghubungi server. Periksa sinyal Anda.';
    if (error.response.status === 403) return 'Anda tidak punya akses untuk tindakan ini.';
    if (error.response.status === 404) return 'Data tidak ditemukan.';
    if (error.response.status >= 500) return cadangan;

    const galat = error.response.data?.errors;
    const pertama = galat && Object.values(galat)[0]?.[0];

    return pertama ?? error.response.data?.message ?? cadangan;
}

export function galatKolom(error) {
    const galat = error.response?.data?.errors ?? {};

    return Object.fromEntries(Object.entries(galat).map(([kolom, pesan]) => [kolom, pesan[0]]));
}
