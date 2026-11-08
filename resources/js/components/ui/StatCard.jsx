import { Link } from 'react-router-dom';

export default function StatCard({ label, nilai, keterangan, ke, nilaiPanjang = false }) {
    const isi = (
        <>
            <p className="text-sm text-redup">{label}</p>
            <p className={`font-semibold break-words tabular-nums ${nilaiPanjang ? 'text-xl sm:text-2xl' : 'text-3xl'}`}>{nilai ?? '–'}</p>
            {keterangan && <p className="text-sm text-redup">{keterangan}</p>}
        </>
    );

    const kelas = 'grid min-w-0 content-start gap-1 rounded border border-garis bg-panel p-4';

    return ke ? <Link to={ke} className={`${kelas} transition-colors hover:bg-kertas motion-reduce:transition-none`}>{isi}</Link> : <div className={kelas}>{isi}</div>;
}
