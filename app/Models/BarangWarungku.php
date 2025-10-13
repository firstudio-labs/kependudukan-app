<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ImageConverterService;

class BarangWarungku extends Model
{
    use HasFactory;

    protected $fillable = [
        'informasi_usaha_id', 'nama_produk', 'jenis_master_id', 'deskripsi', 'harga', 'stok', 'foto'
    ];

    protected $appends = ['foto_url'];

    public function informasiUsaha()
    {
        return $this->belongsTo(InformasiUsaha::class);
    }

    public function warungkuMaster()
    {
        return $this->belongsTo(WarungkuMaster::class, 'jenis_master_id');
    }

    public function getFotoUrlAttribute()
    {
        return ImageConverterService::getImageUrl($this->foto);
    }
}


