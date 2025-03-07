<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;




class KK extends Model
{
    use HasFactory;
    protected $table = 'kk'; // Pastikan tabelnya benar

    protected $fillable = [
        'kk',
        'full_name',
        'address',
        'postal_code',
        'rt',
        'rw',
        'jml_anggota_kk',
        'telepon',
        'email',
        'province_id',
        'district_id',
        'sub_district_id',
        'village_id',
        'dusun',
        'alamat_luar_negeri',
        'kota',
        'negara_bagian',
        'negara',
        'kode_pos_luar_negeri',
    ];

    protected $casts = [
        'family_members' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'nama_lengkap', 'id'); // Jika nama_lengkap adalah user_id
    }

    public function familyMembers()
    {
        return $this->hasMany(FamilyMember::class, 'kk_id');
    }
}
