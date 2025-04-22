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
            $this->formatUserSavingsProfile($report),
            $this->formatAdditionalDetails($report) ?? []
        );

        $output = implode("\n", $summary);
        Log::info("SavingsWhatIfReport Summary:  ", ['summary' => $output]);
        return $output;
    }

    // General Savings Details
    private function formatSavingOverview(SavingsWhatIfReport $report): array {
        return [
            "Saving Overview",
            sprintf(" - ")
        ]
    }
}