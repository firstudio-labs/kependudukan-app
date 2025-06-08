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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}