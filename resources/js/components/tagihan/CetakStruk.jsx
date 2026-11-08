import { Printer } from 'lucide-react';
import { useEffect, useState } from 'react';
import { createPortal, flushSync } from 'react-dom';
import { cetakViaBluetooth, pesanGagalCetak, webBluetoothTersedia } from '../../lib/bluetooth';
import { buatStrukUji } from '../../lib/escpos';
import { buatDataStruk } from '../../lib/struk';
import StrukPreview from '../printer/StrukPreview';
import Alert from '../ui/Alert';
import Button from '../ui/Button';

export default function CetakStruk({ hasil, krama, petugas, cetakUlang = false, ringkas = false }) {
    const [sudahCetak, setSudahCetak] = useState(cetakUlang);
    const [mencetak, setMencetak] = useState(false);
    const [areaAktif, setAreaAktif] = useState(false);
    const [pesan, setPesan] = useState(null);

    const struk = buatDataStruk({ hasil, krama, petugas, cetakUlang: sudahCetak });
    const bluetooth = webBluetoothTersedia();
    const ukuran = ringkas ? 'kecil' : 'biasa';

    useEffect(() => {
        const selesai = () => {
            setAreaAktif(false);
            setSudahCetak(true);
        };

        if (!areaAktif) return undefined;

        window.addEventListener('afterprint', selesai);

        return () => window.removeEventListener('afterprint', selesai);
    }, [areaAktif]);

    async function cetakBluetooth() {
        setPesan(null);
        setMencetak(true);

        try {
            await cetakViaBluetooth(buatStrukUji(struk));
            setSudahCetak(true);
            setPesan({ nada: 'berhasil', isi: 'Struk terkirim ke printer.' });
        } catch (error) {
            setPesan({ nada: 'gagal', isi: pesanGagalCetak(error) });
        } finally {
            setMencetak(false);
        }
    }

    function cetakBrowser() {
        setPesan(null);

        // Area cetak hanya ada saat dipakai, agar dialog cetak hanya berisi struk ini.
        flushSync(() => setAreaAktif(true));
        window.print();
    }

    return (
        <div className="grid gap-3">
            <div className="flex flex-wrap gap-2">
                {bluetooth && (
                    <Button varian={ringkas ? 'garis' : 'utama'} ukuran={ukuran} onClick={cetakBluetooth} disabled={mencetak} aria-label={`Cetak struk ${hasil.no_struk}`}>
                        <Printer className="size-4" aria-hidden="true" />
                        {mencetak ? 'Mencetak...' : ringkas ? 'Cetak' : 'Cetak struk'}
                    </Button>
                )}
                <Button varian={bluetooth || ringkas ? 'garis' : 'utama'} ukuran={ukuran} onClick={cetakBrowser} aria-label={`Cetak struk ${hasil.no_struk} lewat browser`}>
                    {!bluetooth && <Printer className="size-4" aria-hidden="true" />}
                    {bluetooth ? (ringkas ? 'Browser' : 'Cetak lewat browser') : (ringkas ? 'Cetak' : 'Cetak struk')}
                </Button>
            </div>
            {pesan && <Alert nada={pesan.nada}>{pesan.isi}</Alert>}
            {areaAktif && createPortal(
                <div className="area-cetak hidden print:block">
                    <StrukPreview struk={struk} />
                </div>,
                document.body,
            )}
        </div>
    );
}
