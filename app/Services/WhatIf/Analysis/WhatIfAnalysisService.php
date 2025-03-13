<?php

namespace App\Services\WhatIf\Analysis;
use App\Models\Debt;


class WhatIfAnalysisService
{
    /**
     * Algo 1: 'interest-rate-change'
     */
    public function interestRateChangeScenario($debtId, $newInterestRate, $monthlyIncome, $totalExpenses)
    {
        // Get the debt's record from the database.
        $debtRecord = Debt::findOrFail($debtId);
        
        // Initialize variables for the following calculations.
        $remainingBalance = $debtRecord->amount;
        $monthlyInterestRate = $newInterestRate / 100 / 12;         // Convert the new annual interest rate to monthly.
        $monthlyPayment = $debtRecord->monthly_payment;
        $timeline = [];                                             // An array to hold the results of each month's calculation.
        $currentMonth = 0;


        // Check if this months payment is affordable.
        $disposableIncome = $monthlyIncome - $totalExpenses;
        if ($monthlyPayment > $disposableIncome) {                                      
            return ['error' => 'Current payment exceeds your disposable income'];
        }

        // Calculate monthly payments until the debt is paid off or 30 years.
        while ($remainingBalance > 0 && $currentMonth < 360) {                         
            $currentMonth++;

            // Calculate this months interest and principal amounts.
            $monthlyInterest = $remainingBalance * $monthlyInterestRate;
            $monthlyPrincipal = $monthlyPayment - $monthlyInterest;   

            // Check if payment covers interest.
            if ($monthlyPrincipal < 0) {
                return ['error' => 'Payment too low to cover monthly interest at the new rate'];
            }

            // Update the debt's remaining balance
            $remainingBalance -= $monthlyPrincipal;

            // Record this month's details.
            $timeline[] = [
                'month' => $currentMonth,
                'balance' => max(0, $remainingBalance),
                'interest_paid' => $monthlyInterest,
                'principal_paid' => $monthlyPrincipal,
            ];

            // Break immediatley once debt is paid off.
            if ($remainingBalance <= 0) break;
        }

        // Return the results.
        return [
            'debt_name' => $debtRecord->debt_name,
            'original_debt_amount' => $debtRecord->amount,
            'original_interest_rate' => $debtRecord->interest_rate,
            'original_monthly_debt_payment' => $debtRecord->monthly_payment,
            'original_minimum_debt_payment' => $debtRecord->minimum_payment,
            'new_interest_rate' => $newInterestRate,
            'timeline' => $timeline,
            'total_months' => $currentMonth,
            'total_interest_paid' => array_sum(array_column($timeline, 'interest_paid')),
        ];
    }

    /**
     * Algo 2 - 'payment-change'
     */
    public function changeMonthlyPaymentScenario ( $debtId, $newMonthlyPayment, $monthlyIncome, $totalExpenses)
    {
        // Get the debt's record from the database.
        $debtRecord = Debt::findOrFail($debtId);
        
        // Initialize variables for the following calculations.
        $remainingBalance = $debtRecord->amount;
        $monthlyInterestRate = $debtRecord->interest_rate / 100/ 12;       // Convert the annual rate to monthly
        $timeline = [];                                                    // An array to hold the results of each month's calculation.
        $currentMonth = 0;

        // Check if the new payment is affordable.
        $disposableIncome = $monthlyIncome - $totalExpenses;
        if ($newMonthlyPayment > $disposableIncome) {                               
            return ['error' => 'Monthly payment exceeds your disposable income'];
        }


        // Calculate monthly payments until the debt is paid off or 30 years.
        while ($remainingBalance > 0 && $currentMonth < 360) {
            $currentMonth++;

            // Calculate this months interest and principal amounts.
            $monthlyInterest = $remainingBalance * $monthlyInterestRate;
            $monthlyPrincipal = $newMonthlyPayment - $monthlyInterest;  

            // Check if payment covers interest.
            if ($monthlyPrincipal < 0) {
                return ['error' => 'Payment too low to cover your monthly interest'];
            }

            // Update the debt's remaining balance
            $remainingBalance -= $monthlyPrincipal;

            // Record this month's details.
            $timeline[] = [
                'month' => $currentMonth,
                'balance' => max(0, $remainingBalance),
                'interest_paid' => $monthlyInterest,
                'principal_paid' => $monthlyPrincipal,
            ];

            // Break immediatley once debt is paid off.
            if ($remainingBalance <= 0) break;

        }

        // Return the results.
        return [
            'debt_name' => $debtRecord->debt_name,
            'original_debt_amount' => $debtRecord->amount,
            'original_interest_rate' => $debtRecord->interest_rate,
            'original_monthly_debt_payment' => $debtRecord->monthly_payment,
            'original_minimum_debt_payment' => $debtRecord->minimum_payment,
            'new_monthly_debt_payment' => $newMonthlyPayment,
            'timeline' => $timeline,
            'total_months' => $currentMonth,
            'total_interest_paid' => array_sum(array_column($timeline, 'interest_paid')),
        ];

    }
}

