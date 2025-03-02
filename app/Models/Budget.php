<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    // Define which fields can be mass-assigned (for security purposes)
    protected $fillable = [
        'user_id',
        'income',
        'rent_payment',
        'car_payment',
        'recommended_savings',
        'addtl_savings',
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

    // Do we really need timestamps here?
    // public $timestamps = true;
}
