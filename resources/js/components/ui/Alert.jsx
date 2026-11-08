const NADA = {
    peringatan: 'bg-latar-peringatan text-peringatan',
    gagal: 'bg-latar-gagal text-gagal',
    berhasil: 'bg-latar-berhasil text-berhasil',
};

export default function Alert({ nada = 'peringatan', role = 'status', children }) {
    return (
        <p role={role} className={`rounded px-3 py-2 text-sm ${NADA[nada]}`}>
            {children}
        </p>
    );
}
