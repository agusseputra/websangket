import { useEffect, useState } from 'react';
import api from '../lib/api';

export default function usePeriodeTahun() {
    const [pilihanTahun, setPilihanTahun] = useState([]);

    useEffect(() => {
        api.get('/periode-tahun')
            .then((res) => setPilihanTahun(res.data.map((tahun) => ({ nilai: tahun, label: String(tahun) }))))
            .catch(() => setPilihanTahun([]));
    }, []);

    return { pilihanTahun };
}
