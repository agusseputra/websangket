const PEMFORMAT_RUPIAH = new Intl.NumberFormat('id-ID');

export function rupiah(nilai) {
    return `Rp${PEMFORMAT_RUPIAH.format(nilai ?? 0)}`;
}
