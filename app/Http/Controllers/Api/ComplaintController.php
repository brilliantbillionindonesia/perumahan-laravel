<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\ComplaintStatus;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function list()
    {
        $complaints = Complaint::with(['category', 'status', 'user'])->get();

        $data = $complaints->map(function ($c) {
            return [
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
                'submitted_at' => $c->created_at,
            ];
        });

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Data complaint berhasil diambil',
            'data' => $data
        ], 200);
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
            'submitted_at' => $c->created_at,
        ];

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Detail complaint berhasil diambil',
            'data' => $data
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'category_code' => 'required|string|exists:complaint_categories,code',
            'status_code' => 'required|string|exists:complaint_statuses,code',
            'housing_id' => 'required|exists:housings,id',
        ]);

        $category = ComplaintCategory::where('code', $validated['category_code'])->firstOrFail();
        $status = ComplaintStatus::where('code', $validated['status_code'])->firstOrFail();

        $complaint = Complaint::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'housing_id' => $validated['housing_id'],
            'category_id' => $category->id,
            'status_id' => $status->id,
            'user_id' => $request->user()->id,
        ]);

        $data = [
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
            'submitted_at' => $complaint->created_at,
        ];

        return response()->json([
            'success' => true,
            'code' => 201,
            'message' => 'Complaint berhasil ditambahkan',
            'data' => $data,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $complaint = Complaint::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string',
            'description' => 'sometimes|string',
            'note' => 'sometimes|string',
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
            'submitted_at' => $complaint->created_at,
        ];

        return response()->json([
            'success' => true,
            'code' => 200,
            'message' => 'Complaint berhasil diperbarui',
            'data' => $data
        ], 200);
    }
}