<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tagihan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class GenerateMonthlyTagihan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-monthly-tagihan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate tagihan bulanan per NIK/kategori/sub_kategori dan carry-over tunggakan bulan sebelumnya';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        // Gunakan tanggal akhir bulan untuk field tanggal tagihan yang di-generate
        $currentMonthDate = $now->copy()->endOfMonth();
        $previousMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $previousMonthEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();

        $this->info('Memulai generate tagihan untuk bulan: ' . $currentMonthDate->format('Y-m'));

        // Ambil tagihan bulan sebelumnya yang terbaru per (villages_id, nik, kategori_id, sub_kategori_id)
        // Gunakan subquery untuk mendapatkan record terakhir (berdasarkan tanggal dan created_at)
        $subQuery = Tagihan::query()
            ->select('id')
            ->whereBetween('tanggal', [$previousMonthStart->toDateString(), $previousMonthEnd->toDateString()])
            ->whereNotNull('nik')
            ->whereNotNull('kategori_id')
            ->whereNotNull('sub_kategori_id')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc');

        // DB-specific way to get latest per group: use window function if available, fallback to manual grouping in PHP
        // Karena fokusnya kejelasan, kita lakukan grouping manual via collection
        $previousMonthTagihans = Tagihan::query()
            ->whereBetween('tanggal', [$previousMonthStart->toDateString(), $previousMonthEnd->toDateString()])
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique(function ($row) {
                return implode('|', [
                    $row->villages_id,
                    $row->nik,
                    $row->kategori_id,
                    $row->sub_kategori_id,
                ]);
            });

        if ($previousMonthTagihans->isEmpty()) {
            $this->info('Tidak ada basis tagihan pada bulan sebelumnya. Tidak ada yang digenerate.');
            return self::SUCCESS;
        }

        $createdCount = 0;
        foreach ($previousMonthTagihans as $prev) {
            // Cek apakah tagihan untuk kombinasi ini sudah ada di bulan berjalan
            $exists = Tagihan::query()
                ->where('villages_id', $prev->villages_id)
                ->where('nik', $prev->nik)
                ->where('kategori_id', $prev->kategori_id)
                ->where('sub_kategori_id', $prev->sub_kategori_id)
                ->whereMonth('tanggal', $currentMonthDate->month)
                ->whereYear('tanggal', $currentMonthDate->year)
                ->exists();

            if ($exists) {
                continue;
            }

            $baseNominal = (float) ($prev->nominal ?? 0);
            $isUnpaid = $prev->status !== 'lunas' && $baseNominal > 0;
            $carryOver = $isUnpaid ? $baseNominal : 0.0;
            $newNominal = $baseNominal + $carryOver; // default: duplikasi nominal + tunggakan bila belum lunas

            // Rangkaian keterangan breakdown
            $detail = [
                'Generate otomatis ' . $currentMonthDate->format('F Y'),
                'Basis dari bulan ' . $previousMonthStart->format('F Y') . ' dengan nominal: Rp ' . number_format($baseNominal, 0, ',', '.'),
            ];
            if ($isUnpaid) {
                $detail[] = 'Carry-over tunggakan bulan sebelumnya: Rp ' . number_format($carryOver, 0, ',', '.');
            } else {
                $detail[] = 'Tidak ada tunggakan dari bulan sebelumnya.';
            }
            $detail[] = 'Nominal awal bulan ini dapat diubah manual jika diperlukan.';

            $keterangan = trim(implode("\n", $detail));

            Tagihan::create([
                'villages_id' => $prev->villages_id,
                'nik' => $prev->nik,
                'kategori_id' => $prev->kategori_id,
                'sub_kategori_id' => $prev->sub_kategori_id,
                'nominal' => $newNominal,
                'keterangan' => $keterangan,
                'status' => 'belum_lunas',
                'tanggal' => $currentMonthDate->toDateString(),
            ]);

            $createdCount++;
        }

        $this->info("Selesai. Tagihan baru dibuat: {$createdCount}");
        return self::SUCCESS;
    }
}
