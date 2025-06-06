<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SKCK;
use App\Models\Administration;
use App\Models\AhliWaris;
use App\Models\Domisili;
use App\Models\DomisiliUsaha;
use App\Models\IzinKeramaian;
use App\Models\Kehilangan;
use App\Models\Kelahiran;
use App\Models\Kematian;
use App\Models\PengantarKtp;
use App\Models\RumahSewa;
use App\Services\WilayahService;
use App\Services\JobService;

class RiwayatSuratController extends Controller
{
    protected $wilayahService;
    protected $jobService;

    /**
     * Create a new controller instance.
     */
    public function __construct(WilayahService $wilayahService, JobService $jobService)
    {

        $this->wilayahService = $wilayahService;
        $this->jobService = $jobService;
    }

    public function index()
    {
        $user = Auth::user();
        $nik = $user->nik;

        // Collect all letter types with their names
        $allLetters = collect([]);

        // SKCK - Make sure letter_type is lowercase to match the switch statement
        $skckLetters = SKCK::where('nik', $nik)
            ->select('id', 'letter_number', 'purpose', DB::raw("'skck' as letter_type"), 'created_at', 'is_accepted')
            ->get();
        $allLetters = $allLetters->concat($skckLetters);

        // Administrasi
        $administrasiLetters = Administration::where('nik', $nik)
            ->select('id', 'letter_number', 'purpose', DB::raw("'administrasi' as letter_type"), 'created_at', 'is_accepted')
            ->get();
        $allLetters = $allLetters->concat($administrasiLetters);

        // Domisili
        $domisiliLetters = Domisili::where('nik', $nik)
            ->select('id', 'letter_number', 'purpose', DB::raw("'domisili' as letter_type"), 'created_at', 'is_accepted')
            ->get();
        $allLetters = $allLetters->concat($domisiliLetters);

        // Domisili Usaha (using consistent format without spaces)
        $domisiliUsahaLetters = DomisiliUsaha::where('nik', $nik)
            ->select('id', 'letter_number', 'purpose', DB::raw("'domisiliusaha' as letter_type"), 'created_at', 'is_accepted')
            ->get();
        $allLetters = $allLetters->concat($domisiliUsahaLetters);

        // Kehilangan
        $kehilanganLetters = Kehilangan::where('nik', $nik)
            ->select('id', 'letter_number', 'lost_items as purpose', DB::raw("'kehilangan' as letter_type"), 'created_at', 'is_accepted')
            ->get();
        $allLetters = $allLetters->concat($kehilanganLetters);

        // Pengantar KTP (using consistent format without spaces)
        $ktpLetters = PengantarKtp::where('nik', $nik)
            ->select('id', 'letter_number', DB::raw("'Pembuatan KTP' as purpose"), DB::raw("'pengantarktp' as letter_type"), 'created_at', 'is_accepted')
            ->get();
        $allLetters = $allLetters->concat($ktpLetters);

        // Rumah Sewa (using consistent format without spaces)
        $rumahSewaLetters = RumahSewa::where('nik', $nik)
            ->select('id', 'letter_number', DB::raw("'Izin Rumah Sewa' as purpose"), DB::raw("'izinrumahsewa' as letter_type"), 'created_at', 'is_accepted')
            ->get();
        $allLetters = $allLetters->concat($rumahSewaLetters);

        // Izin Keramaian (using consistent format without spaces)
        $keramaianLetters = IzinKeramaian::where('nik', $nik)
            ->select('id', 'letter_number', 'event as purpose', DB::raw("'izinkeramaian' as letter_type"), 'created_at', 'is_accepted')
            ->get();
        $allLetters = $allLetters->concat($keramaianLetters);

        // Kelahiran
        $kelahiranLetters = Kelahiran::where('father_nik', $nik)
            ->orWhere('mother_nik', $nik)
            ->select('id', 'letter_number', DB::raw("'Surat Kelahiran' as purpose"), DB::raw("'kelahiran' as letter_type"), 'created_at', 'is_accepted')
            ->get();
        $allLetters = $allLetters->concat($kelahiranLetters);

        // Kematian
        $kematianLetters = Kematian::where('nik', $nik)
            ->select('id', 'letter_number', DB::raw("'Surat Kematian' as purpose"), DB::raw("'kematian' as letter_type"), 'created_at', 'is_accepted')
            ->get();
        $allLetters = $allLetters->concat($kematianLetters);

        // Ahli Waris (using consistent format without spaces)
        $ahliWarisLetters = AhliWaris::where('heir_name', 'like', "%{$user->name}%")
            ->select('id', 'letter_number', DB::raw("'Surat Keterangan Ahli Waris' as purpose"), DB::raw("'ahliwaris' as letter_type"), 'created_at', 'is_accepted')
            ->get();
        $allLetters = $allLetters->concat($ahliWarisLetters);

        // Sort letters by created_at date
        $allLetters = $allLetters->sortBy('created_at');

        return view('user.riwayat-surat.index', compact('allLetters'));
    }

    public function showSKCK($id)
    {
        $skck = SKCK::findOrFail($id);
        return response()->json([
            'success' => true,
            'skck' => $skck
        ]);
    }

    public function showAdministrasi($id)
    {
        $administrasi = Administration::findOrFail($id);
        return response()->json([
            'success' => true,
            'administrasi' => $administrasi
        ]);
    }

    public function showDomisili($id)
    {
        $domisili = Domisili::findOrFail($id);
        return response()->json([
            'success' => true,
            'domisili' => $domisili
        ]);
    }

    public function showDomisiliUsaha($id)
    {
        $domisiliUsaha = DomisiliUsaha::findOrFail($id);
        return response()->json([
            'success' => true,
            'domisiliusaha' => $domisiliUsaha
        ]);
    }

    public function showKehilangan($id)
    {
        $kehilangan = Kehilangan::findOrFail($id);
        return response()->json([
            'success' => true,
            'kehilangan' => $kehilangan
        ]);
    }

    public function showKTP($id)
    {
        $ktp = PengantarKtp::findOrFail($id);
        return response()->json([
            'success' => true,
            'pengantarktp' => $ktp
        ]);
    }

    public function showRumahSewa($id)
    {
        $rumahSewa = RumahSewa::findOrFail($id);
        return response()->json([
            'success' => true,
            'izinrumahsewa' => $rumahSewa
        ]);
    }

    public function showKeramaian($id)
    {
        $keramaian = IzinKeramaian::findOrFail($id);
        return response()->json([
            'success' => true,
            'izinkeramaian' => $keramaian
        ]);
    }

    public function showKelahiran($id)
    {
        $kelahiran = Kelahiran::findOrFail($id);
        return response()->json([
            'success' => true,
            'kelahiran' => $kelahiran
        ]);
    }

    public function showKematian($id)
    {
        $kematian = Kematian::findOrFail($id);
        return response()->json([
            'success' => true,
            'kematian' => $kematian
        ]);
    }

    public function showAhliWaris($id)
    {
        $ahliWaris = AhliWaris::findOrFail($id);
        return response()->json([
            'success' => true,
            'ahliwaris' => $ahliWaris
        ]);
    }
}