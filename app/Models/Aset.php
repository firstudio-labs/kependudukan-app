<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    use HasFactory;

    protected $table = 'aset';

    protected $fillable = [
        'nama_aset',
        'nik_pemilik',
        'nama_pemilik',
        'address',
        'province_id',
        'district_id',
        'sub_district_id',
        'village_id',
        'rt',
        'rw',
        'klasifikasi_id',
        'jenis_aset_id',
        'tag_lokasi',
        'foto_aset_depan',
        'foto_aset_samping'
    ];

    public function klasifikasi()
    {
        return $this->belongsTo(Klasifikasi::class);
    }

    public function jenisAset()
    {
        return $this->belongsTo(JenisAset::class);
    }
}