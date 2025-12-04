## Prasyarat

Sebelum memulai, pastikan Anda memiliki hal-hal berikut yang terpasang di mesin lokal Anda:

1. **PHP 8.2 atau lebih tinggi**
2. **Composer** – Dependency Manager untuk PHP
3. **Node.js** – Untuk Tailwind CSS dan dependensi front-end lainnya
4. **NPM** – Node package manager

## Langkah-Langkah Instalasi dan Run

### Clone Repository

Clone terlebih dahulu menggunakan perintah berikut:

```bash
git clone https://github.com/firstudio-labs/kependudukan-app.git
cd kependudukan-app
```

### Install Dependensi

```bash
composer install
```

### Salin file `.env.example` untuk membuat file `.env` baru:

```bash
cp .env.example .env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sql_pelayanan_de
DB_USERNAME=sql_pelayanan_de
DB_PASSWORD=9a5af142796ec
DB_COLLATION=utf8mb4_unicode_ci
```

### Generasi kunci aplikasi:

```bash
php artisan key:generate
```

### Jalankan Migrasi dan Seeder

```bash
php artisan migrate --seed
```

### Instal dependensi JavaScript menggunakan npm:

```bash
npm install
```

### Build CSS

```bash
npm run build
```

### Jalankan Aplikasi

```bash
php artisan serve
```

### Run CSS/Frontend

```bash
npm run dev
```

## Menjalankan Aplikasi

### Langkah 5: Run CSS/Frontend

```bash
npm run dev
```

### Langkah 7: Run Server

```bash
php artisan serve
```

Kunjungi [http://localhost:8000](http://localhost:8000) di browser untuk mengakses aplikasi.
