export default function IconButton({ label, ikon: Ikon, ref, className = '', ...props }) {
    return (
        <button
            ref={ref}
            type="button"
            aria-label={label}
            {...props}
            className={`inline-flex size-11 shrink-0 items-center justify-center rounded text-teks hover:bg-kertas ${className}`}
        >
            <Ikon className="size-6" aria-hidden="true" />
        </button>
    );
}
