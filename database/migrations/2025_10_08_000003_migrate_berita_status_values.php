<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Map status lama ke status baru
        DB::table('berita_desas')->where('status', 'approved')->update(['status' => 'published']);
        DB::table('berita_desas')->where('status', 'rejected')->delete();
        // Ubah seluruh pending menjadi archived (tidak ada status pending lagi)
        DB::table('berita_desas')->where('status', 'pending')->update(['status' => 'archived']);
    }

    public function down(): void
    {
        // Kembalikan published ke approved
        DB::table('berita_desas')->where('status', 'published')->update(['status' => 'approved']);
        // Kembalikan archived ke pending (karena sebelumnya pending dihapus)
        DB::table('berita_desas')->where('status', 'archived')->update(['status' => 'pending']);
        // Catatan: data yang dihapus saat rejected tidak bisa dikembalikan
    }
};


