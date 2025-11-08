<?php

namespace App\Http\Controllers\Web\Management;

use App\Http\Controllers\Controller;
use App\Models\Citizen;
use Illuminate\Http\Request;
use App\Models\Housing;
use App\Models\User;
use App\Models\Village;
use App\Models\Subdistrict;
use App\Models\District;
use App\Models\Province;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class HousingController extends Controller
{
    public function dashboard()
    {
        // === Data Total ===
        $totalUsers = User::count();
        $totalHousings = Housing::count();
        $totalCitizens = Citizen::count();

        // === Driver Database ===
        $driver = DB::getDriverName();

        $monthQuery = match ($driver) {
            'mysql'  => "MONTH(created_at)",
            'pgsql'  => "EXTRACT(MONTH FROM created_at)",
            'sqlite' => "strftime('%m', created_at)",
            default  => "MONTH(created_at)",
        };

        $yearQuery = match ($driver) {
            'mysql'  => "YEAR(created_at)",
            'pgsql'  => "EXTRACT(YEAR FROM created_at)",
            'sqlite' => "strftime('%Y', created_at)",
            default  => "YEAR(created_at)",
        };

        $currentYear = now()->year;

        // === Pertumbuhan per Bulan ===
        $housingGrowth = Housing::selectRaw("$monthQuery as month, COUNT(*) as count")
            ->whereRaw("$yearQuery = ?", [$currentYear])
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $userGrowthByMonth = User::selectRaw("$monthQuery as month, COUNT(*) as count")
            ->whereRaw("$yearQuery = ?", [$currentYear])
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $citizenGrowth = Citizen::selectRaw("$monthQuery as month, COUNT(*) as count")
            ->whereRaw("$yearQuery = ?", [$currentYear])
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        // === Format bulan (Jan–Dec) ===
        $months = collect(range(1, 12))
            ->map(fn($m) => now()->month($m)->format('M'))
            ->toArray();

        // === Data Sales Volume (Bar Chart) ===
        $salesVolume = [
            'labels' => $months,
            'data' => [
                'Perumahan' => array_values(array_replace(array_fill(1, 12, 0), $housingGrowth)),
                'Pengguna'  => array_values(array_replace(array_fill(1, 12, 0), $userGrowthByMonth)),
                'Warga'     => array_values(array_replace(array_fill(1, 12, 0), $citizenGrowth)),
            ],
            'colors' => ['#EF4444', '#10B981', '#3B82F6'],
        ];

        // === Pertumbuhan per Tahun (Line Chart) ===
        $userGrowth = User::selectRaw("YEAR(created_at) as year, COUNT(*) as count")
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->map(fn($row) => [
                'year' => (int) $row->year,
                'count' => (int) $row->count,
            ])
            ->toArray();

        // === Customer Volume (Doughnut Chart) ===
        $customerVolume = [
            'labels' => ['Perumahan', 'Pengguna', 'Warga'],
            'data' => [$totalHousings, $totalUsers, $totalCitizens],
            'colors' => ['#EF4444', '#10B981', '#3B82F6'],
        ];

        // === Cards Statistik ===
        $stats = [
            [
                'title' => 'Total Pengguna',
                'value' => $totalUsers,
                'change' => '',
                'icon'  => '👥',
                'color' => 'bg-blue-100 text-blue-600',
            ],
            [
                'title' => 'Total Perumahan',
                'value' => $totalHousings,
                'change' => '',
                'icon'  => '🏠',
                'color' => 'bg-green-100 text-green-600',
            ],
            [
                'title' => 'Total Warga',
                'value' => $totalCitizens,
                'change' => '',
                'icon'  => '🙎🏻‍♂️🙍🏼‍♀️',
                'color' => 'bg-yellow-100 text-yellow-600',
            ],
            [
                'title' => 'Tahun Aktif',
                'value' => $currentYear,
                'change' => '',
                'icon'  => '🪩',
                'color' => 'bg-gray-100 text-gray-600',
            ],
        ];

        // 🔹 Kirim semua data yang dibutuhkan ke view
        return view('admin.dashboard', compact(
            'stats',
            'customerVolume',
            'salesVolume',
            'userGrowth', // <== ini dikirim ke view untuk chart line
        ));
    }

    public function index(Request $request)
    {
        $query = Housing::with(['province', 'district', 'subdistrict', 'village']);

        // Filter pencarian
        if ($search = $request->get('search')) {
            $query->where('housing_name', 'like', "%{$search}%")->orWhere('address', 'like', "%{$search}%");
        }

        $housings = $query->paginate(10)->withQueryString();

        return view('admin.housings.index', compact('housings', 'search'));
    }

    public function create()
    {
        $villages = Village::all();
        $subdistricts = Subdistrict::all();
        $districts = District::all();
        $provinces = Province::all();

        return view('admin.housings.create', compact('villages', 'subdistricts', 'districts', 'provinces'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'housing_name' => 'required',
            'address' => 'required',
            'rt' => 'required',
            'rw' => 'required',
            'village_code' => 'required',
            'subdistrict_code' => 'required',
            'district_code' => 'required',
            'province_code' => 'required',
            'postal_code' => 'required',
        ]);

        Housing::create($validated);
        return redirect()->route('housings.index')->with('success', 'Data perumahan berhasil ditambahkan');
    }

    public function edit($id)
    {
        try {
            $housing = Housing::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $housing,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan atau error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $housing = Housing::with(['province', 'district', 'subdistrict', 'village'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $housing
        ]);
    }

    public function update(Request $request, $id)
    {
        $housing = Housing::findOrFail($id);

        $validated = $request->validate([
            'housing_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'rt' => 'nullable|string|max:10',
            'rw' => 'nullable|string|max:10',
            'province_code' => 'required|string',
            'district_code' => 'required|string',
            'subdistrict_code' => 'required|string',
            'village_code' => 'required|string',
            'postal_code' => 'required|string|max:10',
        ]);

        $housing->update($validated);

        // 🔍 Cek apakah request datang dari AJAX (fetch)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui!'
            ]);
        }

        // 🔁 Kalau bukan dari AJAX, pakai session seperti create/delete
        return redirect()
            ->back()
            ->with('updated', 'Data berhasil diperbarui!');
    }

    public function destroy(Housing $housing)
    {
        $housing->delete();
        return redirect()->route('housings.index')->with('success', 'Data perumahan berhasil dihapus');
    }

    public function residents($id)
    {
        $housing = Housing::with([
            'houses.familyCard.citizens' => function ($query) {
                $query->select(
                    'id',
                    'family_card_id',
                    'citizen_card_number',
                    'fullname',
                    'birth_place',
                    'birth_date',
                    'gender',
                    'religion',
                    'marital_status',
                    'work_type',
                    'education_type'
                );
            }
        ])->findOrFail($id);

        // 🔹 Ambil semua warga dari setiap rumah
        $allResidents = $housing->houses
            ->flatMap(fn($house) => $house->familyCard?->citizens ?? collect())
            ->sortBy('fullname') // optional, biar urut rapi
            ->values();

        // 🔹 Manual paginate
        $page = request()->get('page', 1);
        $perPage = 10;
        $items = $allResidents->forPage($page, $perPage);
        $residents = new LengthAwarePaginator(
            $items,
            $allResidents->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.housings.residents', compact('housing', 'residents'));
    }
}
