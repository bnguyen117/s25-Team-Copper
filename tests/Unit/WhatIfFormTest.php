<?php

namespace Tests\Unit;

use App\Livewire\WhatIfForm;
use App\Models\User;
use App\Models\Debt;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class WhatIfFormTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $debt;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->debt = Debt::create([
            'user_id' => $this->user->id,
            'debt_name' => 'Test Debt',
            'monthly_payment' => 150.00,
            'interest_rate' => 5.0,
            'amount' => 10000.00,
        ]);
        $this->actingAs($this->user);
    }

    /** @test */
    public function save_what_if_report_saves_correct_record()
    {
        $state = [
            'debt_id' => $this->debt->id,
            'financial_goal_id' => null,
            'debt_what_if_scenario' => 'interest-rate-change',
            'debt_new_interest_rate' => 7.50,
            'new_monthly_debt_payment' => null,
        ];

        $result = [
            'original_debt_amount' => 8000.00,
            'original_interest_rate' => 5.0,
            'original_monthly_debt_payment' => 500.00,
            'original_minimum_debt_payment' => 100.00,
            'total_months' => 65,
            'total_interest_paid' => 2000.00,
            'timeline' => [],
            'goal_impact' => null,
        ];
        Auth::login($this->user);
        new WhatIfForm()->saveWhatIfReport($state, $result);
        // Check that the record is saved correctly
        $this->assertDatabaseHas('what_if_reports', [
            'user_id' => $this->user->id,
            'debt_id' => $this->debt->id,
            'financial_goal_id' => null,
            'what_if_scenario' => 'interest-rate-change',
            'original_debt_amount' => 8000.00,
            'original_interest_rate' => 5.0,
            'original_monthly_debt_payment' => 500.00,
            'original_minimum_debt_payment' => 100.00,
            'new_interest_rate' => 7.50,
            'new_monthly_debt_payment' => null,
            'total_months' => 65,
            'total_interest_paid' => 2000.00,
            'timeline' => json_encode([]),
            'goal_impact' => null,
        ]);
    }
}