<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Penandatangan;

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
        'purpose',
        'is_accepted' // Add this field
    ];

    /**
     * Get the signer associated with this administration.
     */
    public function signer()
    {
        return $this->belongsTo(Penandatangan::class, 'signing');
    }
}
