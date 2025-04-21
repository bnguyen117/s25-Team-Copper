<?php

namespace App\Gamify\Badges;

use QCod\Gamify\BadgeType;

class DebtBuilder extends BadgeType
{
    /**
     * Description for badge
     *
     * @var string
     */
    protected $description = 'You added your first two debts.';
    protected $icon = 'debt-builder.png';

    /**
     * Check is user qualifies for badge
     *
     * @param $user
     * @return bool
     */
    public function qualifier($user)
    {
        return $user->debts()->count() >= 2;  //returns true if user has at least 2 debts, probably wrong logic
    }
}
