import { rupiah } from '../../lib/format';
import { kelasInput } from '../ui/Field';

export default function TagihanPilih({ tagihan, pilihan, onUbah }) {
    return (
        <ul className="divide-y divide-garis rounded border border-garis">
            {tagihan.map((baris) => {
                const nominal = pilihan[baris.id];
                const tercentang = nominal !== undefined;
                const idCentang = `tagihan-${baris.id}`;

                return (
                    <li key={baris.id} className="grid gap-3 p-3">
                        <label htmlFor={idCentang} className="flex min-h-11 items-center gap-3">
                            <input
                                id={idCentang}
                                type="checkbox"
                                className="size-5 shrink-0"
                                checked={tercentang}
                                onChange={(e) => onUbah(baris.id, e.target.checked ? String(baris.total_tagihan) : undefined)}
                            />
                            <span className="min-w-0 flex-1">
                                <span className="block font-medium">{baris.tahun} · {baris.jenis_label}</span>
                                {baris.kekurangan_sebelumnya > 0 && (
                                    <span className="block text-sm text-redup">
                                        {rupiah(baris.nominal)} + kekurangan {rupiah(baris.kekurangan_sebelumnya)}
                                    </span>
                                )}
                            </span>
                            <span className="font-semibold tabular-nums whitespace-nowrap">{rupiah(baris.total_tagihan)}</span>
                        </label>
                        {tercentang && (
                            <div className="grid gap-1 pl-8">
                                <label htmlFor={`nominal-${baris.id}`} className="text-sm font-medium">Nominal dibayar</label>
                                <input
                                    id={`nominal-${baris.id}`}
                                    type="number"
                                    inputMode="numeric"
                                    min="1"
                                    max={baris.total_tagihan}
                                    required
                                    value={nominal}
                                    onChange={(e) => onUbah(baris.id, e.target.value)}
                                    className={kelasInput}
                                />
                                <p className="text-sm text-redup">Boleh kurang. Kekurangannya tampil pada tagihan tahun berikutnya.</p>
                            </div>
                        )}
                    </li>
                );
            })}
        </ul>
    );
}
