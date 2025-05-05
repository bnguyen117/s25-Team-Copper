<?php

namespace App\Services\Rewards;

use App\Gamify\Badges\DebtBuilder;
use App\Gamify\Points\DebtCreated;
use App\Gamify\Badges\BudgetNovice;
use App\Gamify\Points\BudgetCreated;
use QCod\Gamify\Gamify;

class BadgeService
{
    public function awardDebtPoints($user = null)
    {
        $user = $user ?? Auth::user();
        $user->givePoint(new DebtCreated($user));
    }

    public function awardBudgetBadge($user = null)
    {
        $user = $user ?? auth()->user();
        $user->givePoint(new BudgetCreated($user));
    if ((new BudgetNovice())->qualifier($user)) {
        session()->flash('badge_awarded', '🎉 You earned the Budget Novice badge!');
        }
    }
}