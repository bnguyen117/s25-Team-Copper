<?php

namespace Tests\Unit;

use App\Services\WhatIf\Analysis\WhatIfAnalysisService;
use App\Models\Debt;
use App\Models\FinancialGoal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatIfAnalysisServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $debt;
    protected $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->debt = Debt::create([
            'user_id' => $this->user->id,
            'debt_name' => 'Test Debt',
            'monthly_payment' => 500.00,
            'interest_rate' => 5.00,
            'amount' => 8000.00,
            'minimum_payment' => 100.00,
        ]);
        $this->service = new WhatIfAnalysisService();
        $this->actingAs($this->user);
    }

    /** @test */
    public function interest_rate_change_calculates_correct_timeline()
    {
        $newInterestRate = 6.00;
        $monthlyIncome = 5000.00;
        $totalMonthlyExpenses = 2000.00;

        $result = $this->service->debtInterestRateChangeScenario(
            $this->debt->id,
            $newInterestRate,
            $monthlyIncome,
            $totalMonthlyExpenses
        );

        $this->assertArrayNotHasKey('error', $result);
        $this->assertEquals($this->debt->amount, $result['original_debt_amount']);
        $this->assertEquals($this->debt->interest_rate, $result['original_interest_rate']);
        $this->assertEquals($this->debt->monthly_payment, $result['original_monthly_debt_payment']);
        $this->assertEquals($this->debt->minimum_payment, $result['original_minimum_debt_payment']);
        $this->assertNotEmpty($result['timeline']);
        $this->assertGreaterThan(0, $result['total_months']);
        $this->assertGreaterThan(0, $result['total_interest_paid']);
        $this->assertArrayNotHasKey('goal_impact', $result);
    }

    /** @test */
    public function interest_rate_change_returns_error_when_payment_insufficient()
    {
        $newInterestRate = 80.0;
        $monthlyIncome = 5000.00;
        $totalMonthlyExpenses = 2000.00;

        $result = $this->service->debtInterestRateChangeScenario(
            $this->debt->id,
            $newInterestRate,
            $monthlyIncome,
            $totalMonthlyExpenses
        );

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('exceeds your monthly payment', $result['error']);
    }

    /** @test */
    public function interest_rate_change_includes_goal_impact_when_goal_provided()
    {
        $financialGoal = FinancialGoal::create([
            'user_id' => $this->user->id,
            'goal_name' => 'Vacation',
            'target_amount' => 5000.00,
            'current_amount' => 1000.00,
            'priority' => 'high',
        ]);
        $newInterestRate = 6.0;
        $monthlyIncome = 5000.00;
        $totalMonthlyExpenses = 2000.00;

        $result = $this->service->debtInterestRateChangeScenario(
            $this->debt->id,
            $newInterestRate,
            $monthlyIncome,
            $totalMonthlyExpenses,
            $financialGoal->id
        );

        $this->assertArrayNotHasKey('error', $result);
        $this->assertArrayHasKey('goal_impact', $result);
        $this->assertIsArray($result['goal_impact']);
        $this->assertEquals('Vacation', $result['goal_impact']['goal_name']);
        $this->assertEquals(3000.00, $result['goal_impact']['monthly_savings']);
        $this->assertEquals(4000.00, $result['goal_impact']['amount_still_needed']);
    }

    /** @test */
    public function monthly_payment_change_calculates_correct_timeline()
    {
        $newMonthlyPayment = 600.00;
        $monthlyIncome = 5000.00;
        $totalMonthlyExpenses = 2000.00;

        $result = $this->service->changeMonthlyPaymentScenario(
            $this->debt->id,
            $newMonthlyPayment,
            $monthlyIncome,
            $totalMonthlyExpenses
        );

        $this->assertArrayNotHasKey('error', $result);
        $this->assertEquals($this->debt->amount, $result['original_debt_amount']);
        $this->assertEquals($this->debt->interest_rate, $result['original_interest_rate']);
        $this->assertEquals($this->debt->monthly_payment, $result['original_monthly_debt_payment']);
        $this->assertEquals($this->debt->minimum_payment, $result['original_minimum_debt_payment']);
        $this->assertNotEmpty($result['timeline']);
        $this->assertGreaterThan(0, $result['total_months']);
        $this->assertGreaterThan(0, $result['total_interest_paid']);
        $this->assertArrayNotHasKey('goal_impact', $result);
    }

    /** @test */
    public function monthly_payment_change_returns_error_when_payment_below_minimum_or_insufficient()
    {
        $newMonthlyPayment = 50.00; // < minimum payment of 100
        $monthlyIncome = 5000.00;
        $totalMonthlyExpenses = 2000.00;

        $result = $this->service->changeMonthlyPaymentScenario(
            $this->debt->id,
            $newMonthlyPayment,
            $monthlyIncome,
            $totalMonthlyExpenses
        );

        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('below your debt\'s minimum', $result['error']);
    }

    /** @test */
    public function monthly_payment_change_includes_goal_impact_when_goal_provided()
    {
        $financialGoal = FinancialGoal::create([
            'user_id' => $this->user->id,
            'goal_name' => 'Vacation',
            'target_amount' => 5000.00,
            'current_amount' => 1000.00,
            'priority' => 'high',
        ]);
        $newMonthlyPayment = 600.00;
        $monthlyIncome = 5000.00;
        $totalMonthlyExpenses = 2000.00;

        $result = $this->service->changeMonthlyPaymentScenario(
            $this->debt->id,
            $newMonthlyPayment,
            $monthlyIncome,
            $totalMonthlyExpenses,
            $financialGoal->id
        );

        $this->assertArrayNotHasKey('error', $result);
        $this->assertArrayHasKey('goal_impact', $result);
        $this->assertIsArray($result['goal_impact']);
        $this->assertEquals('Vacation', $result['goal_impact']['goal_name']);
        $this->assertEquals(2900.00, $result['goal_impact']['monthly_savings']); // 5000 - (2000 + (600-500))
        $this->assertEquals(4000.00, $result['goal_impact']['amount_still_needed']); // 5000 - 1000
    }
}