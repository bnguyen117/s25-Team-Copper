<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Debt;
use App\Models\FinancialGoal;
use App\Models\Budget;
use App\Models\Transaction;
use Filament\Notifications\Notification;
use Carbon\Carbon;


class DashboardController extends Controller
{
    /**
     * Display the user's dashboard.
     */
    public function index()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Reset session flags if user has modified their debts.
        if (session('debt_action_occurred')) {
            session()->forget('debt_notification_shown');
            session()->forget('debt_action_occurred');
        }

        // Retrieve debts belonging to the logged-in user
        $debts = Debt::where('user_id', $user->id)->get();

        // Retrieve debt transactions
        $debtTransactions = Transaction::where('user_id', $user->id)
            ->whereNotNull('debt_id')
            ->get(['debt_id', 'principal_paid', 'transaction_date']);

        // Check for debts with due dates within 3 days from today
        $today = Carbon::today();
        $debtsDueSoon = $debts->filter(function ($debt) use ($today) {
            if ($debt->due_date) {
                $dueDate = Carbon::parse($debt->due_date);
                return $dueDate->isBetween($today, $today->copy()->addDays(3));
            }
            return false;
        });

        // Show a Filament notification for debts due within 3 days, once per session, or until the user modifies their debts.
        if ($debtsDueSoon->isNotEmpty() && !session('debt_notification_shown')) {
            $debtDetails = $debtsDueSoon->map(function ($debt) {
                $dueDate = Carbon::parse($debt->due_date)->format('M j, Y');
                return "{$debt->debt_name} ({$dueDate})";
            })->implode('<br>');

            Notification::make()
                ->title('Upcoming Debt Due Dates')
                ->warning()
                ->body("The following debts are due within 3 days:<br>{$debtDetails}")
                ->send();
            session(['debt_notification_shown' => true]);
        }

        // Calculate the total sum of minimum payments
        $totalMinimumPayments = $debts->sum('monthly_payment');

        // Calculate total debt amount
        $totalDebt = $debts->sum('amount');

        // Prepare data for the chart
        $debtChartData = $debts->map(function ($debt) use ($debtTransactions) {
            $totalPaid = $debtTransactions->where('debt_id', $debt->id)->sum('principal_paid');
            return [
                'id' => $debt->id,
                'name' => $debt->debt_name,
                'amount' => $debt->amount,
                'initial_amount' => $debt->amount + $totalPaid, // Adding back the amount paid to get the debt's initial amount.
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

        // Retrieve or create the user's budget with a default income of 5000.
        $budget = Budget::where('user_id', $user->id)->first();
        if (!$budget) {
            $budget = Budget::Create([
                    'user_id' => $user->id,
                    'income' => 5000,
                    'needs_percentage' => 50,
                    'wants_percentage' => 30,
                    'savings_percentage' => 20,
                    'budgeted_needs' => 5000 * 0.50,
                    'budgeted_wants' => 5000 * 0.30,
                    'budgeted_savings' => 5000 * 0.20,
                    'needs_progress' => 0,
                    'wants_progress' => 0,
                    'savings_progress' => 0,
                    'remaining_balance' => 5000,
            ]);
    }

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
            'budget' => $budget->income,
            'remaining_balance' => $budget->remaining_balance,
            'debtTransactions' => $debtTransactions,
        ]);
    }
}
