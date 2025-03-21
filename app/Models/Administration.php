<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administration extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'administration';

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
        'job_type_id', // Changed from job_id to match the database column
        'religion',
        'citizen_status',
        'address',
        'signing',
        'rt',
        'letter_date',
        'statement_content',
        'purpose'
    ];

    /**
     * Get the job associated with this administration.
     */
    // public function job()
    // {
    //     return $this->belongsTo(Job::class);
    // }

    // /**
    //  * Get the province associated with this administration.
    //  */
    // public function province()
    // {
    //     return $this->belongsTo(Province::class);
    // }

    // /**
    //  * Get the district associated with this administration.
    //  */
    // public function district()
    // {
    //     return $this->belongsTo(District::class);
    // }

    // /**
    //  * Get the subdistrict associated with this administration.
    //  */
    // public function subdistrict()
    // {
    //     return $this->belongsTo(Subdistrict::class);
    // }

    // /**
    //  * Get the village associated with this administration.
    //  */
    // public function village()
    // {
    //     return $this->belongsTo(Village::class);
    // }
}
