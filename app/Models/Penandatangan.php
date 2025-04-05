<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penandatangan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'penandatangan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'judul',
        'keterangan'
    ];

    /**
     * Ensure the returned array includes the id field
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];
}
