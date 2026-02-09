<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MeetingSummaryService
{
    protected $apiKey;
    protected $endpoint = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', env('OPENAI_API_KEY'));
    }

    /**
     * Summarize the given transcript using OpenAI API.
     *
     * @param string $transcript
     * @return string
     * @throws \Exception
     */
    public function summarize(string $transcript): string
    {
        if (empty($this->apiKey)) {
            // Mock response if API key is missing (for dev/demo)
            return "【AI要約機能】\nOpenAI APIキーが設定されていません。.envファイルにOPENAI_API_KEYを設定してください。\n\n現在の文字起こし文字数: " . mb_strlen($transcript) . "文字";
        }

        // Truncate if too long (approx 100k chars limit for lightweight models, but let's be safe with 30k chars for now ~10k tokens)
        // Adjust based on model context window.
        $truncatedTranscript = mb_substr($transcript, 0, 30000);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->endpoint, [
                        'model' => 'gpt-4o-mini', // Cost-effective and fast
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => 'あなたはプロフェッショナルな議事録作成アシスタントです。'
                            ],
                            [
                                'role' => 'user',
                                'content' => "以下の会議の文字起こしを、重要な決定事項とネクストアクションを中心に日本語で要約してください。箇条書きで見やすく整理してください。\n\n---\n\n" . $truncatedTranscript
                            ]
                        ],
                        'temperature' => 0.5,
                        'max_tokens' => 1000,
                    ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content') ?? '要約の生成に失敗しました（空のレスポンス）。';
            }

            Log::error('OpenAI API Error: ' . $response->body());
            throw new \Exception('AI要約の生成に失敗しました: ' . $response->status());

        } catch (\Exception $e) {
            Log::error('MeetingSummaryService Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
