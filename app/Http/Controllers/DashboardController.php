<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Debt;
use App\Models\FinancialGoal;
use App\Models\Budget;


class DashboardController extends Controller
{
    /**
     * Display the user's dashboard.
     */
    public function index()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Retrieve debts belonging to the logged-in user
        $debts = Debt::where('user_id', $user->id)->get();

        // Calculate the total sum of minimum payments
        $totalMinimumPayments = $debts->sum('monthly_payment');

        // Calculate total debt amount
        $totalDebt = $debts->sum('amount');

        // Prepare data for the chart
        $debtChartData = $debts->map(function ($debt) {
            return [
                'name' => $debt->debt_name,
                'amount' => $debt->amount,
                'monthly_payment' => $debt->monthly_payment,
            ];
        });

        // Retrieve the distinct categories for the logged-in user
        $categories = Debt::where('user_id', $user->id)
            ->distinct()
            ->pluck('category');

        // Retrieve the latest active financial goal
        $financeGoal = FinancialGoal::where('user_id', $user->id)
            ->where('status', 'active')
            ->latest()
            ->first();

        // Group debts by category and sum the amounts
        $debtByCategory = $debts->groupBy('category')->map(function ($group) {
        return $group->sum('amount');
        });

        // Retrieve the user's budget.
        $budget = Budget::where('user_id', $user->id)->first();

        $goalName = $financeGoal ? $financeGoal->goal_name : 'No Goal Set';
        $targetAmount = $financeGoal ? $financeGoal->target_amount : 1; // Prevent division by zero
        $currentAmount = $financeGoal ? $financeGoal->current_amount : 0;
        $goalProgress = $targetAmount > 0 ? round(($currentAmount / $targetAmount) * 100) : 0;

        return view('dashboard', [
            'user' => $user,
            'debts' => $debts,
            'debt' => $totalDebt, // Pass the total debt to the view
            'debt2' => $totalMinimumPayments,
            'debtChartData' => $debtChartData,
            'goalName' => $goalName,
            'goalProgress' => $goalProgress,
            'categories' => $debtByCategory->keys(),        // aggregated categories
            'debtAmounts' => $debtByCategory->values(),       // aggregated sums per category
            'budget' => $budget ? $budget->income : 5000,
        ]);
    }
}
