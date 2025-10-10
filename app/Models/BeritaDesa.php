<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeritaDesa extends Model
{
    use HasFactory;

    protected $table = 'berita_desas';

    protected $fillable = [
        'judul',
        'gambar',
        'deskripsi',
        'komentar',
        'status',
        'user_id',
        'nik_penduduk',
        'province_id',
        'districts_id',
        'sub_districts_id',
        'villages_id'
    ];

    // Tambahkan gambar_url ke appends agar selalu muncul di JSON
    protected $appends = ['gambar_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class, 'nik_penduduk', 'nik');
    }

    // Relasi dengan wilayah
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'districts_id');
    }

    public function subDistrict()
    {
        return $this->belongsTo(SubDistrict::class, 'sub_districts_id');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'villages_id');
    }

    // Accessor untuk URL gambar
    public function getGambarUrlAttribute()
    {
        return $this->gambar ? asset('storage/' . $this->gambar) : null;
    }

    // Scope untuk filter berdasarkan wilayah
    public function scopeByProvince($query, $provinceId)
    {
        return $query->where('province_id', $provinceId);
    }

    public function scopeByDistrict($query, $districtId)
    {
        return $query->where('districts_id', $districtId);
    }

    public function scopeBySubDistrict($query, $subDistrictId)
    {
        return $query->where('sub_districts_id', $subDistrictId);
    }

    public function scopeByVillage($query, $villageId)
    {
        return $query->where('villages_id', $villageId);
    }
}