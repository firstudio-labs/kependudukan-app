# API WebP Conversion untuk Warungku

Dokumentasi implementasi WebP conversion pada API Warungku untuk optimasi gambar produk.

## Endpoint yang Mendukung WebP Conversion

### 1. **POST** `/api/warungku` - Create Product

**Request:**

```bash
curl -X POST http://localhost:8000/api/warungku \
  -H "Authorization: Bearer <TOKEN>" \
  -F "nama_produk=Produk Baru" \
  -F "harga=50000" \
  -F "stok=10" \
  -F "foto=@/path/to/image.jpg"
```

**Response:**

```json
{
    "message": "Produk berhasil dibuat",
    "data": {
        "id": 1,
        "nama_produk": "Produk Baru",
        "foto": "warungku/68ed5531626ba_1760384305.webp",
        "foto_url": "http://localhost:8000/storage/warungku/68ed5531626ba_1760384305.webp"
    }
}
```

### 2. **PUT** `/api/warungku/{id}` - Update Product

**Request:**

```bash
curl -X PUT http://localhost:8000/api/warungku/1 \
  -H "Authorization: Bearer <TOKEN>" \
  -F "nama_produk=Produk Updated" \
  -F "foto=@/path/to/new-image.jpg"
```

**Response:**

```json
{
    "message": "Produk berhasil diperbarui",
    "data": {
        "id": 1,
        "nama_produk": "Produk Updated",
        "foto": "warungku/68ed55317683e_1760384517.webp",
        "foto_url": "http://localhost:8000/storage/warungku/68ed55317683e_1760384517.webp"
    }
}
```

### 3. **DELETE** `/api/warungku/{id}` - Delete Product

**Request:**

```bash
curl -X DELETE http://localhost:8000/api/warungku/1 \
  -H "Authorization: Bearer <TOKEN>"
```

**Response:**

```json
{
    "message": "Produk berhasil dihapus"
}
```

## Validasi File Upload

### Format yang Diterima

-   **JPEG** (.jpg, .jpeg)
-   **PNG** (.png)
-   **GIF** (.gif)
-   **WebP** (.webp)

### Ukuran Maksimal

-   **10MB** (10240 KB) - akan di-resize otomatis

### Validasi Laravel

```php
'foto' => 'nullable|image|max:10240' // 10MB max, akan di-resize dan konversi ke WebP
```

## Proses Konversi Otomatis

### 1. Upload Baru (POST)

```php
if ($request->hasFile('foto')) {
    // Convert to WebP format with optimized dimensions (max 800x600)
    $path = ImageConverterService::convertToWebP($request->file('foto'), 'warungku', 85, 800, 600);
    if ($path) {
        $item->foto = $path; // File otomatis .webp
    }
}
```

### 2. Update (PUT)

```php
if ($request->hasFile('foto')) {
    // Delete old image if exists
    if ($barangWarungku->foto) {
        ImageConverterService::deleteImage($barangWarungku->foto);
    }

    // Convert to WebP format with optimized dimensions (max 800x600)
    $path = ImageConverterService::convertToWebP($request->file('foto'), 'warungku', 85, 800, 600);
    if ($path) {
        $barangWarungku->foto = $path;
    }
}
```

### 3. Delete (DELETE)

```php
// Delete associated image file
if ($barangWarungku->foto) {
    ImageConverterService::deleteImage($barangWarungku->foto);
}
```

## Optimasi yang Diterapkan

### Dimensi Gambar

-   **Maksimal**: 800x600px
-   **Aspect Ratio**: Terjaga (tidak distorsi)
-   **Resize**: Otomatis jika melebihi batas

### Kualitas WebP

-   **Kualitas**: 85% (optimal untuk web)
-   **Format**: WebP (25-35% lebih kecil dari JPEG)
-   **Kompresi**: Lossy dengan kualitas visual terjaga

### Penamaan File

-   **Format**: `{uniqid}_{timestamp}.webp`
-   **Contoh**: `68ed5531626ba_1760384305.webp`
-   **Unik**: Tidak ada konflik nama file

## Response Format

### URL Gambar

```json
{
    "foto": "warungku/68ed5531626ba_1760384305.webp",
    "foto_url": "http://localhost:8000/storage/warungku/68ed5531626ba_1760384305.webp"
}
```

### Error Handling

```json
{
    "message": "The foto field must be an image.",
    "errors": {
        "foto": ["The foto field must be an image."]
    }
}
```

## Testing

### Test Upload dengan cURL

```bash
# Test upload gambar baru
curl -X POST http://localhost:8000/api/warungku \
  -H "Authorization: Bearer <TOKEN>" \
  -F "nama_produk=Test Product" \
  -F "harga=100000" \
  -F "stok=5" \
  -F "foto=@test-image.jpg" \
  -v
```

### Test Update dengan cURL

```bash
# Test update dengan gambar baru
curl -X PUT http://localhost:8000/api/warungku/1 \
  -H "Authorization: Bearer <TOKEN>" \
  -F "nama_produk=Updated Product" \
  -F "foto=@new-image.png" \
  -v
```

## Keuntungan WebP di API

### 1. **Ukuran File Lebih Kecil**

-   25-35% lebih kecil dari JPEG
-   Bandwidth lebih hemat
-   Loading lebih cepat

### 2. **Kualitas Visual Optimal**

-   Kualitas 85% dengan ukuran minimal
-   Tidak ada distorsi gambar
-   Warna dan detail terjaga

### 3. **Konsistensi Data**

-   Semua gambar dalam format WebP
-   Dimensi seragam (max 800x600px)
-   Penamaan file yang terstruktur

### 4. **Manajemen File Otomatis**

-   File lama otomatis dihapus saat update
-   Tidak ada file sampah
-   Storage lebih efisien

## Troubleshooting

### Error: "File too large"

-   Pastikan file < 10MB
-   Cek konfigurasi PHP: `upload_max_filesize`

### Error: "Invalid image format"

-   Gunakan format: JPEG, PNG, GIF, WebP
-   Cek header `Content-Type`

### Error: "Image conversion failed"

-   Cek log Laravel: `storage/logs/laravel.log`
-   Pastikan extension GD terinstall
-   Cek permission direktori `storage/app/public`

## Monitoring

### Cek File WebP

```bash
# List file WebP di storage
ls -la storage/app/public/warungku/*.webp

# Cek ukuran direktori
du -sh storage/app/public/warungku/
```

### Cek Log Konversi

```bash
# Monitor log konversi
tail -f storage/logs/laravel.log | grep "Image conversion"
```

## Konfigurasi

### PHP Settings

```ini
upload_max_filesize = 10M
post_max_size = 12M
max_file_uploads = 20
```

### Laravel Validation

```php
'foto' => 'nullable|image|max:10240' // 10MB
```

### WebP Settings

```php
// config/image.php
'quality' => 85,        // Kualitas WebP
'max_width' => 800,     // Lebar maksimal
'max_height' => 600,    // Tinggi maksimal
```
