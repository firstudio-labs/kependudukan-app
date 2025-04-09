<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelayanan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pelayanan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nama',
        'province_id',
        'district_id',
        'sub_district_id',
        'village_id',
        'alamat',
        'keperluan',
        'no_antrian',
    ];

    /**
     * Get the keperluan record associated with this pelayanan.
     */
    public function keperluanData()
    {
        return $this->belongsTo(Keperluan::class, 'keperluan');
    }

    /**
     * Check if the keperluan is for pelayanan surat.
     *
     * @return bool
     */
    public function isPelayananSurat()
    {
        $keperluan = $this->keperluanData;
        return $keperluan ? (stripos($keperluan->keterangan, 'surat') !== false) : false;
    }

    /**
     * Generate a new queue number for today, grouped by village.
     *
     * @param string $villageId The village ID
     * @return int
     */
    public static function generateQueueNumber($villageId)
    {
        $today = now()->format('Y-m-d');
        $lastQueue = self::whereDate('created_at', $today)
            ->where('village_id', $villageId)
            ->max('no_antrian') ?? 0;
        return $lastQueue + 1;
    }
}
