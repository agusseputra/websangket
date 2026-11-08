export default function DefinitionGrid({ items }) {
    return (
        <dl className="grid gap-3 sm:grid-cols-2">
            {items.map(({ label, isi }) => (
                <div key={label} className="min-w-0">
                    <dt className="text-sm text-redup">{label}</dt>
                    <dd className="break-words">{isi || '–'}</dd>
                </div>
            ))}
        </dl>
    );
}
