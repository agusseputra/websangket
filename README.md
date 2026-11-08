# Dudukan Sangket

Aplikasi PWA untuk mencatat krama serta pembayaran Dudukan dan Penanjung Batu Desa Adat Sangket.
Backend memakai Laravel dan MySQL, frontend memakai React.

## Menjalankan di lokal

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Lengkapi `.env`: kredensial database, `ADMIN_EMAIL`, `ADMIN_PASSWORD`, dan `NIK_HASH_KEY`.
Buat `NIK_HASH_KEY` dengan:

```bash
php -r "echo 'base64:'.base64_encode(random_bytes(32)).PHP_EOL;"
```

Lalu siapkan database dan jalankan aplikasi:

```bash
php artisan migrate --seed
npm run build
php artisan serve
```

Pengujian dijalankan dengan `php artisan test`.

## Kunci yang wajib dicadangkan

`APP_KEY` dan `NIK_HASH_KEY` harus dicadangkan di tempat aman yang terpisah dari backup database.

- Jika `APP_KEY` hilang atau berubah, semua NIK di database tidak bisa dibaca lagi.
- Jika `NIK_HASH_KEY` hilang atau berubah, cek duplikat dan pencarian NIK tidak lagi cocok dengan data lama.

Jangan menjalankan `php artisan key:generate` di server yang sudah berisi data.

## Sebelum rilis

- Ganti kata sandi admin awal dengan kata sandi yang kuat.
- Set `APP_ENV=production`, `APP_DEBUG=false`, dan `SESSION_SECURE_COOKIE=true`.
- Gunakan HTTPS, karena GPS, Bluetooth, dan pemasangan PWA membutuhkannya di HP.
- Isi `APP_URL` dan `SANCTUM_STATEFUL_DOMAINS` dengan domain produksi.
# websangket
