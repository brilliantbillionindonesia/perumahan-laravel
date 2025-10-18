<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Housing;
use App\Models\User;
use App\Models\Village;
use App\Models\Subdistrict;
use App\Models\District;
use App\Models\Province;

class ManageHousingController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalHousings = Housing::count();

        // Gunakan SQLite-safe query (tanpa fungsi YEAR)
        $userGrowth = User::selectRaw("strftime('%Y', created_at) as year, COUNT(*) as count")->groupBy('year')->orderBy('year')->get()->toArray();

        return view('admin.dashboard', compact('totalUsers', 'totalHousings', 'userGrowth'));
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
}
