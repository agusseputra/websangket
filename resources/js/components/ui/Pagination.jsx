import Button from './Button';

export default function Pagination({ meta, onPindah }) {
    // Daftar 50 baris cukup panjang, jadi halaman baru dibaca dari atas.
    function pindah(halaman) {
        onPindah(halaman);
        window.scrollTo({ top: 0 });
    }

    if (!meta || meta.last_page <= 1) {
        return meta ? <p className="text-sm text-redup">{meta.total} data</p> : null;
    }

    return (
        <nav aria-label="Halaman" className="flex flex-wrap items-center justify-between gap-3">
            <p className="text-sm text-redup">
                Halaman {meta.current_page} dari {meta.last_page}, {meta.total} data
            </p>
            <div className="flex gap-2">
                <Button varian="garis" disabled={meta.current_page <= 1} onClick={() => pindah(meta.current_page - 1)}>Sebelumnya</Button>
                <Button varian="garis" disabled={meta.current_page >= meta.last_page} onClick={() => pindah(meta.current_page + 1)}>Berikutnya</Button>
            </div>
        </nav>
    );
}
