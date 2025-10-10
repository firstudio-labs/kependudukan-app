<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriTagihan extends Model
{
    protected $fillable = [
        'nama_kategori'
    ];

    public function subKategoris(): HasMany
    {
        return $this->hasMany(SubKategoriTagihan::class, 'kategori_id');
    }

    public function tagihans(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'kategori_id');
    }
}
