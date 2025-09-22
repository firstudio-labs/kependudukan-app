<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InformasiUsahaChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'penduduk_id', 'informasi_usaha_id', 'requested_changes', 'current_data', 'status', 'reviewer_id', 'reviewer_note', 'reviewed_at'
    ];

    protected $casts = [
        'requested_changes' => 'array',
        'current_data' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class);
    }

    public function informasiUsaha()
    {
        return $this->belongsTo(InformasiUsaha::class);
    }
}


