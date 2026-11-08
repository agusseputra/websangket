import Badge from '../ui/Badge';

const TAMPILAN = {
    dibuat: { label: 'Belum dibayar', nada: 'netral' },
    dibayar: { label: 'Dibayar', nada: 'peringatan' },
    pending: { label: 'Pending', nada: 'peringatan' },
    valid: { label: 'Valid', nada: 'berhasil' },
    dispensasi: { label: 'Dispensasi', nada: 'netral' },
};

export default function StatusTagihanBadge({ status }) {
    const { label, nada } = TAMPILAN[status];

    return <Badge nada={nada}>{label}</Badge>;
}
