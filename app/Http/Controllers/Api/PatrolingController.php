<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response as HttpStatusCodes;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Http\Services\ActivityLogService;

class PatrolingController extends Controller
{
    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'housing_id' => ['nullable', 'string'], // hanya admin
            'year' => ['required', 'integer'],
            'month' => ['required', 'integer', 'between:1,12'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
            'search' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => $validator->errors()->first(),
                ],
                HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $year = (int) $request->input('year');
        $month = (int) $request->input('month');
        $page = (int) $request->input('page', 1);
        $perPage = (int) $request->input('per_page', 31);
        $housingId = $request->input('housing_id', null);

        $startOfMonth = sprintf('%04d-%02d-01', $year, $month);
        $endOfMonth = date('Y-m-t', strtotime($startOfMonth));

        $builder = DB::table('patrollings as p')
            ->join('citizens as c', 'c.id', '=', 'p.citizen_id')
            ->join('houses as h', 'h.id', '=', 'p.house_id')
            ->where('p.housing_id', $housingId)
            ->whereBetween('p.patrol_date', [$startOfMonth, $endOfMonth])
            ->whereNull('p.deleted_at')
            ->select('p.patrol_date as date', 'p.presence', 'p.note', 'p.replaced_by', 'c.id as citizen_id', 'c.fullname as citizen_name', 'h.id as house_id', 'h.block', 'h.number')
            ->orderBy('p.patrol_date', 'asc');

        $results = $builder->get();

        // 🧩 Kelompokkan berdasarkan tanggal
        $grouped = $results
            ->groupBy('date')
            ->map(function ($items, $date) {
                return [
                    'date' => $date,
                    'member' => $items
                        ->map(function ($item) {
                            return [
                                'citizen_id' => $item->citizen_id,
                                'citizen_name' => $item->citizen_name,
                                'house_id' => $item->house_id,
                                'block' => $item->block,
                                'number' => $item->number,
                                'presence' => $item->presence,
                                'note' => $item->note,
                                'replaced_by' => $item->replaced_by,
                            ];
                        })
                        ->values(),
                ];
            })
            ->values();

        // 🧮 Pagination manual setelah digroup
        $paginated = $grouped->forPage($page, $perPage)->values();

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Berhasil menampilkan data',
            'data' => $paginated,
        ]);
    }

    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'housing_id' => ['nullable', 'string'], // hanya admin
            'date' => ['required', 'date_format:Y-m-d'],
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => $validator->errors()->first(),
                ],
                HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $date = $request->input('date');
        $housingId = $request->input('housing_id', null);

        // Query utama
        $builder = DB::table('patrollings as p')->join('citizens as c', 'c.id', '=', 'p.citizen_id')->join('houses as h', 'h.id', '=', 'p.house_id')->where('p.housing_id', $housingId)->whereDate('p.patrol_date', $date)->whereNull('p.deleted_at')->select('p.patrol_date as date', 'p.presence', 'p.note', 'p.replaced_by', 'c.id as citizen_id', 'c.fullname as citizen_name', 'h.id as house_id', 'h.block', 'h.number')->orderBy('h.block')->orderBy('h.number');

        $results = $builder->get();

        if ($results->isEmpty()) {
            return response()->json(
                [
                    'success' => true,
                    'code' => HttpStatusCodes::HTTP_OK,
                    'message' => 'Tidak ada data ronda untuk tanggal tersebut.',
                    'data' => null,
                ],
                HttpStatusCodes::HTTP_OK,
            );
        }

        // Format hasil agar konsisten dengan struktur list()
        $members = $results
            ->map(function ($item) {
                return [
                    'citizen_id' => $item->citizen_id,
                    'citizen_name' => $item->citizen_name,
                    'is_presence' => $item->presence === 'hadir' || $item->presence === true,
                    'replaced_by' => $item->replaced_by,
                    'note' => $item->note,
                    'house_id' => $item->house_id,
                    'block' => $item->block,
                    'number' => $item->number,
                ];
            })
            ->values();

        $data = [
            'date' => $date,
            'member' => $members,
        ];

        return response()->json(
            [
                'success' => true,
                'code' => HttpStatusCodes::HTTP_OK,
                'message' => 'Berhasil menampilkan data',
                'data' => $data,
            ],
            HttpStatusCodes::HTTP_OK,
        );
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'housing_id' => ['required', 'uuid', 'exists:housings,id'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'frequency' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ],
                HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $validated = $validator->validated();
        $housingId = $validated['housing_id'];
        $frequency = $validated['frequency'] ?? 1;
        $forceRegenerate = true;

        // Hapus data lama jika force_regenerate = true
        DB::table('patrollings')->where('housing_id', $housingId)->delete();

        // Generate range tanggal
        $datesToGenerate = collect();
        $currentDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        while ($currentDate->lte($endDate)) {
            $datesToGenerate->push($currentDate->copy());
            $currentDate->addDay();
        }

        // Ambil semua kepala keluarga
        $houses = DB::table('houses')->where('housing_id', $housingId)->whereNotNull('head_citizen_id')->select('id as house_id', 'head_citizen_id as citizen_id')->get();

        if ($houses->isEmpty()) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_OK,
                    'message' => 'Tidak ada data rumah pada perumahan ini.',
                    'data' => [],
                ],
                HttpStatusCodes::HTTP_OK,
            );
        }

        $totalHouses = $houses->count();
        $totalDates = $datesToGenerate->count();

        // Acak urutan warga
        $shuffledHouses = $houses->shuffle()->values();

        $dataPatrols = [];

        if ($frequency == 1) {
            // === Logika Khusus Frequency 1 ===
            // Semua warga harus kebagian minimal 1x
            // Jika warga > jumlah hari → beberapa hari punya 2 orang

            $citizens = $shuffledHouses->toArray();

            // Hitung dasar
            $baseCountPerDay = intdiv($totalHouses, $totalDates);
            $remainder = $totalHouses % $totalDates; // sisa warga yang belum dapat

            // Semua hari dapat baseCountPerDay orang
            $extraDays = collect($datesToGenerate)->shuffle()->take($remainder);

            $index = 0;
            foreach ($datesToGenerate as $date) {
                // Hari ini dapat 1 orang, atau 2 orang kalau termasuk extra day
                $todayCount = 1 + ($extraDays->contains($date) ? 1 : 0);

                for ($i = 0; $i < $todayCount; $i++) {
                    if ($index >= $totalHouses) {
                        break;
                    }

                    $house = $citizens[$index];
                    $dataPatrols[] = [
                        'housing_id' => $housingId,
                        'citizen_id' => $house->citizen_id,
                        'house_id' => $house->house_id,
                        'patrol_date' => $date->format('Y-m-d'),
                        'presence' => null,
                        'note' => null,
                        'replaced_by' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $index++;
                }
            }
        } else {
            // === Logika Normal (frequency >= 2) ===
            $houseIndex = 0;
            foreach ($datesToGenerate as $date) {
                for ($i = 0; $i < $frequency; $i++) {
                    if ($houseIndex >= $totalHouses) {
                        $houseIndex = 0;
                    }
                    $house = $shuffledHouses[$houseIndex];
                    $dataPatrols[] = [
                        'housing_id' => $housingId,
                        'citizen_id' => $house->citizen_id,
                        'house_id' => $house->house_id,
                        'patrol_date' => $date->format('Y-m-d'),
                        'presence' => null,
                        'note' => null,
                        'replaced_by' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $houseIndex++;
                }
            }
        }

        // Simpan hasil ke database
        // Tambahkan UUID untuk setiap data sebelum insert
        $dataPatrols = collect($dataPatrols)
            ->map(function ($data) {
                $data['id'] = (string) Str::uuid(); // generate UUID manual
                return $data;
            })
            ->toArray();
        DB::table('patrollings')->insert($dataPatrols);

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => $forceRegenerate ? 'Berhasil membuat ulang jadwal ronda' : 'Berhasil membuat jadwal patroli bergilir.',
            'data' => [
                'total_inserted' => count($dataPatrols),
                'frequency_input' => $frequency,
                'total_citizens' => $totalHouses,
                'total_dates' => $totalDates,
                'days_with_extra_person' => $frequency == 1 ? $extraDays->map->format('Y-m-d')->values() : [],
            ],
        ]);
    }

    public function update(Request $request)
    {
        // 1️⃣ Validasi input
        $validator = Validator::make($request->all(), [
            'patrol_id' => ['required', 'uuid', 'exists:patrollings,id'],
            'new_citizen_id' => ['nullable', 'uuid', 'exists:citizens,id'],
            'new_patrol_date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ],
                HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $validated = $validator->validated();
        $patrolId = $validated['patrol_id'];

        // 2️⃣ Ambil data patroli yang akan diubah
        $patrol = DB::table('patrollings')->where('id', $patrolId)->first();

        if (!$patrol) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                    'message' => 'Data jadwal ronda tidak ditemukan.',
                ],
                HttpStatusCodes::HTTP_NOT_FOUND,
            );
        }

        // 3️⃣ Pastikan user yang update adalah admin housing
        $user = $request->user();
        $housingUser = DB::table('housing_users')->where('user_id', $user->id)->where('housing_id', $patrol->housing_id)->where('is_active', 1)->first();

        if (!$housingUser || $housingUser->role_code !== 'admin') {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_FORBIDDEN,
                    'message' => 'Hanya admin housing yang dapat memperbarui jadwal ronda.',
                ],
                HttpStatusCodes::HTTP_FORBIDDEN,
            );
        }

        // 4️⃣ Siapkan data yang akan diupdate
        $updateData = [];

        if (isset($validated['new_citizen_id'])) {
            $isValidCitizen = DB::table('houses')->where('housing_id', $patrol->housing_id)->where('head_citizen_id', $validated['new_citizen_id'])->exists();

            if (!$isValidCitizen) {
                return response()->json(
                    [
                        'success' => false,
                        'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                        'message' => 'Warga baru tidak valid atau bukan dari perumahan ini.',
                    ],
                    HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                );
            }

            $newHouse = DB::table('houses')->where('housing_id', $patrol->housing_id)->where('head_citizen_id', $validated['new_citizen_id'])->first();

            $updateData['citizen_id'] = $validated['new_citizen_id'];
            $updateData['house_id'] = $newHouse->id ?? $patrol->house_id;
        }

        if (isset($validated['new_patrol_date'])) {
            $updateData['patrol_date'] = $validated['new_patrol_date'];
        }

        if (empty($updateData)) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_BAD_REQUEST,
                    'message' => 'Tidak ada data yang diperbarui.',
                ],
                HttpStatusCodes::HTTP_BAD_REQUEST,
            );
        }

        $updateData['updated_at'] = now();

        // 5️⃣ Jalankan update
        DB::table('patrollings')->where('id', $patrolId)->update($updateData);

        // 6️⃣ Ambil ulang data setelah update
        $updatedPatrol = DB::table('patrollings')->where('id', $patrolId)->first();

        // 7️⃣ Catat ke activity log
        ActivityLogService::logModel(
            model: 'patrollings',
            rowId: $patrolId,
            json: [
                'before' => $patrol,
                'after' => $updatedPatrol,
            ],
            type: 'update',
        );

        // 8️⃣ Response sukses
        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Data jadwal ronda berhasil diperbarui.',
            'data' => [
                'patrol_id' => $updatedPatrol->id,
                'patrol_date' => $updatedPatrol->patrol_date,
                'citizen_id' => $updatedPatrol->citizen_id,
                'house_id' => $updatedPatrol->house_id,
                'updated_at' => $updatedPatrol->updated_at,
            ],
        ]);
    }

    public function presence(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patrol_id' => ['required', 'uuid', 'exists:patrollings,id'],
            'presence' => ['nullable'],
            'note' => ['nullable', 'string', 'max:255'],
            'replacement_citizen_id' => ['nullable', 'uuid', 'exists:citizens,id'],
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ],
                HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $validated = $validator->validated();
        $patrolId = $validated['patrol_id'];

        $patrol = DB::table('patrollings')->where('id', $patrolId)->first();

        if (!$patrol) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                    'message' => 'Data jadwal ronda tidak ditemukan.',
                ],
                HttpStatusCodes::HTTP_NOT_FOUND,
            );
        }

        $user = $request->user();
        $housingUser = DB::table('housing_users')->where('user_id', $user->id)->where('housing_id', $patrol->housing_id)->where('is_active', 1)->first();

        if (!$housingUser || $housingUser->role_code !== 'admin') {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_FORBIDDEN,
                    'message' => 'Hanya admin housing yang dapat memperbarui jadwal patroli.',
                ],
                HttpStatusCodes::HTTP_FORBIDDEN,
            );
        }

        $updateData = [];

        if (isset($validated['presence'])) {
            $presenceValue = $validated['presence'];
            if (is_string($presenceValue)) {
                $presenceValue = strtolower($presenceValue) === 'hadir' ? 1 : 0;
            } else {
                $presenceValue = filter_var($presenceValue, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
            }
            $updateData['presence'] = $presenceValue;
        }

        if (isset($validated['note'])) {
            $updateData['note'] = $validated['note'];
        }

        if (isset($validated['replacement_citizen_id'])) {
            $isValidReplacement = DB::table('houses')->where('housing_id', $patrol->housing_id)->where('head_citizen_id', $validated['replacement_citizen_id'])->exists();

            if (!$isValidReplacement) {
                return response()->json(
                    [
                        'success' => false,
                        'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                        'message' => 'Warga pengganti tidak valid atau bukan dari perumahan ini.',
                    ],
                    HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                );
            }

            $updateData['replaced_by'] = $validated['replacement_citizen_id'];
        }

        if (empty($updateData)) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_BAD_REQUEST,
                    'message' => 'Tidak ada data yang diperbarui.',
                ],
                HttpStatusCodes::HTTP_BAD_REQUEST,
            );
        }

        $updateData['updated_at'] = now();

        DB::table('patrollings')->where('id', $patrolId)->update($updateData);

        $updatedPatrol = DB::table('patrollings')->where('id', $patrolId)->first();

        // 🟢 Tambahkan Activity Log
        ActivityLogService::logModel(model: 'patrollings', rowId: $updatedPatrol->id, json: (array) $updatedPatrol, type: 'update');

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Data jadwal ronda berhasil diperbarui.',
            'data' => [
                'patrol_id' => $updatedPatrol->id,
                'patrol_date' => $updatedPatrol->patrol_date,
                'citizen_id' => $updatedPatrol->citizen_id,
                'replaced_by' => $updatedPatrol->replaced_by,
                'presence' => (bool) $updatedPatrol->presence,
                'note' => $updatedPatrol->note,
                'updated_at' => $updatedPatrol->updated_at,
            ],
        ]);
    }

    public function me(Request $request)
    {
        // 1️⃣ Validasi input
        $validator = Validator::make($request->all(), [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
            'search' => ['nullable', 'string', 'max:100'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'month' => ['required', 'integer', 'between:1,12'],
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ],
                HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        // 2️⃣ Ambil data validasi
        $validated = $validator->validated();
        $page = $validated['page'] ?? 1;
        $perPage = $validated['per_page'] ?? 10;
        $search = $validated['search'] ?? null;
        $year = $validated['year'];
        $month = str_pad($validated['month'], 2, '0', STR_PAD_LEFT);

        // 3️⃣ Ambil user yang login
        $user = $request->user();
        $housingUser = DB::table('housing_users')->where('user_id', $user->id)->where('is_active', 1)->first();

        if (!$housingUser) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_FORBIDDEN,
                    'message' => 'Data warga tidak ditemukan untuk pengguna ini.',
                ],
                HttpStatusCodes::HTTP_FORBIDDEN,
            );
        }

        // 4️⃣ Ambil citizen dari housing_user
        $citizen = DB::table('citizens')->where('id', $housingUser->citizen_id)->first();

        if (!$citizen) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                    'message' => 'Data warga tidak ditemukan.',
                ],
                HttpStatusCodes::HTTP_NOT_FOUND,
            );
        }

        // 5️⃣ Ambil house berdasarkan family_card_id warga
        $house = DB::table('houses')->where('family_card_id', $citizen->family_card_id)->first();

        if (!$house) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                    'message' => 'Rumah tidak ditemukan untuk warga ini.',
                ],
                HttpStatusCodes::HTTP_NOT_FOUND,
            );
        }

        $houseId = $house->id;

        // 6️⃣ Query jadwal ronda berdasarkan house_id
        $query = DB::table('patrollings as p')->join('houses as h', 'p.house_id', '=', 'h.id')->join('citizens as c', 'p.citizen_id', '=', 'c.id')->select('p.patrol_date as date', 'p.citizen_id', 'c.fullname as citizen_name', 'p.house_id', 'h.block', 'h.number', 'p.replaced_by', 'p.note', 'p.presence')->where('p.house_id', $houseId)->whereYear('p.patrol_date', $year)->whereMonth('p.patrol_date', $month);

        // 7️⃣ Filter pencarian (opsional)
        if (!empty($search)) {
            $query->where('c.name', 'like', "%{$search}%");
        }

        // 8️⃣ Ambil data & paginate
        $patrols = $query->orderBy('p.patrol_date', 'asc')->paginate($perPage, ['*'], 'page', $page);

        if ($patrols->isEmpty()) {
            return response()->json(
                [
                    'success' => true,
                    'code' => HttpStatusCodes::HTTP_OK,
                    'message' => 'Tidak ada jadwal ronda untuk rumah ini pada bulan dan tahun tersebut.',
                    'data' => [],
                ],
                HttpStatusCodes::HTTP_OK,
            );
        }

        // 9️⃣ Group by tanggal
        $grouped = collect($patrols->items())
            ->groupBy('date')
            ->map(function ($members) {
                return [
                    'date' => $members->first()->date,
                    'member' => $members
                        ->map(function ($m) {
                            return [
                                'citizen_id' => $m->citizen_id,
                                'citizen_name' => $m->citizen_name,
                                'block' => $m->block,
                                'number' => $m->number,
                                'presence' => (bool) $m->presence,
                                'replaced_by' => $m->replaced_by,
                                'note' => $m->note,
                            ];
                        })
                        ->values(),
                ];
            })
            ->values();

        // 🔟 Response sukses
        return response()->json(
            [
                'success' => true,
                'code' => HttpStatusCodes::HTTP_OK,
                'message' => 'Berhasil menampilkan jadwal ronda rumah Anda.',
                'pagination' => [
                    'current_page' => $patrols->currentPage(),
                    'per_page' => $patrols->perPage(),
                    'total' => $patrols->total(),
                    'last_page' => $patrols->lastPage(),
                ],
                'data' => $grouped,
            ],
            HttpStatusCodes::HTTP_OK,
        );
    }
}
