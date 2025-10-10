<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KesenianBudaya extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jenis',
        'nama',
        'tag_lokasi',
        'alamat',
        'kontak',
        'foto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


