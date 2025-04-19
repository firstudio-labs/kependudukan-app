<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Penandatangan;

class Kematian extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sr_kematian';

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
                'death_cause',
'death_place',
'death_date',
        'info',
        'rt',
        'rt_letter_date',
        'death_place',
        'death_date',
        'info',
        'rt',
        'rt_letter_date',
        'reporter_name',
        'reporter_relation',
                'signing',
        'is_accepted'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
                'birth_date' => 'date',
        'death_date' => 'date',
        'rt_letter_date' => 'date',
    ];

    /**
     * Get the signer associated with this death certificate.
     */
    public function signer()
    {
        return $this->belongsTo(Penandatangan::class, 'signing');
    }
}
