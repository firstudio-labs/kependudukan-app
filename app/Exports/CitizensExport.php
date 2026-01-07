<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CitizensExport implements FromArray, WithHeadings, WithColumnFormatting, ShouldAutoSize
{
    protected $data;
    protected $isTemplate;

    public function __construct(array $data, $isTemplate = false)
    {
        $this->data = $data;
        $this->isTemplate = $isTemplate;
    }

    /**
     * Mengembalikan data yang akan diekspor.
     * Controller seharusnya hanya memberikan baris data, tanpa header.
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
        if ($this->isTemplate) {
            // Untuk template, gunakan heading yang sesuai dengan kolom template
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

        // Untuk export data, gunakan heading yang lebih user-friendly
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
        ];
    }

    /**
     * Menentukan format kolom.
     */
    public function columnFormats(): array
    {
        // NIK: Kolom A
        // Nomor KK: Kolom B
        // NIK Ayah: Kolom V
        // NIK Ibu: Kolom W
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'V' => NumberFormat::FORMAT_TEXT,
            'W' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
