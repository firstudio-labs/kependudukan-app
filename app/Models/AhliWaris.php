<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Penandatangan;

class AhliWaris extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ahli_waris';

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
        'religion',
        'address',
        'family_status',
        'heir_name',
        'deceased_name',
        'death_place',
        'death_date',
        'death_certificate_number',
        'death_certificate_date',
        'inheritance_letter_date',
        'inheritance_type',
        'signing'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'nik' => 'array',
        'full_name' => 'array',
        'birth_place' => 'array',
        'birth_date' => 'array',
        'gender' => 'array',
        'religion' => 'array',
        'address' => 'array',
        'family_status' => 'array',
        'death_date' => 'date',
        'death_certificate_date' => 'date',
        'inheritance_letter_date' => 'date',
    ];

    public function signer()
    {
        return $this->belongsTo(Penandatangan::class, 'signing');
    }
}
