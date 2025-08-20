<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use App\Models\ProfileChangeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CitizenService;

class BiodataApprovalController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');

        $query = ProfileChangeRequest::query()->where('village_id', $user->villages_id);
        if ($status) {
            $query->where('status', $status);
        }

        $requests = $query->orderByDesc('created_at')->paginate(20);

        return view('admin.desa.biodata-approval.index', compact('requests'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $requestModel = ProfileChangeRequest::findOrFail($id);
        abort_unless($requestModel->village_id === $user->villages_id, 403);
        return view('admin.desa.biodata-approval.show', compact('requestModel'));
    }

    public function approve(Request $request, $id)
    {
        $user = Auth::user();
        $requestModel = ProfileChangeRequest::findOrFail($id);
        abort_unless($requestModel->village_id === $user->villages_id, 403);
        abort_unless($requestModel->status === 'pending', 400, 'Status tidak valid');

        $citizenService = app(CitizenService::class);
        $updateResult = $citizenService->updateCitizen($requestModel->nik, $requestModel->requested_changes ?? []);

        if (!is_array($updateResult) || ($updateResult['status'] ?? 'ERROR') === 'ERROR') {
            return back()->with('error', 'Gagal menerapkan perubahan ke data penduduk');
        }

        $requestModel->status = 'approved';
        $requestModel->reviewed_at = now();
        $requestModel->reviewed_by = $user->id;
        $requestModel->reviewer_note = $request->input('reviewer_note');
        $requestModel->save();

        return redirect()->route('admin.desa.biodata-approval.index')->with('success', 'Permintaan disetujui dan data diperbarui');
    }

    public function reject(Request $request, $id)
    {
        $user = Auth::user();
        $requestModel = ProfileChangeRequest::findOrFail($id);
        abort_unless($requestModel->village_id === $user->villages_id, 403);
        abort_unless($requestModel->status === 'pending', 400, 'Status tidak valid');

        $requestModel->status = 'rejected';
        $requestModel->reviewed_at = now();
        $requestModel->reviewed_by = $user->id;
        $requestModel->reviewer_note = $request->input('reviewer_note');
        $requestModel->save();

        return redirect()->route('admin.desa.biodata-approval.index')->with('success', 'Permintaan ditolak');
    }
}


