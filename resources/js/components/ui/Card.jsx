export default function Card({ judul, children }) {
    return (
        <section className="grid min-w-0 gap-3 rounded border border-garis bg-panel p-4">
            {judul && <h2 className="font-semibold">{judul}</h2>}
            {children}
        </section>
    );
}
