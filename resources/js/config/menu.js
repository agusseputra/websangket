import { Building2, CalendarDays, ClipboardList, FileUp, Home, Map as IkonPeta, Newspaper, Printer, ReceiptText, Tags, UserCog, Users, WalletCards } from 'lucide-react';

/**
 * Menu sidebar. Item dengan `grup` tampil sebagai accordion; item dan grup disaring sesuai peran pengguna.
 */
export const MENU = [
    { ke: '/admin', label: 'Beranda', ikon: Home, peran: ['admin'] },
    {
        grup: 'Kasukretan',
        item: [
            { ke: '/krama', label: 'Krama', ikon: Users, peran: ['admin', 'petugas'] },
            { ke: '/peta', label: 'Peta', ikon: IkonPeta, peran: ['admin'] },
            { ke: '/tunggakan', label: 'Tunggakan', ikon: WalletCards, peran: ['admin', 'petugas'] },
            { ke: '/tagihan', label: 'Tagihan', ikon: ReceiptText, peran: ['admin', 'petugas'] },
            { ke: '/laporan', label: 'Laporan warga', ikon: ClipboardList, peran: ['admin'] },
            { ke: '/periode', label: 'Periode iuran', ikon: CalendarDays, peran: ['admin'] },
            { ke: '/perumahan', label: 'Perumahan', ikon: Building2, peran: ['admin'] },
            { ke: '/impor-krama', label: 'Impor Excel', ikon: FileUp, peran: ['admin'] },
        ],
    },
    {
        grup: 'Berita',
        item: [
            { ke: '/berita', label: 'Berita', ikon: Newspaper, peran: ['admin'] },
            { ke: '/kategori-berita', label: 'Kategori', ikon: Tags, peran: ['admin'] },
        ],
    },
    {
        grup: 'Pengaturan',
        item: [
            { ke: '/pengguna', label: 'Pengguna', ikon: UserCog, peran: ['admin'] },
            { ke: '/uji-printer', label: 'Setting printer', ikon: Printer, peran: ['admin'] },
        ],
    },
];

export function menuUntuk(peran) {
    return MENU.flatMap((entri) => {
        if (!entri.grup) return entri.peran.includes(peran) ? [entri] : [];

        const item = entri.item.filter((satu) => satu.peran.includes(peran));

        return item.length > 0 ? [{ ...entri, item }] : [];
    });
}
