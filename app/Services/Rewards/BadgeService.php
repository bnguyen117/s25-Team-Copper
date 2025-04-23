<?php

namespace App\Services\Rewards;

use App\Gamify\Badges\DebtBuilder;
use App\Gamify\Points\DebtCreated;

class BadgeService
{
    public function syncDebtRelatedBadges($user)
    {
        /*
        if ((new DebtBuilder())->qualifier($user)) {   
            //$user->attachBadge(new DebtBuilder());
            $user->givePoint(new DebtCreated($debt));
        } 
        /*else {
            //$user->detachBadge(DebtBuilder::class);
            return;
        }
            */
    
            /* DebtBuilder badge, didn't work
            $user = Auth::user();
            $user->givePoint(new DebtCreated($user));
            */

        
    }
}