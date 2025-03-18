<?php

namespace App\Services\WhatIf\Analysis;
use App\Models\Debt;
use App\Models\FinancialGoal;


class WhatIfAnalysisService {

    /** Algo 1: 'interest-rate-change' */
    public function interestRateChangeScenario($debtId, $newInterestRate, $monthlyIncome, $totalMonthlyExpenses, $financialGoalId = null)
    {
        // Grab debt record
        $debt = Debt::find($debtId);

        // Set up timeline variables
        $balance = $debt->amount;
        $monthlyRate = $newInterestRate / 100 / 12;
        $timeline = [];
        $month = 0;

        // Build timeline up to 30 years
        while ($balance > 0 && $month++ < 360) {
            $interest = $balance * $monthlyRate;
            $principal = $debt->monthly_payment - $interest;

            if ($principal > $balance) $principal = $balance;

            if ($principal < 0) {
                return ['error' => sprintf(
                    "<br>A new rate of %.2f%% raises initial interest payment to $%.2f <br><br> 
                    This exceeds your monthly payment of $%.2f by $%.2f", 
                    $newInterestRate, $interest, $debt->monthly_payment, $interest - $debt->monthly_payment)];
            }
            $balance -= $principal;
            $timeline[] = [
                'month' => $month,
                'balance' => max(0, $balance),
                'interest_paid' => $interest,
                'principal_paid' => $principal
            ];
        }

        // Build result array
        $result = [
            'original_debt_amount' => $debt->amount,
            'original_interest_rate' => $debt->interest_rate,
            'original_monthly_debt_payment' => $debt->monthly_payment,
            'original_minimum_debt_payment' => $debt->minimum_payment,
            'timeline' => $timeline,
            'total_months' => $month,
            'total_interest_paid' => array_sum(array_column($timeline, 'interest_paid'))
        ];

        // Add goal impact if provided
        if ($financialGoalId) 
            $result['goal_impact'] = $this->calculateGoalImpact($financialGoalId, $monthlyIncome, $totalMonthlyExpenses);

        return $result;
    }

    /**
     * Algo 2 - 'payment-change'
     */
    public function changeMonthlyPaymentScenario($debtId, $newMonthlyPayment, $monthlyIncome, $totalMonthlyExpenses, $financialGoalId = null)
    {
        // Grab debt record
        $debt = Debt::find($debtId);

        // Check if new payment meets minimum required by debt
        if ($newMonthlyPayment < $debt->minimum_payment) {
            return ['error' => sprintf("New payment $%.2f is below your debt's minimum of $%.2f", 
            $newMonthlyPayment, $debt->minimum_payment)];
        }

        // Set up timeline variables
        $totalMonthlyExpenses += $newMonthlyPayment - $debt->monthly_payment;
        $balance = $debt->amount;
        $monthlyRate = $debt->interest_rate / 100 / 12;
        $timeline = [];
        $month = 0;
        
        // Build timeline up to 30 years.
        while ($balance > 0 && $month++ < 360) {
            $interest = $balance * $monthlyRate;
            $principal = $newMonthlyPayment - $interest;

            if ($principal > $balance) $principal = $balance;
            
            if ($principal < 0) {
                return ['error' => sprintf("New monthly payment of $%.2f is below initial interest of $%.2f by $%.2f", 
                $newMonthlyPayment, $interest, $interest - $newMonthlyPayment)];
            }
            $balance -= $principal;
            $timeline[] = [
                'month' => $month,
                'balance' => max(0, $balance),
                'interest_paid' => $interest,
                'principal_paid' => $principal,
            ];
        }
        
        // Build result array
        $result = [
            'original_debt_amount' => $debt->amount,
            'original_interest_rate' => $debt->interest_rate,
            'original_monthly_debt_payment' => $debt->monthly_payment,
            'original_minimum_debt_payment' => $debt->minimum_payment,
            'timeline' => $timeline,
            'total_months' => $month,
            'total_interest_paid' => array_sum(array_column($timeline, 'interest_paid')),
        ];
        
        // Add goal impact if provided
        if ($financialGoalId)
            $result['goal_impact'] = $this->calculateGoalImpact($financialGoalId, $monthlyIncome, $totalMonthlyExpenses);
        
        return $result;
    }


    /** Calculate the impact of a scenario on a financial goal */
    private function calculateGoalImpact($financialGoalId, $monthlyIncome, $totalMonthlyExpenses)
    {
        $goal = FinancialGoal::find($financialGoalId);
        
        $monthlySavings = max(0, $monthlyIncome - $totalMonthlyExpenses);
        $remainingAmount = $goal->target_amount - $goal->current_amount;

        // Number of months until goal is achieved.
        $monthsToGoal = $monthlySavings > 0 ? ceil($remainingAmount / $monthlySavings) : null;
    
        $result = [
            'goal_name' => $goal->goal_name,
            'monthly_savings' => $monthlySavings,
            'months_to_goal' => $monthsToGoal,
            'achieve_by_months' => (int)round(now()->diffInMonths($goal->achieve_by)),
            'target_amount' => $goal->target_amount,
            'current_amount' => $goal->current_amount,
            'remaining_amount' => $remainingAmount,
        ];
    
        if ($monthlySavings == 0) {
            $result['total_expenses'] = $totalMonthlyExpenses;
            $result['monthly_income'] = $monthlyIncome;
            $result['shortfall'] = $totalMonthlyExpenses - $monthlyIncome;
        }

        elseif ($monthsToGoal > $result['achieve_by_months']) {
            // Extra savings needed to meet the goal on time
            $extraSavingsNeeded = ceil($remainingAmount / $result['achieve_by_months']) - $monthlySavings;
            $result['extra_savings_needed'] = max(0, $extraSavingsNeeded);
        }

        return $result;
    }
}

