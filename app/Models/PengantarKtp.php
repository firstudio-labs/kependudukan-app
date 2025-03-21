<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengantarKtp extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sr_ktp';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'province_id',
        'district_id',
        'subdistrict_id',
        'village_id',
        'letter_number',
        'application_type',
        'nik',
        'full_name',
        'kk', // Changed from family_card_number to kk
        'address',
        'rt',
        'rw',
        'hamlet',
        'village_name',
        'subdistrict_name',
        'signing'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'nik' => 'integer',
    ];
}
