<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class CitizensExport extends DefaultValueBinder implements FromArray, WithHeadings, WithCustomValueBinder
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
        return array_slice($this->data, 1);
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

    // Custom binder: paksa kolom NIK/KK menjadi string explicit
    public function bindValue(Cell $cell, $value)
    {
        // Daftar kolom yang berisi NIK 16 digit: A (NIK), B (Nomor KK), V (NIK Ayah), W (NIK Ibu)
        if (in_array($cell->getColumn(), ['A', 'B', 'V', 'W']) && is_numeric($value)) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        // Untuk kolom lain, pakai default
        return parent::bindValue($cell, $value);
    }
}