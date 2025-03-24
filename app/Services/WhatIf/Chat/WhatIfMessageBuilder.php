<?php

namespace App\Services\WhatIf\Chat;

use App\Models\WhatIfReport;

class WhatIfMessageBuilder
{
    /**
     * Build the initial user message based on the report scenario
     */
    public function buildInitialMessage(WhatIfReport $report): string
    {
        $message = 
            "Hello! I'm here to help with your financial planning based on your What-If Report " .
            "for **{$report->debt->debt_name}** using the **{$report->what_if_scenario}** scenario. " .
            "\nHere's your report summary:";

        if ($report->what_if_scenario === 'payment-change') {
            $message .= $this->buildPaymentChangeMessage($report) . "\n";
        } 
        
        elseif ($report->what_if_scenario === 'interest-rate-change') {
            $message .= $this->buildInterestRateChangeMessage($report) . "\n";
        }

        $message .= "\nHow can I assist you with this report today?";
        return $message;
    }

    /**
     * Build message for payment change scenario
     */
    private function buildPaymentChangeMessage(WhatIfReport $report): string
    {
        return 
        "\n\n**Original Debt Details**\n" .
        "- **Monthly Payment**: $" . number_format($report->original_monthly_debt_payment, 2) . "/month\n" .
        "- **Debt Amount**: $" . number_format($report->original_debt_amount, 2) . "\n" .
        "- **Interest Rate**: " . number_format($report->original_interest_rate ?? 0, 2) . "%\n\n" .
        "**New Payment Details**\n" .
        "- **New Monthly Payment**: $" . number_format($report->new_monthly_debt_payment, 2) . "/month\n" .
        "- **Total Months to Pay Off**: {$report->total_months}\n" .
        "- **Total Interest Paid**: $" . number_format($report->total_interest_paid, 2);
    }

    /**
     * Build message for interest rate change scenario
     */
    private function buildInterestRateChangeMessage(WhatIfReport $report): string
    {
        return 
        "\n\n**Original Debt Details**\n" .
        "- **Interest Rate**: " . number_format($report->original_interest_rate ?? 0, 2) . "%\n" .
        "- **Monthly Payment**: $" . number_format($report->original_monthly_debt_payment, 2) . "/month\n" .
        "- **Debt Amount**: $" . number_format($report->original_debt_amount, 2) . "\n\n" .
        "**New Interest Rate Details**\n" .
        "- **New Interest Rate**: " . number_format($report->new_interest_rate, 2) . "%\n" .
        "- **Total Months to Pay Off**: {$report->total_months}\n" .
        "- **Total Interest Paid**: $" . number_format($report->total_interest_paid, 2);
    }
}