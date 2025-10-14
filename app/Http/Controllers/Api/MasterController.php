<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpStatusCodes;
use App\Http\Services\ActivityLogService;
use Cache;
use Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class MasterController extends Controller
{
    private array $allowedEntities = [
        'roles',
        'permissions',
        'subdistricts',
        'permission_role',
        'permission_roles',
        'subdistricts',
        'districts',
        'villages',
        'provinces',
        'complaint_categories',
        'complaint_statuses',
        'housing_settings'
    ];

    public function cacheColumns($table){
        return Cache::remember(
            "schema:{$table}:columns",
            3600,
            fn() => Schema::getColumnListing($table)
        );
    }

    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entity' => [
                'required',
                'string',
                Rule::in($this->allowedEntities),
            ],
            'columns' => ['nullable', 'array'],
            'columns.*' => ['string'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
            'order_by' => ['nullable', 'string'],
            'order_dir' => ['nullable', Rule::in(['asc', 'desc', 'ASC', 'DESC'])],
            'with_trashed' => ['nullable', 'boolean'],
            'filters' => ['nullable', 'array'],
            'filter_query' => ['nullable', 'string'],
            'filters.*.column' => ['required', 'string'],
            'filters.*.operator' => ['nullable', Rule::in(['=', '!=', '>', '>=', '<', '<='])],
            'filters.*.value' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $table = $request->input('entity');
        $columns = $request->input('columns'); // nullable
        $filters = $request->input('filters'); // nullable
        $page = (int) ($request->input('page', 1));
        $perPage = (int) ($request->input('per_page', 15));
        $orderBy = $request->input('order_by');
        $orderDir = $request->input('order_dir', 'asc');
        $withTrashed = (bool) ($request->input('with_trashed', false));


        // Validate columns (if provided)
        $cachedColumns = $this->cacheColumns($table);
        if ($columns) {
            $invalid = array_diff($columns, $cachedColumns);

            if (!empty($invalid)) {
                return response()->json([
                    'success' => false,
                    'code'    => HttpStatusCodes::HTTP_BAD_REQUEST,
                    'message' => "Invalid columns in `{$table}`: " . implode(', ', $invalid),
                ], HttpStatusCodes::HTTP_BAD_REQUEST);
            }
        }

        // Validate columns (if provided)
        if ($filters) {
            foreach ($filters as $filter) {
                if (!in_array($filter['column'], $cachedColumns, true)) {
                    return response()->json([
                        'success' => false,
                        'code'    => HttpStatusCodes::HTTP_BAD_REQUEST,
                        'message' => "Invalid filter column `{$filter['column']}` in table `{$table}`.",
                    ], HttpStatusCodes::HTTP_BAD_REQUEST);
                }
            }
        }

        $builder = DB::table($table);

        if(!$filters &&$request->has('filter_query') && $request->input('filter_query') !== null){
            $filter = str_replace('where', '', $request->input('filter_query'));
            $builder->whereRaw($filter);
        }

        if ($withTrashed === false) {
            $builder->where('deleted_at', null);
        }

        if ($columns) {
            $builder->select($columns);
        } else {
            $builder->select('*');
        }

        if($filters){
            foreach ($filters as $filter) {
                $operator = isset($filter['operator']) ? $filter['operator'] : '=';
                if($operator === '='){
                    if($filter['value'] === 'null'){
                        $builder->whereNull($filter['column']);
                    } else {
                        $builder->where($filter['column'], $filter['value']);
                    }
                } else {
                    $builder->where($filter['column'], $operator, $filter['value']);
                }
            }
        }

        if ($orderBy && in_array($request->input('order_by'), $cachedColumns, true)) {
            $builder->orderBy($orderBy, $orderDir);
        } else {
            $builder->orderBy('created_at', 'desc');
        }

        $paginator = $builder->paginate(
            $perPage,
            ['*'],
            'page',
            $page
        );

        // Bentuk respons konsisten
        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Success',
            'data' => $paginator->items(),
            // 'meta' => [
            //     'current_page' => $paginator->currentPage(),
            //     'per_page' => $paginator->perPage(),
            //     'total' => $paginator->total(),
            //     'last_page' => $paginator->lastPage(),
            //     'from' => $paginator->firstItem(),
            //     'to' => $paginator->lastItem(),
            // ],
            // 'links' => [
            //     'first' => $paginator->url(1),
            //     'prev' => $paginator->previousPageUrl(),
            //     'next' => $paginator->nextPageUrl(),
            //     'last' => $paginator->url($paginator->lastPage()),
            // ],
        ], HttpStatusCodes::HTTP_OK);
    }

    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entity' => [
                'required',
                'string',
                Rule::in($this->allowedEntities),
            ],
            'rowId' => ['required', 'exists:' . $request->input('entity') . ',id,deleted_at,NULL'],
            'with_trashed' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $table = $request->input('entity');
        $rowId = $request->input('rowId');
        $withTrashed = (bool) ($request->input('with_trashed', false));

        $data = DB::table($table)->where('id', $rowId);

        if ($withTrashed === false) {
            $data->where('deleted_at', null);
        }

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Success',
            'data' => $data->first()
        ], HttpStatusCodes::HTTP_OK);

    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entity' => [
                'required',
                'string',
                Rule::in($this->allowedEntities),
            ],
            'data' => ['required', 'array'],
            'data.*.name' => ['required', 'string'],
            'data.*.value' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $table = $request->input('entity');

        $rawData = collect($request->input('data'))
            ->mapWithKeys(fn($item) => [$item['name'] => $item['value']])
            ->toArray();

        $validData = [];
        $invalidColumns = [];
        $cachedColumns = $this->cacheColumns($table);
        foreach ($rawData as $col => $val) {
            if (in_array($col, $cachedColumns, true)) {
                $validData[$col] = $val;
            } else {
                $invalidColumns[] = $col;
            }
        }
        if (empty($validData)) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => "No valid columns found for table `{$table}`.",
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!empty($invalidColumns)) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_BAD_REQUEST,
                'message' => "Invalid columns: " . implode(', ', $invalidColumns),
            ], HttpStatusCodes::HTTP_BAD_REQUEST);
        }

        $validData['created_at'] = now();
        $validData['updated_at'] = now();

        try {
            $rowId = DB::table($table)->insertGetId($validData);

            $insertedData = DB::table($table)->where('id', $rowId)->first();

            ActivityLogService::logModel(
                model: $table,
                rowId: $rowId,
                json: (array) $insertedData, // cast ke array
                type: 'create',
            );

            return response()->json([
                'success' => true,
                'code' => HttpStatusCodes::HTTP_CREATED,
                'message' => "Data berhasil ditambahkan ke tabel {$table}",
                'data' => $insertedData,
            ], HttpStatusCodes::HTTP_CREATED);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                preg_match("/Duplicate entry '(.+?)'/", $e->getMessage(), $matches);
                $duplicateValue = $matches[1] ?? null;
                return response()->json([
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_CONFLICT, // 409
                    'message' => $duplicateValue
                        ? "Nilai '{$duplicateValue}' sudah ada."
                        : "Data sudah ada.",
                ], HttpStatusCodes::HTTP_CONFLICT);
            }

            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Database error: ' . $e->getMessage(),
            ], HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entity' => [
                'required',
                'string',
                Rule::in($this->allowedEntities),
            ],
            'rowId' => ['required', 'exists:' . $request->input('entity') . ',id,deleted_at,NULL'],
            'data' => ['required', 'array'],
            'data.*.name' => ['required', 'string'],
            'data.*.value' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $table = $request->input('entity');

        $rawData = collect($request->input('data'))
            ->mapWithKeys(fn($item) => [$item['name'] => $item['value']])
            ->toArray();

        $validData = [];
        $invalidColumns = [];
        $cachedColumns = $this->cacheColumns($table);
        foreach ($rawData as $col => $val) {
            if (in_array($col, $cachedColumns, true)) {
                $validData[$col] = $val;
            } else {
                $invalidColumns[] = $col;
            }
        }
        if (empty($validData)) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => "No valid columns found for table `{$table}`.",
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!empty($invalidColumns)) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_BAD_REQUEST,
                'message' => "Invalid columns: " . implode(', ', $invalidColumns),
            ], HttpStatusCodes::HTTP_BAD_REQUEST);
        }

        try {
            $validData['created_at'] = now();
            $validData['updated_at'] = now();

            $rowId = $request->input('rowId');
            DB::table($table)->where('id', $rowId)->update($validData);
            $insertedData = DB::table($table)->where('id', $rowId)->first();

            ActivityLogService::logModel(
                model: $table,
                rowId: $rowId,
                json: (array) $insertedData, // cast ke array
                type: 'update',
            );

            return response()->json([
                'success' => true,
                'code' => HttpStatusCodes::HTTP_CREATED,
                'message' => "Data id {$rowId} tabel {$table} berhasil diperbarui",
                'data' => $insertedData,
            ], HttpStatusCodes::HTTP_CREATED);
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) {
                preg_match("/Duplicate entry '(.+?)'/", $e->getMessage(), $matches);
                $duplicateValue = $matches[1] ?? null;

                return response()->json([
                    'success' => false,
                    'code' => HttpStatusCodes::HTTP_CONFLICT, // 409
                    'message' => $duplicateValue
                        ? "Nilai '{$duplicateValue}' sudah ada."
                        : "Data sudah ada.",
                ], HttpStatusCodes::HTTP_CONFLICT);
            }

            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Database error: ' . $e->getMessage(),
            ], HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR);

        }

    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entity' => [
                'required',
                'string',
                Rule::in($this->allowedEntities),
            ],
            'rowId' => ['required', 'exists:' . $request->input('entity') . ',id,deleted_at,NULL'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $table = $request->input('entity');
        $rowId = $request->input('rowId');

        try {
            DB::table($table)->where('id', $rowId)->update(
                [
                    'deleted_at' => now(),
                ]
            );

            ActivityLogService::logModel(
                model: $table,
                rowId: $rowId,
                json: [],
                type: 'delete',
            );

            return response()->json([
                'success' => true,
                'code' => HttpStatusCodes::HTTP_OK,
                'message' => "Data id {$rowId} tabel {$table} berhasil dihapus",
            ], HttpStatusCodes::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Database error: ' . $e->getMessage(),
            ], HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function restore(Request $request)
    {
        $table = $request->input('entity');
        $validator = Validator::make($request->all(), [
            'entity' => [
                'required',
                'string',
                Rule::in($this->allowedEntities),
            ],
            'rowId' => [
                'required',
                Rule::exists($table, 'id')->where(function ($q) use ($table) {
                    $q->whereNotNull('deleted_at');
                }),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rowId = $request->input('rowId');

        try {
            DB::table($table)->where('id', $rowId)->update(
                [
                    'deleted_at' => NULL,
                ]
            );

            ActivityLogService::logModel(
                model: $table,
                rowId: $rowId,
                json: [],
                type: 'restore',
            );

            return response()->json([
                'success' => true,
                'code' => HttpStatusCodes::HTTP_OK,
                'message' => "Data id {$rowId} tabel {$table} berhasil dipulihkan",
            ], HttpStatusCodes::HTTP_OK);
        } catch (QueryException $e) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'Database error: ' . $e->getMessage(),
            ], HttpStatusCodes::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
