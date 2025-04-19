<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Penandatangan;

class DomisiliUsaha extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'domisili_usaha';

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
        'rt',
        'letter_date',
        'business_type',
        'business_address',
        'business_year',
        'purpose',
        'signing',
        'is_accepted' // Add this field
    ];

    public function signer()
    {
        return $this->belongsTo(Penandatangan::class, 'signing');
    }
}
