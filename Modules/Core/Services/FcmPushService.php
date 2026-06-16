<?php

namespace Modules\Core\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmPushService
{
    public function sendToToken(string $deviceToken, string $title, string $body, array $data = []): bool
    {
        $credentialsPath = $this->credentialsPath();
        $projectId = config('services.firebase.project_id');

        if (! $credentialsPath || ! is_readable($credentialsPath) || ! $projectId) {
            Log::warning('FCM skipped: set FIREBASE_CREDENTIALS and FIREBASE_PROJECT_ID in .env');

            return false;
        }

        $accessToken = $this->accessToken($credentialsPath);
        if (! $accessToken) {
            Log::warning('FCM skipped: could not obtain OAuth access token');

            return false;
        }

        $message = [
            'token' => $deviceToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'android' => [
                'priority' => 'HIGH',
                'notification' => [
                    'channel_id' => 'order_alerts_channel',
                    'sound' => 'default',
                ],
            ],
            'apns' => [
                'headers' => [
                    'apns-priority' => '10',
                ],
            ],
        ];

        if ($data !== []) {
            $message['data'] = array_map(static fn ($v) => (string) $v, $data);
        }

        $payload = ['message' => $message];

        $url = sprintf(
            'https://fcm.googleapis.com/v1/projects/%s/messages:send',
            $projectId
        );
        $response = Http::withToken($accessToken)
            ->acceptJson()
            ->post($url, $payload);

        if (! $response->successful()) {
            Log::warning('FCM send failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        }

        return true;
    }

    private function credentialsPath(): ?string
    {
        $path = config('services.firebase.credentials');
        if (! $path) {
            return null;
        }

        if (! str_starts_with($path, '/')) {
            $path = base_path($path);
        }

        return $path;
    }

    private function accessToken(string $jsonPath): ?string
    {
        $jsonKey = json_decode(file_get_contents($jsonPath), true);
        if (! is_array($jsonKey)) {
            return null;
        }

        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $credentials = new ServiceAccountCredentials($scopes, $jsonKey);
        $token = $credentials->fetchAuthToken();

        return $token['access_token'] ?? null;
    }
}
