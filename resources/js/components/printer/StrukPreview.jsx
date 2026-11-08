function Baris({ kiri, kanan, tebal = false }) {
    return (
        <p className={`flex justify-between gap-2 ${tebal ? 'font-bold' : ''}`}>
            <span>{kiri}</span>
            <span>{kanan}</span>
        </p>
    );
}

function Pemisah() {
    return <hr className="my-2 border-dashed border-garis" />;
}

export default function StrukPreview({ struk }) {
    return (
        <div className="w-full max-w-xs rounded border border-garis bg-panel p-4 font-mono text-xs">
            <p className="text-center font-bold">{struk.judul}</p>
            <p className="text-center">{struk.subjudul}</p>
            <Pemisah />
            {struk.baris.map(([kiri, kanan]) => <Baris key={kiri} kiri={kiri} kanan={kanan} />)}
            <Pemisah />
            {struk.rincian.map(([kiri, kanan]) => <Baris key={kiri} kiri={kiri} kanan={kanan} />)}
            <Pemisah />
            <Baris kiri={struk.total[0]} kanan={struk.total[1]} tebal />
            {(struk.catatan ?? []).map(([kiri, kanan]) => <Baris key={kiri} kiri={kiri} kanan={kanan} />)}
            <p className="mt-3 text-center">{struk.penutup}</p>
        </div>
    );
}
