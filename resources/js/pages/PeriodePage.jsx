import { useCallback, useEffect, useState } from 'react';
import Alert from '../components/ui/Alert';
import Button from '../components/ui/Button';
import Card from '../components/ui/Card';
import ConfirmDialog from '../components/ui/ConfirmDialog';
import PageHeader from '../components/ui/PageHeader';
import TextField from '../components/ui/TextField';
import api from '../lib/api';
import { rupiah } from '../lib/format';
import { galatKolom, pesanGalat } from '../lib/galat';

function RekapPeriode({ rekap }) {
    if (rekap.length === 0) {
        return <p className="text-sm text-redup">Belum ada tagihan.</p>;
    }

    return (
        <div className="overflow-x-auto">
            <table className="w-full text-left text-sm">
                <thead>
                    <tr className="border-b border-garis">
                        <th scope="col" className="py-2 pr-4 font-semibold">Jenis</th>
                        <th scope="col" className="py-2 pr-4 text-right font-semibold">Baris</th>
                        <th scope="col" className="py-2 pr-4 text-right font-semibold">Valid</th>
                        <th scope="col" className="py-2 pr-4 text-right font-semibold">Belum dihitung</th>
                        <th scope="col" className="py-2 text-right font-semibold">Tunggakan</th>
                    </tr>
                </thead>
                <tbody className="tabular-nums">
                    {rekap.map((baris) => (
                        <tr key={baris.jenis} className="border-b border-garis last:border-0">
                            <td className="py-2 pr-4 whitespace-nowrap">{baris.jenis_label}</td>
                            <td className="py-2 pr-4 text-right">{baris.baris}</td>
                            <td className="py-2 pr-4 text-right whitespace-nowrap">{rupiah(baris.valid)}</td>
                            <td className="py-2 pr-4 text-right whitespace-nowrap">{rupiah(baris.dibayar + baris.pending)}</td>
                            <td className="py-2 text-right whitespace-nowrap">{rupiah(baris.tunggakan)}</td>
                        </tr>
                    ))}
                </tbody>
            </table>
        </div>
    );
}

function KartuPeriode({ periode, onUbah, onGenerate }) {
    const [dudukan, setDudukan] = useState(String(periode.jumlah_dudukan));
    const [penanjung, setPenanjung] = useState(String(periode.jumlah_penanjung_batu));
    const [galat, setGalat] = useState({});
    const [menyimpan, setMenyimpan] = useState(false);

    async function simpan(event) {
        event.preventDefault();
        setGalat({});
        setMenyimpan(true);

        try {
            await onUbah(periode, { tahun: periode.tahun, jumlah_dudukan: Number(dudukan), jumlah_penanjung_batu: Number(penanjung) });
        } catch (error) {
            setGalat(galatKolom(error));
        } finally {
            setMenyimpan(false);
        }
    }

    return (
        <Card judul={`Periode ${periode.tahun}`}>
            <form onSubmit={simpan} className="grid items-start gap-3 md:grid-cols-[1fr_1fr_auto]">
                <TextField id={`dudukan-${periode.id}`} label="Tarif Dudukan" type="number" min="0" required value={dudukan} onChange={(e) => setDudukan(e.target.value)} galat={galat.jumlah_dudukan} />
                <TextField id={`penanjung-${periode.id}`} label="Tarif Penanjung Batu" type="number" min="0" required value={penanjung} onChange={(e) => setPenanjung(e.target.value)} galat={galat.jumlah_penanjung_batu} />
                <Button varian="garis" type="submit" disabled={menyimpan} className="md:mt-6">Simpan tarif</Button>
            </form>
            <p className="text-sm text-redup">
                {periode.generated_at ? `Generate terakhir ${periode.generated_at}. ` : 'Belum di-Generate. '}
                Perubahan tarif hanya berlaku untuk baris yang dibuat setelahnya.
            </p>
            <RekapPeriode rekap={periode.rekap} />
            <div className="flex justify-end">
                <Button onClick={() => onGenerate(periode)}>{periode.generated_at ? 'Generate ulang' : 'Generate tagihan'}</Button>
            </div>
        </Card>
    );
}

export default function PeriodePage() {
    const [data, setData] = useState(null);
    const [tahun, setTahun] = useState('');
    const [galatForm, setGalatForm] = useState('');
    const [galat, setGalat] = useState('');
    const [info, setInfo] = useState('');
    const [generate, setGenerate] = useState(null);
    const [memproses, setMemproses] = useState(false);

    const muat = useCallback(async () => {
        try {
            const res = await api.get('/periode');
            setData(res.data);
        } catch (error) {
            setGalat(pesanGalat(error, 'Periode gagal dimuat.'));
        }
    }, []);

    useEffect(() => {
        muat();
    }, [muat]);

    async function tambah(event) {
        event.preventDefault();
        setGalatForm('');
        setMemproses(true);

        try {
            await api.post('/periode', { tahun: Number(tahun), ...data.tarif_awal });
            setTahun('');
            await muat();
        } catch (error) {
            setGalatForm(galatKolom(error).tahun ?? pesanGalat(error));
        } finally {
            setMemproses(false);
        }
    }

    async function ubah(periode, nilai) {
        setInfo('');
        await api.put(`/periode/${periode.id}`, nilai);
        setInfo(`Tarif periode ${periode.tahun} tersimpan.`);
        await muat();
    }

    async function bukaGenerate(periode) {
        setGalat('');
        setInfo('');

        try {
            const res = await api.get(`/periode/${periode.id}/pratinjau`);
            setGenerate({ periode, pratinjau: res.data });
        } catch (error) {
            setGalat(pesanGalat(error, 'Pratinjau gagal dimuat.'));
        }
    }

    async function jalankanGenerate() {
        setMemproses(true);

        try {
            const res = await api.post(`/periode/${generate.periode.id}/generate`);
            setInfo(`Generate ${generate.periode.tahun} selesai: ${res.data.dudukan} baris Dudukan dan ${res.data.penanjung_batu} baris Penanjung Batu.`);
            setGenerate(null);
            await muat();
        } catch (error) {
            setGalat(pesanGalat(error, 'Generate gagal. Tidak ada tagihan yang dibuat.'));
            setGenerate(null);
        } finally {
            setMemproses(false);
        }
    }

    const pratinjau = generate?.pratinjau;

    return (
        <>
            <PageHeader judul="Periode iuran" deskripsi="Atur tarif, lalu buat tagihan setiap awal tahun." />
            {galat && <Alert nada="gagal" role="alert">{galat}</Alert>}
            {info && <Alert nada="berhasil">{info}</Alert>}

            {data && (
                <Card judul="Tambah periode">
                    <form onSubmit={tambah} className="grid items-start gap-3 sm:grid-cols-[1fr_auto]">
                        <TextField
                            id="tahun"
                            label="Tahun"
                            type="number"
                            min="2020"
                            max="2100"
                            required
                            value={tahun}
                            onChange={(e) => setTahun(e.target.value)}
                            galat={galatForm}
                            bantuan={`Tarif awal: Dudukan ${rupiah(data.tarif_awal.jumlah_dudukan)}, Penanjung Batu ${rupiah(data.tarif_awal.jumlah_penanjung_batu)}. Bisa diubah sebelum Generate.`}
                        />
                        <Button type="submit" disabled={memproses} className="sm:mt-6">Tambah</Button>
                    </form>
                </Card>
            )}

            {data?.data.map((periode) => (
                <KartuPeriode key={`${periode.id}-${periode.jumlah_dudukan}-${periode.jumlah_penanjung_batu}`} periode={periode} onUbah={ubah} onGenerate={bukaGenerate} />
            ))}

            {generate && (
                <ConfirmDialog
                    judul={`Generate tagihan ${generate.periode.tahun}?`}
                    pesan={pratinjau.krama === 0
                        ? 'Semua krama aktif sudah punya tagihan untuk periode ini.'
                        : `${pratinjau.dudukan} baris Dudukan (${rupiah(pratinjau.total_dudukan)}) dan ${pratinjau.penanjung_batu} baris Penanjung Batu (${rupiah(pratinjau.total_penanjung_batu)}) akan dibuat untuk ${pratinjau.krama} krama.`}
                    labelYa="Generate"
                    memproses={memproses}
                    onYa={jalankanGenerate}
                    onBatal={() => setGenerate(null)}
                />
            )}
        </>
    );
}
