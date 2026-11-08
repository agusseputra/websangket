import { useMemo } from 'react';
import { rupiah } from '../../lib/format';
import Card from '../ui/Card';
import CetakStruk from './CetakStruk';
import StatusTagihanBadge from './StatusTagihanBadge';

function kelompokkan(tagihan) {
    const perStruk = new Map();

    tagihan.forEach((baris) => {
        // Data impor dan dispensasi tidak punya nomor struk, jadi tampil sendiri-sendiri.
        const kunci = baris.no_struk ?? `tanpa-${baris.id}`;
        perStruk.set(kunci, [...(perStruk.get(kunci) ?? []), baris]);
    });

    return [...perStruk.entries()].map(([kunci, daftar]) => ({ kunci, noStruk: daftar[0].no_struk, daftar }));
}

function BarisRiwayat({ baris }) {
    return (
        <li className="flex flex-wrap items-center justify-between gap-2 text-sm">
            <span className="min-w-0">{baris.tahun} · {baris.jenis_label}</span>
            <span className="flex items-center gap-2">
                <span className="tabular-nums">{rupiah(baris.nominal_bayar)}</span>
                <StatusTagihanBadge status={baris.status} />
            </span>
        </li>
    );
}

export default function RiwayatTagihan({ tagihan, krama, tunggakan }) {
    const kelompok = useMemo(() => kelompokkan(tagihan), [tagihan]);

    if (kelompok.length === 0) return null;

    return (
        <Card judul="Riwayat tagihan">
            <ul className="divide-y divide-garis">
                {kelompok.map(({ kunci, noStruk, daftar }) => (
                    <li key={kunci} className="grid gap-2 py-3 first:pt-0 last:pb-0">
                        {noStruk && (
                            <div className="flex flex-wrap items-center justify-between gap-2">
                                <div className="min-w-0">
                                    <p className="font-mono text-sm font-medium break-all">{noStruk}</p>
                                    <p className="text-sm text-redup">{daftar[0].tanggal_bayar} · {daftar[0].metode === 'transfer' ? 'Transfer' : 'Tunai'}</p>
                                </div>
                                <CetakStruk
                                    hasil={{ no_struk: noStruk, tagihan: daftar, tunggakan }}
                                    krama={krama}
                                    petugas={{ name: daftar[0].petugas ?? '-' }}
                                    cetakUlang
                                    ringkas
                                />
                            </div>
                        )}
                        <ul className="grid gap-1">
                            {daftar.map((baris) => <BarisRiwayat key={baris.id} baris={baris} />)}
                        </ul>
                    </li>
                ))}
            </ul>
        </Card>
    );
}
