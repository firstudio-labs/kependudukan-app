<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporDesa extends Model
{
    use HasFactory;

    protected $table = 'lapor_desas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ruang_lingkup',
        'bidang',
        'keterangan'
    ];

    /**
     * Get the laporan associated with this master category
     */
    public function laporanDesas()
    {
        return $this->hasMany(LaporanDesa::class);
    }
}