<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaranaUmum extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kategori_sarana_id',
        'nama_sarana',
        'tag_lokasi',
        'alamat',
        'kontak',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriSarana::class, 'kategori_sarana_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


