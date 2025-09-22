<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaDesa extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul', 'gambar', 'deskripsi', 'alamat', 'tag_lokasi',
        'user_id', 'province_id', 'districts_id', 'sub_districts_id', 'villages_id'
    ];

    protected $appends = ['gambar_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getGambarUrlAttribute()
    {
        return $this->gambar ? asset('storage/' . $this->gambar) : null;
    }

    /**
     * Return sanitized HTML for description, allowing only safe basic tags
     */
    public function getDeskripsiSanitizedAttribute(): string
    {
        $allowedTags = '<p><br><strong><b><em><i><ul><ol><li><a>'; // allow basic formatting
        $html = strip_tags((string) $this->deskripsi, $allowedTags);
        // Optionally, we can ensure target and rel on links in the future
        return $html;
    }
}


