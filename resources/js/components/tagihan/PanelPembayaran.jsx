import { Link } from 'react-router-dom';
import { rupiah } from '../../lib/format';
import Button from '../ui/Button';
import StatusTagihanBadge from './StatusTagihanBadge';

function bisaDivalidasi(baris) {
    return baris.status === 'dibayar' || baris.status === 'pending';
}

function RincianBaris({ baris, admin, onAksi }) {
    const dibayar = baris.nominal_bayar !== null;

    return (
        <li className="grid gap-1 rounded bg-latar px-3 py-2">
            <div className="flex flex-wrap items-center justify-between gap-x-3 gap-y-1">
                <span className="min-w-0 text-sm font-medium">{baris.tahun} · {baris.jenis_label}</span>
                <span className="flex items-center gap-1">
                    <StatusTagihanBadge status={baris.status} />
                    {admin && bisaDivalidasi(baris) && (
                        <Button varian="hantu" ukuran="kecil" className="-my-1" onClick={() => onAksi('koreksi', baris)}>Koreksi</Button>
                    )}
                    {admin && baris.status === 'dibuat' && (
                        <Button varian="hantu" ukuran="kecil" className="-my-1" onClick={() => onAksi('dispensasi', baris)}>Dispensasi</Button>
                    )}
                </span>
            </div>
            <p className="text-sm text-redup tabular-nums">
                Tagihan {rupiah(baris.total_tagihan)}
                {dibayar && ` · Dibayar ${rupiah(baris.nominal_bayar)}`}
                {dibayar && baris.sisa > 0 && ` · Kurang ${rupiah(baris.sisa)}`}
            </p>
        </li>
    );
}

export default function PanelPembayaran({ pembayaran, admin, terpilih, onCentang, onAksi }) {
    const bisaPilih = admin ? pembayaran.item.filter(bisaDivalidasi).map((baris) => baris.id) : [];
    const tercentang = bisaPilih.length > 0 && bisaPilih.every((id) => terpilih.includes(id));
    const info = [pembayaran.no_struk, pembayaran.tanggal_bayar, pembayaran.metode, pembayaran.petugas].filter(Boolean).join(' · ');
    const totalBayar = pembayaran.item.reduce((jumlah, baris) => jumlah + (baris.nominal_bayar ?? 0), 0);
    const totalTagihan = pembayaran.item.reduce((jumlah, baris) => jumlah + baris.total_tagihan, 0);

    return (
        <li className="flex items-start gap-3 px-4 py-3">
            {bisaPilih.length > 0 && (
                <input
                    type="checkbox"
                    aria-label={`Pilih pembayaran ${pembayaran.no_struk ?? ''} ${pembayaran.krama}`}
                    className="mt-1 size-5 shrink-0"
                    checked={tercentang}
                    onChange={(e) => onCentang(bisaPilih, e.target.checked)}
                />
            )}
            <div className="grid min-w-0 flex-1 gap-2">
                <div className="grid gap-0.5">
                    <div className="flex items-start justify-between gap-3">
                        <Link to={`/krama/${pembayaran.krama_id}/bayar`} className="min-w-0 font-medium break-words underline-offset-2 hover:underline">{pembayaran.krama}</Link>
                        <span className="shrink-0 font-semibold tabular-nums">{rupiah(totalBayar || totalTagihan)}</span>
                    </div>
                    {info && <p className="text-sm break-words text-redup">{info}</p>}
                </div>
                <ul className="grid gap-1">
                    {pembayaran.item.map((baris) => <RincianBaris key={baris.id} baris={baris} admin={admin} onAksi={onAksi} />)}
                </ul>
            </div>
        </li>
    );
}
