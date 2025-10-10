<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubKategoriTagihan extends Model
{
    protected $fillable = [
        'kategori_id',
        'nama_sub_kategori'
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriTagihan::class, 'kategori_id');
    }

    public function tagihans(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'sub_kategori_id');
    }
}
