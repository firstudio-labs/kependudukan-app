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
        'user_id'
    ];

    // Tambahkan gambar_url ke appends agar selalu muncul di JSON
    protected $appends = ['gambar_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor untuk URL gambar
    public function getGambarUrlAttribute()
    {
        return $this->gambar ? asset('storage/' . $this->gambar) : null;
    }
}