<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    //
    protected $fillable = [
        'user_id',
        'budget_name',
        'total_income',
        'total_expenses',
    ];

    /**
     * 
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
