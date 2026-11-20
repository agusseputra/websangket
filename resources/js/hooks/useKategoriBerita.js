import { useCallback, useEffect, useState } from 'react';
import api from '../lib/api';
import { pesanGalat } from '../lib/galat';

export default function useKategoriBerita() {
    const [kategori, setKategori] = useState([]);
    const [memuat, setMemuat] = useState(true);
    const [galat, setGalat] = useState('');

    const muat = useCallback(async () => {
        setMemuat(true);
        setGalat('');

        try {
            const res = await api.get('/kategori-berita');
            setKategori(res.data);
        } catch (error) {
            setGalat(pesanGalat(error, 'Kategori berita gagal dimuat.'));
        } finally {
            setMemuat(false);
        }
    }, []);

    useEffect(() => {
        muat();
    }, [muat]);

    return { kategori, memuat, galat, muat };
}
