<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.ai_service.url', 'http://127.0.0.1:8000');
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout(30)
            ->throw();
    }

    public function health(): array
    {
        try {
            $response = $this->client()->get('/health');
            return $response->json();
        } catch (RequestException $e) {
            Log::error('AI Service health check failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function predict(string $imagePath): array
    {
        try {
            $response = $this->client()->attach(
                'file', file_get_contents($imagePath), basename($imagePath)
            )->post('/predict');

            return $response->json();
        } catch (RequestException $e) {
            Log::error('AI Service prediction failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
