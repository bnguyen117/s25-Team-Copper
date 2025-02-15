<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialGoal extends Model
{
    //  Columns that are allowed to be mass assigned.
    protected $fillable = [
        'user_id',
        'goal_name',
        'target_amount',
        'current_amount',
        'priority',
        'status',
        'description',
        'achieve_by',
    ];

    /**
     * Set a many to one one relationship.
     * 
     * Where many financial gaols can belong to to one User record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
