const NADA = {
    netral: 'bg-kertas text-teks',
    peringatan: 'bg-latar-peringatan text-peringatan',
    berhasil: 'bg-latar-berhasil text-berhasil',
};

export default function Badge({ nada = 'netral', children }) {
    return <span className={`inline-block rounded px-2 py-0.5 text-xs font-medium whitespace-nowrap ${NADA[nada]}`}>{children}</span>;
}
