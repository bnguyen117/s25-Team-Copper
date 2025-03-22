<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    //
    public function index(){

        $user_id = Auth::id();

        $income = 1000;

        $needs_percentage = 0.50;
        $wants_percentage = 0.30;
        $savings_percentage = 0.20;

        $budgeted_needs = $income * $needs_percentage;
        $budgeted_wants = $income * $wants_percentage;
        $budgeted_savings = $income * $savings_percentage;

        return view('budgets', [
            'user_id' = $user_id,
            'income' = $income,
            'needs_percentage' = $needs_percentage,
            'wants_percentage' = $wants_percentage,
            'savings_percentage' = $savings_percentage,
            'budgeted_needs' = $budgeted_needs,
            'budgeted_wants' = $budgeted_wants,
            'budgeted_savings' = $budgeted_savings,
        ]);
    }
}
