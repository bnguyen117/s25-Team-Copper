<?php

namespace App\Services;
use App\Models\Debt;


class WhatIfAnalysisService
{
    /**
     * Algo 1
     */
    public function changeInterestRateScenario($debtId, $newInterestRate, $monthlyIncome, $monthlyExpenses)
    {
        $debtRecord = Debt::findOrFail($debtId);                         
        $remainingBalance = $debtRecord->amount;                                        // Starts at the total debt amount currently
        $monthlyInterestRate = $newInterestRate / 100 / 12;                             // Convert annual rate to monthly
        $monthlyPayment = $debtRecord->monthly_payment;                                 // The amount the user plans to pay monthly
        $timeline = [];                                                                 // Holds the results of each month's calculation
        $currentMonth = 0;                                                              // Counter variable

        $disposableIncome = $monthlyIncome - $monthlyExpenses;                          // The amount of income left over after expenses
        if ($monthlyPayment > $disposableIncome) {                                      
            return ['error' => 'Current payment exceeds your disposable income'];       // Not enough disposable income this month
        }

        while ($remainingBalance > 0 && $currentMonth < 360) {                          // Continue until debt is paid in full or 30 years.
            $currentMonth++;                                                            // Increment month on each iteration
            $monthlyInterest = $remainingBalance * $monthlyInterestRate;                // Dollar amount of this month's interest
            $monthlyPrincipal = $monthlyPayment - $monthlyInterest;                     // Dollar amount going towards paying down the debt    

            if ($monthlyPrincipal < 0) {
                return ['error' => 'Payment too low to cover monthly interest at the new rate']; // Could not afford this month's interest
            }

            $remainingBalance -= $monthlyPrincipal;                                     // update the debt's remaining balance after this month
            $timeline[] = [                                                             // Add the results of this month to the timeline
                'month' => $currentMonth,
                'balance' => max(0, $remainingBalance),
                'interest_paid' => $monthlyInterest,
                'principal_paid' => $monthlyPrincipal,
            ];

            if ($remainingBalance <= 0) break;                                          // Break immediatley if debt is paid off
        }

        return [                                                                        // Return the result array
            'debt_name' => $debtRecord->debt_name,
            'original_amount' => $debtRecord->amount,
            'minimum_payment' => $debtRecord->minimum_payment,
            'current_payment' => $monthlyPayment,
            'new_interest_rate' => $newInterestRate,
            'timeline' => $timeline,
            'total_months' => $currentMonth,
            'total_interest_paid' => array_sum(array_column($timeline, 'interest_paid')),
        ];
    }

    /**
     * Algo 2
     */
    public function changeMonthlyPaymentScenario ( $debtId, $newMonthlyPayment, $monthlyIncome, $monthlyExpenses)
    {
        $debtRecord = Debt::findOrFail($debtId);
        $remainingBalance = $debtRecord->amount;                                    // Starts at the total debt amount currently
        $monthlyInterestRate = $debtRecord->interest_rate / 100/ 12;                // Convert the annual rate to monthly
        $timeline = [];                                                             // Holds the results of each month's calculation
        $currentMonth = 0;                                                          // Counter variable

        // Disposable income check
        $disposableIncome = $monthlyIncome - $monthlyExpenses;                      // The amount of income left over after expenses
        if ($newMonthlyPayment > $disposableIncome) {                               
            return ['error' => 'Monthly payment exceeds your disposable income'];   // Cannot afford the new payment this month
        }

        // Generate repayment timeline
        while ($remainingBalance > 0 && $currentMonth < 360) {                      // Continue until debt is paid in full or 30 years.
            $currentMonth++;                                                        // Increment month on each iteration
            $monthlyInterest = $remainingBalance * $monthlyInterestRate;            // Dollar amount of this month's interest
            $monthlyPrincipal = $newMonthlyPayment - $monthlyInterest;              // Dollar amount going towards paying down the debt    

            if ($monthlyPrincipal < 0) {                                            // Could not afford this month's interest
                return ['error' => 'Payment too low to cover your monthly interest'];
            }

            $remainingBalance -= $monthlyPrincipal;                                 // update the debt's remaining balance after this month

            $timeline[] = [                                                         // Add the results of this month to the timeline
                'month' => $currentMonth,
                'balance' => max(0, $remainingBalance),
                'interest_paid' => $monthlyInterest,
                'principal_paid' => $monthlyPrincipal,
            ];

            if ($remainingBalance <= 0) {                                           // Break immediatley if debt is paid off
                break;
            }

        }

        return [                                                                    // Return the result array
            'debt_name' => $debtRecord->debt_name,
            'original_amount' => $debtRecord->amount,
            'minimum_payment' => $debtRecord->minimum_payment,
            'current_payment' => $debtRecord->monthly_payment,
            'new_payment' => $newMonthlyPayment,
            'timeline' => $timeline,
            'total_months' => $currentMonth,
            'total_interest_paid' => array_sum(array_column($timeline, 'interest_paid')),
        ];

    }
}

