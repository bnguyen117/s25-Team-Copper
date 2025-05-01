<?php

namespace App\Services\WhatIf\Chat;

use App\Models\SavingsWhatIfReport;
use Illuminate\Support\Facades\Log;

/**
 * A collection of methods to process thee user's information.
 */
class SavingWhatIfReportFormatter {
    /**
     * Generates a summary of the user's overall information.
     * Used to provide context to the AI chatbot in its system prompt.
     */
    public function generateSummary(SavingsWhatIfReport $report): string {
        $summary = array_merge(
            $this->formatSavingOverview($report),
            $this->formatOriginalSavingDetails($report),
            $this->formatScenarioResults($report),
            $this->formatGoalImpact($report) ?? [], // empty if null
        );

        $output = implode("\n", $summary);
        Log::info("SavingsWhatIfReport Summary:  ", ['summary' => $output]);
        return $output;
    }

    // General Savings Details
    private function formatSavingOverview(SavingsWhatIfReport $report): array {
        return [
            "Saving Overview",
            sprintf("  - Savings Name: %s", $report->savings_name)
        ];
    }

    // Snapshot details that represent the savings at the time of report.
    private function formatOriginalSavingDetails(SavingsWhatIfReport $report): array {
        return [
            "Original Savings Details at Time of Report:",
            sprintf("  - Original Savings Amount: $%s", number_format($report->original_savings, 2)),
            sprintf("  - Current Monthly Savings Rate: $%s", number_format($report->current_monthly_savings, 2)),
            sprintf("  - Original Interest Rate: %s%%", number_format($report->original_interest_rate, 2)),
        ];
    }

    // Details that relate to the savings after analysis.
    private function formatScenarioResults(SavingsWhatIfReport $report): array {
        return [
            sprintf("What-If Scenario Results (Algorithm: %s):", $report->what_if_scenario),
            sprintf("  - Total Months Until Full Savings Goal: %d", $report->total_months),
            sprintf("  - Total Interest Earned: $%s", number_format($report->total_interest_earned, 2)),
        ];
    }

    // Details about the impact on the user's goal after analysis.
    private function formatGoalImpact(SavingsWhatIfReport $report): array {
        if (empty($report->goal_impact)) {
            return ["No goal impact data available."];
        }

        $i = $report->goal_impact;
        $lines = [
            "Goal Impact:",
            sprintf("  - Goal Name: %s", $report->goal_impact['goal_name']),
            sprintf("  - Current Amount: $%s", number_format($report->goal_impact['current_amount'], 2)),
            sprintf("  - Target Amount: $%s", number_format($report->goal_impact['target_amount'], 2)),
            sprintf("  - Amount Still Needed: $%s", number_format($report->goal_impact['amount_still_needed'], 2)),
            sprintf("  - Monthly Savings Needed: $%s", number_format($report->goal_impact['monthly_savings'], 2)),
            sprintf("  - Projected Months to Reach Goal: %d", $report->goal_impact['projected_months']),
        ];

        $status = "  - Status: ";
        if ($i['monthly_savings'] == 0) {
            // User has no monthly savings
            $status .= sprintf(
                "No progress possible due to zero savings (Expenses: $%s, Income: $%s, Shortfall: $%s)",
                number_format($i['total_expenses'], 2),
                number_format($i['monthly_income'], 2),
                number_format($i['shortfall'], 2)
            );
        }
        // Check if goal is overdue, delayed, or on track 
        elseif ($i['is_overdue'] && $i['amount_still_needed'] > 0) $status .= "Overdue";
        elseif ($i['projected_months'] > $i['target_months']) $status .= "Delayed beyond target";
        else $status .= "On track";

        $lines[] = $status;
        return $lines;
    }
        
}