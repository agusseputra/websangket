import Badge from '../ui/Badge';

export default function StatusDataBadge({ status }) {
    return status === 'pending' ? <Badge nada="peringatan">Pending</Badge> : <Badge nada="berhasil">Lengkap</Badge>;
}
