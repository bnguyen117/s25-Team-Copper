<?php

namespace App\Services\WhatIf\Chat;

use App\Models\{WhatIfReport, Debt};
use Illuminate\Support\Facades\Log;

/**
 * A collection of methods to process thee user's information.
 */
class WhatIfReportFormatter
{
    /**
     * Generates a summary of the user's overall information.
     * Used to provide context to the AI chatbot in its system prompt.
     */
    public function generateSummary(WhatIfReport $report): string
    {
        $summary = array_merge(
            $this->formatDebtOverview($report),
            $this->formatOriginalDebtDetails($report),
            $this->formatScenarioResults($report),
            $this->formatGoalImpact($report) ?? [], //empty if null
            $this->formatUserDebtProfile($report),
            $this->formatAdditionalDetails($report) ?? []
        );

        $output = implode("\n", $summary);
        Log::info('WhatIfReport Summary:', ['summary' => $output]);
        return $output;
    }

    /** General Debt details */
    private function formatDebtOverview(WhatIfReport $report): array
    {
        return [
            "Debt Overview:",
            sprintf("  - Debt Name: %s", $report->debt->debt_name),
            sprintf("  - Debt Category: %s", $report->debt->category),
        ];
    }

    /** Snapshot details that represent the debt at the time of report. */
    private function formatOriginalDebtDetails(WhatIfReport $report): array
    {
        return [
            "Original Debt Details at Time of Report:",
            sprintf("  - Original Debt Amount: $%s", number_format($report->original_debt_amount, 2)),
            sprintf("  - Original Monthly Payment: $%s", number_format($report->original_monthly_debt_payment, 2)),
            sprintf("  - Original Interest Rate: %s%%", number_format($report->original_interest_rate, 2)),
            sprintf("  - Original Minimum Monthly Payment: $%s", number_format($report->original_minimum_debt_payment, 2)),
        ];
    }

    /** Details that relate to the debt after analysis. */
    private function formatScenarioResults(WhatIfReport $report): array
    {
        $lines = [
            sprintf("What-If Scenario Results (Algorithm: %s):", $report->what_if_scenario),
            sprintf("  - Total Months Until Full Repayment: %d", $report->total_months),
            sprintf("  - Total Interest Paid: $%s", number_format($report->total_interest_paid, 2)),
            sprintf("  - Timeline (Monthly Breakdown): %s", json_encode($report->timeline)),
        ];
        
        // Scenario specific lines
        if ($report->new_interest_rate)
            $lines[] = sprintf("  - New Interest Rate: %s%%", number_format($report->new_interest_rate, 2));
        if ($report->new_monthly_debt_payment)
            $lines[] = sprintf("  - New Monthly Payment: $%s", number_format($report->new_monthly_debt_payment, 2));
    
        return $lines;
    }

    /** Details about the impact on the user's financial goal. */
    private function formatGoalImpact(WhatIfReport $report): ?array
    {
        if (!$report->goal_impact) return null; // Null if user did not select a goal

        $impact = $report->goal_impact;
        $lines = [
            "Goal Impact:",
            sprintf("  - Goal Name: %s", $impact['goal_name']),
            sprintf("  - Current Amount: $%s", number_format($impact['current_amount'], 2)),
            sprintf("  - Target Amount: $%s", number_format($impact['target_amount'], 2)),
            sprintf("  - Remaining Amount: $%s", number_format($impact['remaining_amount'], 2)),
            sprintf("  - Monthly Savings After Expenses: $%s", number_format($impact['monthly_savings'], 2)),
            sprintf("  - Months Until Achieved: %s", $impact['months_to_goal'] ?? 'N/A'),
            sprintf("  - Target Months: %d", $impact['achieve_by_months']),
        ];
    
        $status = $impact['monthly_savings'] == 0 ?

            // User has no monthly savings
            sprintf(
                "  - Status: No progress possible due to zero savings (Expenses: $%s, Income: $%s, Shortfall: $%s)",
                number_format($impact['total_expenses'], 2),
                number_format($impact['monthly_income'], 2),
                number_format($impact['shortfall'], 2)
            )
            
            // Determine if the goal is delayed or on track
            : ($impact['months_to_goal'] > $impact['achieve_by_months']
                ? "  - Status: Delayed beyond target"
                : "  - Status: On track");
    
        $lines[] = $status;
        return $lines;
    }

    /** Details the rest of the user's debts. */
    private function formatUserDebtProfile(WhatIfReport $report): array
    {
        $profile = ["User's Full Debt Profile:"];
    
        // Grab all user debts except the one linked to the report
        $additionalDebts = Debt::where('user_id', $report->user_id)
        ->where('id', '!=', $report->debt_id)->get();
    
        if ($additionalDebts->isEmpty()) {
            $profile[] = "No additional debts found for this user.";
            return $profile;
        }
    
        // Append each additonal debt's information
        foreach ($additionalDebts as $debt) {
            $profile[] = sprintf(
                "- Debt Name: %s, Category: %s, Current Balance: $%s, Interest Rate: %s%%, Monthly Payment: $%s, Minimum Payment: $%s",
                $debt->debt_name,
                $debt->category,
                number_format($debt->amount, 2),
                number_format($debt->interest_rate, 2),
                number_format($debt->monthly_payment, 2),
                number_format($debt->minimum_payment, 2),
            );
        }
        return $profile;
    }

    /** Additional miscellaneous details. */
    private function formatAdditionalDetails(WhatIfReport $report): ?array
    {
        $details = [];

        if ($report->debt->description)
            $details[] = sprintf("Description: %s", $report->debt->description);
    
        if ($report->debt->due_date)
            $details[] = sprintf("Due Date: %s", $report->debt->due_date);
        
        return $details ? $details : null;
    }
}