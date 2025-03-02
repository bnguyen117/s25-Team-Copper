<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Debt;
use App\Models\Budget;

class BudgetController extends Controller
{

    public function createBudget(User $user){
        // Identify the user
        $user = Auth::user();
        
        // Pull up and total the user's debts
        $debts = Debt::where('user_id', $user->id)->get();
        $user_debts = $debts->sum('amount')

        // Total expenses: Rent, etc.
        $user_rent = Budget::where('user_id', $user->id)->value('rent_payment');
        $user_car_payment = Budget::where('user_id', $user->id)->value('car_payment');

        $user_expenses = $user_debts + $user_rent + $user_car_payment;

        // Deduct expenses from Income
        $user_income = Budget::where('user_id', $user->id)->value('income');
        $remaining_balance = $user_income - $user_expenses;

        // Allocate savings - Default: 20%
        $reccomended_savings = $remaining_balance * 0.2;
        $remaining_balance -= $reccomended_savings;
    }
}
