<?php

namespace Tests\Unit;

use App\Services\WhatIf\Chat\WhatIfReportFormatter;
use App\Models\WhatIfReport;
use App\Models\Debt;
use App\Models\FinancialGoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests that the WhatIfReportFormatter generate a correctly formatted summary of the user's financial state.
 */
class WhatIfReportFormatterTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $debt;
    protected $formatter;

    // Setup user, debt, report formatter, and authenticate the user.
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->debt = Debt::create([
            'user_id' => $this->user->id,
            'debt_name' => 'Test Debt',
            'category' => 'Personal Loan',
            'monthly_payment' => 500.00,
            'interest_rate' => 5.00,
            'amount' => 8000.00,
            'minimum_payment' => 100.00,
            'description' => 'Test debt description',
            'due_date' => '2025-03-24',
        ]);
        $this->formatter = new WhatIfReportFormatter();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_generates_correct_summary_for_interest_rate_change()
    {
        // Create a report
        $report = WhatIfReport::create([
            'user_id' => $this->user->id,
            'debt_id' => $this->debt->id,
            'what_if_scenario' => 'debt-interest-rate-change',
            'original_debt_amount' => 8000.00,
            'original_interest_rate' => 5.00,
            'original_monthly_debt_payment' => 500.00,
            'original_minimum_debt_payment' => 100.00,
            'new_interest_rate' => 6.00,
            'total_months' => 18,
            'total_interest_paid' => 1000.25,
            'timeline' => json_encode([['month' => 1, 'balance' => 8000, 'interest_paid' => 100, 'principal_paid' => 500]]),
        ]);

        // Generate a summary
        $summary = $this->formatter->generateSummary($report);

        // Assert expected output
        $this->assertStringContainsString('Debt Overview:', $summary);
        $this->assertStringContainsString('  - Debt Name: Test Debt', $summary);
        $this->assertStringContainsString('  - Debt Category: Personal Loan', $summary);

        $this->assertStringContainsString('Original Debt Details at Time of Report:', $summary);
        $this->assertStringContainsString('  - Original Debt Amount: $8,000.00', $summary);
        $this->assertStringContainsString('  - Original Monthly Payment: $500.00', $summary);
        $this->assertStringContainsString('  - Original Interest Rate: 5.00%', $summary);
        $this->assertStringContainsString('  - Original Minimum Monthly Payment: $100.00', $summary);

        $this->assertStringContainsString('What-If Scenario Results (Algorithm: interest-rate-change):', $summary);
        $this->assertStringContainsString('  - Total Months Until Full Repayment: 18', $summary);
        $this->assertStringContainsString('  - Total Interest Paid: $1,000.25', $summary);
        $this->assertStringContainsString('  - New Interest Rate: 6.00%', $summary);

        $this->assertStringContainsString("User's Full Debt Profile:", $summary);
        $this->assertStringContainsString('No additional debts found for this user.', $summary);

        $this->assertStringContainsString('Description: Test debt description', $summary);
        $this->assertStringContainsString('Due Date: 2025-03-24', $summary);
    }

    /** @test */
    public function it_includes_goal_impact_when_present()
    {
        // Create a goal
        $financialGoal = FinancialGoal::create([
            'user_id' => $this->user->id,
            'goal_name' => 'Vacation',
            'target_amount' => 5000.00,
            'current_amount' => 1000.00,
            'priority' => 'high',
            'achieve_by' => now()->addYear(),
        ]);

        // Define the goal impact
        $goalImpact = [
            'goal_name' => 'Vacation',
            'current_amount' => 1000.00,
            'target_amount' => 5000.00,
            'amount_still_needed' => 4000.00,
            'monthly_savings' => 2900.00,
            'projected_months' => 2,
            'target_months' => 12,
            'is_overdue' => false,
        ];

        // Create  a report.
        $report = WhatIfReport::create([
            'user_id' => $this->user->id,
            'debt_id' => $this->debt->id,
            'financial_goal_id' => $financialGoal->id,
            'what_if_scenario' => 'debt-payment-change',
            'original_debt_amount' => 8000.00,
            'original_interest_rate' => 5.00,
            'original_monthly_debt_payment' => 500.00,
            'original_minimum_debt_payment' => 100.00,
            'new_monthly_debt_payment' => 600.00,
            'total_months' => 15,
            'total_interest_paid' => 575.50,
            'timeline' => json_encode([['month' => 1, 'balance' => 7500, 'interest_paid' => 33.33, 'principal_paid' => 566.67]]),
            'goal_impact' => $goalImpact,
        ]);

        // Generate summary
        $summary = $this->formatter->generateSummary($report);

        // Assert expected output
        $this->assertStringContainsString('What-If Scenario Results (Algorithm: payment-change):', $summary);
        $this->assertStringContainsString('  - New Monthly Payment: $600.00', $summary);

        $this->assertStringContainsString('Goal Impact:', $summary);
        $this->assertStringContainsString('  - Goal Name: Vacation', $summary);
        $this->assertStringContainsString('  - Current Amount: $1,000.00', $summary);
        $this->assertStringContainsString('  - Target Amount: $5,000.00', $summary);
        $this->assertStringContainsString('  - Amount Still Needed: $4,000.00', $summary);
        $this->assertStringContainsString('  - Monthly Savings After Expenses: $2,900.00', $summary);
        $this->assertStringContainsString('  - Months Until Achieved: 2', $summary);
        $this->assertStringContainsString('  - Target Months: 12', $summary);
        $this->assertStringContainsString('  - Status: On track', $summary);
    }

    /** @test */
    public function it_includes_additional_debts_when_present()
    {
        // Define an additonal debt in the user's profile.
        $additionalDebt = Debt::create([
            'user_id' => $this->user->id,
            'debt_name' => 'Credit Card',
            'category' => 'Credit Card', 
            'monthly_payment' => 200.00,
            'interest_rate' => 15.00,
            'amount' => 3000.00,
            'minimum_payment' => 50.00,
        ]);

        // Create a report
        $report = WhatIfReport::create([
            'user_id' => $this->user->id,
            'debt_id' => $this->debt->id,
            'what_if_scenario' => 'debt-interest-rate-change',
            'original_debt_amount' => 8000.00,
            'original_interest_rate' => 5.00,
            'original_monthly_debt_payment' => 500.00,
            'original_minimum_debt_payment' => 100.00,
            'new_interest_rate' => 6.00,
            'total_months' => 18,
            'total_interest_paid' => 550.25,
            'timeline' => json_encode([['month' => 1, 'balance' => 8000, 'interest_paid' => 100, 'principal_paid' => 500]]),
        ]);

        // Generate a summary
        $summary = $this->formatter->generateSummary($report);

        // Assert expected output
        $this->assertStringContainsString("User's Full Debt Profile:", $summary);
        $this->assertStringContainsString(
            '- Debt Name: Credit Card, Category: Credit Card, Current Balance: $3,000.00, Interest Rate: 15.00%, Monthly Payment: $200.00, Minimum Payment: $50.00',
            $summary
        );
        $this->assertStringNotContainsString('No additional debts found', $summary);
    }
}