import { kelasInput, PembungkusField } from './Field';

export default function SelectField({ label, id, galat, bantuan, pilihan, kosong, ...props }) {
    return (
        <PembungkusField id={id} label={label} galat={galat} bantuan={bantuan}>
            <select id={id} aria-invalid={Boolean(galat)} {...props} className={kelasInput}>
                {kosong !== undefined && <option value="">{kosong}</option>}
                {pilihan.map(({ nilai, label: teks }) => (
                    <option key={nilai} value={nilai}>{teks}</option>
                ))}
            </select>
        </PembungkusField>
    );
}
