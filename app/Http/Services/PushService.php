<?php

namespace App\Http\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;

class PushService
{
    protected string $projectId;
    protected string $credentialsPath;

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id');
        $this->credentialsPath = config('services.firebase.credentials');
    }

    protected function getAccessToken(): string
    {
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $credentials = new ServiceAccountCredentials($scopes, $this->credentialsPath);
        $token = $credentials->fetchAuthToken();
        return $token['access_token'];
    }

    public function sendPanic(array $tokens, array $data, string $title, string $body): bool
    {
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
        $accessToken = $this->getAccessToken();

        foreach ($tokens as $token) {
            $payload = [
                'message' => [
                    'token' => $token,
                    // SYSTEM will show this even if app is killed:
                    'notification' => [
                        'title' => $title,                  // ex: "Permintaan PANIC!"
                        'body' => $body,                   // ex: "Dari Admin Mustika • Tap untuk buka"
                    ],
                    // your custom data for routing:
                    'data' => $data + ['click_action' => 'FLUTTER_NOTIFICATION_CLICK'],
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'channel_id' => 'panic_channel',
                            'sound' => 'default',
                            'icon' => 'ic_stat_panic',             // silent
                            'visibility' => 'PUBLIC',
                            'color' => '#C62828',
                        ],
                    ],
                    'apns' => [
                        'headers' => ['apns-priority' => '10'],
                        'payload' => [
                            'aps' => [
                                'content-available' => 1,
                                'sound' => ''        // silent
                            ],
                        ],
                    ],
                ],
            ];

            $resp = Http::withToken($accessToken)->post($url, $payload);
            if (!$resp->successful()) {
                \Log::error('PushService error', ['response' => $resp->body()]);
                return false;
            }
        }
        return true;
    }

    public function sendNotification(array $tokens, array $data, string $title, string $channel, string $body): bool
    {
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
        $accessToken = $this->getAccessToken();

        foreach ($tokens as $token) {
            $payload = [
                'message' => [
                    'token' => $token,
                    // SYSTEM will show this even if app is killed:
                    'notification' => [
                        'title' => $title,                  // ex: "Permintaan PANIC!"
                        'body' => $body,                   // ex: "Dari Admin Mustika • Tap untuk buka"
                    ],
                    // your custom data for routing:
                    'data' => $data + ['click_action' => 'FLUTTER_NOTIFICATION_CLICK'],
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'channel_id' => $channel,
                            'sound' => 'default',
                            'icon' => 'ic_stat_panic',             // silent
                            'visibility' => 'PUBLIC',
                            'color' => '#C62828',
                        ],
                    ],
                    'apns' => [
                        'headers' => ['apns-priority' => '10'],
                        'payload' => [
                            'aps' => [
                                'content-available' => 1,
                                'sound' => ''        // silent
                            ],
                        ],
                    ],
                ],
            ];

            $resp = Http::withToken($accessToken)->post($url, $payload);
            if (!$resp->successful()) {
                \Log::error('PushService error', ['response' => $resp->body()]);
                return false;
            }
        }
        return true;
    }

}
