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
        'nik', 'password', 'no_hp', 'role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'created_at' => 'datetime', // Ensure created_at is cast as datetime
    ];

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
}
