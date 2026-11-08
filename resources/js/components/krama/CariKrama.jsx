import { Search } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import useDebouncedValue from '../../hooks/useDebouncedValue';
import api from '../../lib/api';
import { pesanGalat } from '../../lib/galat';
import { kelasInput } from '../ui/Field';

const MINIMAL_KARAKTER = 2;
const BATAS_HASIL = 8;

export default function CariKrama({ id, label = 'Cek krama', onPilih }) {
    const [kata, setKata] = useState('');
    const [hasil, setHasil] = useState(null);
    const [galat, setGalat] = useState('');
    const [terbuka, setTerbuka] = useState(false);
    const wadahRef = useRef(null);
    const kataTertunda = useDebouncedValue(kata.trim());

    useEffect(() => {
        if (kataTertunda.length < MINIMAL_KARAKTER) {
            setHasil(null);
            return undefined;
        }

        let batal = false;

        setGalat('');

        api.get('/krama', { params: { q: kataTertunda } })
            .then((res) => {
                if (!batal) setHasil({ data: res.data.data.slice(0, BATAS_HASIL), total: res.data.meta.total });
            })
            .catch((error) => {
                if (!batal) setGalat(pesanGalat(error, 'Pencarian gagal.'));
            });

        return () => {
            batal = true;
        };
    }, [kataTertunda]);

    useEffect(() => {
        const saatKlikLuar = (event) => {
            if (!wadahRef.current?.contains(event.target)) setTerbuka(false);
        };

        document.addEventListener('pointerdown', saatKlikLuar);

        return () => document.removeEventListener('pointerdown', saatKlikLuar);
    }, []);

    function pilih(krama) {
        setTerbuka(false);
        setKata('');
        setHasil(null);
        onPilih(krama);
    }

    function saatTombol(event) {
        if (event.key === 'Escape') {
            setTerbuka(false);
        } else if (event.key === 'Enter') {
            event.preventDefault();

            if (hasil?.data[0]) pilih(hasil.data[0]);
        }
    }

    const tampilkan = terbuka && (hasil || galat);

    return (
        <div ref={wadahRef} className="relative grid min-w-0 gap-1">
            <label htmlFor={id} className="text-sm font-medium">{label}</label>
            <div className="relative">
                <Search className="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-redup" aria-hidden="true" />
                <input
                    id={id}
                    type="search"
                    autoComplete="off"
                    placeholder="Nama atau NIK 16 digit"
                    value={kata}
                    onChange={(e) => {
                        setKata(e.target.value);
                        setTerbuka(true);
                    }}
                    onFocus={() => setTerbuka(true)}
                    onKeyDown={saatTombol}
                    className={`${kelasInput} pl-9`}
                />
            </div>

            {tampilkan && (
                <div className="absolute top-full right-0 left-0 z-20 mt-1 max-h-80 overflow-y-auto rounded border border-garis bg-panel shadow-lg">
                    {galat && <p role="alert" className="p-3 text-sm text-gagal">{galat}</p>}
                    {hasil?.data.length === 0 && <p className="p-3 text-sm text-redup">Krama tidak ditemukan.</p>}
                    {hasil?.data.length > 0 && (
                        <ul className="divide-y divide-garis">
                            {hasil.data.map((krama) => (
                                <li key={krama.id}>
                                    <button type="button" onClick={() => pilih(krama)} className="grid min-h-11 w-full gap-0.5 px-3 py-2 text-left hover:bg-kertas">
                                        <span className="font-medium break-words">{krama.nama}</span>
                                        <span className="text-sm text-redup">
                                            {[krama.status_label, krama.perumahan, krama.nomor_rumah].filter(Boolean).join(' · ')}
                                        </span>
                                    </button>
                                </li>
                            ))}
                        </ul>
                    )}
                    {hasil?.total > BATAS_HASIL && (
                        <p className="border-t border-garis p-2 text-center text-sm text-redup">
                            Menampilkan {BATAS_HASIL} dari {hasil.total}. Perjelas pencarian.
                        </p>
                    )}
                </div>
            )}
        </div>
    );
}
