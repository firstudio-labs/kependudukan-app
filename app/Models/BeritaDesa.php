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
        'user_id',
        'id_desa',
        'id_kecamatan',
        'id_kabupaten',
        'id_provinsi'
    ];

    // Tambahkan gambar_url ke appends agar selalu muncul di JSON
    protected $appends = ['gambar_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan wilayah (commented out karena model tidak ada di database lokal)
    // public function desa()
    // {
    //     return $this->belongsTo(Desa::class, 'id_desa');
    // }

    // public function kecamatan()
    // {
    //     return $this->belongsTo(Kecamatan::class, 'id_kecamatan');
    // }

    // public function kabupaten()
    // {
    //     return $this->belongsTo(Kabupaten::class, 'id_kabupaten');
    // }

    // Accessor untuk URL gambar
    public function getGambarUrlAttribute()
    {
        return $this->gambar ? asset('storage/' . $this->gambar) : null;
    }
}