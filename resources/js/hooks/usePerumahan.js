import { useCallback, useEffect, useState } from 'react';
import api from '../lib/api';
import { pesanGalat } from '../lib/galat';

export default function usePerumahan() {
    const [perumahan, setPerumahan] = useState([]);
    const [memuat, setMemuat] = useState(true);
    const [galat, setGalat] = useState('');

    const muat = useCallback(async () => {
        setGalat('');

        try {
            const res = await api.get('/perumahan');
            setPerumahan(res.data);
        } catch (error) {
            setGalat(pesanGalat(error, 'Daftar perumahan gagal dimuat.'));
        } finally {
            setMemuat(false);
        }
    }, []);

    useEffect(() => {
        muat();
    }, [muat]);

    return { perumahan, memuat, galat, muat };
}
