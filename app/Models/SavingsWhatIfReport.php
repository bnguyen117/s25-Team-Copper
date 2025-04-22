<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingsWhatIfReport extends Model
{
    protected $fillable = [
        //Identifiers
        'user_id',                          // Links a report to a user
        'financial_goal_id',                // Links a report to a goal

        // Snapshot values at the time of report generation
        'original_savings',                 // The savings amount
        'original_interest_rate',           // The annual interest rate
        'current_monthly_savings',          // The monthly savings rate

        // New data relating to the report's analysis
        'what_if_scenario',                 // The name of the savings what-if scenario
        'new_interest_rate',                // The new interest rate
        'new_monthly_savings_rate',              // New monthly savings rate
        'total_months',                     // The total months to reach the savings goal
        'total_interest_earned',            // The total interest earned over the time period
        'timeline',                         // A Json array holding the results of each month.
        'goal_impact',                      // A Json array holding the impact on a goal after scenario analysis
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
}
