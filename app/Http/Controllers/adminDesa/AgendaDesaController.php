<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\AgendaDesa;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Jobs\PreloadCacheJob;

class AgendaDesaController extends Controller
{
    protected $cacheStore;

    public function __construct()
    {
        $this->cacheStore = $this->getCacheStore();
    }

    /**
     * Get cache store - prefer Redis jika tersedia, fallback ke default
     */
    private function getCacheStore()
    {
        try {
            if (config('cache.default') === 'redis' || config('cache.stores.redis')) {
                return Cache::store('redis');
            }
        } catch (\Exception $e) {
            Log::warning('Redis tidak tersedia, menggunakan default cache: ' . $e->getMessage());
        }
        return Cache::store(config('cache.default', 'file'));
    }

    public function index()
    {
        $user = Auth::user();
        $agendas = AgendaDesa::where('user_id', $user->id)->latest()->paginate(10);
        return view('admin.desa.agenda.index', compact('agendas'));
    }

    public function create()
    {
        return view('admin.desa.agenda.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|max:2048',
            'deskripsi' => 'required|string',
            'alamat' => 'nullable|string',
            'tag_lokasi' => 'nullable|string',
        ]);

        $agenda = new AgendaDesa($validated);
        $agenda->user_id = $user->id;
        $agenda->province_id = $user->province_id;
        $agenda->districts_id = $user->districts_id;
        $agenda->sub_districts_id = $user->sub_districts_id;
        $agenda->villages_id = $user->villages_id;

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = 'agenda_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('images/agenda', $filename, 'public');
            $agenda->gambar = $path;
        }

        $agenda->save();

        // Clear cache untuk agenda desa
        $this->clearAgendaDesaCache($user->villages_id);
        
        // Dispatch job untuk pre-warm cache di background
        PreloadCacheJob::dispatch('agenda_desa', $user->villages_id)->onQueue('default');

        return redirect()->route('admin.desa.agenda.index')->with('success', 'Agenda berhasil dibuat');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $agenda = AgendaDesa::where('user_id', $user->id)->findOrFail($id);
        return view('admin.desa.agenda.edit', compact('agenda'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $agenda = AgendaDesa::where('user_id', $user->id)->findOrFail($id);
        return view('admin.desa.agenda.show', compact('agenda'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $agenda = AgendaDesa::where('user_id', $user->id)->findOrFail($id);
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|max:2048',
            'deskripsi' => 'required|string',
            'alamat' => 'nullable|string',
            'tag_lokasi' => 'nullable|string',
        ]);

        $agenda->fill($validated);

        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            if ($agenda->gambar && Storage::disk('public')->exists($agenda->gambar)) {
                Storage::disk('public')->delete($agenda->gambar);
            }
            $filename = 'agenda_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('images/agenda', $filename, 'public');
            $agenda->gambar = $path;
        }

        $agenda->save();

        // Clear cache untuk agenda desa
        $this->clearAgendaDesaCache($agenda->villages_id);
        
        // Dispatch job untuk pre-warm cache di background
        PreloadCacheJob::dispatch('agenda_desa', $agenda->villages_id)->onQueue('default');

        return redirect()->route('admin.desa.agenda.index')->with('success', 'Agenda berhasil diperbarui');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $agenda = AgendaDesa::where('user_id', $user->id)->findOrFail($id);
        if ($agenda->gambar && Storage::disk('public')->exists($agenda->gambar)) {
            Storage::disk('public')->delete($agenda->gambar);
        }
        $villageId = $agenda->villages_id;
        $agenda->delete();
        
        // Clear cache untuk agenda desa
        $this->clearAgendaDesaCache($villageId);
        
        return redirect()->route('admin.desa.agenda.index')->with('success', 'Agenda berhasil dihapus');
    }

    /**
     * Clear cache untuk agenda desa berdasarkan village_id
     */
    private function clearAgendaDesaCache($villageId)
    {
        if (!$villageId) return;
        
        // Simpan daftar cache keys yang perlu di-clear
        $cacheKeysList = $this->cacheStore->get("agenda_desa_cache_keys_{$villageId}", []);
        
        // Clear semua cache keys yang tersimpan
        foreach ($cacheKeysList as $key) {
            $this->cacheStore->forget($key);
        }
        
        // Reset daftar cache keys
        $this->cacheStore->forget("agenda_desa_cache_keys_{$villageId}");
    }
}


