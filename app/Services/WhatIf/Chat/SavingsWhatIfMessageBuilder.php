<?php

namespace App\Services\WhatIf\Chat;

use App\Models\SavingsWhatIfReport;

class SavingsWhatIfMessageBuilder {
    // Build the initial user message based on the report scenario
    public function buildInitialMessage(SavingsWhatIfReport $report): string {
        $message = 
        "Hello! I'm here to help with your financial planning based on your What-If Report " .
        "for **{$report->savings_name}** using the **{$report->what_if_scenario}** scenario. " .
        "\nHere's your report summary:";

        if ($report->what_if_scenario === 'savings-change') {
            $message .= $this->buildSavingsChangeMessage($report) . "\n";
        } elseif ($report->what_if_scenario === 'saving-interest-rate-change') {
            $message .= $this->buildSavingsInterestRateChangeMessage($report) . "\n";
        }

        $message .= "\nHow can I assist you with this report today?";
        return $message;
    }

    // Build message for savings change scenario
    private function buildSavingsChangeMessage(SavingsWhatIfReport $report): string {
        return 
        "\n\n**Original Savings Details**\n" .
        "- **Monthly Savings Contribution**: $" . number_format($report->current_monthly_savings, 2) . "/month\n" .
        "- **Savings Amount**: $ " . number_format($report->original_savings, 2) . "\n" .
        "- **Interest Rate**: " . number_format($report->original_interest_rate ?? 0, 2) . "%\n\n" . 
        "**New Savings Contribution Details**\n" .
        "- **New Monthly Savings Contribution**: $" . number_format($report->new_monthly_savings_rate, 2) . "/month\n" .
        "- **Total Months to Save**: {$report->total_months}\n" .
        "- **Total Interest Earned**: $" . number_format($report->total_interest_earned, 2);
    }

    // Build message for interest rate change scenario
    private function buildSavingsInterestRateChangeMessage(SavingsWhatIfReport $report): string {
        return 
        "\n\n**Original Savings Details**\n" .
        "- **Interest Rate**: " . number_format($report->original_interest_rate ?? 0, 2) . "%\n" .
        "- **Monthly Savings Contribution**: $" . number_format($report->current_monthly_savings, 2) . "/month\n" .
        "- **Savings Amount**: $" . number_format($report->original_savings, 2) . "\n\n" .
        "**New Interest Rate Details**\n" .
        "- **New Interest Rate**: " . number_format($report->new_interest_rate, 2) . "%\n" .
        "- **Total Months to Save**: {$report->total_months}\n" .
        "- **Total Interest Earned**: $" . number_format($report->total_interest_earned, 2);
    }
}