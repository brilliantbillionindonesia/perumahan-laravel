<?php

namespace App\Http\Controllers\Api;

use App\Constants\HttpStatusCodes;
use App\Http\Controllers\Controller;
use App\Jobs\DispatchPanicPush;
use App\Models\HousingUser;
use App\Models\PanicEvent;
use App\Models\PanicRecipient;
use DB;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class PanicController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'housing_id' => ['required', 'exists:housings,id'],
            'latitude' => ['nullable'],
            'longitude' => ['nullable'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $housingUser = HousingUser::where('housing_id', $request->input('housing_id'))
        ->where('user_id', auth()->user()->id)
        ->first();

        if (!$housingUser) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_NOT_FOUND,
                'message' => 'Data tidak ditemukan',
            ], HttpStatusCodes::HTTP_NOT_FOUND);
        }

        DB::transaction(function () use ($request, $housingUser) {
            $panicEvent = PanicEvent::create([
                'housing_id' => $request->input('housing_id'),
                'citizen_id' => $housingUser->citizen_id,
                'user_id' => $housingUser->user_id,
                'house_id' => $housingUser->house_id,
                'status' => 'active',
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
            ]);

            $management = HousingUser::where('housing_id', $request->input('housing_id'))
            ->where('role_code', '!=', 'citizen')->get();

            foreach ($management as $key => $value) {
                PanicRecipient::create([
                    'panic_event_id' => $panicEvent->id,
                    'user_id' => $value->user_id,
                    'status' => 'pending',
                    'notified_at' => now(),
                ]);
            }
            // // Kirim ke queue (asinkron)
            // DispatchPanicPush::dispatch(
            //     panicId: $panicEvent->id
            // )->onQueue('notifications');

            // new DispatchPanicPush($panicEvent->id);
            (new DispatchPanicPush($panicEvent->id))->handle(app(\App\Http\Services\PushService::class));

        });

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Success',
        ], HttpStatusCodes::HTTP_OK);
    }

    public function handle(Request $request){
        $validator = Validator::make($request->all(), [
            'panic_id' => ['required', 'exists:panic_events,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $panicEvent = PanicEvent::findOrFail($request->input('panic_id'));

        if ($panicEvent->status != 'active') {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Panic event tidak aktif',
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $panicEvent->update([
            'status' => 'closed',
            'handled_at' => now(),
            'handled_by' => auth()->user()->id
        ]);

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Success',
        ], HttpStatusCodes::HTTP_OK);
    }

    public function active(Request $request){
        $validator = Validator::make($request->all(), [
            'panic_id' => ['required', 'exists:panic_events,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => $validator->errors()->first(),
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        $panicEvent = PanicEvent::findOrFail($request->input('panic_id'));

        if ($panicEvent->status != 'active') {
            return response()->json([
                'success' => false,
                'code' => HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY,
                'message' => 'Panic event tidak aktif',
            ], HttpStatusCodes::HTTP_UNPROCESSABLE_ENTITY);
        }

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Panic event aktif',
        ]);
    }

    public function panicNotifiedToMe(Request $request){
        $data = PanicRecipient::where('user_id', auth()->user()->id)
        ->with('eventActive')->limit(1)->get();

        foreach ($data as $key => $value) {
            $arr = [
                'panic_id' => $value->eventActive->id,
                'name' => $value->eventActive->citizen ? $value->eventActive->citizen->fullname : $value->eventActive->user->name,
                'lat' => $value->eventActive->latitude,
                'long' => $value->eventActive->longitude,
                'status' => $value->status,
                'created_at' => $value->eventActive->created_at
            ];
        }

        return response()->json([
            'success' => true,
            'code' => HttpStatusCodes::HTTP_OK,
            'message' => 'Success',
            'data' => $arr
        ], HttpStatusCodes::HTTP_OK);
    }
}
