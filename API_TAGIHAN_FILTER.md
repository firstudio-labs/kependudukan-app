# API Tagihan Filter - Dokumentasi

## Overview

API ini menyediakan endpoint untuk mengelola tagihan dengan berbagai filter, termasuk filter berdasarkan kategori dan sub kategori.

## Base URL

-   Admin: `/api/admin/tagihan`
-   User: `/api/tagihan`

## Authentication

-   Admin: Bearer token dengan role admin desa
-   User: Bearer token dengan role penduduk

## Endpoints

### 1. List Tagihan dengan Filter

#### Admin Endpoint

```
GET /api/admin/tagihan
```

#### User Endpoint

```
GET /api/tagihan
```

#### Query Parameters

| Parameter         | Type    | Required | Description                                                   |
| ----------------- | ------- | -------- | ------------------------------------------------------------- |
| `status`          | string  | No       | Filter berdasarkan status (`pending`, `lunas`, `belum_lunas`) |
| `search`          | string  | No       | Pencarian berdasarkan keterangan atau NIK                     |
| `bulan`           | integer | No       | Filter berdasarkan bulan (1-12)                               |
| `tahun`           | integer | No       | Filter berdasarkan tahun                                      |
| `kategori_id`     | integer | No       | Filter berdasarkan ID kategori                                |
| `sub_kategori_id` | integer | No       | Filter berdasarkan ID sub kategori                            |
| `start_date`      | date    | No       | Filter tanggal mulai (format: YYYY-MM-DD)                     |
| `end_date`        | date    | No       | Filter tanggal akhir (format: YYYY-MM-DD)                     |
| `per_page`        | integer | No       | Jumlah item per halaman (default: 10)                         |
| `page`            | integer | No       | Nomor halaman                                                 |

#### Example Request

```bash
# Filter berdasarkan kategori dan sub kategori
GET /api/admin/tagihan?kategori_id=1&sub_kategori_id=3&status=pending&per_page=20

# Filter berdasarkan rentang tanggal dan kategori
GET /api/admin/tagihan?start_date=2024-01-01&end_date=2024-12-31&kategori_id=2

# Pencarian dengan filter kategori
GET /api/admin/tagihan?search=air&kategori_id=1&per_page=15
```

#### Response Format

```json
{
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "nik": "1234567890123456",
        "kategori_id": 1,
        "sub_kategori_id": 3,
        "nominal": 50000,
        "status": "pending",
        "tanggal": "2024-01-15",
        "keterangan": "Tagihan air bulan Januari",
        "villages_id": 1,
        "created_at": "2024-01-15T10:00:00.000000Z",
        "updated_at": "2024-01-15T10:00:00.000000Z",
        "kategori": {
          "id": 1,
          "nama_kategori": "Air"
        },
        "sub_kategori": {
          "id": 3,
          "nama_sub_kategori": "Air Bersih"
        }
      }
    ],
    "first_page_url": "http://localhost/api/admin/tagihan?page=1",
    "from": 1,
    "last_page": 5,
    "last_page_url": "http://localhost/api/admin/tagihan?page=5",
    "links": [...],
    "next_page_url": "http://localhost/api/admin/tagihan?page=2",
    "path": "http://localhost/api/admin/tagihan",
    "per_page": 10,
    "prev_page_url": null,
    "to": 10,
    "total": 50
  }
}
```

### 2. Get Kategori dan Sub Kategori

#### Admin Endpoint

```
GET /api/admin/tagihan/kategori
```

#### User Endpoint

```
GET /api/tagihan/kategori
```

#### Response Format

```json
{
    "data": [
        {
            "id": 1,
            "nama_kategori": "Air",
            "sub_kategoris": [
                {
                    "id": 1,
                    "nama_sub_kategori": "Air Bersih"
                },
                {
                    "id": 2,
                    "nama_sub_kategori": "Air Kotor"
                }
            ]
        }
    ]
}
```

### 3. Get Sub Kategori by Kategori ID

#### Admin Endpoint

```
GET /api/admin/tagihan/kategori/{kategoriId}/sub
```

#### Example Request

```bash
GET /api/admin/tagihan/kategori/1/sub
```

#### Response Format

```json
{
    "data": [
        {
            "id": 1,
            "kategori_id": 1,
            "nama_sub_kategori": "Air Bersih"
        },
        {
            "id": 2,
            "kategori_id": 1,
            "nama_sub_kategori": "Air Kotor"
        }
    ]
}
```

## Filter Combinations

### 1. Filter Kategori Saja

```bash
GET /api/admin/tagihan?kategori_id=1
```

### 2. Filter Sub Kategori Saja

```bash
GET /api/admin/tagihan?sub_kategori_id=3
```

### 3. Filter Kategori dan Sub Kategori

```bash
GET /api/admin/tagihan?kategori_id=1&sub_kategori_id=3
```

### 4. Filter dengan Status

```bash
GET /api/admin/tagihan?kategori_id=1&status=pending
```

### 5. Filter dengan Rentang Tanggal

```bash
GET /api/admin/tagihan?kategori_id=1&start_date=2024-01-01&end_date=2024-12-31
```

### 6. Filter Lengkap

```bash
GET /api/admin/tagihan?kategori_id=1&sub_kategori_id=3&status=pending&start_date=2024-01-01&end_date=2024-12-31&per_page=20
```

## Error Responses

### 401 Unauthorized

```json
{
    "message": "Unauthorized"
}
```

### 403 Forbidden

```json
{
    "message": "Forbidden"
}
```

### 422 Validation Error

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "kategori_id": ["The kategori id must be an integer."]
    }
}
```

## Notes

1. **Pagination**: Semua endpoint list mendukung pagination dengan parameter `per_page` dan `page`
2. **Date Format**: Gunakan format `YYYY-MM-DD` untuk parameter tanggal
3. **Filter Combination**: Semua filter dapat dikombinasikan untuk hasil yang lebih spesifik
4. **Search**: Parameter `search` akan mencari di field `keterangan` dan `nik`
5. **Ordering**: Data diurutkan berdasarkan `tanggal` secara descending (terbaru dulu)

## Usage Examples

### JavaScript/Fetch

```javascript
// Filter tagihan berdasarkan kategori
const response = await fetch(
    "/api/admin/tagihan?kategori_id=1&status=pending",
    {
        headers: {
            Authorization: "Bearer " + token,
            "Content-Type": "application/json",
        },
    }
);
const data = await response.json();
```

### cURL

```bash
# Filter tagihan dengan kategori dan sub kategori
curl -X GET "http://localhost/api/admin/tagihan?kategori_id=1&sub_kategori_id=3" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json"
```
