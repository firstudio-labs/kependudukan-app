<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kehilangan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sr_kehilangan';

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
        'nik',
        'full_name',
        'birth_place',
        'birth_date',
        'gender',
        'job_type_id',
        'religion',
        'citizen_status',
        'address',
        'signing',
        'rt',
        'letter_date',
        'lost_items'
    ];
}
