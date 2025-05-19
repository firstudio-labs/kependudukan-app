<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaporanDesa;
use Illuminate\Support\Facades\Auth;

class LaporanDesaController extends Controller
{
    public function index(Request $request)
    {
        // Get the current admin desa's village ID
        $villageId = Auth::user()->villages_id;

        // Start query and only include reports for this village
        $query = LaporanDesa::with('laporDesa')
            ->where('village_id', $villageId);

        // Filter berdasarkan status (only apply if status has a non-empty value)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan pencarian
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_laporan', 'like', "%{$search}%")
                    ->orWhere('deskripsi_laporan', 'like', "%{$search}%")
                    ->orWhereHas('laporDesa', function ($q) use ($search) {
                        $q->where('bidang', 'like', "%{$search}%")
                            ->orWhere('ruang_lingkup', 'like', "%{$search}%");
                    });
            });
        }

        $laporans = $query->latest()->paginate(10);

        // Jika request meminta format JSON (untuk update status count cards)
        if ($request->has('format') && $request->format === 'json') {
            $statusCounts = [
                'Menunggu' => LaporanDesa::where('village_id', $villageId)->where('status', 'Menunggu')->count(),
                'Diproses' => LaporanDesa::where('village_id', $villageId)->where('status', 'Diproses')->count(),
                'Selesai' => LaporanDesa::where('village_id', $villageId)->where('status', 'Selesai')->count(),
                'Ditolak' => LaporanDesa::where('village_id', $villageId)->where('status', 'Ditolak')->count(),
            ];

            return response()->json([
                'status' => 'success',
                'statusCounts' => $statusCounts
            ]);
        }

        return view('admin.desa.laporan-desa.index', compact('laporans'));
    }

    public function show($id)
    {
        // Get the current admin desa's village ID
        $villageId = Auth::user()->villages_id;

        // Find report and ensure it belongs to the admin's village
        $laporan = LaporanDesa::with('laporDesa')
            ->where('village_id', $villageId)
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $laporan
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Menunggu,Diproses,Selesai,Ditolak'
        ]);

        // Get the current admin desa's village ID
        $villageId = Auth::user()->villages_id;

        // Find report and ensure it belongs to the admin's village
        $laporan = LaporanDesa::where('village_id', $villageId)
            ->findOrFail($id);

        $laporan->status = $request->status;
        $laporan->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status laporan berhasil diperbarui'
        ]);
    }

    public function destroy($id)
    {
        // Get the current admin desa's village ID
        $villageId = Auth::user()->villages_id;

        // Find report and ensure it belongs to the admin's village
        $laporan = LaporanDesa::where('village_id', $villageId)
            ->findOrFail($id);

        $laporan->delete();

        return redirect()->route('admin.desa.laporan-desa.index')
            ->with('success', 'Laporan berhasil dihapus');
    }
}
