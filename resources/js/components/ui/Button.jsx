const VARIAN = {
    utama: 'bg-utama font-semibold text-di-utama',
    garis: 'border border-garis font-medium',
    hantu: 'font-medium text-redup hover:bg-kertas hover:text-teks',
};

const UKURAN = {
    biasa: 'min-h-11 px-4',
    kecil: 'min-h-9 px-2 text-sm',
};

export default function Button({ as: Komponen = 'button', varian = 'utama', ukuran = 'biasa', className = '', ...props }) {
    return (
        <Komponen
            {...(Komponen === 'button' ? { type: 'button' } : {})}
            {...props}
            className={`inline-flex items-center justify-center gap-2 rounded text-center transition-colors disabled:opacity-50 motion-reduce:transition-none ${UKURAN[ukuran]} ${VARIAN[varian]} ${className}`}
        />
    );
}
