<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileChangeRequest extends Model
{
    use HasFactory;

    protected $table = 'profile_change_requests';

    protected $fillable = [
        'nik',
        'village_id',
        'current_data',
        'requested_changes',
        'status',
        'requested_at',
        'reviewed_at',
        'reviewed_by',
        'reviewer_note',
    ];

    protected $casts = [
        'current_data' => 'array',
        'requested_changes' => 'array',
        'requested_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}


