<?php

namespace App\Services\WhatIf\Chat;

use App\Models\{WhatIfReport, Debt};
use Illuminate\Support\Facades\Log;

class WhatIfReportFormatter
{

    /**
     * Generates a summary of the user's overall information and given report.
     * Used to provide context to the AI chatbot in its system prompt.
     */
    public function generateSummary(WhatIfReport $report): string
    {
        $summary = [
            $this->formatDebtOverview($report),
            $this->formatOriginalDebtDetails($report),
            $this->formatScenarioResults($report),
            $this->formatAdditionalDetails($report),
            $this->formatUserDebtProfile($report),
        ];
        return implode("\n", array_filter($summary));
    }

    /**
     * General Debt details
     */
    private function formatDebtOverview(WhatIfReport $report): string
    {
        return "Debt Overview:\n" .
            "  - Debt Name: {$report->debt->debt_name}\n" .
            "  - Debt Category: {$report->debt->category}\n";
    }

    /**
     * Snapshot details that represent the debt at the time of report.
     */
    private function formatOriginalDebtDetails(WhatIfReport $report): string
    {
        return "Original Debt Details at Time of Report:\n" .
            "  - Original Debt Amount: $" . number_format($report->original_debt_amount, 2) . "\n" .
            "  - Original Monthly Payment: $" . number_format($report->original_monthly_debt_payment, 2) . "\n" .
            "  - Original Interest Rate: " . number_format($report->original_interest_rate ?? 0, 2) . "%\n" .
            "  - Original Minimum Monthly Payment: $" . number_format($report->original_minimum_debt_payment ?? 0, 2) . "\n";
    }

    /**
     * Details that relate to the debt after analysis.
     */
    private function formatScenarioResults(WhatIfReport $report): string
    {
        $result = "What-If Scenario Results (Algorithm: {$report->what_if_scenario}):\n" .
            "  - Total Months Until Full Repayment: {$report->total_months}\n" .
            "  - Total Interest Paid: $" . number_format($report->total_interest_paid, 2) . "\n" .
            "  - Timeline (Monthly Breakdown): " . json_encode($report->timeline) . "\n";

        if ($report->new_interest_rate) {
            $result .= "  - New Interest Rate: " . number_format($report->new_interest_rate, 2) . "%\n";
        }
        if ($report->new_monthly_debt_payment) {
            $result .= "  - New Monthly Payment: $" . number_format($report->new_monthly_debt_payment, 2) . "\n";
        }
        return $result;
    }

    /**
     * Details on all of the user's debts.
     */
    private function formatUserDebtProfile(WhatIfReport $report): string
    {
        $userDebts = Debt::where('user_id', $report->user_id)->get();
        $profile = ["User's Full Debt Profile:"];

        if ($userDebts->isEmpty()) {
            $profile[] = "No additional debts found for this user.";
        } else {
            foreach ($userDebts as $debt) {
                if ($debt->id !== $report->debt_id) {
                    $profile[] = "- Debt Name: {$debt->debt_name}, " .
                        "Category: {$debt->category}, " .
                        "Current Balance: $" . number_format($debt->amount, 2) . ", " .
                        "Interest Rate: " . number_format($debt->interest_rate, 2) . "%, " .
                        "Monthly Payment: $" . number_format($debt->monthly_payment ?? 0, 2) . ", " .
                        "Minimum Payment: $" . number_format($debt->minimum_payment ?? 0, 2);
                }
            }
        }
        return implode("\n", $profile);
    }


    /**
     * Additonal details miscellaneous details.
     */
    private function formatAdditionalDetails(WhatIfReport $report): ?string
    {
        $details = [];
        if ($report->debt->description) {
            $details[] = "Description: {$report->debt->description}";
        }
        if ($report->debt->due_date) {
            $details[] = "Due Date: {$report->debt->due_date}";
        }
        return $details ? implode("\n", $details) : null;
    }
}