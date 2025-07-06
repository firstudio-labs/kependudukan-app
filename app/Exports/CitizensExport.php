<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CitizensExport implements FromArray, WithHeadings
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Mengembalikan data yang akan diekspor
     */
    public function array(): array
    {
        return $this->data;
    }

    /**
     * Menentukan heading untuk kolom di Excel
     */
    public function headings(): array
    {
        return [
            'NIK',
            'Nomor KK',
            'Nama Lengkap',
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
            'NIK Ayah',
            'NIK Ibu',
            // Tambahkan heading lain sesuai kebutuhan
        ];
    }
}
