{{--
    Lightbox foto untuk halaman publik. Semua <img> di dalam elemen [data-lightbox] (kecuali di dalam <aside>
    atau bertanda data-tanpa-lightbox) dapat diperbesar. Tanpa JavaScript, foto tetap tampil biasa dan tautan
    foto tetap membuka gambar di tab baru.
--}}
<dialog id="lightbox-foto" aria-label="Penampil foto"
    class="m-0 h-dvh max-h-none w-screen max-w-none bg-transparent p-0 text-white backdrop:bg-black/90 open:grid">
    <div class="grid h-full grid-rows-[auto_1fr_auto] gap-2 p-3 sm:p-6">
        <div class="flex items-center justify-between gap-3">
            <p class="rounded-full bg-white/10 px-3 py-1 text-sm tabular-nums" data-lightbox-hitung aria-live="polite"></p>
            <button type="button" data-lightbox-tutup class="grid size-11 place-items-center rounded-full bg-white/10 hover:bg-white/20" aria-label="Tutup foto">
                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>

        <div class="relative grid min-h-0 place-items-center" data-lightbox-panggung>
            <img data-lightbox-gambar alt="" class="absolute inset-0 size-full object-contain drop-shadow-2xl select-none">
            <button type="button" data-lightbox-geser="-1" class="absolute top-1/2 left-0 grid size-12 -translate-y-1/2 place-items-center rounded-full bg-black/50 hover:bg-black/70 sm:left-2" aria-label="Foto sebelumnya">
                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
            </button>
            <button type="button" data-lightbox-geser="1" class="absolute top-1/2 right-0 grid size-12 -translate-y-1/2 place-items-center rounded-full bg-black/50 hover:bg-black/70 sm:right-2" aria-label="Foto berikutnya">
                <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        </div>

        <p data-lightbox-keterangan class="mx-auto max-w-3xl text-center text-sm text-white/85"></p>
    </div>
</dialog>

<script>
    (() => {
        const dialog = document.getElementById('lightbox-foto');
        const wadah = document.querySelector('[data-lightbox]');
        if (!dialog || !wadah || typeof dialog.showModal !== 'function') return;

        const gambar = dialog.querySelector('[data-lightbox-gambar]');
        const keterangan = dialog.querySelector('[data-lightbox-keterangan]');
        const hitung = dialog.querySelector('[data-lightbox-hitung]');
        const tombolGeser = dialog.querySelectorAll('[data-lightbox-geser]');

        // Satu entri per sumber foto, agar foto yang muncul dua kali tidak terulang di galeri.
        const foto = [];
        const indeksSumber = new Map();
        wadah.querySelectorAll('img').forEach((img) => {
            if (img.closest('aside') || img.hasAttribute('data-tanpa-lightbox')) return;

            const sumber = img.currentSrc || img.src;
            if (!indeksSumber.has(sumber)) {
                const figcaption = img.closest('figure')?.querySelector('figcaption')?.textContent.trim();
                indeksSumber.set(sumber, foto.push({ sumber, alt: img.alt, teks: figcaption || img.alt }) - 1);
            }

            const indeks = indeksSumber.get(sumber);
            const tautan = img.closest('a');
            const pemicu = tautan ?? img;
            if (!tautan) {
                img.tabIndex = 0;
                img.setAttribute('role', 'button');
                img.setAttribute('aria-label', `Perbesar foto: ${img.alt}`);
            }
            pemicu.classList.add('cursor-zoom-in');
            pemicu.addEventListener('click', (event) => {
                event.preventDefault();
                buka(indeks);
            });
            pemicu.addEventListener('keydown', (event) => {
                if (!tautan && (event.key === 'Enter' || event.key === ' ')) {
                    event.preventDefault();
                    buka(indeks);
                }
            });
        });

        let aktif = 0;
        const tampilkan = (indeks) => {
            aktif = (indeks + foto.length) % foto.length;
            const { sumber, alt, teks } = foto[aktif];
            gambar.src = sumber;
            gambar.alt = alt;
            keterangan.textContent = teks;
            hitung.textContent = `${aktif + 1} / ${foto.length}`;
            [foto[aktif + 1], foto[aktif - 1]].forEach((tetangga) => tetangga && (new Image().src = tetangga.sumber));
        };
        const buka = (indeks) => {
            tampilkan(indeks);
            tombolGeser.forEach((tombol) => (tombol.hidden = foto.length < 2));
            document.documentElement.style.overflow = 'hidden';
            dialog.showModal();
        };

        dialog.addEventListener('close', () => (document.documentElement.style.overflow = ''));
        dialog.querySelector('[data-lightbox-tutup]').addEventListener('click', () => dialog.close());
        tombolGeser.forEach((tombol) => tombol.addEventListener('click', () => tampilkan(aktif + Number(tombol.dataset.lightboxGeser))));
        // Klik di luar foto dan tombol menutup penampil.
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog || event.target.matches('[data-lightbox-panggung], [data-lightbox-keterangan]')) dialog.close();
        });
        dialog.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowRight') tampilkan(aktif + 1);
            if (event.key === 'ArrowLeft') tampilkan(aktif - 1);
        });

        let awalSentuh = null;
        dialog.addEventListener('touchstart', (event) => (awalSentuh = event.changedTouches[0].clientX), { passive: true });
        dialog.addEventListener('touchend', (event) => {
            const jarak = event.changedTouches[0].clientX - (awalSentuh ?? 0);
            if (awalSentuh !== null && Math.abs(jarak) > 50) tampilkan(aktif + (jarak < 0 ? 1 : -1));
            awalSentuh = null;
        });
    })();
</script>
