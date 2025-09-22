<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformasiUsaha extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'penduduk_id', 'nama_usaha', 'kelompok_usaha', 'alamat', 'tag_lokasi', 'foto',
        'province_id', 'districts_id', 'sub_districts_id', 'villages_id'
    ];

    protected $appends = ['foto_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class);
    }

    public function barangWarungkus()
    {
        return $this->hasMany(BarangWarungku::class);
    }

    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }
}


