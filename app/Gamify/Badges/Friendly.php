<?php

namespace App\Gamify\Badges;

use QCod\Gamify\BadgeType;

class Friendly extends BadgeType
{
    /**
     * Description for badge
     *
     * @var string
     */
    protected $description = 'Visited the community page for the first time!';
    protected $icon = 'friendly.png';

    /**
     * Check is user qualifies for badge
     *
     * @param $user
     * @return bool
     */
    public function qualifier($user)
    {
        return $user->reputations()->where('name', 'community_visited')->exists();
    }
}
