<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CitizensExport implements FromArray, WithHeadings, WithColumnFormatting
{
    protected $data;
    protected $isTemplate;

    public function __construct(array $data, $isTemplate = false)
    {
        $this->data = $data;
        $this->isTemplate = $isTemplate;
    }

    /**
     * Mengembalikan data yang akan diekspor
     */
    public function array(): array
    {
        if ($this->isTemplate) {
            // Untuk template, kembalikan data asli tanpa heading
            return $this->data;
        }

        // Untuk export data, skip baris pertama jika itu adalah header
        $exportData = [];
        foreach ($this->data as $row) {
            if (is_array($row) && !empty($row)) {
                $exportData[] = $row;
            }
        }
        return $exportData;
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
            // Tambahkan heading lain sesuai kebutuhan
        ];
    }

    /**
     * Menentukan format kolom untuk mencegah scientific notation
     */
    public function columnFormats(): array
    {
        if ($this->isTemplate) {
            // Untuk template, format kolom NIK
            return [
                'A' => NumberFormat::FORMAT_TEXT, // nik
                'B' => NumberFormat::FORMAT_TEXT, // no_kk
                'S' => NumberFormat::FORMAT_TEXT, // nik_ayah
                'T' => NumberFormat::FORMAT_TEXT, // nik_ibu
            ];
        }

        // Untuk export data, format kolom NIK, KK, NIK Ayah, NIK Ibu
        return [
            'A' => NumberFormat::FORMAT_TEXT, // NIK
            'B' => NumberFormat::FORMAT_TEXT, // Nomor KK
            'V' => NumberFormat::FORMAT_TEXT, // NIK Ayah
            'W' => NumberFormat::FORMAT_TEXT, // NIK Ibu
        ];
    }
}
