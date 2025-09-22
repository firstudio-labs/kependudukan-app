<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerangkatDesa extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama',
        'jabatan',
        'alamat',
        'foto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}



