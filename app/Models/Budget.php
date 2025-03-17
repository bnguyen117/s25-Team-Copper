<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    //
    protected $fillable = [
        'user_id',
        'budget_type',
        'monthly_income',
        'budgeted_needs',
        'budgeted_wants',
        'budgeted_savings',
        'needs_spending_this_month',
        'wants_spending_this_month',
        'amount_saved_this_month',
    ]
    
}
