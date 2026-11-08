import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import CariKrama from '../components/krama/CariKrama';
import Alert from '../components/ui/Alert';
import PageHeader from '../components/ui/PageHeader';
import SelectField from '../components/ui/SelectField';
import StatCard from '../components/ui/StatCard';
import StatGrid from '../components/ui/StatGrid';
import usePeriodeTahun from '../hooks/usePeriodeTahun';
import api from '../lib/api';
import { rupiah } from '../lib/format';

export default function BerandaAdmin() {
    const navigate = useNavigate();
    const { pilihanTahun } = usePeriodeTahun();
    const [tahun, setTahun] = useState('');
    const [data, setData] = useState({});
    const [galat, setGalat] = useState(false);

    useEffect(() => {
        let batal = false;

        api.get('/admin/ringkasan', { params: { tahun: tahun || undefined } })
            .then((res) => {
                if (!batal) {
                    setGalat(false);
                    setData(res.data);
                }
            })
            .catch(() => {
                if (!batal) setGalat(true);
            });

        return () => {
            batal = true;
        };
    }, [tahun]);

    const periode = data.periode;

    return (
        <>
            <PageHeader judul="Beranda admin" deskripsi="Ringkasan data krama dan pembayaran." />

            <div className="max-w-xl">
                <CariKrama id="cek-krama" onPilih={(krama) => navigate(`/krama/${krama.id}`)} />
            </div>

            {galat && <Alert nada="gagal" role="alert">Ringkasan gagal dimuat. Muat ulang halaman ini.</Alert>}

            <StatGrid>
                <StatCard label="Krama" nilai={data.krama} ke="/krama" />
                <StatCard label="Data pending" nilai={data.krama_pending} ke="/krama?status_data=pending" />
                <StatCard label="Belum ada lokasi" nilai={data.krama_tanpa_lokasi} ke="/krama?lokasi=belum" />
                <StatCard label="Perumahan" nilai={data.perumahan} ke="/perumahan" />
                <StatCard label="Petugas aktif" nilai={data.petugas} ke="/pengguna" />
                <StatCard label="Laporan pending" nilai={data.laporan_pending} ke="/laporan" />
            </StatGrid>

            <section className="grid min-w-0 gap-3">
                <div className="flex flex-wrap items-end justify-between gap-3">
                    <h2 className="text-lg font-semibold">Ringkasan {periode ? `periode ${periode.tahun}` : 'periode'}</h2>
                    <div className="w-full sm:w-48">
                        <SelectField
                            id="ringkasan-tahun"
                            label="Periode tahun"
                            value={tahun || periode?.tahun || ''}
                            onChange={(e) => setTahun(e.target.value)}
                            pilihan={pilihanTahun}
                        />
                    </div>
                </div>

                {data.krama !== undefined && !periode && <p className="rounded border border-garis bg-panel p-4 text-redup">Belum ada periode iuran untuk diringkas.</p>}

                {periode && (
                    <div className="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        {periode.jenis.map((satu) => (
                            <StatCard
                                key={satu.jenis}
                                label={`${satu.label} valid`}
                                nilai={rupiah(satu.valid)}
                                keterangan={`Dari tagihan ${rupiah(satu.tagihan)}${satu.belum_dihitung > 0 ? ` · ${rupiah(satu.belum_dihitung)} belum dihitung` : ''}`}
                                ke={`/tagihan?status=valid&jenis=${satu.jenis}&tahun=${periode.tahun}`}
                                nilaiPanjang
                            />
                        ))}
                        <StatCard
                            label="Krama bayar valid"
                            nilai={`${periode.krama_bayar_valid} / ${periode.krama_total}`}
                            keterangan="Krama dengan pembayaran valid, dari semua krama"
                            ke={`/tagihan?status=valid&tahun=${periode.tahun}`}
                            nilaiPanjang
                        />
                        <StatCard
                            label="Total tunggakan"
                            nilai={rupiah(periode.tunggakan_total)}
                            keterangan={periode.jenis.map((satu) => `${satu.label} ${rupiah(satu.tunggakan)}`).join(' · ')}
                            ke={`/tunggakan?tahun=${periode.tahun}`}
                            nilaiPanjang
                        />
                    </div>
                )}
            </section>
        </>
    );
}
