<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ZoomService
{
    protected string $accountId;
    protected string $clientId;
    protected string $clientSecret;
    protected string $hostUserId;

    public function __construct()
    {
        $this->accountId = config('zoom.account_id');
        $this->clientId = config('zoom.client_id');
        $this->clientSecret = config('zoom.client_secret');
        // 'me' is a special keyword in Zoom API context, but if referencing a specific user, 
        // using their email or ID is safer if 'me' refers to the token owner (which is the app itself in S2S).
        // For S2S, 'me' often doesn't work as expected if the app isn't a user. We usually need a specific userId.
        // However, config defaults to 'me'. Let's assume 'me' might fail in S2S context if not scoped to a user.
        // Usually S2S app acts on behalf of the account.
        // To create a meeting, we need to specify which user is hosting.
        // If ZOOM_HOST_USER_ID is not set, we might default to the account owner or first user, 
        // but 'me' is invalid for S2S token (it has no "user" identity).
        // I will default to 'me' but it might fail. The user didn't specify a host ID in the prompt example values,
        // but it is in .env.example.
        $this->hostUserId = config('zoom.host_user_id', 'me');
    }

    /**
     * Get Server-to-Server OAuth Access Token
     */
    public function getAccessToken()
    {
        // Check Cache
        if (Cache::has('zoom_s2s_token')) {
            return Cache::get('zoom_s2s_token');
        }

        $url = 'https://zoom.us/oauth/token?grant_type=account_credentials&account_id=' . $this->accountId;

        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->post($url);

        if ($response->failed()) {
            Log::error('Zoom OAuth Failed: ' . $response->body());
            throw new \Exception('Zoomアクセストークンの取得に失敗しました。');
        }

        $data = $response->json();
        $token = $data['access_token'];
        $expiresIn = $data['expires_in'];

        // Cache token (subtract 60s for safety)
        Cache::put('zoom_s2s_token', $token, $expiresIn - 60);

        return $token;
    }

    /**
     * Create a Zoom Meeting
     * 
     * @param string $topic
     * @param string $startTime (ISO 8601)
     * @param int $duration (minutes)
     * @param string|null $agenda
     * @return array
     */
    public function createMeeting(string $topic, string $startTime, int $duration, ?string $agenda = null)
    {
        $token = $this->getAccessToken();
        $userId = $this->hostUserId;

        // Note: For S2S, if 'me' is used, it might error. Real user ID is preferred.
        // If config is 'me', we try it, but usually S2S API requires userId.
        // Endpoints: POST /users/{userId}/meetings

        $url = "https://api.zoom.us/v2/users/{$userId}/meetings";

        $response = Http::withToken($token)
            ->post($url, [
                'topic' => $topic,
                'type' => 2, // Scheduled Meeting
                'start_time' => $startTime, // Check format: YYYY-MM-DDTHH:MM:SSZ
                'duration' => $duration,
                'timezone' => 'Asia/Tokyo',
                'agenda' => $agenda,
                'settings' => [
                    'host_video' => true,
                    'participant_video' => true,
                    'join_before_host' => false,
                    'mute_upon_entry' => true,
                    // 'waiting_room' => true,
                    'auto_recording' => 'cloud', // Optional, maybe beneficial
                ]
            ]);

        if ($response->failed()) {
            Log::error('Zoom Create Meeting Failed: ' . $response->body());
            throw new \Exception('Zoomミーティングの作成に失敗しました: ' . $response->json('message', 'Unknown Error'));
        }

        return $response->json();
    }

    /**
     * Get Past Meeting Details (For Sync)
     * Using /past_meetings/{meetingId} to get actual duration and end_time
     */
    public function getPastMeeting(string $meetingId)
    {
        $token = $this->getAccessToken();

        // Double encode if it contains '/' (UUID)
        if (str_contains($meetingId, '/') || str_contains($meetingId, '+')) {
            $meetingId = urlencode(urlencode($meetingId));
        }

        $url = "https://api.zoom.us/v2/past_meetings/{$meetingId}";

        $response = Http::withToken($token)->get($url);

        if ($response->failed()) {
            if ($response->status() === 404) {
                return null;
            }
            Log::error('Zoom Get Past Meeting Failed: ' . $response->body());
            throw new \Exception('Zoom過去ミーティング情報の取得に失敗しました: ' . $response->json('message', 'Unknown Error'));
        }

        return $response->json();
    }

    /**
     * Get Meeting Participants (Report)
     * Using /report/meetings/{meetingId}/participants
     */
    /**
     * Get Meeting Participants (Report)
     * Using /report/meetings/{meetingId}/participants
     * Scope: report:read:admin
     */
    public function getParticipants(string $meetingId)
    {
        $token = $this->getAccessToken();

        // Double encode if it contains '/' (UUID)
        if (str_contains($meetingId, '/') || str_contains($meetingId, '+')) {
            $meetingId = urlencode(urlencode($meetingId));
        }

        // Report API - returns 'participants' array
        // https://api.zoom.us/v2/report/meetings/{meetingId}/participants
        $url = "https://api.zoom.us/v2/report/meetings/{$meetingId}/participants?page_size=300";

        $response = Http::withToken($token)->get($url);

        if ($response->failed()) {
            if ($response->status() === 404) {
                return null;
            }
            Log::error('Zoom Get Participants (Report) Failed: ' . $response->body());
            // Don't throw, just return empty so sync continues? 
            // Better to log error but return empty array or null to avoid breaking loop.
            return [];
        }

        return $response->json('participants');
    }
}
