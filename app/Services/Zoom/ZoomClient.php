<?php

namespace App\Services\Zoom;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ZoomClient
{
    private ?string $accountId = null;
    private ?string $clientId = null;
    private ?string $clientSecret = null;
    private string $baseUrl = 'https://api.zoom.us/v2';

    public function __construct()
    {
        $this->accountId = config('services.zoom.account_id');
        $this->clientId = config('services.zoom.client_id');
        $this->clientSecret = config('services.zoom.client_secret');
    }

    /**
     * Get Access Token (S2S OAuth)
     */
    public function getAccessToken(): ?string
    {
        return Cache::remember('zoom_access_token', 3500, function () {
            $response = Http::asForm()
                ->withBasicAuth($this->clientId, $this->clientSecret)
                ->post('https://zoom.us/oauth/token', [
                    'grant_type' => 'account_credentials',
                    'account_id' => $this->accountId,
                ]);

            if ($response->failed()) {
                Log::error('Zoom OAuth Failed', $response->json());
                return null;
            }

            return $response->json('access_token');
        });
    }

    /**
     * Create Meeting
     */
    public function createMeeting(string $topic, string $startTime, int $durationMinutes): ?array
    {
        // Check if credentials exist
        if (empty($this->accountId) || empty($this->clientId) || empty($this->clientSecret)) {
            Log::warning('Zoom Credentials missing. Running in MOCK MODE.');

            // Mock Response
            $mockId = rand(1000000000, 9999999999);
            return [
                'id' => $mockId,
                'topic' => $topic,
                'join_url' => "https://zoom.us/j/{$mockId}?pwd=mock",
                'start_url' => "https://zoom.us/s/{$mockId}?pwd=mock",
                'password' => 'mock123',
            ];
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return null;
        }

        // Use 'me' if the account ID belongs to the owner, otherwise we need a specific user ID.
        // For S2S, usually we need to specify the user ID. 
        // We will default to the user specified in config, or try 'me' context if possible, 
        // but often S2S 'me' is invalid. Let's use a config value for HOST_USER_ID or fetch the first user.
        // For simplicity in MVP, we assume we configure the host user ID.
        $userId = config('services.zoom.host_user_id');

        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/users/{$userId}/meetings", [
                'topic' => $topic,
                'type' => 2, // Scheduled Meeting
                'start_time' => $startTime, // ISO 8601
                'duration' => $durationMinutes,
                'timezone' => 'Asia/Tokyo',
                'settings' => [
                    'host_video' => true,
                    'participant_video' => true,
                    'join_before_host' => false,
                    'mute_upon_entry' => true,
                    'waiting_room' => true,
                ],
            ]);

        if ($response->failed()) {
            Log::error('Zoom Create Meeting Failed', $response->json());
            return null;
        }

        return $response->json();
    }

    /**
     * Delete Meeting
     */
    public function deleteMeeting(string $meetingId): bool
    {
        // Mock Mode check
        if (empty($this->accountId) || empty($this->clientId) || empty($this->clientSecret)) {
            Log::warning('running in MOCK MODE: Meeting deleted.');
            return true;
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return false;
        }

        $response = Http::withToken($token)
            ->delete("{$this->baseUrl}/meetings/{$meetingId}");

        if ($response->failed() && $response->status() !== 404) {
            Log::error('Zoom Delete Meeting Failed', $response->json());
            return false;
        }

        return true;
    }
}
