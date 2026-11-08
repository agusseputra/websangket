export default function FotoBudaya({ foto, keterangan, className = '', prioritas = false }) {
    return (
        <figure className={`group relative min-w-0 overflow-hidden rounded-3xl bg-kertas ${className}`}>
            <img
                src={foto.src}
                alt={foto.alt}
                width={foto.lebar}
                height={foto.tinggi}
                loading={prioritas ? 'eager' : 'lazy'}
                decoding="async"
                className="size-full object-cover transition-transform duration-700 group-hover:scale-105 motion-reduce:transition-none"
            />
            {keterangan && (
                <figcaption className="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/75 to-transparent px-4 pt-10 pb-3 text-sm font-medium text-white">
                    {keterangan}
                </figcaption>
            )}
        </figure>
    );
}
