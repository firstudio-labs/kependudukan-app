<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisAset extends Model
{
    use HasFactory;

    protected $table = 'jenis_aset';

    protected $fillable = [
        'kode',
        'jenis_aset',
        'keterangan',
        'klasifikasi_id', // tambahkan ini
    ];

    public function klasifikasi()
    {
        return $this->belongsTo(Klasifikasi::class, 'klasifikasi_id');
    }
}
