export default function PageHeader({ judul, deskripsi }) {
    return (
        <header className="grid gap-1">
            <h1 className="text-2xl font-semibold text-balance">{judul}</h1>
            {deskripsi && <p className="text-redup">{deskripsi}</p>}
        </header>
    );
}
