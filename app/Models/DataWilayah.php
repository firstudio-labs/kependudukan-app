<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataWilayah extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'luas_wilayah', 'foto_peta', 'batas_wilayah', 'jumlah_dusun', 'jumlah_rt'
    ];

    protected $casts = [
        'batas_wilayah' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


