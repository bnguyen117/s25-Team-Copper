<?php

namespace App\Gamify\Badges;

use QCod\Gamify\BadgeType;

class BudgetNovice extends BadgeType
{
    /**
     * Description for badge
     *
     * @var string
     */
    protected $description = 'You created your first budget!';
    protected $icon = 'budget-novice.png';

    /**
     * Check is user qualifies for badge
     *
     * @param $user
     * @return bool
     */
    public function qualifier($user)
    {
        //return $user->getPoints() >= 1000;
        return $user->budgets()->count() >= 1;
    }
}
