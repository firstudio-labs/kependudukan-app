<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use App\Models\KategoriTagihan;
use App\Models\SubKategoriTagihan;
use App\Models\Tagihan;
use App\Models\Penduduk;
use App\Services\CitizenService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterTagihanController extends Controller
{
    protected $citizenService;

    public function __construct(CitizenService $citizenService)
    {
        $this->citizenService = $citizenService;
    }

    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();
        $villagesId = $user->villages_id;

        // Search parameters
        $searchKategori = $request->input('search_kategori');
        $searchSubKategori = $request->input('search_sub_kategori');
        $searchTagihan = $request->input('search_tagihan');

        // Get kategoris
        $kategorisQuery = KategoriTagihan::query();
        if (!empty($searchKategori)) {
            $kategorisQuery->where('nama_kategori', 'like', "%{$searchKategori}%");
        }
        $kategoris = $kategorisQuery->orderBy('nama_kategori')->get();

        // Get sub kategoris
        $subKategorisQuery = SubKategoriTagihan::with('kategori');
        if (!empty($searchSubKategori)) {
            $subKategorisQuery->where('nama_sub_kategori', 'like', "%{$searchSubKategori}%");
        }
        $subKategoris = $subKategorisQuery->orderBy('nama_sub_kategori')->get();

        // Default vars to keep legacy view references safe (Tagihan moved to its own page)
        $searchTagihan = null;
        $tagihans = collect();
        $penduduks = [];
        $pendudukLookup = collect();

        return view('admin.desa.master-tagihan.index', compact(
            'kategoris', 
            'subKategoris', 
            'searchKategori',
            'searchSubKategori',
            'searchTagihan',
            'tagihans',
            'penduduks',
            'pendudukLookup',
        ));
    }

    /**
     * Halaman khusus Tagihan (dipisah dari master kategori & sub-kategori)
     */
    public function tagihanIndex(Request $request)
    {
        $user = Auth::guard('web')->user();
        $villagesId = $user->villages_id;

        $searchTagihan = $request->input('search_tagihan');

        // Get tagihans
        $tagihansQuery = Tagihan::with(['kategori', 'subKategori'])
            ->where('villages_id', $villagesId);
        if (!empty($searchTagihan)) {
            $tagihansQuery->where('keterangan', 'like', "%{$searchTagihan}%");
        }
        $tagihans = $tagihansQuery->orderBy('created_at', 'desc')->paginate(10);

        // Get kategoris for dropdowns
        $kategoris = KategoriTagihan::orderBy('nama_kategori')->get();

        // Get penduduks for dropdown
        $penduduksResponse = $this->citizenService->getCitizensByVillageId($villagesId);
        $penduduks = $penduduksResponse['data']['citizens'] ?? [];
        $pendudukLookup = collect($penduduks)->keyBy('nik');

        return view('admin.desa.master-tagihan.tagihan', compact(
            'kategoris', 
            'tagihans', 
            'penduduks',
            'pendudukLookup',
            'searchTagihan'
        ));
    }

    // Kategori methods
    public function storeKategori(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_tagihans,nama_kategori'
        ]);

        KategoriTagihan::create($validated);
        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan');
    }

    public function updateKategori(Request $request, $id)
    {
        $kategori = KategoriTagihan::findOrFail($id);
        
        $validated = $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_tagihans,nama_kategori,' . $id
        ]);

        $kategori->update($validated);
        return redirect()->back()->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroyKategori($id)
    {
        $kategori = KategoriTagihan::findOrFail($id);
        $kategori->delete();
        return redirect()->back()->with('success', 'Kategori berhasil dihapus');
    }

    // Sub Kategori methods
    public function storeSubKategori(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_tagihans,id',
            'nama_sub_kategori' => 'required|string|max:255'
        ]);

        SubKategoriTagihan::create($validated);
        return redirect()->back()->with('success', 'Sub kategori berhasil ditambahkan');
    }

    public function updateSubKategori(Request $request, $id)
    {
        $subKategori = SubKategoriTagihan::findOrFail($id);
        
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_tagihans,id',
            'nama_sub_kategori' => 'required|string|max:255'
        ]);

        $subKategori->update($validated);
        return redirect()->back()->with('success', 'Sub kategori berhasil diperbarui');
    }

    public function destroySubKategori($id)
    {
        $subKategori = SubKategoriTagihan::findOrFail($id);
        $subKategori->delete();
        return redirect()->back()->with('success', 'Sub kategori berhasil dihapus');
    }

    // Tagihan methods
    public function storeTagihan(Request $request)
    {
        $user = Auth::guard('web')->user();
        
        $validated = $request->validate([
            'nik' => 'required|string',
            'kategori_id' => 'required|exists:kategori_tagihans,id',
            'sub_kategori_id' => 'required|exists:sub_kategori_tagihans,id',
            'nominal' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:pending,lunas,belum_lunas',
            'tanggal' => 'required|date'
        ]);

        $validated['villages_id'] = $user->villages_id;
        
        Tagihan::create($validated);
        return redirect()->back()->with('success', 'Tagihan berhasil ditambahkan');
    }

    public function updateTagihan(Request $request, $id)
    {
        $tagihan = Tagihan::findOrFail($id);
        
        $validated = $request->validate([
            'nik' => 'required|string',
            'kategori_id' => 'required|exists:kategori_tagihans,id',
            'sub_kategori_id' => 'required|exists:sub_kategori_tagihans,id',
            'nominal' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:pending,lunas,belum_lunas',
            'tanggal' => 'required|date'
        ]);

        // Jika nominal diubah, hitung ulang total dengan carry-over
        if (isset($validated['nominal'])) {
            $nominalBulanIni = $validated['nominal'];
            $carryOver = $this->calculateCarryOver($tagihan);
            $validated['nominal'] = $nominalBulanIni + $carryOver;
            
            // Update keterangan untuk mencerminkan perubahan nominal
            $validated['keterangan'] = $this->updateKeteranganNominal($tagihan, $nominalBulanIni, $carryOver);
        }

        $tagihan->update($validated);
        return redirect()->back()->with('success', 'Tagihan berhasil diperbarui');
    }

    private function calculateCarryOver($tagihan)
    {
        // Jika keterangan mengandung "Carry-over", ekstrak nilai carry-over
        if (strpos($tagihan->keterangan, 'Carry-over tunggakan bulan sebelumnya') !== false) {
            preg_match('/Carry-over tunggakan bulan sebelumnya: Rp ([0-9.,]+)/', $tagihan->keterangan, $matches);
            if (isset($matches[1])) {
                return (float) str_replace(['.', ','], ['', '.'], $matches[1]);
            }
        }
        return 0;
    }

    private function updateKeteranganNominal($tagihan, $nominalBulanIni, $carryOver)
    {
        $lines = explode("\n", $tagihan->keterangan);
        $newLines = [];
        
        foreach ($lines as $line) {
            if (strpos($line, 'Basis dari bulan') !== false) {
                // Update baris basis dengan nominal baru
                $newLines[] = "Basis dari bulan " . \Carbon\Carbon::now()->subMonthNoOverflow()->format('F Y') . " dengan nominal: Rp " . number_format($nominalBulanIni, 0, ',', '.');
            } elseif (strpos($line, 'Carry-over tunggakan bulan sebelumnya') !== false) {
                // Update baris carry-over
                if ($carryOver > 0) {
                    $newLines[] = "Carry-over tunggakan bulan sebelumnya: Rp " . number_format($carryOver, 0, ',', '.');
                } else {
                    $newLines[] = "Tidak ada tunggakan dari bulan sebelumnya.";
                }
            } else {
                $newLines[] = $line;
            }
        }
        
        return implode("\n", $newLines);
    }

    public function showTagihan($id)
    {
        $tagihan = Tagihan::with(['kategori', 'subKategori'])->findOrFail($id);
        
        // Hitung nominal bulan ini (tanpa carry-over)
        $nominalBulanIni = $this->calculateNominalBulanIni($tagihan);
        
        return response()->json([
            'id' => $tagihan->id,
            'nik' => $tagihan->nik,
            'kategori_id' => $tagihan->kategori_id,
            'sub_kategori_id' => $tagihan->sub_kategori_id,
            'nominal' => $tagihan->nominal, // Total nominal (dengan carry-over)
            'nominal_bulan_ini' => $nominalBulanIni, // Nominal bulan ini saja
            'keterangan' => $tagihan->keterangan,
            'status' => $tagihan->status,
            'tanggal' => $tagihan->tanggal->format('Y-m-d'),
            'kategori' => $tagihan->kategori,
            'sub_kategori' => $tagihan->subKategori
        ]);
    }

    private function calculateNominalBulanIni($tagihan)
    {
        // Jika keterangan mengandung "Carry-over", berarti ada carry-over
        if (strpos($tagihan->keterangan, 'Carry-over tunggakan bulan sebelumnya') !== false) {
            // Parse keterangan untuk mendapatkan nominal basis
            preg_match('/Basis dari bulan .* dengan nominal: Rp ([0-9.,]+)/', $tagihan->keterangan, $matches);
            if (isset($matches[1])) {
                $nominalBasis = (float) str_replace(['.', ','], ['', '.'], $matches[1]);
                return $nominalBasis;
            }
        }
        
        // Jika tidak ada carry-over, nominal bulan ini = total nominal
        return $tagihan->nominal;
    }

    public function updateStatus(Request $request, $id)
    {
        $tagihan = Tagihan::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,lunas,belum_lunas'
        ]);

        $tagihan->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diperbarui',
            'status' => $tagihan->status
        ]);
    }

    public function destroyTagihan($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        $tagihan->delete();
        return redirect()->back()->with('success', 'Tagihan berhasil dihapus');
    }

    // AJAX methods
    public function getSubKategorisByKategori($kategoriId)
    {
        $subKategoris = SubKategoriTagihan::where('kategori_id', $kategoriId)->get();
        return response()->json($subKategoris);
    }
}
