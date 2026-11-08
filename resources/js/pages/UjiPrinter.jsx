import { useMemo, useState } from 'react';
import StrukPreview from '../components/printer/StrukPreview';
import Alert from '../components/ui/Alert';
import Button from '../components/ui/Button';
import Card from '../components/ui/Card';
import PageHeader from '../components/ui/PageHeader';
import { cetakViaBluetooth, pesanGagalCetak, webBluetoothTersedia } from '../lib/bluetooth';
import { buatStrukUji, CONTOH_STRUK, keBase64 } from '../lib/escpos';

export default function UjiPrinter() {
    const [catatan, setCatatan] = useState([]);
    const [mencetak, setMencetak] = useState(false);
    const [hasil, setHasil] = useState(null);

    const bytes = useMemo(() => buatStrukUji(), []);
    const tersedia = webBluetoothTersedia();
    const tautanRawBt = `intent:base64,${keBase64(bytes)}#Intent;scheme=rawbt;package=ru.a402d.rawbtprinter;end;`;

    function catat(pesan) {
        setCatatan((sebelumnya) => [...sebelumnya, pesan]);
    }

    async function cetak() {
        setCatatan([]);
        setHasil(null);
        setMencetak(true);

        try {
            await cetakViaBluetooth(bytes, catat);
            setHasil({ berhasil: true, pesan: 'Data terkirim. Periksa apakah struk keluar dari printer.' });
        } catch (error) {
            setHasil({ berhasil: false, pesan: pesanGagalCetak(error) });
        } finally {
            setMencetak(false);
        }
    }

    return (
        <>
            <PageHeader judul="Uji printer" deskripsi="Pastikan printer thermal 58 mm bisa mencetak dari browser HP." />
            <div className="grid items-start gap-6 lg:grid-cols-2">
                <div className="grid min-w-0 gap-6">
                    <Card judul="Cetak langsung lewat Bluetooth">
                        {tersedia ? (
                            <p className="text-sm text-redup">Nyalakan printer 58 mm, lalu pilih printer pada dialog browser.</p>
                        ) : (
                            <Alert>Browser ini tidak mendukung Web Bluetooth. Di iPhone, buka halaman ini lewat aplikasi Bluefy. Di Android, gunakan Chrome.</Alert>
                        )}
                        <Button onClick={cetak} disabled={!tersedia || mencetak}>
                            {mencetak ? 'Mencetak...' : 'Cetak struk uji'}
                        </Button>
                        {hasil && <Alert nada={hasil.berhasil ? 'berhasil' : 'gagal'}>{hasil.pesan}</Alert>}
                        {catatan.length > 0 && <pre className="overflow-x-auto rounded bg-kertas p-3 text-xs">{catatan.join('\n')}</pre>}
                    </Card>
                    <Card judul="Cadangan untuk Android">
                        <p className="text-sm text-redup">Untuk printer Bluetooth klasik. Aplikasi RawBT harus sudah terpasang dan terhubung ke printer.</p>
                        <Button as="a" varian="garis" href={tautanRawBt}>Kirim ke RawBT</Button>
                    </Card>
                </div>
                <section className="grid min-w-0 gap-3">
                    <h2 className="font-semibold">Pratinjau struk 58 mm</h2>
                    <StrukPreview struk={CONTOH_STRUK} />
                </section>
            </div>
        </>
    );
}
