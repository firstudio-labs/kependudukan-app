<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarungkuMaster extends Model
{
    use HasFactory;

    protected $table = 'warungku_masters';

    protected $fillable = [
        'klasifikasi',
        'jenis',
    ];
}


