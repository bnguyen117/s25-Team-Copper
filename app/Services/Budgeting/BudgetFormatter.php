<?php

namespace App\Services\Budgeting;
use App\Models\{Budget, Debt, FinancialGoal};
use Illuminate\Support\Facades\Auth;


class BudgetFormatter {

    /**
     * Generate a summary of the user's budget, debts, and goals.
     */
    public function generateSummary(): string
    {
        $user = Auth::user();
        $budget = Budget::where('user_id', $user->id)->latest()->first();
        $summary = array_merge(
            $this->formatBudgetOverview($budget),
            $this->formatUserDebtProfile($user->id),
            $this->formatUserGoals($user->id)
        );
        return implode("\n", $summary);
    }

    /**
     * Format the user's budget information
     */
    private function formatBudgetOverview(?Budget $budget): array {
        if (!$budget) return ["Budget Overview:", "  - No budget data available for this user."];

        return [
            "Budget Overview:",
            sprintf("  - Monthly Income: $%s", number_format($budget->income, 2)),
            sprintf("  - Needs Percentage: %s%%", number_format($budget->needs_percentage ?? 0, 2)),
            sprintf("  - Wants Percentage: %s%%", number_format($budget->wants_percentage ?? 0, 2)),
            sprintf("  - Savings Percentage: %s%%", number_format($budget->savings_percentage ?? 0, 2)),
            sprintf("  - Budgeted Needs: $%s", number_format($budget->budgeted_needs ?? 0, 2)),
            sprintf("  - Budgeted Wants: $%s", number_format($budget->budgeted_wants ?? 0, 2)),
            sprintf("  - Budgeted Savings: $%s", number_format($budget->budgeted_savings ?? 0, 2)),
            sprintf("  - Needs Progress: $%s", number_format($budget->needs_progress ?? 0, 2)),
            sprintf("  - Wants Progress: $%s", number_format($budget->wants_progress ?? 0, 2)),
            sprintf("  - Savings Progress: $%s", number_format($budget->savings_progress ?? 0, 2)),
            sprintf("  - Remaining Balance: $%s", number_format($budget->remaining_balance ?? 0, 2)),
            sprintf("  - Last Updated: %s", $budget->updated_at->toDateString()),
        ];
    }

    /**
     * Format the user's debt information
     */
    private function formatUserDebtProfile(int $userId): array {
        $profile = ["User's Debt Profile:"];
        $debts = Debt::where('user_id', $userId)->get();

        if ($debts->isEmpty()) {
            $profile[] = "  - No debts found for this user.";
            return $profile;
        }

        foreach ($debts as $debt) {
            $profile[] = sprintf(
                "  - Debt Name: %s, Category: %s, Current Balance: $%s, Interest Rate: %s%%, Monthly Payment: $%s, Minimum Payment: $%s, Due Date: %s, Description: %s",
                $debt->debt_name,
                $debt->category,
                number_format($debt->amount, 2),
                number_format($debt->interest_rate, 2),
                number_format($debt->monthly_payment, 2),
                number_format($debt->minimum_payment, 2),
                $debt->due_date ? $debt->due_date->toDateString() : 'N/A',
                $debt->description ?: 'No description'
            );
        }
        return $profile;
    }

    /**
     * Format the user's goal information
     */
    private function formatUserGoals(int $userId): array {
        $goals = ["User's Financial Goals:"];
        $userGoals = FinancialGoal::where('user_id', $userId)->get();

        if ($userGoals->isEmpty()) {
            $goals[] = "  - No financial goals found for this user.";
            return $goals;
        }

        foreach ($userGoals as $goal) {
            $goals[] = sprintf(
                "  - Goal Name: %s, Current Amount: $%s, Target Amount: $%s, Priority: %s, Status: %s, Achieve By: %s, Description: %s",
                $goal->goal_name,
                number_format($goal->current_amount ?? 0, 2),
                number_format($goal->target_amount ?? 0, 2),
                $goal->priority ?: 'Not set',
                $goal->status ?: 'Not set',
                $goal->achieve_by ? $goal->achieve_by->toDateString() : 'N/A',
                $goal->description ?: 'No description',
            );
        }
        return $goals;
    }
}