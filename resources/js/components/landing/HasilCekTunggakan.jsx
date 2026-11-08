import { rupiah } from '../../lib/format';
import StatusTagihanBadge from '../tagihan/StatusTagihanBadge';
import DefinitionGrid from '../ui/DefinitionGrid';

export default function HasilCekTunggakan({ hasil }) {
    const alamat = [hasil.perumahan, hasil.nomor_rumah].filter(Boolean).join(' · ');

    return (
        <div aria-live="polite" className="grid gap-5 rounded-3xl border border-langit-sedang bg-panel p-5">
            <div className="grid gap-4 md:grid-cols-[1fr_auto] md:items-start">
                <DefinitionGrid
                    items={[
                        { label: 'Nama', isi: hasil.nama },
                        { label: 'Status krama', isi: hasil.status },
                        { label: 'Alamat perumahan', isi: alamat },
                    ]}
                />
                <div className={`grid gap-1 rounded-lg p-4 ${hasil.tunggakan > 0 ? 'bg-latar-peringatan' : 'bg-latar-berhasil'}`}>
                    <p className="text-sm">Tunggakan</p>
                    <p className="text-2xl font-semibold tabular-nums">{rupiah(hasil.tunggakan)}</p>
                </div>
            </div>

            <div className="grid gap-2">
                <h3 className="font-semibold">Riwayat tagihan</h3>
                {hasil.riwayat.length === 0 ? (
                    <p className="text-sm text-redup">Belum ada tagihan.</p>
                ) : (
                    <ul className="divide-y divide-garis rounded border border-garis">
                        {hasil.riwayat.map((baris) => (
                            <li key={baris.id} className="grid gap-1 px-3 py-2 text-sm">
                                <div className="flex flex-wrap items-center justify-between gap-2">
                                    <span className="font-medium">{baris.tahun} · {baris.jenis}</span>
                                    <StatusTagihanBadge status={baris.status} />
                                </div>
                                <p className="text-redup tabular-nums">
                                    Tagihan {rupiah(baris.total_tagihan)}
                                    {baris.nominal_bayar !== null && ` · Dibayar ${rupiah(baris.nominal_bayar)}`}
                                    {baris.tanggal_bayar && ` · ${baris.tanggal_bayar}`}
                                </p>
                            </li>
                        ))}
                    </ul>
                )}
            </div>
        </div>
    );
}
