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

    public function sendSilentData(array $tokens, array $data): bool
    {
        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
        $accessToken = $this->getAccessToken();

        foreach ($tokens as $token) {
            $payload = [
                'message' => [
                    'token' => $token,
                    'data' => $data,
                    'android' => ['priority' => 'high'],
                    'apns' => [
                        'headers' => ['apns-priority' => '10'],
                        'payload' => ['aps' => ['content-available' => 1]],
                    ],
                ],
            ];

            dump($payload);
            $response = Http::withToken($accessToken)
                ->post($url, $payload);

            if (!$response->successful()) {
                \Log::error('PushService error', ['response' => $response->body()]);
                return false;
            }
        }

        return true;
    }
}
