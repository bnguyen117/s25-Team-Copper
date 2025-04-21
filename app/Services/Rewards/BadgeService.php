<?php

namespace App\Services\Rewards;

use App\Gamify\Badges\DebtBuilder;

class BadgeService
{
    public function syncDebtRelatedBadges($user)
    {
        
        if ((new DebtBuilder())->qualifier($user)) {   
            $user->attachBadge(new DebtBuilder());
        } else {
            $user->detachBadge(DebtBuilder::class);
        }
    }
}