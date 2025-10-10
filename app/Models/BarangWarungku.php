<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangWarungku extends Model
{
    use HasFactory;

    protected $fillable = [
        'informasi_usaha_id', 'nama_produk', 'klasifikasi_id', 'jenis_id', 'deskripsi', 'harga', 'stok', 'foto'
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
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }
}


