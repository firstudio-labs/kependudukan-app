# WebP Image Conversion Feature

Fitur ini mengkonversi gambar produk secara otomatis ke format WebP untuk optimasi performa dan ukuran file.

## Fitur

-   ✅ Konversi otomatis ke WebP saat upload gambar produk
-   ✅ Resize gambar dengan aspect ratio yang terjaga (max 800x600)
-   ✅ Kualitas WebP yang dapat dikonfigurasi (default: 85%)
-   ✅ Penghapusan otomatis gambar lama saat update
-   ✅ Command Artisan untuk konversi gambar yang sudah ada
-   ✅ Support untuk berbagai format input (JPEG, PNG, GIF, WebP)
-   ✅ Upload file hingga 10MB (akan di-resize otomatis)
-   ✅ Optimasi ukuran file otomatis tanpa kehilangan kualitas visual

## Instalasi

1. Install package Intervention Image:

```bash
composer install
```

2. Pastikan extension GD atau Imagick terinstall di PHP:

```bash
# Untuk Ubuntu/Debian
sudo apt-get install php-gd

# Atau untuk Imagick
sudo apt-get install php-imagick
```

3. Jalankan command untuk konversi gambar yang sudah ada:

```bash
php artisan images:convert-to-webp
```

## Penggunaan

### Upload Gambar Baru

Gambar akan otomatis dikonversi ke WebP saat upload melalui form produk:

```php
// Di controller sudah otomatis menggunakan ImageConverterService
if ($request->hasFile('foto')) {
    $path = ImageConverterService::convertToWebP($request->file('foto'), 'warungku', 85, 800, 600);
    if ($path) {
        $item->foto = $path;
    }
}
```

### Konversi Gambar yang Sudah Ada

Gunakan command Artisan untuk mengkonversi semua gambar produk yang sudah ada:

```bash
# Konversi semua gambar
php artisan images:convert-to-webp

# Paksa konversi ulang (termasuk yang sudah WebP)
php artisan images:convert-to-webp --force
```

### Konfigurasi

File konfigurasi: `config/image.php`

```php
return [
    'driver' => 'gd',           // Driver: 'gd' atau 'imagick'
    'quality' => 85,            // Kualitas WebP (1-100)
    'max_width' => 800,         // Lebar maksimal
    'max_height' => 600,        // Tinggi maksimal
];
```

## API

### ImageConverterService

#### convertToWebP()

Konversi file upload ke WebP:

```php
$path = ImageConverterService::convertToWebP(
    $uploadedFile,    // UploadedFile
    'warungku',       // Directory
    85,               // Quality
    800,              // Max width
    600               // Max height
);
```

#### convertExistingToWebP()

Konversi file yang sudah ada ke WebP:

```php
$path = ImageConverterService::convertExistingToWebP(
    'warungku/old-image.jpg',  // Path file lama
    'warungku',                // Directory
    85,                        // Quality
    800,                       // Max width
    600                        // Max height
);
```

#### deleteImage()

Hapus file gambar:

```php
$deleted = ImageConverterService::deleteImage('warungku/image.webp');
```

#### getImageUrl()

Dapatkan URL gambar:

```php
$url = ImageConverterService::getImageUrl('warungku/image.webp');
// Returns: http://domain.com/storage/warungku/image.webp
```

## Keuntungan WebP

-   **Ukuran file lebih kecil**: 25-35% lebih kecil dari JPEG
-   **Kualitas lebih baik**: Kualitas visual yang sama dengan ukuran lebih kecil
-   **Support modern browser**: Chrome, Firefox, Safari, Edge
-   **Transparansi**: Support alpha channel seperti PNG
-   **Animasi**: Support animasi seperti GIF

## Troubleshooting

### Error: "Class 'Intervention\Image\Facades\Image' not found"

Pastikan package Intervention Image sudah terinstall:

```bash
composer install
```

### Error: "GD extension not loaded"

Install extension GD:

```bash
# Ubuntu/Debian
sudo apt-get install php-gd
sudo systemctl restart apache2  # atau nginx

# CentOS/RHEL
sudo yum install php-gd
sudo systemctl restart httpd
```

### Error: "Imagick extension not loaded"

Install extension Imagick:

```bash
# Ubuntu/Debian
sudo apt-get install php-imagick
sudo systemctl restart apache2

# CentOS/RHEL
sudo yum install php-imagick
sudo systemctl restart httpd
```

### Gambar tidak terkonversi

1. Cek log Laravel: `storage/logs/laravel.log`
2. Pastikan direktori `storage/app/public` writable
3. Jalankan: `php artisan storage:link`

## Testing

Test konversi gambar:

```bash
# Test command konversi
php artisan images:convert-to-webp

# Cek hasil di storage/app/public/warungku/
ls -la storage/app/public/warungku/
```

## Monitoring

Monitor penggunaan storage:

```bash
# Cek ukuran direktori warungku
du -sh storage/app/public/warungku/

# Hitung jumlah file WebP
find storage/app/public/warungku/ -name "*.webp" | wc -l
```
