<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanDesa extends Model
{
    use HasFactory;

    protected $fillable = [
        'lapor_desa_id',
        'judul_laporan',
        'deskripsi_laporan',
        'gambar',
        'lokasi',
        'tag_lokasi',
        'status',
        'user_id',
        'village_id'
    ];



    public function laporDesa()
    {
        return $this->belongsTo(LaporDesa::class, 'lapor_desa_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function getGambarUrlAttribute()
    {
        return $this->gambar ? '/storage/' . $this->gambar : null;
    }


}
