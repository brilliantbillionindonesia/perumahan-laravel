<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\DispatchComplaintAction;
use App\Jobs\DispatchComplaintStore;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\ComplaintStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Constants\HttpStatusCodes;
use App\Http\Services\ActivityLogService;
use App\Models\ComplaintLogs;
use Illuminate\Support\Facades\DB;

class ComplaintController extends Controller
{
    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
            'search' => ['nullable', 'string'],
            'is_me' => ['nullable', 'boolean'],
            'category_code' => ['nullable', 'string'],
            'status_code' => ['nullable', 'string']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $housingId = $request->current_housing->housing_id;

        // Pagination setup
        $page = max((int) $request->get('page', 1), 1);
        $perPage = min((int) $request->get('per_page', 10), 30);

        // Query dasar
        $query = Complaint::with(['category', 'status', 'user', 'updatedBy'])->where('housing_id', $housingId);

        if ($request->get('is_me')) {
            $query->where('user_id', $request->user()->id);
        }

        // Filter search (title & description)
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter category_code
        if ($categoryCode = $request->get('category_code')) {
            $query->where('category_code', $categoryCode);
        }

        // Filter status_code
        if ($statusCode = $request->get('status_code')) {
            $query->where('status_code', $statusCode);
        }

        // Order by terbaru
        $query->orderBy('submitted_at', 'desc');

        // Pagination
        $complaints = $query->paginate($perPage, ['*'], 'page', $page);

        // Format response
        $data = $complaints->map(function ($c) {
            return [
                'complaint_id' => $c->id,
                'user_id' => $c->user_id,
                'user_name' => $c->user?->name,
                'category_code' => $c->category_code,
                'category_name' => $c->category?->name,
                'title' => $c->title,
                'description' => $c->description,
                'status_code' => $c->status_code,
                'status_name' => $c->status?->name,
                'updated_by' => $c->updatedBy?->id,
                'updated_by_name' => $c->updatedBy?->name,
                'submitted_at' => $c->submitted_at,
                'updated_at' => $c->updated_at,
            ];
        });

        return response()->json(
            [
                'success' => true,
                'code' => HttpStatusCodes::HTTP_OK,
                'message' => 'Berhasil menampilkan data',
                'data' => $data,
            ],
            HttpStatusCodes::HTTP_OK
        );
    }

    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'housing_id' => 'required|exists:housings,id',
            'id' => 'required|exists:complaints,id',
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

        $data = $validator->validated();

        // 🔍 Ambil data complaint beserta relasi
        $complaint = Complaint::with(['category', 'status', 'user', 'updatedBy'])
            ->where('housing_id', $data['housing_id'])
            ->where('id', $data['id'])
            ->first();

        if (!$complaint) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                    'message' => 'Data pengaduan tidak ditemukan.',
                ],
                HttpStatusCodes::HTTP_NOT_FOUND,
            );
        }

        // ✅ Format response data
        $responseData = [
            'complaint_id' => $complaint->id,
            'title' => $complaint->title,
            'description' => $complaint->description,
            'user_id' => $complaint->user_id,
            'user_name' => $complaint->user?->name,
            'category_code' => $complaint->category_code,
            'category_name' => $complaint->category?->name,
            'status_code' => $complaint->status_code,
            'status_name' => $complaint->status?->name,
            'updated_by' => $complaint->updatedBy?->id,
            'updated_by_name' => $complaint->updatedBy?->name,
            'submitted_at' => $complaint->submitted_at,
            'updated_at' => $complaint->updated_at,
        ];

        // ✅ Response sukses
        return response()->json(
            [
                'success' => true,
                'code' => HttpStatusCodes::HTTP_OK,
                'message' => 'Berhasil menampilkan data pengaduan',
                'data' => $responseData,
            ],
            HttpStatusCodes::HTTP_OK,
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'category_code' => 'required|string|exists:complaint_categories,code',
            'housing_id' => 'required|exists:housings,id',
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

        $data = $validator->validated();

        $category = ComplaintCategory::where('code', $data['category_code'])->firstOrFail();

        $status = ComplaintStatus::where('code', 'new')->firstOrFail();
        if (!$status) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => $validator->errors()->first(),
                ],
                HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $data = $validator->validated();
        $category = ComplaintCategory::where('code', $data['category_code'])->firstOrFail();
        $status = ComplaintStatus::where('code', 'new')->firstOrFail();
        DB::transaction(function () use ($data, $category, $status, $request, &$created) {
            $complaint = Complaint::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'housing_id' => $data['housing_id'],
                'category_code' => $category->code,
                'status_code' => $status->code,
                'user_id' => $request->user()->id,
                'submitted_at' => now(),
                'updated_by' => $request->user()->id,
            ]);

            ComplaintLogs::create([
                'complaint_id' => $complaint->id,
                'status_code' => $status->code, // ✅ isi status_code
                'logged_by' => $request->user()->id,
                'logged_at' => now(),
                'note' => 'Pengaduan dibuat',
            ]);

            ActivityLogService::logModel(
                model: $complaint->getTable(),
                rowId: $complaint->id,
                json: $complaint->toArray(), // ini tetap array untuk JSON
                type: 'create',
            );

            $created = [
                'id' => $complaint->id,
                'title' => $complaint->title,
                'description' => $complaint->description,
                'user_id' => $complaint->user_id,
                'user_name' => $complaint->user?->name,
                'housing_id' => $complaint->housing_id,
                'category_code' => $category->code,
                'category_name' => $category->name,
                'status_code' => $status->code,
                'status_name' => $status->name,
                'submitted_at' => $complaint->submitted_at,
            ];


            DispatchComplaintStore::dispatch(
                complaintId: $complaint->id
            )->onQueue('notifications');

            // (new DispatchComplaintStore($complaint->id))->handle(app(\App\Http\Services\PushService::class));
        });


        return response()->json(
            [
                'success' => true,
                'code' => HttpStatusCodes::HTTP_CREATED,
                'message' => 'Pengaduan berhasil ditambahkan',
                'data' => $created,
            ],
            201,
        );
    }

    public function update(Request $request)
    {
        $id = $request->json('id'); // 🔒 Ambil dari body JSON, bukan query param

        if (!$id) {
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_BAD_REQUEST,
                    'message' => 'Field "id" wajib diisi di body JSON',
                ],
                HttpStatusCodes::HTTP_BAD_REQUEST,
            );
        }

        // ✅ Validasi body JSON
        $validator = Validator::make($request->json()->all(), [
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
            'category_code' => 'sometimes|string|exists:complaint_categories,code',
            'housing_id' => 'required|exists:housings,id',
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

        DB::beginTransaction();

        // 🔍 Ambil data complaint
        $complaint = Complaint::with(['status', 'category', 'user'])->find($id);
        if (!$complaint) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                    'message' => 'Complaint tidak ditemukan',
                ],
                HttpStatusCodes::HTTP_NOT_FOUND,
            );
        }

        // 🔒 Cegah edit jika status sudah closed
        if ($complaint->status_code === 'closed') {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_FORBIDDEN,
                    'message' => 'Pengaduan tidak dapat diperbarui',
                ],
                HttpStatusCodes::HTTP_FORBIDDEN,
            );
        }

        // 🔐 Hanya user pemilik yang bisa update
        if ($complaint->user_id !== $request->user()->id) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_FORBIDDEN,
                    'message' => 'Kamu tidak memiliki izin untuk memperbarui pengaduan ini',
                ],
                HttpStatusCodes::HTTP_FORBIDDEN,
            );
        }

        // 🔧 Field yang boleh diupdate
        $updatable = array_filter(
            [
                'title' => $validated['title'] ?? null,
                'description' => $validated['description'] ?? null,
                'category_code' => $validated['category_code'] ?? null,
            ],
            fn($v) => $v !== null,
        );

        if (empty($updatable)) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_BAD_REQUEST,
                    'message' => 'Tidak ada data yang diperbarui',
                ],
                HttpStatusCodes::HTTP_BAD_REQUEST,
            );
        }

        // 🔄 Update data
        $complaint->update($updatable);
        $complaint->refresh()->load(['category', 'status', 'user']);

        // 🧾 Activity Log
        ActivityLogService::logModel(model: $complaint->getTable(), rowId: $complaint->id, json: $complaint->toArray(), type: 'update');

        // 🗒️ Complaint Log
        ComplaintLogs::create([
            'complaint_id' => $complaint->id,
            'status_code' => $complaint->status_code,
            'logged_by' => $request->user()->id,
            'logged_at' => now(),
            'note' => 'Pengguna memperbarui pengaduan',
        ]);

        DB::commit();

        // ✅ Response sukses
        return response()->json(
            [
                'success' => true,
                'code' => HttpStatusCodes::HTTP_OK,
                'message' => 'Complaint berhasil diperbarui',
                'data' => [
                    'id' => $complaint->id,
                    'title' => $complaint->title,
                    'description' => $complaint->description,
                    'category_code' => $complaint->category?->code,
                    'category_name' => $complaint->category?->name,
                    'status_code' => $complaint->status?->code,
                    'status_name' => $complaint->status?->name,
                    'user' => $complaint->user,
                    'updated_at' => $complaint->updated_at,
                ],
            ],
            HttpStatusCodes::HTTP_OK,
        );
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:complaints,id',
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
        $id = $validated['id'];

        DB::beginTransaction();

        // 🔍 Ambil data complaint
        $complaint = Complaint::with(['status', 'user'])->find($id);
        if (!$complaint) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                    'message' => 'Complaint tidak ditemukan',
                ],
                HttpStatusCodes::HTTP_NOT_FOUND,
            );
        }

        // 🔐 Hanya user pemilik yang boleh hapus
        if ($complaint->user_id !== $request->user()->id) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_FORBIDDEN,
                    'message' => 'Kamu tidak memiliki izin untuk menghapus pengaduan ini',
                ],
                HttpStatusCodes::HTTP_FORBIDDEN,
            );
        }

        // 🚫 Cegah penghapusan jika status closed
        if ($complaint->status_code === 'closed') {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_BAD_REQUEST,
                    'message' => 'Pengaduan dengan status closed tidak dapat dihapus',
                ],
                HttpStatusCodes::HTTP_BAD_REQUEST,
            );
        }

        // 🚫 Hanya status "new" yang bisa dihapus
        if ($complaint->status_code !== 'new') {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_BAD_REQUEST,
                    'message' => 'Hanya pengaduan dengan status "new" yang dapat dihapus',
                ],
                HttpStatusCodes::HTTP_BAD_REQUEST,
            );
        }

        // 🧾 Simpan data sebelum delete untuk kebutuhan log
        $complaintData = $complaint->toArray();

        // 🗑️ Hapus complaint
        $complaint->delete();

        // 🧾 Activity Log
        ActivityLogService::logModel(model: $complaint->getTable(), rowId: $id, json: $complaintData, type: 'delete');

        // 🗒️ Complaint Logs
        ComplaintLogs::create([
            'complaint_id' => $id,
            'status_code' => $complaint->status_code,
            'logged_by' => $request->user()->id,
            'logged_at' => now(),
            'note' => 'Pengguna menghapus pengaduan dengan status "new"',
        ]);

        DB::commit();

        // ✅ Response sukses
        return response()->json(
            [
                'success' => true,
                'code' => HttpStatusCodes::HTTP_OK,
                'message' => 'Pengaduan berhasil dihapus',
            ],
            HttpStatusCodes::HTTP_OK,
        );
    }

    public function action(Request $request)
    {
        // ✅ Validasi hanya note (optional) dan id (required)
        $validator = Validator::make($request->all(), [
            'complaint_id' => 'required|exists:complaints,id',
            'note' => 'nullable|string|max:255',
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

        $validated = $validator->validated();

        DB::transaction(function () use ($request, $validated, &$complaint) {

            $complaint = Complaint::with(['status', 'category', 'user'])
            ->where('housing_id', $request->housing_id)
            ->findOrFail($validated['complaint_id']);

            // 🔒 Jika status sudah CLOSED, tidak bisa diubah lagi
            if ($complaint->status_code === 'closed') {
                return response()->json(
                    [
                        'success' => false,
                        'code' => HttpStatusCodes::HTTP_FORBIDDEN,
                        'message' => 'Pengaduan sudah ditutup dan tidak dapat diubah lagi.',
                    ],
                    HttpStatusCodes::HTTP_FORBIDDEN,
                );
            }

            // 🔄 Ubah status otomatis dari NEW menjadi CLOSED
            $complaint->update([
                'status_code' => 'closed',
                'updated_by' => $request->user()->id,
            ]);

            // refresh relasi
            $complaint->refresh()->load(['status', 'category', 'user']);

            // 📝 Simpan ke complaint logs
            ComplaintLogs::create([
                'complaint_id' => $complaint->id,
                'status_code' => 'closed',
                'logged_by' => $request->user()->id,
                'logged_at' => now(),
                'note' => $validated['note'] ?? null,
            ]);

            // 🧾 Simpan ke activity log
            ActivityLogService::logModel(model: $complaint->getTable(), rowId: $complaint->id, json: $complaint->toArray(), type: 'update');

            DispatchComplaintAction::dispatch(
                complaintId: $complaint->id
            )->onQueue('notifications');
        });

        // ✅ Response sukses
        return response()->json(
            [
                'success' => true,
                'code' => HttpStatusCodes::HTTP_OK,
                'message' => 'Pengaduan selesai',
                'data' => [
                    'id' => $complaint->id,
                    'title' => $complaint->title,
                    'description' => $complaint->description,
                    'category_code' => $complaint->category_code,
                    'category_name' => $complaint->category?->name,
                    'status_code' => $complaint->status_code,
                    'status_name' => $complaint->status?->name,
                    'note' => $validated['note'] ?? null,
                    'updated_by' => $request->user()->name,
                    'updated_at' => now(),
                ],
            ],
            HttpStatusCodes::HTTP_OK,
        );
    }

    public function history(Request $request)
    {

        $validator = Validator::make($request->json()->all(), [
            'housing_id' => 'required|exists:housings,id',
        ]);

        $validator = Validator::make($request->all(), [

            'complaint_id' => 'required|exists:complaints,id',
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

        // ✅ Ambil log tanpa duplikasi status_code (hanya log terbaru per status)
        $logs = ComplaintLogs::with(['loggedBy', 'complaint.status'])
            ->where('complaint_id', $validated['complaint_id'])
            ->orderBy('logged_at', 'desc')
            ->get()
            ->unique('status_code') // hanya 1 per status_code
            ->sortBy('logged_at') // urutkan kembali dari yang lama ke baru
            ->values();

        if ($logs->isEmpty()) {
            return response()->json(
                [
                    'success' => true,
                    'code' => HttpStatusCodes::HTTP_OK,
                    'message' => 'Tidak ada riwayat pengaduan ditemukan',
                    'data' => [],
                ],
                HttpStatusCodes::HTTP_OK,
            );
        }

        $data = $logs->map(function ($log) {
            return [
                'complaint_id' => $log->complaint_id,
                'status_code' => $log->status_code,
                'status_name' => $log->status?->name,
                'note' => $log->note ?? '',
                'logged_by' => $log->logged_by,
                'logged_name' => $log->loggedBy?->name ?? '-',
                'logged_at' => $log->logged_at,
            ];
        });

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

}
