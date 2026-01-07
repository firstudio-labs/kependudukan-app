<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting; // 1. Tambahkan ini
use Maatwebsite\Excel\Concerns\ShouldAutoSize;       // Opsional: Agar lebar kolom otomatis rapi
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;     // 2. Tambahkan ini

class CitizensExport implements FromArray, WithHeadings, WithColumnFormatting, ShouldAutoSize
{
    protected $data;
    protected $isTemplate;

    public function __construct(array $data, $isTemplate = false)
    {
        $this->data = $data;
        $this->isTemplate = $isTemplate;
    }

    public function array(): array
    {
        // Karena kita sudah menghapus header manual di Controller, 
        // kita bisa langsung mengembalikan $this->data
        return $this->data;
    }

    public function headings(): array
    {
        // ... (Kode headings Anda tetap sama) ...
        // Pastikan urutan headings SAMA PERSIS dengan urutan array di controller
        
        if ($this->isTemplate) {
             // ... template headings ...
             return [
                'nik',
                'no_kk',
                'nama_lgkp',
                'jenis_kelamin',
                'tanggal_lahir',
                'umur',
                'tempat_lahir',
                'alamat',
                'no_rt',
                'no_rw',
                'kode_pos',
                'no_prop',
                'nama_prop',
                'no_kab',
                'nama_kab',
                'no_kec',
                'nama_kec',
                'no_kel',
                'kelurahan',
                'shdk',
                'status_kawin',
                'pendidikan',
                'agama',
                'pekerjaan',
                'golongan_darah',
                'akta_lahir',
                'no_akta_lahir',
                'akta_kawin',
                'no_akta_kawin',
                'akta_cerai',
                'no_akta_cerai',
                'nama_ayah',
                'nama_ibu',
                'nik_ayah',
                'nik_ibu'
             ];
        }

        return [
            'NIK',          // Kolom A
            'Nomor KK',     // Kolom B
            'Nama Lengkap', // Kolom C
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Tempat Lahir',
            'Usia',
            'Alamat',
            'RT',
            'RW',
            'ID Provinsi',
            'ID Kabupaten',
            'ID Kecamatan',
            'ID Desa',
            'Kode Pos',
            'Status Kewarganegaraan',
            'Agama',
            'Golongan Darah',
            'Status Dalam Keluarga',
            'Nama Ayah',
            'Nama Ibu',
            'NIK Ayah',     // Kolom V
            'NIK Ibu',      // Kolom W
        ];
    }

    /**
     * 3. Definisikan Format Kolom disini
     */
    public function columnFormats(): array
    {
        // Format Text memaksa Excel membaca angka panjang sebagai String
        // sehingga tidak diubah jadi 3.32E+15 dan angka belakang tidak jadi 0
        return [
            'A' => NumberFormat::FORMAT_TEXT, // Kolom NIK
            'B' => NumberFormat::FORMAT_TEXT, // Kolom KK
            'V' => NumberFormat::FORMAT_TEXT, // Kolom NIK Ayah (Sesuaikan huruf kolom jika urutan berubah)
            'W' => NumberFormat::FORMAT_TEXT, // Kolom NIK Ibu
        ];
    }
}