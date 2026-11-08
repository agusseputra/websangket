import { Link } from 'react-router-dom';
import Badge from '../ui/Badge';
import StatusDataBadge from './StatusDataBadge';

function alamat(krama) {
    return [krama.perumahan, krama.nomor_rumah].filter(Boolean).join(' · ') || 'Perumahan belum diisi';
}

function KartuKrama({ krama }) {
    return (
        <li>
            <Link to={`/krama/${krama.id}`} className="grid gap-2 rounded border border-garis bg-panel p-4 hover:bg-kertas">
                <div className="flex items-start justify-between gap-3">
                    <p className="min-w-0 font-semibold break-words">{krama.nama}</p>
                    <StatusDataBadge status={krama.status_data} />
                </div>
                <p className="text-sm text-redup">{alamat(krama)}</p>
                <div className="flex flex-wrap gap-2">
                    <Badge>{krama.status_label}</Badge>
                    {!krama.ada_lokasi && <Badge nada="peringatan">Belum ada lokasi</Badge>}
                </div>
            </Link>
        </li>
    );
}

function TabelKrama({ data }) {
    return (
        <div className="overflow-x-auto rounded border border-garis bg-panel">
            <table className="w-full text-left text-sm">
                <thead className="bg-kertas">
                    <tr>
                        <th scope="col" className="px-4 py-3 font-semibold">Nama</th>
                        <th scope="col" className="px-4 py-3 font-semibold">NIK</th>
                        <th scope="col" className="px-4 py-3 font-semibold">Status</th>
                        <th scope="col" className="px-4 py-3 font-semibold">Alamat</th>
                        <th scope="col" className="px-4 py-3 font-semibold">Lokasi</th>
                        <th scope="col" className="px-4 py-3 font-semibold">Data</th>
                    </tr>
                </thead>
                <tbody>
                    {data.map((krama) => (
                        <tr key={krama.id} className="border-t border-garis hover:bg-kertas">
                            <td className="px-4 py-3 font-medium">
                                <Link to={`/krama/${krama.id}`} className="underline-offset-2 hover:underline">{krama.nama}</Link>
                            </td>
                            <td className="px-4 py-3 font-mono text-xs whitespace-nowrap">{krama.nik_tersamar ?? '–'}</td>
                            <td className="px-4 py-3 whitespace-nowrap">{krama.status_label}</td>
                            <td className="px-4 py-3">{alamat(krama)}</td>
                            <td className="px-4 py-3 whitespace-nowrap">{krama.ada_lokasi ? 'Sudah' : 'Belum'}</td>
                            <td className="px-4 py-3"><StatusDataBadge status={krama.status_data} /></td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

export default function KramaList({ data }) {
    return (
        <>
            <ul className="grid gap-3 lg:hidden">
                {data.map((krama) => <KartuKrama key={krama.id} krama={krama} />)}
            </ul>
            <div className="hidden lg:block">
                <TabelKrama data={data} />
            </div>
        </>
    );
}
