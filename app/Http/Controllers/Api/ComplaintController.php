<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\ComplaintStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Constants\HttpStatusCodes;
use App\Http\Services\ActivityLogService;
use App\Models\ComplaintLogs;

class ComplaintController extends Controller
{
    public function list(Request $request)
    {
        $complaints = Complaint::with(['category', 'status', 'user'])
            ->when($request->housing_id, function ($q) use ($request) {
                $q->where('housing_id', $request->housing_id);
            })
            ->get();

        $data = $complaints->map(
            fn($c) => [
                'id' => $c->id,
                'title' => $c->title,
                'description' => $c->description,
                'user_id' => $c->user_id,
                'user_name' => $c->user?->name,
                'housing_id' => $c->housing_id,
                'category_code' => $c->category?->code,
                'category_name' => $c->category?->name,
                'status_code' => $c->status?->code,
                'status_name' => $c->status?->name,
                'submitted_at' => $c->submitted_at,
            ],
        );

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Data complaint berhasil diambil',
            'data' => $data,
        ]);
    }

    public function show($id)
    {
        $c = Complaint::with(['category', 'status', 'user'])->findOrFail($id);

        $data = [
            'id' => $c->id,
            'title' => $c->title,
            'description' => $c->description,
            'user_id' => $c->user_id,
            'user_name' => $c->user?->name,
            'housing_id' => $c->housing_id,
            'category_code' => $c->category?->code,
            'category_name' => $c->category?->name,
            'status_code' => $c->status?->code,
            'status_name' => $c->status?->name,
            'submitted_at' => $c->submitted_at,
        ];

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Detail complaint berhasil diambil',
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'         => 'required|string',
            'description'   => 'required|string',
            'category_code' => 'required|string|exists:complaint_categories,code',
            'housing_id'    => 'required|exists:housings,id',
            // status_code tidak usah divalidasi, FE tidak perlu kirim
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code'    => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }
    
        $data     = $validator->validated();
        $category = ComplaintCategory::where('code', $data['category_code'])->firstOrFail();
    
        // default status = new
        $status   = ComplaintStatus::where('code', 'new')->firstOrFail();
    
        $complaint = Complaint::create([
            'title'        => $data['title'],
            'description'  => $data['description'],
            'housing_id'   => $data['housing_id'],
            'category_code'=> $category->code,
            'status_code'  => $status->code,
            'user_id'      => $request->user()->id,
            'submitted_at' => now(),
            'updated_by'   => $request->user()->id,
        ]);
    
        
        ActivityLogService::logModel(
            model: $complaint->getTable(),
            rowId: $complaint->id,
            json: $complaint->toArray(),      // ini tetap array untuk JSON
            type: 'create',
        );
        
    
        // complaint log
        ComplaintLogs::create([
            'complaint_id' => $complaint->id,
            'logged_by'    => $request->user()->id,
            'logged_at'    => now(),
            'status'       => $status->code,
            'note'         => 'Pengaduan dibuat',
        ]);
    
        $data = [
            'id'            => $complaint->id,
            'title'         => $complaint->title,
            'description'   => $complaint->description,
            'user_id'       => $complaint->user_id,
            'user_name'     => $complaint->user?->name,
            'housing_id'    => $complaint->housing_id,
            'category_code' => $category->code,
            'category_name' => $category->name,
            'status_code'   => $status->code,
            'status_name'   => $status->name,
            'submitted_at'  => $complaint->submitted_at,
        ];
    
        return response()->json([
            'success' => true,
            'code'    => 201,
            'message' => 'Complaint berhasil ditambahkan',
            'data'    => $data,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
            'category_code' => 'sometimes|string|exists:complaint_categories,code',
            'status_code' => 'sometimes|string|exists:complaint_statuses,code',
        ]);

        if (isset($validated['category_code'])) {
            $category = ComplaintCategory::where('code', $validated['category_code'])->firstOrFail();
            $validated['category_id'] = $category->id;
            unset($validated['category_code']);
        }

        if (isset($validated['status_code'])) {
            $status = ComplaintStatus::where('code', $validated['status_code'])->firstOrFail();
            $validated['status_id'] = $status->id;
            unset($validated['status_code']);
        }

        $complaint->update($validated);
        $complaint->load(['category', 'status', 'user']);

        $data = [
            'id' => $complaint->id,
            'title' => $complaint->title,
            'description' => $complaint->description,
            'user_id' => $complaint->user_id,
            'user_name' => $complaint->user?->name,
            'housing_id' => $complaint->housing_id,
            'category_code' => $complaint->category?->code,
            'category_name' => $complaint->category?->name,
            'status_code' => $complaint->status?->code,
            'status_name' => $complaint->status?->name,
            'submitted_at' => $complaint->submitted_at,
        ];

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Complaint berhasil diperbarui',
            'data' => $data,
        ]);
    }
}
