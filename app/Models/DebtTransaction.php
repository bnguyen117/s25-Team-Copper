<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DebtTransaction extends Model
{
    protected $fillable = [
        'debt_id',
        'amount',
        'interest_paid',
        'principal_paid',
        'transaction_type',
        'description',
        'transaction_date'
    ];

    protected $casts = [
        'transaction_date' => 'date'
    ];

    const OPTIONS = [
        'payment' => 'Payment',
    ];

    public function debt() 
    {
        return $this->belongsTo(Debt::class);
    }
}
