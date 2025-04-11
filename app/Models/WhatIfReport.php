<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use PhpParser\Node\NullableType;

class WhatIfReport extends Model
{
    protected $fillable = [
        // Idententifiers
        'user_id',                          // Links a report to a user
        'debt_id',                          // Links a report to a debt
        'financial_goal_id',                // Links a report to a goal

        // Snapshot values at the time of report generation
        'original_debt_amount',             // The debt amount
        'original_interest_rate',           // The annual interest rate 
        'original_monthly_debt_payment',    // The monthly payment
        'original_minimum_debt_payment',    // The minimum monthly payment 

        // New data relating to the report's analysis
        'what_if_scenario',                 // The name of the debt what-if scenario
        'new_interest_rate',                // The new interest rate
        'new_monthly_debt_payment',         // New monthly debt payment amount
        'total_months',                     // The total months to pay off the debt
        'total_interest_paid',              // The total interest paid over the time period
        'timeline',                         // A Json array holding the results of each month.
        'goal_impact',                      // A Json array holding the impact on a goal after scenrio analysis
    ];

    protected $casts = [
        'timeline' => 'array', // JSON to array.
        'goal_impact' => 'array'
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