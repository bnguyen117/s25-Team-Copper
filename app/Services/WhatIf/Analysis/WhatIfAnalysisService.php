<?php

namespace App\Services\WhatIf\Analysis;
use App\Models\Debt;
use App\Models\FinancialGoal;


class WhatIfAnalysisService {

    /** Algo 1: 'interest-rate-change' */
    public function debtInterestRateChangeScenario($debtId, $newInterestRate, $monthlyIncome, $totalMonthlyExpenses, $financialGoalId = null)
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

    /**
     * Algo 3 - 'no-goal-savings-interest-rate-change'
     */
    public function noGoalSavingsInterestRateChangeScenario($currentSavingsAmt, $currentMonthlySavings, $currentInterestRate, $newInterestRate, $monthlyIncome, $totalMonthlyExpenses, $financialGoalId = null) {
        //Once budget is setup, add savings id or something like that.
        
        // Set up timeline variables
        $balance = $currentSavingsAmt;
        $monthlyRate = $newInterestRate / 100 / 12;
        $timeline = [];
        $month = 0;

        //Build timeline up to 30 years 
        while ($month++ < 360) {
            $balance += $currentMonthlySavings;
            $interestEarned = $balance * $monthlyRate;
            $balance += $interestEarned;
            $timeline[] = [
                'month' => $month,
                'balance' => max(0, $balance),
                'interest_earned' => $interestEarned,
                'monthly_savings' => $currentMonthlySavings
            ];
        }

        // Build result array
        $result = [
            'original_savings_amount' => $currentSavingsAmt,
            'original_interest_rate' => $currentInterestRate,
            'original_monthly_savings' => $currentMonthlySavings,
            'total_saved' => $balance,
            'timeline' => $timeline,
            'total_months' => $month,
            'total_interest_earned' => array_sum(array_column($timeline, 'interest_earned')),
        ];

        return $result;
    }

    /**
     * Algo 4 - 'no-goal-savings-monthly-contribution-change'
     */
    public function noGoalMonthlySavingsChangeScenario($currentSavingsAmt, $currentMonthlySavings, $currentInterestRate, $newMonthlySavings, $monthsToSave, $monthlyIncome, $totalMonthlyExpenses, $financialGoalId = null) {
        // Set up timeline variables
        $balance = $currentSavingsAmt;
        $monthlyRate = $currentInterestRate / 100 / 12;
        $timeline = [];
        $month = 0;

        // Build timeline up to 30 years
        while ($month++ < $monthsToSave) {
            $balance += $newMonthlySavings;
            $interestEarned = $balance * $monthlyRate;
            $balance += $interestEarned;
            $timeline[] = [
                'month' => $month,
                'balance' => max(0, $balance),
                'interest_earned' => $interestEarned,
                'monthly_savings' => $newMonthlySavings
            ];
        }

        // Build result array
        $result = [
            'original_savings_amount' => $currentSavingsAmt,
            'original_interest_rate' => $currentInterestRate,
            'original_monthly_savings' => $currentMonthlySavings,
            'total_saved' => $balance, 
            'timeline' => $timeline,
            'total_months' => $month,
            'total_interest_earned' => array_sum(array_column($timeline, 'interest_earned')),
        ];

        return $result;
    }

    /**
     * Algo 5 - 'goal-savings-interest-rate-change'
     */
    public function goalSavingsInterestRateChangeScenario($financialGoalId, $currentInterestRate, $newInterestRate, $monthlyIncome, $totalMonthlyExpenses)
    {
        // Grab goal record
        $goal = FinancialGoal::find($financialGoalId);

        // Set up timeline variables
        $balance = $goal->current_amount;
        $MonthlyRate = $newInterestRate / 100 / 12;
        $timeline = [];
        $month = 0;
        $monthlySavings = $goal->monthly_savings;

        // Build timeline until the goal is reached.
        while ($balance < $goal->target_amount) {
            $balance += $monthlySavings;
            $interestEarned = $balance * ($MonthlyRate);
            $balance += $interestEarned;
            if ($monthlySavings > $goal->target_amount - $balance) {
                $monthlySavings = $goal->target_amount - $balance;
            }
            $timeline[] = [
                'month' => $month,
                'balance' => max(0, $balance),
                'interest_earned' => $interestEarned,
                'monthly_savings' => $monthlySavings
            ];
            if ($month++ > 360) break; // Prevent infinite loop
        }

        // Build result array
        $result = [
            'original_savings_amount' => $goal->current_amount,
            'original_interest_rate' => $currentInterestRate,
            'original_monthly_savings' => $goal->monthly_savings,
            'total_saved' => $balance,
            'timeline' => $timeline,
            'total_months' => $month,
            'total_interest_earned' => array_sum(array_column($timeline, 'interest_earned')),
        ];

        // Add goal impact
        $result['goal_impact'] = $this->calculateGoalImpact($financialGoalId, $monthlyIncome, $totalMonthlyExpenses);
        return $result;
    }

    /**
     * Algo 6 - 'goal-monthly-savings-change'
     */
    public function goalMonthlySavingsChangeScenario($financialGoalId, $currentInterestRate, $newMonthlySavings, $monthlyIncome, $totalMonthlyExpenses) {
        // Grab goal record
        $goal = FinancialGoal::find($financialGoalId);

        // Set up timeline variables
        $balance = $goal->current_amount;
        $monthlyRate = $currentInterestRate / 100 / 12;
        $timeline = [];
        $month = 0;

        // Build timeline until the goal is reached.
        while ($balance < $goal->target_amount) {
            $balance += $newMonthlySavings;
            $interestEarned = $balance * ($monthlyRate);
            $balance += $interestEarned;
            if ($newMonthlySavings > $goal->target_amount - $balance) {
                $newMonthlySavings = $goal->target_amount - $balance;
            }
            $timeline[] = [
                'month' => $month,
                'balance' => max(0, $balance),
                'interest_earned' => $interestEarned,
                'monthly_savings' => $newMonthlySavings
            ];
            if ($month++ > 360) break; // Prevent infinite loop
        }

        // Build result array
        $result = [
            'original_savings_amount' => $goal->current_amount,
            'original_interest_rate' => $currentInterestRate,
            'original_monthly_savings' => $goal->monthly_savings,
            'total_saved' => $balance,
            'timeline' => $timeline,
            'total_months' => $month,
            'total_interest_earned' => array_sum(array_column($timeline, 'interest_earned')),
        ];

        // Add goal impact
        if ($financialGoalId) 
            $result['goal_impact'] = $this->calculateGoalImpact($financialGoalId, $monthlyIncome, $totalMonthlyExpenses);

        return $result;
    }


    /** Calculate the impact of a scenario on a financial goal */
    private function calculateGoalImpact($financialGoalId, $monthlyIncome, $totalMonthlyExpenses)
    {
        $goal = FinancialGoal::find($financialGoalId);
        
        $monthlySavings = max(0, $monthlyIncome - $totalMonthlyExpenses);
        $amountStillNeeded = max(0, $goal->target_amount - $goal->current_amount);

        // The user's goal target in months
        $targetMonthsRaw = now()->diffInMonths($goal->achieve_by);
        $targetMonths = max(1, ceil($targetMonthsRaw));

        // Check if the goal is overdue
        $isOverdue = $goal->achieve_by <= now();

        // The projected number of months the user will acheive their goal based on monthly savings.
        $projectedMonths = $monthlySavings > 0 && $amountStillNeeded > 0 ? ceil($amountStillNeeded / $monthlySavings) : 0;
        
        $result = [
            'goal_name' => $goal->goal_name,
            'monthly_savings' => $monthlySavings,
            'projected_months' => $projectedMonths,
            'target_months' => $targetMonths,
            'target_amount' => $goal->target_amount,
            'current_amount' => $goal->current_amount,
            'amount_still_needed' => $amountStillNeeded,
            'is_overdue' => $isOverdue,
        ];

        if ($amountStillNeeded > 0 && $monthlySavings > 0) {
            $extraSavingsNeeded = ceil($amountStillNeeded / $result['target_months']) - $monthlySavings;
            $result['extra_savings_needed'] = max(0, $extraSavingsNeeded);
        } 
        else $result['extra_savings_needed'] = 0;
    
        if ($monthlySavings == 0) {
            $result['total_expenses'] = $totalMonthlyExpenses;
            $result['monthly_income'] = $monthlyIncome;
            $result['shortfall'] = $totalMonthlyExpenses - $monthlyIncome;
        }

        return $result;
    }
}

