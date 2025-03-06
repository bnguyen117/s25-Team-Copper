<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatIfReport extends Model
{
    protected $fillable = [
        'user_id',
        'debt_id',
        'algorithm',
        'original_amount',
        'current_payment',
        'minimum_payment',
        'new_interest_rate',
        'new_payment',
        'total_months',
        'total_interest_paid',
        'timeline',
    ];

    protected $casts = [
        'timeline' => 'array', // JSON to array.
    ];

    /**
     * Many to one relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Many to one relationship
     */
    public function debt()
    {
        return $this->belongsTo(Debt::class, 'debt_id');
    }
}