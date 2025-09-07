<?php

namespace App\Http\Controllers\adminDesa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Administration;
use App\Models\Kehilangan;
use App\Models\SKCK;
use App\Models\Domisili;
use App\Models\DomisiliUsaha;
use App\Models\AhliWaris;
use App\Models\Kelahiran;
use App\Models\Kematian;
use App\Models\IzinKeramaian;
use App\Models\RumahSewa;

class AllLettersController extends Controller
{
    /**
     * Tampilkan daftar semua surat milik desa admin yang login
     */
    public function index(Request $request)
    {
        $this->authorizeAdminDesa();

        $villageId = Auth::user()->villages_id;
        $search = $request->get('search');
        $type = $request->get('type'); // jenis surat spesifik

        // Siapkan mapping model dan kolom pencarian dasar agar konsisten
        $sources = [
            'administrasi' => [Administration::query()->where('village_id', $villageId), ['nik', 'full_name', 'statement_content', 'purpose', 'signing'], 'Administrasi Umum'],
            'kehilangan' => [Kehilangan::query()->where('village_id', $villageId), ['full_name', 'description', 'location'], 'Kehilangan'],
            'skck' => [SKCK::query()->where('village_id', $villageId), ['full_name', 'purpose'], 'SKCK'],
            'domisili' => [Domisili::query()->where('village_id', $villageId), ['full_name', 'address'], 'Domisili'],
            'domisili_usaha' => [DomisiliUsaha::query()->where('village_id', $villageId), ['owner_name', 'business_name', 'business_address'], 'Domisili Usaha'],
            'ahli_waris' => [AhliWaris::query()->where('village_id', $villageId), ['heir_name', 'deceased_name'], 'Ahli Waris'],
            'kelahiran' => [Kelahiran::query()->where('village_id', $villageId), ['child_name', 'father_name', 'mother_name'], 'Kelahiran'],
            'kematian' => [Kematian::query()->where('village_id', $villageId), ['deceased_name', 'cause_of_death'], 'Kematian'],
            'keramaian' => [IzinKeramaian::query()->where('village_id', $villageId), ['organizer_name', 'event_name', 'location'], 'Izin Keramaian'],
            'rumah_sewa' => [RumahSewa::query()->where('village_id', $villageId), ['full_name', 'rental_address'], 'Rumah Sewa'],
        ];

        $items = collect();

        $applySearch = function ($query, array $columns, ?string $keyword) {
            if ($keyword) {
                $query->where(function ($q) use ($columns, $keyword) {
                    foreach ($columns as $col) {
                        $q->orWhere($col, 'like', "%{$keyword}%");
                    }
                });
            }
        };

        // Jika filter type dipilih, hanya ambil dari satu sumber untuk efisiensi
        if ($type && isset($sources[$type])) {
            [$query, $columns, $label] = $sources[$type];
            $applySearch($query, $columns, $search);
            $paginated = $query->latest()->paginate(10)->through(function ($row) use ($type, $label) {
                return [
                    'type' => $type,
                    'type_label' => $label,
                    'id' => $row->id,
                    'nik' => $row->nik ?? null,
                    'full_name' => $row->full_name ?? ($row->owner_name ?? ($row->child_name ?? ($row->deceased_name ?? ($row->organizer_name ?? null)))),
                    'purpose' => $row->purpose ?? ($row->statement_content ?? ($row->event_name ?? null)),
                    'letter_date' => $row->letter_date ?? $row->created_at,
                    'is_accepted' => $row->is_accepted ?? null,
                ];
            });

            return view('admin.desa.surat.index', [
                'items' => $paginated,
                'filters' => [
                    'search' => $search,
                    'type' => $type,
                ],
            ]);
        }

        // Jika tanpa filter type, ambil sebagian dari masing-masing sumber lalu gabungkan dan paginasi manual
        foreach ($sources as $key => [$query, $columns, $label]) {
            $applySearch($query, $columns, $search);
            $rows = $query->latest()->limit(50)->get()->map(function ($row) use ($key, $label) {
                return [
                    'type' => $key,
                    'type_label' => $label,
                    'id' => $row->id,
                    'nik' => $row->nik ?? null,
                    'full_name' => $row->full_name ?? ($row->owner_name ?? ($row->child_name ?? ($row->deceased_name ?? ($row->organizer_name ?? null)))),
                    'purpose' => $row->purpose ?? ($row->statement_content ?? ($row->event_name ?? null)),
                    'letter_date' => $row->letter_date ?? $row->created_at,
                    'is_accepted' => $row->is_accepted ?? null,
                ];
            });
            $items = $items->merge($rows);
        }

        $items = $items->sortByDesc('letter_date');

        // Paginate collection manually
        $perPage = 10;
        $currentPage = max((int) $request->get('page', 1), 1);
        $total = $items->count();
        $currentItems = $items->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.desa.surat.index', [
            'items' => $paginator,
            'filters' => [
                'search' => $search,
                'type' => $type,
            ],
        ]);
    }

    private function authorizeAdminDesa(): void
    {
        if (!Auth::check() || Auth::user()->role !== 'admin desa') {
            abort(403, 'Unauthorized');
        }
    }
}


