<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'user_id',
        'debt_id',
        'category',
        'name',
        'amount',
        'interest_paid',
        'princiapl_paid',
        'transaction_date',
        'transaction_type',
        'description',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'interest_paid' => 'decimal:2',
        'princiapl_paid' => 'decimal:2',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function debt() {
        return $this->belongsTo(Debt::class);
    }
}