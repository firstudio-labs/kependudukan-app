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

        // Untuk export data, kembalikan semua data (header sudah di-handle oleh headings())
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

        // Untuk export data, gunakan heading sesuai format standar (35 kolom)
        return [
            'NIK',
            'NO_KK',
            'NAMA_LGKP',
            'JENIS_KELAMIN',
            'TANGGAL_LAHIR',
            'UMUR',
            'TEMPAT_LAHIR',
            'ALAMAT',
            'NO_RT',
            'NO_RW',
            'KODE_POS',
            'NO_PROP',
            'NAMA_PROP',
            'NO_KAB',
            'NAMA_KAB',
            'NO_KEC',
            'NAMA_KEC',
            'NO_KEL',
            'KELURAHAN',
            'SHDK',
            'STATUS_KAWIN',
            'PENDIDIKAN',
            'AGAMA',
            'PEKERJAAN',
            'GOLONGAN_DARAH',
            'AKTA_LAHIR',
            'NO_AKTA_LAHIR',
            'AKTA_KAWIN',
            'NO_AKTA_KAWIN',
            'AKTA_CERAI',
            'NO_AKTA_CERAI',
            'NIK_AYAH',
            'NAMA_AYAH',
            'NIK_IBU',
            'NAMA_IBU',
        ];
    }

    // Custom binder: paksa kolom NIK/KK menjadi string explicit
    public function bindValue(Cell $cell, $value)
    {
        // Daftar kolom yang berisi NIK 16 digit: 
        // A (NIK), B (NO_KK), AF (NIK_AYAH), AH (NIK_IBU)
        if (in_array($cell->getColumn(), ['A', 'B', 'AF', 'AH']) && is_numeric($value)) {
            $cell->setValueExplicit((string) $value, DataType::TYPE_STRING);
            return true;
        }

        // Untuk kolom lain, pakai default
        return parent::bindValue($cell, $value);
    }
}