<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsahaDesa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jenis',
        'nama',
        'ijin',
        'tahun_didirikan',
        'ketua',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


