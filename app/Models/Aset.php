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

    /**
     * Get latitude from tag_lokasi
     * 
     * @return string|null
     */
    public function getLatitudeAttribute()
    {
        if (!$this->tag_lokasi) {
            return null;
        }

        $parts = explode(',', $this->tag_lokasi);
        return isset($parts[0]) ? trim($parts[0]) : null;
    }

    /**
     * Get longitude from tag_lokasi
     * 
     * @return string|null
     */
    public function getLongitudeAttribute()
    {
        if (!$this->tag_lokasi) {
            return null;
        }

        $parts = explode(',', $this->tag_lokasi);
        return isset($parts[1]) ? trim($parts[1]) : null;
    }

    /**
     * Set tag_lokasi from latitude and longitude
     * 
     * @param float|string $latitude
     * @param float|string $longitude
     * @return void
     */
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
}