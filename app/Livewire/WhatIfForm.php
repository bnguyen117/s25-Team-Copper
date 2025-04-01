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

    public $analysis_type;                 // Type of analysis (debt or savings)
    public $debt_id;                        // Selected Debt ID
    public $financial_goal_id;              // Selected Financial goal ID (optional)
    public $monthly_income;                 // User's monthly income
    public $monthly_debt_expenses;          // User's monthly debt expenses (calculated from the user's monthly payments)
    public $monthly_non_debt_expenses;      // User's monthly non-debt expenses (e.g. groceries, utility bills, etc)
    public $current_savings_amt;            // User's current savings amount
    public $current_monthly_savings;                 // User's monthly savings
    public $current_savings_interest_rate;  // User's current annual interest rate for savings
    public $debt_what_if_scenario;               // User's chosen what-if scenario for debt type
    public $savings_what_if_scenario;             // User's chosen what-if scenario for savings type
    public $debt_new_interest_rate;              // New interest rate for 'interest-rate-change' scenario
    public $new_monthly_debt_payment;       // New monthly payment for 'payment-change' scenario
    public $savings_new_annual_interest_rate; // New annual interest rate for 'interest-rate-change' scenario
    public $new_monthly_savings;            // New monthly savings for 'savings-change' scenario
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

                // Required field to select the type of analysis (debt or savings) using a drop down menu.
                Select::make('analysis_type')
                    ->label('Analysis Type')
                    ->options([
                        'debt' => 'Debt Analysis',
                        'savings' => 'Savings Analysis',
                    ])
                    ->required()
                    ->reactive()
                    ->live()
                    ->placeholder('Select an analysis type'),

                // Required field for the user's monthly income.
                TextInput::make('monthly_income')
                    ->label('Monthly Income')
                    ->type('number')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->maxValue(9999999)
                    ->placeholder('Enter your monthly income')
                    ->required(fn ($get) => $get('analysis_type') === 'debt' || $get('analysis_type') === 'savings') // Required for both analysis types
                    ->visible(fn ($get) => $get('analysis_type') === 'debt' || $get('analysis_type') === 'savings'), // Shows if debt or savings analysis is selected

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
                    })
                    ->visible(fn ($get) => $get('analysis_type') === 'debt' || $get('analysis_type') === 'savings'), // Shows if debt or savings analysis is selected
            

                // Required field to select a debt.
                Select::make('debt_id')
                    ->label('Debt to Analyze')
                    ->options(fn () => $this->getCurrentUserDebts())
                    ->placeholder('Choose a debt')
                    ->required(fn ($get) => $get('analysis_type') === 'debt')
                    ->reactive()
                    ->visible(fn ($get) => $get('analysis_type') === 'debt'), // Only show if 'debt' is selected

                //Required field to input user's current savings amount.
                TextInput::make('current_savings_amt') 
                    ->label('Current Savings Amount')
                    ->type('number')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->maxValue(9999999)
                    ->placeholder('How much do you currently have saved?')
                    ->reactive()
                    ->required(fn ($get) => $get('analysis_type') === 'savings') // Required for savings analysis
                    ->visible(fn ($get) => $get('analysis_type') === 'savings'), // Only show if 'savings' is selected

                //Required field to input user's current monthly savings rate.
                TextInput::make('current_monthly_savings')
                    ->label('Current Monthly Savings Amount')
                    ->type('number')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->maxValue(9999999)
                    ->placeholder('How much are you currently saving per month?')
                    ->reactive()
                    ->required(fn ($get) => $get('analysis_type') === 'savings') // Required for savings analysis
                    ->visible(fn ($get) => $get('analysis_type') === 'savings'), // Only show if 'savings' is selected

                //Optional field to input user's current interest rate for savings.
                TextInput::make('current_savings_interest_rate')
                    ->label('Current Annual Interest Rate (%)')
                    ->type('number')
                    ->numeric()
                    ->rules(['numeric', 'between:0,100', 'decimal:0,2'])
                    ->suffix('%')
                    ->minValue(0)
                    ->maxValue(100)
                    ->placeholder('What is your current annual interest rate?')
                    ->reactive()
                    ->required(fn ($get) => $get('analysis_type') === 'savings') // Required for savings analysis
                    ->visible(fn ($get) => $get('analysis_type') === 'savings'), // Only show if 'savings' is selected

                // Optional field to select a financial goal.
                Select::make('financial_goal_id')
                    ->label('Financial Goal (Optional)')
                    ->options(fn () => $this->getCurrentUserGoals())
                    ->nullable()
                    ->placeholder('Select a goal')
                    ->visible(fn ($get) => $get('analysis_type') === 'debt' || $get('analysis_type') === 'savings'), // Shows if debt or savings analysis is selected

                // Required field to choose a what-if scenario - controls visibility of the following fields.
                Select::make('debt_what_if_scenario')
                    ->label('Scenario')
                    ->placeholder('Select a scenario')
                    ->required(fn ($get) => $get('analysis_type') === 'debt')                  
                    ->options([
                        'interest-rate-change' => 'What if my interest rate changes?',
                        'payment-change' => 'What if I change my monthly payment?',
                    ])
                    ->reactive()
                    ->visible(fn ($get) => $get('analysis_type') === 'debt'), // Only show if 'debt' is selected

                // Required field to choose a savings what-if scenario.
                Select::make('savings_what_if_scenario')
                    ->label('Scenario')
                    ->placeholder('Select a scenario')
                    ->required(fn ($get) => $get('analysis_type') === 'savings')                  
                    ->options([
                        'interest-rate-change' => 'What if my interest rate changes?',
                        'savings-change' => 'What if I change my monthly savings?',
                    ])
                    ->reactive()
                    ->required(fn ($get) => $get('analysis_type') === 'savings')
                    ->visible(fn ($get) => $get('analysis_type') === 'savings'), // Only show if 'savings' is selected

                /**
                 * What-If Scenario Specific Fields.
                 */
                // Required field for the new annual interest rate (visible for 'interest-rate-change').
                TextInput::make('debt_new_interest_rate')
                    ->type('number')
                    ->numeric()
                    ->rules(['numeric', 'between:0,100', 'decimal:0,2'])
                    ->label('New Interest Rate (%)')
                    ->suffix('%')
                    ->minValue(0)
                    ->maxValue(100)
                    ->placeholder('Input a new annual interest rate')
                    ->visible(fn ($get) => $get('debt_what_if_scenario') === 'interest-rate-change')
                    ->required(fn ($get) => $get('debt_what_if_scenario') === 'interest-rate-change')
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
                    ->visible(fn ($get) => $get('debt_what_if_scenario') === 'payment-change')
                    ->required(fn ($get) => $get('debt_what_if_scenario') === 'payment-change')
                    ->helperText(fn ($get) => ($debt = Debt::find($get('debt_id'))) ? "Current monthly payment: $" . number_format($debt->monthly_payment, 2) : null)
                    ->reactive(),             

                // Required field for new annual interest rate for savings.  (visible for interest rate change)
                TextInput::make('savings_new_annual_interest_rate')
                    ->type('number')
                    ->numeric()
                    ->rules(['numeric', 'between:0,100', 'decimal:0,2'])
                    ->label('New Interest Rate (%)')
                    ->suffix('%')
                    ->minValue(0)
                    ->maxValue(100)
                    ->placeholder('Input a new annual interest rate')
                    ->visible(fn ($get) => $get('savings_what_if_scenario') === 'interest-rate-change')
                    ->required(fn ($get) => $get('savings_what_if_scenario') === 'interest-rate-change')
                    ->helperText(fn ($get) => $get('current_savings') ? "Current rate: " . number_format($get('current_savings'), 2) . "%" : null)
                    ->reactive(),  

                // Required field for new monthly savings amount.  (visible for savings amount change)
                TextInput::make('new_monthly_savings') 
                    ->type('number')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->maxValue(9999999)
                    ->placeholder('Input a new monthly savings amount')
                    ->visible(fn ($get) => $get('savings_what_if_scenario') === 'savings-change')
                    ->required(fn ($get) => $get('savings_what_if_scenario') === 'savings-change')
                    ->helperText(fn ($get) => $get('current_monthly_savings') ? "Current monthly savings: $" . number_format($get('current_monthly_savings'), 2) : null)
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

        $what_if_scenario = $state['debt_what_if_scenario'] ?? $state['savings_what_if_scenario'];

        // Checks if the analysis type is debt or savings, then chooses algorithm based on selected scenario.
        if ($state['analysis_type'] === 'debt') {
            // Call the interest-rate-change algorithm.
            if ($state['debt_what_if_scenario'] === 'interest-rate-change') {}
                $result = (new WhatIfAnalysisService)->debtInterestRateChangeScenario(
                    $state['debt_id'],                                            
                    $state['debt_new_interest_rate'],                                   
                    $state['monthly_income'],
                    $total_monthly_expenses,
                    $state['financial_goal_id']
                );
            } 
            
            // Call the payment-change algorithm.
            elseif ($state['debt_what_if_scenario'] === 'payment-change') {
                $result = (new WhatIfAnalysisService)->changeMonthlyPaymentScenario(
                    $state['debt_id'],                                            
                    $state['new_monthly_debt_payment'],                                   
                    $state['monthly_income'],
                    $total_monthly_expenses,
                    $state['financial_goal_id']
                );
            }

        elseif ($state['analysis_type'] === 'savings') {
            if ($state['savings_what_if_scenario'] === 'interest-rate-change') {
                $result = (new WhatIfAnalysisService)->savingsInterestRateChangeScenario(
                    $state['current_savings_amt'],
                    $state['current_monthly_savings'],  
                    $state['current_savings_interest_rate'],                                 
                    $state['savings_new_annual_interest_rate'],                                   
                    $state['monthly_income'],
                    $total_monthly_expenses,
                    $state['financial_goal_id']
                );
            }

            elseif ($state['savings_what_if_scenario'] === 'savings-change') {
                $result = (new WhatIfAnalysisService)->changeMonthlySavingsScenario(
                    $state['current_savings_amt'],
                    $state['current_monthly_savings'],  
                    $state['current_savings_interest_rate'],                                 
                    $state['new_monthly_savings'],                                   
                    $state['monthly_income'],
                    $total_monthly_expenses,
                    $state['financial_goal_id']
                );
            }
        }

        

        // If result returned an error, save it to $this->what_if_report.
        if (isset($result['error'])) $this->what_if_report = $result;
        
        // Save the WhatIfReport record to the DB and $this->what_if_report.
        else $this->what_if_report = $this->saveWhatIfReport($state, $result, $what_if_scenario);
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
    public function saveWhatIfReport(array $state, array $result, $what_if_scenario): WhatIfReport {
        return WhatIfReport::create([
            // Indentifiers and scenario choice   
            'analysis_type' => $state['analysis_type'],       
            'user_id' => Auth::id(),
            'debt_id' => $state['debt_id'] ?? null,
            'financial_goal_id' => $state['financial_goal_id'],
            'what_if_scenario' => $what_if_scenario ?? null,

            // Original debt state
            'original_debt_amount' => $result['original_debt_amount'] ?? null,
            'original_interest_rate' => $result['original_interest_rate'] ?? null,
            'original_monthly_debt_payment' => $result['original_monthly_debt_payment'] ?? null,
            'original_minimum_debt_payment' => $result['original_minimum_debt_payment'] ?? null,
            'original_savings_amount' => $result['original_savings_amount'] ?? null,
            'original_monthly_savings' => $result['original_monthly_savings'] ?? null,
            'original_savings_interest_rate' => $result['original_savings_interest_rate'] ?? null,

            //Scenario inputs
            'new_interest_rate' => $state['debt_new_interest_rate'] ?? null,
            'new_monthly_debt_payment' => $state['new_monthly_debt_payment'] ?? null,
            'new_annual_interest_rate' => $state['savings_new_annual_interest_rate'] ?? null,
            'new_monthly_savings' => $state['new_monthly_savings'] ?? null,

            // Scenario outcomes
            'total_months' => $result['total_months'],
            'total_interest_earned' => $result['total_interest_earned'] ?? null,
            'total_interest_paid' => $result['total_interest_paid'] ?? null,
            'timeline' => $result['timeline'],
            'goal_impact' => $result['goal_impact'] ?? null,
        ]);
    }
}