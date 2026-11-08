import { useEffect, useRef, useState } from 'react';
import Button from './Button';
import { kelasInput } from './Field';

export default function AlasanDialog({ judul, pesan, labelYa, memproses, galat, onYa, onBatal }) {
    const [alasan, setAlasan] = useState('');
    const inputRef = useRef(null);

    useEffect(() => {
        inputRef.current?.focus();

        const saatTombol = (event) => {
            if (event.key === 'Escape') onBatal();
        };

        document.addEventListener('keydown', saatTombol);

        return () => document.removeEventListener('keydown', saatTombol);
    }, [onBatal]);

    function kirim(event) {
        event.preventDefault();
        onYa(alasan);
    }

    return (
        <div className="fixed inset-0 z-50 grid place-items-center bg-black/50 p-4">
            <form onSubmit={kirim} role="dialog" aria-modal="true" aria-labelledby="alasan-judul" className="grid w-full max-w-md gap-4 rounded border border-garis bg-panel p-5">
                <h2 id="alasan-judul" className="text-lg font-semibold">{judul}</h2>
                <p className="text-redup">{pesan}</p>
                <div className="grid gap-1">
                    <label htmlFor="alasan" className="text-sm font-medium">Alasan</label>
                    <input ref={inputRef} id="alasan" required maxLength={255} value={alasan} onChange={(e) => setAlasan(e.target.value)} className={kelasInput} />
                    {galat && <p className="text-sm text-gagal">{galat}</p>}
                </div>
                <div className="flex flex-wrap justify-end gap-2">
                    <Button varian="garis" onClick={onBatal} disabled={memproses}>Batal</Button>
                    <Button type="submit" disabled={memproses}>{memproses ? 'Memproses...' : labelYa}</Button>
                </div>
            </form>
        </div>
    );
}
