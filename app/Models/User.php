<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users'; // Pastikan tabelnya benar

    protected $fillable = [
        'nik',
        'nama',
        'username',
        'email',
        'password',
        'no_hp',
        'role',
        'alamat',
        'image',
        'foto_pengguna',
        'province_id',
        'districts_id',
        'sub_districts_id',
        'villages_id',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'created_at' => 'datetime', // Ensure created_at is cast as datetime
    ];

    // Relationships - menggunakan string namespace untuk lazy loading
    public function province()
    {
        // Kita gunakan string namespace untuk menghindari autoloading class sebelum runtime
        return $this->belongsTo('App\Models\Province', 'province_id');
    }

    public function district()
    {
        return $this->belongsTo('App\Models\District', 'districts_id');
    }

    public function subDistrict()
    {
        return $this->belongsTo('App\Models\SubDistrict', 'sub_districts_id');
    }

    public function village()
    {
        // Untuk mengambil nama desa, kita gunakan accessor
        return $this->belongsTo('App\Models\Village', 'villages_id');
    }

    public function kepalaDesa()
    {
        return $this->hasOne(KepalaDesa::class);
    }


    /**
     * Mendapatkan nama desa dari village_id
     */
    public function getVillageNameAttribute()
    {
        if (!$this->villages_id) {
            return 'Tidak Ada Desa';
        }

        try {
            $villageData = app(\App\Services\WilayahService::class)->getVillageById($this->villages_id);
            if ($villageData && isset($villageData['name'])) {
                return $villageData['name'];
            } elseif ($villageData && isset($villageData['data']['name'])) {
                return $villageData['data']['name'];
            }
            return 'Desa #' . $this->villages_id;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error getting village name: ' . $e->getMessage());
            return 'Desa #' . $this->villages_id;
        }
    }

    // Add a scope to get monthly registration counts
    public static function getMonthlyRegistrations($year = null)
    {
        $year = $year ?? date('Y');

        return self::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    // Add this method to your User model
    public static function getMonthlyRegistrationsByRole()
    {
        return self::selectRaw('MONTH(created_at) as month, role, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month', 'role')
            ->orderBy('month')
            ->get();
    }
}
