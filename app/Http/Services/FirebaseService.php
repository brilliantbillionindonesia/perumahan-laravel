<?php

namespace App\Http\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $projectId = config('services.firebase.project_id');
        $credentialPath = config('services.firebase.credentials');

        // 🔥 Inisialisasi Firebase
        $factory = (new Factory)
            ->withServiceAccount($credentialPath)
            ->withProjectId($projectId);

        $this->messaging = $factory->createMessaging();
    }

    /**
     * Kirim notifikasi ke topic atau token
     */
    public function sendNotification($target, $title, $body, $data = [])
    {
        $notification = Notification::create($title, $body);

        $message = CloudMessage::withTarget($target)
            ->withNotification($notification)
            ->withData($data);

        return $this->messaging->send($message);
    }
}
