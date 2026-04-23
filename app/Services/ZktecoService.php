<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ZktecoService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(env('ZKTECO_SERVICE_URL', 'http://127.0.0.1:8001'), '/');
    }

    private function post(string $path, array $data = []): array
    {
        try {
            $response = Http::timeout(10)->post("{$this->baseUrl}{$path}", $data);
            // Don't throw — return the response body even on 4xx/5xx
            // so callers can handle errors gracefully
            return $response->json() ?? [];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \RuntimeException(
                "Cannot connect to fingerprint service at {$this->baseUrl}.",
                503
            );
        }
    }

    private function get(string $path): array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}{$path}");
            return $response->json() ?? [];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            throw new \RuntimeException(
                "Cannot connect to fingerprint service at {$this->baseUrl}.",
                503
            );
        }
    }

    public function isServiceRunning(): bool
    {
        try {
            $response = Http::timeout(3)->get("{$this->baseUrl}/status");
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function init(): array         { return $this->post('/init'); }
    public function terminate(): array    { return $this->post('/terminate'); }
    public function status(): array       { return $this->get('/status'); }
    public function capture(): array      { return $this->post('/capture'); }

    public function identify(string $template): array
    {
        return $this->post('/identify', ['template' => $template]);
    }

    public function match(string $t1, string $t2): array
    {
        return $this->post('/match', ['template1' => $t1, 'template2' => $t2]);
    }

    public function register(int $fingerId, array $templates): array
    {
        return $this->post('/register', ['finger_id' => $fingerId, 'templates' => $templates]);
    }

    public function loadTemplates(array $members): array
    {
        return $this->post('/load-templates', ['members' => $members]);
    }

    public function clearDb(): array
    {
        $response = Http::timeout(10)->delete("{$this->baseUrl}/db/clear");
        $response->throw();
        return $response->json();
    }
}
