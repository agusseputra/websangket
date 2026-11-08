export const kelasInput = 'min-h-11 w-full min-w-0 rounded border border-garis bg-latar px-3 text-base font-normal aria-invalid:border-gagal';

export function PembungkusField({ id, label, galat, bantuan, children }) {
    return (
        <div className="grid min-w-0 content-start gap-1">
            <label htmlFor={id} className="text-sm font-medium">{label}</label>
            {children}
            {bantuan && !galat && <p className="text-sm text-redup">{bantuan}</p>}
            {galat && <p id={`${id}-galat`} className="text-sm text-gagal">{galat}</p>}
        </div>
    );
}
