<?php

namespace App\Services\Rewards;

use Illuminate\Support\Facades\Auth;

use App\Gamify\Badges\DebtBuilder;
use App\Gamify\Points\DebtCreated;
use QCod\Gamify\Gamify;

class BadgeService
{
    public function awardDebtPoints($user = null)
    {
        $user = $user ?? Auth::user();
        $user->givePoint(new DebtCreated($user));
    }

}