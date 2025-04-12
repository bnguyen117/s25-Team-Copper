<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    //  Columns that are allowed to be mass assigned.
    protected $fillable = [
        'user_id',
        'debt_name',
        'amount',
        'interest_rate',
        'minimum_payment',
        'monthly_payment',
        'description',
        'due_date',
        'category',
    ];

    protected $casts = [
        'due_date' => 'date'
    ];

    /**
     * Set a many to one one relationship.
     * 
     * Where many debts can be linked to one User record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(DebtTransaction::class);
    }
}
