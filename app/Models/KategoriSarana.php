<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriSarana extends Model
{
    use HasFactory;

    protected $fillable = [
        'jenis_sarana',
        'kategori',
    ];

    public function saranasUmum()
    {
        return $this->hasMany(SaranaUmum::class, 'kategori_sarana_id');
    }
}


