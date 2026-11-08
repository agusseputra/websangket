import { useEffect, useState } from 'react';

export default function useDebouncedValue(nilai, jeda = 350) {
    const [tertunda, setTertunda] = useState(nilai);

    useEffect(() => {
        const timer = setTimeout(() => setTertunda(nilai), jeda);

        return () => clearTimeout(timer);
    }, [nilai, jeda]);

    return tertunda;
}
