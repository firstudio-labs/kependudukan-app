<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RumahSewa extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'izin_rumahsewa';

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
        'full_name', // Changed from organizer_name to match table
        'address',   // Changed from organizer_address to match table
        'responsible_name',
        'rental_address',
        'street',
        'alley_number',
        'rt',
        'building_area',
        'room_count',
        'rental_type',
        'valid_until',
        'signing'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'valid_until' => 'date',
        'room_count' => 'integer',
        // Removed 'rt' from integer casts to allow values like '001'
    ];

    /**
     * Get the penandatangan that signed this document.
     */
    public function penandatangan()
    {
        return $this->belongsTo(Penandatangan::class, 'signing', 'id');
    }
}
