export default function JudulSection({ id, label, judul, deskripsi, rata = 'kiri' }) {
    return (
        <div className={`grid max-w-3xl gap-3 ${rata === 'tengah' ? 'mx-auto text-center' : ''}`}>
            {label && <p className="text-sm font-semibold tracking-wide text-langit-tua">{label}</p>}
            <h2 id={id} className="font-display text-3xl font-bold text-balance sm:text-4xl">{judul}</h2>
            {deskripsi && <p className="text-lg text-redup">{deskripsi}</p>}
        </div>
    );
}
