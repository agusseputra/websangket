import { STATUS_KRAMA } from '../../config/krama';
import SelectField from '../ui/SelectField';
import TextField from '../ui/TextField';

export default function KramaFilter({ cari, onCari, filter, onFilter, perumahan }) {
    return (
        <div className="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
            <div className="sm:col-span-2 xl:col-span-1">
                <TextField
                    id="cari"
                    type="search"
                    label="Cari"
                    placeholder="Nama atau NIK lengkap"
                    value={cari}
                    onChange={(e) => onCari(e.target.value)}
                />
            </div>
            <SelectField
                id="filter-status-data"
                label="Status data"
                kosong="Semua"
                value={filter.status_data}
                onChange={(e) => onFilter('status_data', e.target.value)}
                pilihan={[{ nilai: 'pending', label: 'Pending' }, { nilai: 'lengkap', label: 'Lengkap' }]}
            />
            <SelectField
                id="filter-status"
                label="Status krama"
                kosong="Semua"
                value={filter.status}
                onChange={(e) => onFilter('status', e.target.value)}
                pilihan={STATUS_KRAMA}
            />
            <SelectField
                id="filter-perumahan"
                label="Perumahan"
                kosong="Semua"
                value={filter.perumahan_id}
                onChange={(e) => onFilter('perumahan_id', e.target.value)}
                pilihan={perumahan.map(({ id, nama }) => ({ nilai: id, label: nama }))}
            />
            <SelectField
                id="filter-lokasi"
                label="Lokasi"
                kosong="Semua"
                value={filter.lokasi}
                onChange={(e) => onFilter('lokasi', e.target.value)}
                pilihan={[{ nilai: 'belum', label: 'Belum ditandai' }, { nilai: 'ada', label: 'Sudah ditandai' }]}
            />
        </div>
    );
}
