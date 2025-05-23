<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    // Define which fields can be mass-assigned (for security purposes)
    protected $fillable = [
        'user_id',
        'income',
        'needs_percentage',
        'wants_percentage',
        'savings_percentage',
        'budgeted_needs',
        'budgeted_wants',
        'budgeted_savings',
        'needs_progress',
        'wants_progress',
        'savings_progress',
        'remaining_balance',
    ];

    /**
     * Set a one-to-one relationship.
     * 
     * Where one user can have one budget.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // If you want to ensure that created_at and updated_at timestamps are managed by Eloquent, leave them enabled (default is true)
    public $timestamps = true;
}
