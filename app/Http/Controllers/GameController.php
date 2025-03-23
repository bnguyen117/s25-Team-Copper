<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GameLayerService;

class GameController extends Controller
{
    protected $gameLayerService;

    public function __construct(GameLayerService $gameLayerService)
    {
        $this->gameLayerService = $gameLayerService;
    }

    public function showGame($gameId)
    {
        $gameDetails = $this->gameLayerService->fetchGameDetails($gameId);

        return response()->json($gameDetails);
    }
}