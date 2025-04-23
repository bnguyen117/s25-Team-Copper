<?php

namespace App\Gamify\Points;

use QCod\Gamify\PointType;

class CommunityVisited extends PointType
{
    /**
     * Number of points
     *
     * @var int
     */
    public $points = 20;

    /**
     * Point constructor
     *
     * @param $subject
     */
    public function __construct($subject)
    {
        $this->subject = $subject;
        $this->name = 'community_visited';
    }

    /**
     * User who will be receive points
     *
     * @return mixed
     */
    public function payee()
    {
        //return $this->getSubject()->user;
        return $this->subject;
    }
}
