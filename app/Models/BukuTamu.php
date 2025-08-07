<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuTamu extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'buku_tamu';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama',
        'alamat',
        'no_telepon',
        'email',
        'keperluan',
        'pesan', // Tetap menggunakan 'pesan' sesuai database
        'tanda_tangan',
        'foto', // Menambahkan kolom foto
        'province_id',
        'district_id',
        'sub_district_id',
        'village_id',
    ];
}
