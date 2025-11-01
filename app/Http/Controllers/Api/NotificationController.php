<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpStatusCodes;
use App\Http\Controllers\Controller;
use App\Models\NotificationRecipient;
use Cache;
use DB;
use Illuminate\Http\Request;
use Validator;

class NotificationController extends Controller
{
    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $userId = auth()->user()->id;
        $housingId = $request->input('housing_id');

        $page = (int) $request->input('page', 1);
        $perPage = (int) $request->input('per_page', 10);
        $userId = auth()->id(); // misal notifikasi per user

        $query = DB::table('notification_recipients as nr')
            ->join('notifications as n', 'n.id', '=', 'nr.notification_id')
            ->select(
                'nr.notification_id',
                'nr.status',
                'nr.is_read',
                'n.type',
                'n.title',
                'n.message as description',
                'n.data_json',
                'nr.delivered_at',
                'nr.read_at',
            )
            ->where('nr.user_id', $userId)
            ->where('n.housing_id', $housingId)
            ->orderByDesc('n.created_at');

        $data = $query
            ->limit(5)
            // ->skip(($page - 1) * $perPage)
            // ->take($perPage)
            ->get()
            ->map(function ($item) {
                $item->data_json = !empty($item->data_json)
                    ? json_decode($item->data_json, true)
                    : null;
                return $item;
            });

        NotificationRecipient::
        where('is_read', 0)
        ->where('user_id', auth()->id())
        ->update([
            'is_read' => 1,
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Berhasil menampilkan data',
            'data' => $data,
        ], HttpStatusCodes::HTTP_OK);
    }

    public function has()
    {
        $userId = auth()->id();
        $housingId = request()->input('housing_id');

        // cache key per user & per housing
        $key = "notif_unread_count:{$housingId}:{$userId}";

        $count = Cache::remember($key, 10, function () use ($userId, $housingId) {
            // ambil hanya 11 item biar cepat
            return DB::table('notification_recipients as nr')
                ->join('notifications as n', 'n.id', '=', 'nr.notification_id')
                ->where('nr.status', 'sent')
                ->where('nr.is_read', 0)
                ->where('nr.user_id', $userId)
                ->where('n.housing_id', $housingId)
                ->select('nr.notification_id') // ambil kolom minimal
                ->limit(11)
                ->get()
                ->count();
        });

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Berhasil menampilkan data',
            'data' => $count,
        ]);
    }

    public function read(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => ['required', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $notification = NotificationRecipient::where('notification_id', $request->input('notification_id'))
        ->where('user_id', auth()->id())
        ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Notifikasi tidak ditemukan',
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $notification->is_read = 1;
        $notification->read_at = now();
        $notification->save();

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Berhasil membaca notifikasi',
        ], HttpStatusCodes::HTTP_OK);
    }
}
