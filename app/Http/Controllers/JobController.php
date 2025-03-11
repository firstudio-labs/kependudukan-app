<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\JobService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class JobController extends Controller
{
    protected $jobService;

    public function __construct(JobService $jobService)
    {
        $this->jobService = $jobService;
    }

    public function index()
    {
        // Get all jobs from the service
        $allJobs = $this->jobService->getAllJobs();

        // Convert array to collection and paginate
        $perPage = 10; // Number of items per page
        $currentPage = request()->query('page', 1);
        $jobs = new LengthAwarePaginator(
            collect($allJobs)->forPage($currentPage, $perPage),
            count($allJobs),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('superadmin.datamaster.job.index', compact('jobs'));
    }

    public function create()
    {
        return view('superadmin.datamaster.job.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required',
            'name' => 'required',
        ]);

        $response = $this->jobService->createJob($validatedData);

        if ($response['status'] === 'CREATED') {
            return redirect()->route('superadmin.datamaster.job.index')->with('success', $response['message']);
        } else {
            return back()->with('error', $response['message']);
        }
    }

    public function edit($id)
    {
        Log::info("Mencoba mengedit job dengan ID: " . $id);

        try {
            $job = $this->jobService->getJobById($id);

            Log::info("Response dari service: ", ['job' => $job]);

            if (!$job || !isset($job['id'])) {
                Log::error("Job tidak ditemukan untuk ID: " . $id);
                return redirect()->route('superadmin.datamaster.job.index')
                               ->with('error', 'Data pekerjaan tidak ditemukan');
            }

            return view('superadmin.datamaster.job.edit', compact('job'));
        } catch (\Exception $e) {
            Log::error("Error saat mengambil data job: " . $e->getMessage());
            return redirect()->route('superadmin.datamaster.job.index')
                           ->with('error', 'Terjadi kesalahan saat mengambil data');
        }
    }

    public function update(Request $request, $id)
    {
        Log::info("Mencoba update job ID {$id} dengan data: ", $request->all());

        $validatedData = $request->validate([
            'code' => 'required',
            'name' => 'required',
        ]);

        Log::info("Setelah validasi, data dikirim ke API: ", $validatedData);

        $response = $this->jobService->updateJob($id, $validatedData);

        Log::info("Response dari API: ", $response);

        if (isset($response['status']) && $response['status'] === 'OK') {
            return redirect()->route('superadmin.datamaster.job.index')->with('success', $response['message']);
        } else {
            return back()->with('error', $response['message'] ?? 'Gagal memperbarui data.');
        }
    }

    public function destroy($id)
    {
        $response = $this->jobService->deleteJob($id);

        if ($response['status'] === 'OK') {
            return redirect()->route('superadmin.datamaster.job.index')->with('success', $response['message']);
        } else {
            return redirect()->route('superadmin.datamaster.job.index')->with('error', $response['message']);
        }
    }
}
