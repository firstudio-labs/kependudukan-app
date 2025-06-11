<?php

namespace App\Http\Controllers\Api;

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

    public function index(Request $request)
    {
        // Get token owner from request attributes
        $tokenOwner = $request->attributes->get('token_owner');
        if (!$tokenOwner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 401);
        }

        $nik = $tokenOwner->nik;

        // Collect all letter types with their names
        $allLetters = collect([]);

        // SKCK
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

        // Domisili Usaha
        $domisiliUsahaLetters = DomisiliUsaha::where('nik', $nik)
            ->select('id', 'letter_number', 'purpose', DB::raw("'domisiliusaha' as letter_type"), 'created_at', 'is_accepted')
            ->get();
        $allLetters = $allLetters->concat($domisiliUsahaLetters);

        // Kehilangan
        $kehilanganLetters = Kehilangan::where('nik', $nik)
            ->select('id', 'letter_number', 'lost_items as purpose', DB::raw("'kehilangan' as letter_type"), 'created_at', 'is_accepted')
            ->get();
        $allLetters = $allLetters->concat($kehilanganLetters);

        // Pengantar KTP
        $ktpLetters = PengantarKtp::where('nik', $nik)
            ->select('id', 'letter_number', DB::raw("'Pembuatan KTP' as purpose"), DB::raw("'pengantarktp' as letter_type"), 'created_at', 'is_accepted')
            ->get();
        $allLetters = $allLetters->concat($ktpLetters);

        // Rumah Sewa
        $rumahSewaLetters = RumahSewa::where('nik', $nik)
            ->select('id', 'letter_number', DB::raw("'Izin Rumah Sewa' as purpose"), DB::raw("'izinrumahsewa' as letter_type"), 'created_at', 'is_accepted')
            ->get();
        $allLetters = $allLetters->concat($rumahSewaLetters);

        // Izin Keramaian
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

        // Ahli Waris
        $ahliWarisLetters = AhliWaris::where('heir_name', 'like', "%{$tokenOwner->name}%")
            ->select('id', 'letter_number', DB::raw("'Surat Keterangan Ahli Waris' as purpose"), DB::raw("'ahliwaris' as letter_type"), 'created_at', 'is_accepted')
            ->get();
        $allLetters = $allLetters->concat($ahliWarisLetters);

        // Sort letters by created_at date
        $allLetters = $allLetters->sortBy('created_at');

        return response()->json([
            'status' => 'success',
            'data' => $allLetters
        ]);
    }

    public function showSKCK(Request $request, $id)
    {
        $tokenOwner = $request->attributes->get('token_owner');
        if (!$tokenOwner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 401);
        }

        $skck = SKCK::where('nik', $tokenOwner->nik)->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $skck
        ]);
    }

    public function showAdministrasi(Request $request, $id)
    {
        $tokenOwner = $request->attributes->get('token_owner');
        if (!$tokenOwner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 401);
        }

        $administrasi = Administration::where('nik', $tokenOwner->nik)->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $administrasi
        ]);
    }

    public function showDomisili(Request $request, $id)
    {
        $tokenOwner = $request->attributes->get('token_owner');
        if (!$tokenOwner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 401);
        }

        $domisili = Domisili::where('nik', $tokenOwner->nik)->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $domisili
        ]);
    }

    public function showDomisiliUsaha(Request $request, $id)
    {
        $tokenOwner = $request->attributes->get('token_owner');
        if (!$tokenOwner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 401);
        }

        $domisiliUsaha = DomisiliUsaha::where('nik', $tokenOwner->nik)->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $domisiliUsaha
        ]);
    }

    public function showKehilangan(Request $request, $id)
    {
        $tokenOwner = $request->attributes->get('token_owner');
        if (!$tokenOwner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 401);
        }

        $kehilangan = Kehilangan::where('nik', $tokenOwner->nik)->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $kehilangan
        ]);
    }

    public function showKTP(Request $request, $id)
    {
        $tokenOwner = $request->attributes->get('token_owner');
        if (!$tokenOwner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 401);
        }

        $ktp = PengantarKtp::where('nik', $tokenOwner->nik)->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $ktp
        ]);
    }

    public function showRumahSewa(Request $request, $id)
    {
        $tokenOwner = $request->attributes->get('token_owner');
        if (!$tokenOwner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 401);
        }

        $rumahSewa = RumahSewa::where('nik', $tokenOwner->nik)->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $rumahSewa
        ]);
    }

    public function showKeramaian(Request $request, $id)
    {
        $tokenOwner = $request->attributes->get('token_owner');
        if (!$tokenOwner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 401);
        }

        $keramaian = IzinKeramaian::where('nik', $tokenOwner->nik)->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $keramaian
        ]);
    }

    public function showKelahiran(Request $request, $id)
    {
        $tokenOwner = $request->attributes->get('token_owner');
        if (!$tokenOwner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 401);
        }

        $kelahiran = Kelahiran::where('father_nik', $tokenOwner->nik)
            ->orWhere('mother_nik', $tokenOwner->nik)
            ->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $kelahiran
        ]);
    }

    public function showKematian(Request $request, $id)
    {
        $tokenOwner = $request->attributes->get('token_owner');
        if (!$tokenOwner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 401);
        }

        $kematian = Kematian::where('nik', $tokenOwner->nik)->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $kematian
        ]);
    }

    public function showAhliWaris(Request $request, $id)
    {
        $tokenOwner = $request->attributes->get('token_owner');
        if (!$tokenOwner) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 401);
        }

        $ahliWaris = AhliWaris::where('heir_name', 'like', "%{$tokenOwner->name}%")->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $ahliWaris
        ]);
    }
}

