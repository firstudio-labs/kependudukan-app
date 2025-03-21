<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users'; // Pastikan tabelnya benar

    protected $fillable = [
        'nik',
        'username',
        'email',
        'password',
        'no_hp',
        'role',
        'alamat',
        'province_id',
        'districts_id',
        'sub_districts_id',
        'villages_id',
        'status'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'created_at' => 'datetime', // Ensure created_at is cast as datetime
    ];

    // Relationships
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'districts_id');
    }

    public function subDistrict()
    {
        return $this->belongsTo(SubDistrict::class, 'sub_districts_id');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, 'villages_id');
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
