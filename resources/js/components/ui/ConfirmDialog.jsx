import { useEffect, useRef } from 'react';
import Button from './Button';

export default function ConfirmDialog({ judul, pesan, labelYa, memproses, onYa, onBatal, children }) {
    const batalRef = useRef(null);

    useEffect(() => {
        batalRef.current?.focus();

        const saatTombol = (event) => {
            if (event.key === 'Escape') onBatal();
        };

        document.addEventListener('keydown', saatTombol);

        return () => document.removeEventListener('keydown', saatTombol);
    }, [onBatal]);

    return (
        <div className="fixed inset-0 z-50 grid place-items-center bg-black/50 p-4">
            <div role="alertdialog" aria-modal="true" aria-labelledby="dialog-judul" className="grid w-full max-w-md gap-4 rounded border border-garis bg-panel p-5">
                <h2 id="dialog-judul" className="text-lg font-semibold">{judul}</h2>
                <p className="text-redup">{pesan}</p>
                {children}
                <div className="flex flex-wrap justify-end gap-2">
                    <Button ref={batalRef} varian="garis" onClick={onBatal} disabled={memproses}>Batal</Button>
                    <Button onClick={onYa} disabled={memproses}>{memproses ? 'Memproses...' : labelYa}</Button>
                </div>
            </div>
        </div>
    );
}
