## Prasyarat
Sebelum memulai, pastikan Anda memiliki hal-hal berikut yang terpasang di mesin lokal Anda:

1. **PHP 8.2 atau lebih tinggi**  
2. **Composer** – Dependency Manager untuk PHP  
3. **Node.js** – Untuk Tailwind CSS dan dependensi front-end lainnya  
4. **NPM** – Node package manager
   
## Langkah-Langkah Instalasi dan Run

### Langkah 1: Clone Repository

Clone terlebih dahulu menggunakan perintah berikut:

```bash
git clone https://github.com/firstudio-labs/kependudukan-app.git
cd kependudukan-app
```

### Langkah 2: Install Dependensi
```bash
composer install
```

### Langkah 3: Buat Database di MySQL
Buat database baru bernama kependudukan di MySQL:
```bash
CREATE DATABASE kependudukan;
```

### Langkah 4: Install Tailwind CSS
```bash
npm install
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
```

### Langkah 5: Build CSS
```bash
npm run dev
```

### Langkah 6: Jalankan Migrasi dan Seeder
```bash
php artisan migrate --seed
```

### Langkah 7: Jalankan Aplikasi
```bash
php artisan serve
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
