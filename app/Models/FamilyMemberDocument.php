<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyMemberDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'nik',
        'document_type',
        'file_path',
        'file_name',
        'mime_type',
        'extension',
        'file_size',
        'tag_lokasi'
    ];

    
    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class, 'nik', 'nik');
    }

    public function getDocumentTypeNameAttribute()
    {
        $types = [
            'foto_diri' => 'Foto Diri',
            'foto_ktp' => 'Foto KTP',
            'foto_akta' => 'Akta Kelahiran',
            'ijazah' => 'Ijazah'
        ];

        return $types[$this->document_type] ?? $this->document_type;
    }
}