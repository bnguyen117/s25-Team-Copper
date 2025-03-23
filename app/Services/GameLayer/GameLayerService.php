<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GameLayerService
{
    protected $apiKey;
    protected $baseUrl = "https://api.gamelayer.io";

    public function __construct()
    {
        $this->apiKey = config('services.gamelayer.api_key');
    }

    /**
     * Fetch game details by ID
     */
    public function fetchGameDetails($gameId)
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Accept' => 'application/json'
        ])->get("{$this->baseUrl}/games/{$gameId}");

        return $response->json();
    }
}