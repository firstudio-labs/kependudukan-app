<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BeritaDesa;
use Illuminate\Http\Request;

class BeritaDesaController extends Controller
{
    public function index(Request $request)
    {
        $query = BeritaDesa::with('user');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $berita = $query->latest()->paginate(10);
        return view('user.berita-desa.index', compact('berita'));
    }

    public function show($id)
    {
        $berita = BeritaDesa::with('user')->findOrFail($id);
        return response()->json(['data' => $berita]);
    }
}