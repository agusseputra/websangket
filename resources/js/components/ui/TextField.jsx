import { kelasInput, PembungkusField } from './Field';

export default function TextField({ label, id, galat, bantuan, ...props }) {
    return (
        <PembungkusField id={id} label={label} galat={galat} bantuan={bantuan}>
            <input id={id} aria-invalid={Boolean(galat)} aria-describedby={galat ? `${id}-galat` : undefined} {...props} className={kelasInput} />
        </PembungkusField>
    );
}
