<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tagihan extends Model
{
    protected $fillable = [
        'villages_id',
        'nik',
        'kategori_id',
        'sub_kategori_id',
        'nominal',
        'keterangan',
        'status',
        'tanggal'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2'
    ];

    public function penduduk(): BelongsTo
    {
        return $this->belongsTo(Penduduk::class, 'penduduk_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriTagihan::class, 'kategori_id');
    }

    public function subKategori(): BelongsTo
    {
        return $this->belongsTo(SubKategoriTagihan::class, 'sub_kategori_id');
    }
}
