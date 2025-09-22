<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use App\Models\KesenianBudaya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KesenianBudayaController extends Controller
{
    private array $allowedJenis = [
        'Kuda Lumping','Kobrosiswo','Warokan','Topeng ireng','Wayang','Janen/Sholawatan',
        'Kosidah/Nasyidariyah','Dolilak','Rebana','Lainnya'
    ];

    public function index(Request $request)
    {
        $user = Auth::guard('web')->user();
        $search = $request->input('search');
        $jenis = $request->input('jenis');
        $query = KesenianBudaya::where('user_id', $user->id);
        if (!empty($jenis) && in_array($jenis, $this->allowedJenis)) {
            $query->where('jenis', $jenis);
        }
        if (!empty($search)) {
            $query->where(function($q) use ($search){
                $q->where('nama','like',"%{$search}%")
                  ->orWhere('alamat','like',"%{$search}%")
                  ->orWhere('kontak','like',"%{$search}%");
            });
        }
        $items = $query->orderBy('created_at','desc')->paginate(10);
        $allowedJenis = $this->allowedJenis;
        return view('admin.desa.kesenian-budaya.index', compact('items','search','jenis','allowedJenis'));
    }

    public function create()
    {
        $allowedJenis = $this->allowedJenis;
        return view('admin.desa.kesenian-budaya.create', compact('allowedJenis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|in:Kuda Lumping,Kobrosiswo,Warokan,Topeng ireng,Wayang,Janen/Sholawatan,Kosidah/Nasyidariyah,Dolilak,Rebana,Lainnya',
            'nama' => 'required|string|max:255',
            'tag_lokasi' => 'nullable|string|max:100',
            'alamat' => 'nullable|string|max:255',
            'kontak' => 'nullable|string|max:255',
        ]);
        $validated['user_id'] = Auth::guard('web')->id();
        KesenianBudaya::create($validated);
        return redirect()->route('admin.desa.kesenian-budaya.index')->with('success','Data kesenian & budaya ditambahkan');
    }

    public function edit(KesenianBudaya $kesenianBudaya)
    {
        $this->authorizeOwner($kesenianBudaya);
        $allowedJenis = $this->allowedJenis;
        return view('admin.desa.kesenian-budaya.edit', ['item' => $kesenianBudaya, 'allowedJenis' => $allowedJenis]);
    }

    public function update(Request $request, KesenianBudaya $kesenianBudaya)
    {
        $this->authorizeOwner($kesenianBudaya);
        $validated = $request->validate([
            'jenis' => 'required|in:Kuda Lumping,Kobrosiswo,Warokan,Topeng ireng,Wayang,Janen/Sholawatan,Kosidah/Nasyidariyah,Dolilak,Rebana,Lainnya',
            'nama' => 'required|string|max:255',
            'tag_lokasi' => 'nullable|string|max:100',
            'alamat' => 'nullable|string|max:255',
            'kontak' => 'nullable|string|max:255',
        ]);
        $kesenianBudaya->update($validated);
        return redirect()->route('admin.desa.kesenian-budaya.index')->with('success','Data kesenian & budaya diperbarui');
    }

    public function destroy(KesenianBudaya $kesenianBudaya)
    {
        $this->authorizeOwner($kesenianBudaya);
        $kesenianBudaya->delete();
        return redirect()->route('admin.desa.kesenian-budaya.index')->with('success','Data kesenian & budaya dihapus');
    }

    private function authorizeOwner(KesenianBudaya $item): void
    {
        if ($item->user_id !== Auth::guard('web')->id()) {
            abort(403);
        }
    }
}


