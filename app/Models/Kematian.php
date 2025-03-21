<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kematian extends Model
{
    use HasFactory;

    protected $table = 'sr_kematian';

    protected $fillable = [
        'province_id',
        'district_id',
        'subdistrict_id',
        'village_id',
        'letter_number',
        'nik',
        'full_name',
        'birth_place',
        'birth_date',
        'gender',
        'job_type_id',
        'religion',
        'citizen_status',
        'address',
        'death_cause',
        'death_place',
        'death_date',
        'info',
        'rt',
        'rt_letter_date',
        'reporter_name',
        'reporter_relation',
        'signing',
    ];

    // Just cast the dates, no more array casting
    protected $casts = [
        'birth_date' => 'date',
        'death_date' => 'date',
        'rt_letter_date' => 'date',
    ];
}
