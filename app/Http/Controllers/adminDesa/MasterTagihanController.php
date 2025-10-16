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
        // status filter dihapus sesuai permintaan

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
        $filterKategori = $request->input('filter_kategori');
        $filterSubKategori = $request->input('filter_sub_kategori');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Ambil penduduk desa untuk lookup nama & KK (via CitizenService)
        $penduduksResponse = $this->citizenService->getCitizensByVillageId($villagesId, 1, 10000);
        $penduduks = $penduduksResponse['data']['citizens'] ?? [];
        // Build lookup yang robust: gunakan kunci NIK sebagai string
        $pendudukLookup = collect($penduduks)->mapWithKeys(function ($p) {
            $nik = isset($p['nik']) ? (string) $p['nik'] : '';
            return [$nik => $p];
        });

        // Get tagihans
        $tagihansQuery = Tagihan::with(['kategori', 'subKategori'])
            ->where('villages_id', $villagesId);
        if (!empty($searchTagihan)) {
            $term = trim($searchTagihan);

            // Siapkan daftar NIK berdasarkan kecocokan nama atau KK dari CitizenService
            $niksByName = collect($penduduks)
                ->filter(function ($c) use ($term) {
                    return str_contains(strtolower($c['full_name'] ?? ''), strtolower($term));
                })
                ->pluck('nik')
                ->all();

            $niksByKK = collect($penduduks)
                ->filter(function ($c) use ($term) {
                    return str_contains((string)($c['kk'] ?? ''), (string)$term);
                })
                ->pluck('nik')
                ->all();

            $nikPool = array_values(array_unique(array_merge($niksByName, $niksByKK)));

            $tagihansQuery->where(function ($q) use ($term, $nikPool) {
                // cari pada keterangan
                $q->where('keterangan', 'like', "%{$term}%")
                  // cari pada NIK
                  ->orWhere('nik', 'like', "%{$term}%")
                  // cari berdasarkan nama penduduk (mapping NIK dari citizen service)
                  ->orWhereIn('nik', $nikPool);
            });
        }
        
        // Filter kategori (opsional)
        if (!empty($filterKategori)) {
            $tagihansQuery->where('kategori_id', $filterKategori);
        }
        
        // Filter sub kategori (opsional)
        if (!empty($filterSubKategori)) {
            $tagihansQuery->where('sub_kategori_id', $filterSubKategori);
        }
        
        // Filter tanggal (opsional)
        if (!empty($startDate) && !empty($endDate)) {
            $tagihansQuery->whereDate('tanggal', '>=', $startDate)
                          ->whereDate('tanggal', '<=', $endDate);
        } elseif (!empty($startDate)) {
            $tagihansQuery->whereDate('tanggal', '>=', $startDate);
        } elseif (!empty($endDate)) {
            $tagihansQuery->whereDate('tanggal', '<=', $endDate);
        }
        $tagihans = $tagihansQuery->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());

        // Lengkapi lookup untuk NIK yang tidak ditemukan di cache CitizenService desa (fallback by NIK)
        try {
            $neededNiks = collect($tagihans->items())
                ->pluck('nik')
                ->map(fn($n) => (string) $n)
                ->unique()
                ->values();

            $missingNiks = $neededNiks->filter(function ($nik) use ($pendudukLookup) {
                return !$pendudukLookup->has($nik);
            });

            foreach ($missingNiks as $nik) {
                $detail = $this->citizenService->getCitizenByNIK($nik);
                if (is_array($detail) && isset($detail['data']) && is_array($detail['data'])) {
                    $data = $detail['data'];
                    // Normalisasi bentuk agar konsisten dengan struktur lain
                    $pendudukLookup->put((string)($data['nik'] ?? $nik), [
                        'nik' => (string)($data['nik'] ?? $nik),
                        'full_name' => $data['full_name'] ?? ($data['name'] ?? ($data['nama_lengkap'] ?? '')),
                        'kk' => $data['kk'] ?? ($data['no_kk'] ?? null),
                        'address' => $data['address'] ?? ($data['alamat'] ?? null),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // abaikan fallback error; tampilan akan tetap menampilkan NIK
        }

        // Get kategoris for dropdowns with subKategoris relationship
        $kategoris = KategoriTagihan::with('subKategoris')->orderBy('nama_kategori')->get();

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

    /**
     * Halaman form pembuatan tagihan multiple
     */
    public function createMultiple()
    {
        $user = Auth::guard('web')->user();
        $villagesId = $user->villages_id;

        // Get kategoris for dropdowns
        $kategoris = KategoriTagihan::orderBy('nama_kategori')->get();

        // Get penduduks for checklist - ambil semua tanpa pagination
        $penduduksResponse = $this->citizenService->getCitizensByVillageId($villagesId, 1, 10000);
        $penduduks = $penduduksResponse['data']['citizens'] ?? [];

        return view('admin.desa.master-tagihan.create-multiple', compact(
            'kategoris', 
            'penduduks'
        ));
    }

    /**
     * Store multiple tagihans
     */
    public function storeMultiple(Request $request)
    {
        $user = Auth::guard('web')->user();
        
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_tagihans,id',
            'sub_kategori_id' => 'required|exists:sub_kategori_tagihans,id',
            'nominal' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:pending,lunas,belum_lunas',
            'tanggal' => 'required|date',
            'selected_niks' => 'required|array|min:1',
            'selected_niks.*' => 'required|string'
        ]);

        $createdCount = 0;
        $errors = [];

        foreach ($validated['selected_niks'] as $nik) {
            try {
                $tagihanData = [
                    'nik' => $nik,
                    'kategori_id' => $validated['kategori_id'],
                    'sub_kategori_id' => $validated['sub_kategori_id'],
                    'nominal' => $validated['nominal'],
                    'keterangan' => $validated['keterangan'],
                    'status' => $validated['status'],
                    'tanggal' => $validated['tanggal'],
                    'villages_id' => $user->villages_id
                ];

                Tagihan::create($tagihanData);
                $createdCount++;
            } catch (\Exception $e) {
                $errors[] = "Gagal membuat tagihan untuk NIK {$nik}: " . $e->getMessage();
            }
        }

        if ($createdCount > 0) {
            $message = "Berhasil membuat {$createdCount} tagihan";
            if (count($errors) > 0) {
                $message .= ". Terdapat " . count($errors) . " error: " . implode(', ', $errors);
            }
            return redirect()->route('admin.desa.master-tagihan.tagihan.index')
                ->with('success', $message);
        } else {
            return redirect()->back()
                ->with('error', 'Gagal membuat tagihan: ' . implode(', ', $errors))
                ->withInput();
        }
    }

    // AJAX methods
    public function getSubKategorisByKategori($kategoriId)
    {
        $subKategoris = SubKategoriTagihan::where('kategori_id', $kategoriId)->get();
        return response()->json($subKategoris);
    }
}
