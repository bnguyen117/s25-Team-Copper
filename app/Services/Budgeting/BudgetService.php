<?php

namespace App\Services\Budgeting;

use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class BudgetService
{
    /**
     * Ensures the user's form input is valid for their budget.
     */
    public function validateBudget(array $data, callable $notify): void
    {
        // Determine category and store budget.
        $category = $data['transaction_type'] === 'debt' ? 'needs' : $data['category'];
        $budget = Budget::where('user_id', Auth::id())->first();
        
        $amount = $data['amount'];
        $budgetedCategory = "budgeted_{$category}";
        $categoryProgress = "{$category}_progress";
        $currentProgress = $budget->$categoryProgress;
        $budgetCap = $budget->$budgetedCategory;

        // Ensure the budget allocation is positive.
        if ($budgetCap <= 0) {
            $notify('Invalid Budget', "No budget set for $category. Please update your budget.");
            throw ValidationException::withMessages([]);
        }

        // Ensure the transaction does not exceed the category's budget cap.
        if ($currentProgress + $amount > $budgetCap) {
            $notify('Category Budget Exceeded',
             "Transaction amount 
             ($".number_format($amount, 2).") 
             exceeds remaining $category budget 
             ($".number_format($budgetCap - $currentProgress, 2).")
             .");
            throw ValidationException::withMessages([]);
        }
    }

    public function updateBudget(Transaction $record, bool $reverse = false, callable $notify): void
    {
        if (!$record->category) return;

        // Set the category's spending progress field (e.g. needs_progress)
        $categoryProgress = "{$record->category}_progress";

        $budget = Budget::where('user_id', $record->user_id)->first();
        // Reverse or apply the transaction's budget impact.
        if ($reverse) {
            $budget->$categoryProgress = $budget->$categoryProgress - $record->amount;
            $budget->remaining_balance += $record->amount;
        } else {
            $budget->$categoryProgress += $record->amount;
            $budget->remaining_balance -= $record->amount;
        }
        $budget->save();
    }

    /**
    * Generate a summary of the user's budget spending progress and percentage allocation.
    */
    public function getBudgetSummary(): array
    {
        $budget = Budget::where('user_id', Auth::id())->first();
        $categories = ['needs', 'wants', 'savings'];

        return [
            // Summarize spending progress and budgeted amount for each category.
            'spendingSummary' => array_combine($categories, 
            array_map(fn ($cat) => [
                'spent' => (float) $budget->{"{$cat}_progress"},
                'budget' => (float) $budget->{"budgeted_{$cat}"},
            ], $categories)),

            // Summarize the percentage allocation for each category.
            'percentageSummary' => array_combine($categories, 
            array_map(fn ($cat) => [
                'percentage' => (float) ($budget->{"{$cat}_percentage"}),
            ], $categories)),
        ];
    }
}