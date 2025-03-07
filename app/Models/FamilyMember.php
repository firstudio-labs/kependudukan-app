<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FamilyMember extends Model
{
    use HasFactory;

    protected $fillable = ['kk_id', 'full_name', 'family_status'];

    public function kk()
    {
        return $this->belongsTo(KK::class);
    }
}
