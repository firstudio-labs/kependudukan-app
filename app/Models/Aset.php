<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aset extends Model
{
    use HasFactory;

    protected $table = 'aset';

    protected $fillable = [
        'user_id',
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

    public function user()
    {
        return $this->belongsTo(Penduduk::class, 'user_id');
    }

    public function getLatitudeAttribute()
    {
        if (!$this->tag_lokasi) {
            return null;
        }

        $parts = explode(',', $this->tag_lokasi);
        return isset($parts[0]) ? trim($parts[0]) : null;
    }

    public function getLongitudeAttribute()
    {
        if (!$this->tag_lokasi) {
            return null;
        }

        $parts = explode(',', $this->tag_lokasi);
        return isset($parts[1]) ? trim($parts[1]) : null;
    }

    public function setCoordinates($latitude, $longitude)
    {
        if ($latitude && $longitude) {
            $this->tag_lokasi = "$latitude, $longitude";
        }
    }

    public function klasifikasi()
    {
        return $this->belongsTo(Klasifikasi::class);
    }

    public function jenisAset()
    {
        return $this->belongsTo(JenisAset::class);
    }

    // Ensure these accessors use the correct storage paths
    public function getFotoAsetDepanUrlAttribute()
    {
        return $this->foto_aset_depan ? '/storage/' . $this->foto_aset_depan : null;
    }

    public function getFotoAsetSampingUrlAttribute()
    {
        return $this->foto_aset_samping ? '/storage/' . $this->foto_aset_samping : null;
    }
}