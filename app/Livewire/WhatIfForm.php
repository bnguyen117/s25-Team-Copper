<?php
 
namespace App\Livewire;
 
use App\Services\WhatIf\Analysis\WhatIfAnalysisService;
use App\Models\WhatIfReport;
use App\Models\Debt;
use App\Models\FinancialGoal;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use Livewire\Component;
 
class WhatIfForm extends Component implements HasForms
{
    use InteractsWithForms;

    public $debt_id;                        // Selected Debt ID
    public $financial_goal_id;              // Selected Financial goal ID (optional)
    public $monthly_income;                 // User's monthly income
    public $monthly_debt_expenses;          // User's monthly debt expenses (calculated from the user's monthly payments)
    public $monthly_non_debt_expenses;      // User's monthly non-debt expenses (e.g. groceries, utility bills, etc)
    public $monthly_saved;                 // User's monthly savings (not currently used)
    public $what_if_scenario;               // User's chosen what-if scenario
    public $new_interest_rate;              // New interest rate for 'interest-rate-change' scenario
    public $new_monthly_debt_payment;       // New monthly payment for 'payment-change' scenario
    public $new_monthly_savings;            // New monthly savings for 'savings-change' scenario (not currently used)
    public $what_if_report;                 // Stores the what-if report data; Passed to the view for rendering


    /**
     * On component mount:
     *  - Resets the form and what_if_report with clearForm().
     *  - Sets $this->monthly_debt_expenses to the sum of the user's debt payments.
     */
    public function mount(): void {
        $this->clearForm();
        $this->monthly_debt_expenses = $this->calculateTotalDebtPayments();
    }

    /** Render the component's view; passes $this->what_if_report as 'report' */
    public function render(): View { 
        return view('livewire.what-if.form', ['report' => $this->what_if_report]); 
    }

    // Create a drop down menu for the user to select a debt or savings scenario.
    

    /**
     * Defines the form's field structure.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([

                // Required field for the user's monthly income.
                TextInput::make('monthly_income')
                    ->label('Monthly Income')
                    ->type('number')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->maxValue(9999999)
                    ->placeholder('Enter your monthly income')
                    ->required(),

                // Optional field for additonal non-debt expenses.
                TextInput::make('monthly_non_debt_expenses')
                    ->label('Monthly Non-Debt Expenses')
                    ->type('number')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->maxValue(9999999)
                    ->placeholder('Expenses like groceries, utilities, etc.')
                    ->reactive()
                    ->helperText(function ($get) {
                        $nonDebtExpenses = $get('monthly_non_debt_expenses') ?: 0;
                        $totalExpenses = $this->monthly_debt_expenses + $nonDebtExpenses;
                        return "(debt + non-debt) expenses: $" . number_format($totalExpenses, 2);
                    }),
                
                // Required field to select the type of analysis (debt or savings) using a drop down menu.
                Select::make('analysis_type')
                    ->label('Analysis Type')
                    ->options([
                        'debt' => 'Debt Analysis',
                        'savings' => 'Savings Analysis',
                    ])
                    ->reactive()
                    ->placeholder('Select an analysis type'),

                // Required field to select a debt.
                Select::make('debt_id')
                    ->label('Debt to Analyze')
                    ->options(fn () => $this->getCurrentUserDebts())
                    ->placeholder('Choose a debt')
                    ->required()
                    ->reactive()
                    ->visible(fn ($get) => $get('analysis_type') === 'debt'), // Only show if 'debt' is selected

                // Optional field to select a financial goal.
                Select::make('financial_goal_id')
                    ->label('Financial Goal (Optional)')
                    ->options(fn () => $this->getCurrentUserGoals())
                    ->nullable()
                    ->placeholder('Select a goal'),

                // Required field to choose a what-if scenario - controls visibility of the following fields.
                Select::make('what_if_scenario')
                    ->label('Scenario')
                    ->placeholder('Select a scenario')
                    ->required()                  
                    ->options([
                        'interest-rate-change' => 'What if my interest rate changes?',
                        'payment-change' => 'What if I change my monthly payment?',
                    ])
                    ->reactive(),

                /**
                 * What-If Scenario Specific Fields.
                 */
                // Required field for the new annual interest rate (visible for 'interest-rate-change').
                TextInput::make('new_interest_rate')
                    ->type('number')
                    ->numeric()
                    ->rules(['numeric', 'between:0,100', 'decimal:0,2'])
                    ->label('New Interest Rate (%)')
                    ->suffix('%')
                    ->minValue(0)
                    ->maxValue(100)
                    ->placeholder('Input a new annual interest rate')
                    ->visible(fn ($get) => $get('what_if_scenario') === 'interest-rate-change')
                    ->required(fn ($get) => $get('what_if_scenario') === 'interest-rate-change')
                    ->helperText(fn ($get) => ($debt = Debt::find($get('debt_id'))) ? "Current rate: " . number_format($debt->interest_rate, 2) . "%" : null)
                    ->reactive(),

                // Required field for the new monthly debt payment (visible for 'payment-change)'.
                TextInput::make('new_monthly_debt_payment')
                    ->type('number')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->maxValue(9999999)
                    ->placeholder('Input a new monthly payment')
                    ->visible(fn ($get) => $get('what_if_scenario') === 'payment-change')
                    ->required(fn ($get) => $get('what_if_scenario') === 'payment-change')
                    ->helperText(fn ($get) => ($debt = Debt::find($get('debt_id'))) ? "Current monthly payment: $" . number_format($debt->monthly_payment, 2) : null)
                    ->reactive(),             
            ]);
    }

    /** Called on form submission and runs the selected what-if scenario */
    public function analyze(): void
    {
        // Save the form's state.
        $state = $this->form->getState();

        // Sum total expenses.
        $total_monthly_expenses = $this->monthly_debt_expenses + ($state['monthly_non_debt_expenses'] ?? 0);

        // Call the interest-rate-change algorithm.
        if ($state['what_if_scenario'] === 'interest-rate-change') {
            $result = (new WhatIfAnalysisService)->interestRateChangeScenario(
                $state['debt_id'],                                            
                $state['new_interest_rate'],                                   
                $state['monthly_income'],
                $total_monthly_expenses,
                $state['financial_goal_id']
            );
        } 
        
        // Call the payment-change algorithm.
        elseif ($state['what_if_scenario'] === 'payment-change') {
            $result = (new WhatIfAnalysisService)->changeMonthlyPaymentScenario(
                $state['debt_id'],                                            
                $state['new_monthly_debt_payment'],                                   
                $state['monthly_income'],
                $total_monthly_expenses,
                $state['financial_goal_id']
            );
        }

        // If result returned an error, save it to $this->what_if_report.
        if (isset($result['error'])) $this->what_if_report = $result;
        
        // Save the WhatIfReport record to the DB and $this->what_if_report.
        else $this->what_if_report = $this->saveWhatIfReport($state, $result);
    }


    /**
     * Clears the form's fields.
     * Sets what_if_report to null.
     * Refreshes $this->monthly_expenses with the latest debt totals.
     */
    public function clearForm(): void{
        $this->form->fill();
        $this->what_if_report = null;
        $this->monthly_debt_expenses = $this->calculateTotalDebtPayments();
    }


    /** Gets the currently authenticated user's debts. */
    private function getCurrentUserDebts(): array { 
        return Debt::where('user_id', Auth::id())->pluck('debt_name', 'id')->toArray(); 
    }


    /** Gets the current authenticated user's goals */
    private function getCurrentUserGoals(): array {
         return FinancialGoal::where('user_id', Auth::id())->pluck('goal_name', 'id')->toArray();
        }

    /** Sums the currently authenticated user's monthly debt payments. */
    private function calculateTotalDebtPayments(): float { 
        return Debt::where('user_id', Auth::id())->sum('monthly_payment'); 
    }
    

    /** Create a WhatIfReport record in the database. */
    private function saveWhatIfReport(array $state, array $result): WhatIfReport {
        return WhatIfReport::create([
            // Indentifiers and scenario choice            
            'user_id' => Auth::id(),
            'debt_id' => $state['debt_id'],
            'financial_goal_id' => $state['financial_goal_id'],
            'what_if_scenario' => $state['what_if_scenario'],

            // Original debt state
            'original_debt_amount' => $result['original_debt_amount'],
            'original_interest_rate' => $result['original_interest_rate'],
            'original_monthly_debt_payment' => $result['original_monthly_debt_payment'],
            'original_minimum_debt_payment' => $result['original_minimum_debt_payment'],

            //Scenario inputs
            'new_interest_rate' => $state['new_interest_rate'] ?? null,
            'new_monthly_debt_payment' => $state['new_monthly_debt_payment'] ?? null,

            // Scenario outcomes
            'total_months' => $result['total_months'],
            'total_interest_paid' => $result['total_interest_paid'],
            'timeline' => $result['timeline'],
            'goal_impact' => $result['goal_impact'],
        ]);
    }
}