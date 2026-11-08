import { LOGO_DESA } from '../../config/landing';

const BARIS = [
    { tahun: '2026', jumlah: 'Rp200.000', keterangan: 'Dudukan' },
    { tahun: '2026', jumlah: 'Rp1.000.000', keterangan: 'Penanjung Batu' },
    { tahun: '2027', jumlah: 'Rp200.000', keterangan: 'Dudukan' },
];

export default function KartuDudukanContoh() {
    return (
        <figure className="w-full max-w-md -rotate-1 rounded-2xl border border-langit-tua/30 bg-panel p-5 shadow-xl">
            <div className="flex items-center gap-3 border-b-2 border-teks pb-3">
                <img src={LOGO_DESA} alt="" className="size-12 shrink-0 object-contain" />
                <div className="min-w-0">
                    <p className="text-sm font-semibold tracking-wide">DESA ADAT SANGKET</p>
                    <p className="text-xs text-redup">Kartu Dudukan Kasukretan Krama</p>
                </div>
            </div>
            <table className="mt-3 w-full border border-teks text-left text-xs sm:text-sm">
                <thead>
                    <tr>
                        <th scope="col" className="border border-teks px-2 py-1.5 font-semibold">Tahun</th>
                        <th scope="col" className="border border-teks px-2 py-1.5 font-semibold">Jumlah</th>
                        <th scope="col" className="border border-teks px-2 py-1.5 font-semibold">Keterangan</th>
                    </tr>
                </thead>
                <tbody className="tabular-nums">
                    {BARIS.map((baris) => (
                        <tr key={`${baris.tahun}-${baris.keterangan}`}>
                            <td className="border border-teks px-2 py-1.5">{baris.tahun}</td>
                            <td className="border border-teks px-2 py-1.5 whitespace-nowrap">{baris.jumlah}</td>
                            <td className="border border-teks px-2 py-1.5">{baris.keterangan}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
            <figcaption className="mt-2 text-xs text-redup">Contoh pengisian kartu, bukan data krama.</figcaption>
        </figure>
    );
}
