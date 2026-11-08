import { Building2, CalendarDays, ClipboardList, FileUp, Home, Map as IkonPeta, Printer, ReceiptText, UserCog, Users, WalletCards } from 'lucide-react';

export const MENU = [
    { ke: '/admin', label: 'Beranda', ikon: Home, peran: ['admin'] },
    { ke: '/krama', label: 'Data krama', ikon: Users, peran: ['admin', 'petugas'] },
    { ke: '/peta', label: 'Peta krama', ikon: IkonPeta, peran: ['admin'] },
    { ke: '/tunggakan', label: 'Tunggakan', ikon: WalletCards, peran: ['admin', 'petugas'] },
    { ke: '/tagihan', label: 'Tagihan dan validasi', ikon: ReceiptText, peran: ['admin'] },
    { ke: '/tagihan', label: 'Tagihan', ikon: ReceiptText, peran: ['petugas'] },
    { ke: '/laporan', label: 'Laporan warga', ikon: ClipboardList, peran: ['admin'] },
    { ke: '/periode', label: 'Periode iuran', ikon: CalendarDays, peran: ['admin'] },
    { ke: '/perumahan', label: 'Perumahan', ikon: Building2, peran: ['admin'] },
    { ke: '/impor-krama', label: 'Impor Excel', ikon: FileUp, peran: ['admin'] },
    { ke: '/pengguna', label: 'Pengguna', ikon: UserCog, peran: ['admin'] },
    { ke: '/uji-printer', label: 'Uji printer', ikon: Printer, peran: ['admin'] },
];

export function menuUntuk(peran) {
    return MENU.filter((item) => item.peran.includes(peran));
}
