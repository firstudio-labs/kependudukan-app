<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelahiran extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sr_kelahiran';

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
        'father_nik',
        'father_full_name',
        'father_birth_place',
        'father_birth_date',
        'father_job',
        'father_religion',
        'father_address',
        'mother_nik',
        'mother_full_name',
        'mother_birth_place',
        'mother_birth_date',
        'mother_job',
        'mother_religion',
        'mother_address',
        'child_name',
        'child_gender',
        'child_birth_date',
        'child_birth_place',
        'child_religion',
        'child_address',
        'child_order',
        'signing'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'father_birth_date' => 'date',
        'mother_birth_date' => 'date',
        'child_birth_date' => 'date',
    ];
}
