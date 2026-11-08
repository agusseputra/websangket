import { kelasInput, PembungkusField } from './Field';

export default function TextAreaField({ label, id, galat, ...props }) {
    return (
        <PembungkusField id={id} label={label} galat={galat}>
            <textarea id={id} rows={3} aria-invalid={Boolean(galat)} {...props} className={`${kelasInput} py-2`} />
        </PembungkusField>
    );
}
