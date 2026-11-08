import { rupiah } from '../../lib/format';

export default function CatatanKartu({ tagihan }) {
    return (
        <div className="overflow-x-auto">
            <table className="w-full border border-teks text-left text-sm">
                <thead>
                    <tr>
                        <th scope="col" className="border border-teks px-3 py-2 font-semibold">Tahun</th>
                        <th scope="col" className="border border-teks px-3 py-2 font-semibold">Jumlah Pembayaran</th>
                        <th scope="col" className="border border-teks px-3 py-2 font-semibold">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    {tagihan.map((baris) => (
                        <tr key={baris.id}>
                            <td className="border border-teks px-3 py-2 tabular-nums">{baris.tahun}</td>
                            <td className="border border-teks px-3 py-2 tabular-nums whitespace-nowrap">{rupiah(baris.nominal_bayar)}</td>
                            <td className="border border-teks px-3 py-2">{baris.jenis_label}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}
