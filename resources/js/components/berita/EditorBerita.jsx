import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import { useEffect, useRef } from 'react';
import api from '../../lib/api';
import { pesanGalat } from '../../lib/galat';
import { periksaGambar } from '../../lib/berita';

const TOOLBAR = [
    [{ header: [2, 3, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    [{ list: 'ordered' }, { list: 'bullet' }, 'blockquote'],
    [{ align: [] }],
    ['link', 'image'],
    ['clean'],
];

/**
 * Teks editor isi berita berbasis Quill. Gambar diunggah ke server lebih dulu, lalu URL-nya disisipkan.
 */
export default function EditorBerita({ id, labelId, nilaiAwal, onUbah, onGalatGambar, galat }) {
    const wadahRef = useRef(null);
    const quillRef = useRef(null);
    const onUbahRef = useRef(onUbah);
    const onGalatRef = useRef(onGalatGambar);

    onUbahRef.current = onUbah;
    onGalatRef.current = onGalatGambar;

    useEffect(() => {
        const wadah = wadahRef.current;
        const editor = document.createElement('div');

        // Isi lama dimasukkan sebelum Quill dibuat agar langsung diubah menjadi format editor.
        editor.innerHTML = nilaiAwal ?? '';
        wadah.append(editor);

        const quill = new Quill(editor, {
            theme: 'snow',
            placeholder: 'Tulis isi berita di sini...',
            modules: {
                toolbar: {
                    container: TOOLBAR,
                    handlers: { image: () => pilihGambar(quill) },
                },
            },
        });

        quill.root.id = id;
        quill.root.setAttribute('role', 'textbox');
        quill.root.setAttribute('aria-multiline', 'true');
        quill.root.setAttribute('aria-labelledby', labelId);
        quill.on('text-change', () => onUbahRef.current(quill.getText().trim() === '' && !quill.root.querySelector('img') ? '' : quill.root.innerHTML));
        quillRef.current = quill;

        return () => {
            quillRef.current = null;
            wadah.innerHTML = '';
        };
        // Editor hanya dibuat sekali; perubahan isi berikutnya dikelola Quill sendiri.
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    useEffect(() => {
        const root = quillRef.current?.root;

        if (!root) return;

        if (galat) {
            root.setAttribute('aria-invalid', 'true');
            root.setAttribute('aria-describedby', `${id}-galat`);
        } else {
            root.removeAttribute('aria-invalid');
            root.removeAttribute('aria-describedby');
        }
    }, [galat, id]);

    function pilihGambar(quill) {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/jpeg,image/png,image/webp,image/gif';

        input.addEventListener('change', async () => {
            const file = input.files?.[0];

            if (!file) return;

            const masalah = await periksaGambar(file, { tipe: ['image/jpeg', 'image/png', 'image/webp', 'image/gif'] });

            if (masalah) {
                onGalatRef.current?.(masalah);

                return;
            }

            const data = new FormData();
            data.append('gambar', file);
            onGalatRef.current?.('');

            try {
                const res = await api.post('/berita/gambar', data);
                const posisi = quill.getSelection(true)?.index ?? quill.getLength();

                quill.insertEmbed(posisi, 'image', res.data.url, 'user');
                quill.setSelection(posisi + 1, 0);
            } catch (error) {
                onGalatRef.current?.(pesanGalat(error, 'Gambar gagal diunggah.'));
            }
        });

        input.click();
    }

    return <div ref={wadahRef} className={`editor-berita grid ${galat ? 'editor-galat' : ''}`} />;
}
