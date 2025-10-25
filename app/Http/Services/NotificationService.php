<?php

namespace App\Http\Services;

use App\Models\Notification;
use App\Models\NotificationRecipient;
use Illuminate\Support\Facades\Http;

class NotificationService
{

    public function sendNotification(
        $housingId,
        $type,
        $title,
        $message,
        $channel,
        $json,
        $recipients
    ) {

        $notification = Notification::create([
            'housing_id' => $housingId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'channel' => $channel,
            'data_json' => $json
        ]);

        foreach ($recipients as $recipient) {
            NotificationRecipient::create([
                'notification_id' => $notification->id,
                'user_id' => $recipient->user_id,
                'status' => 'sent',
                'delivered_at' => now()
            ]);
        }

        return $notification;
    }
}
