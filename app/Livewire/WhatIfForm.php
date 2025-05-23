<?php
 
namespace App\Livewire;
 
use App\Services\WhatIf\Analysis\WhatIfAnalysisService;
use App\Models\WhatIfReport;
use App\Models\Debt;
use App\Models\FinancialGoal;
use App\Models\SavingsWhatIfReport;
use Filament\Forms\Components\Checkbox;
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
    public $saving_for_goal;               // Determines if the user is saving for a goal
    public $current_savings_amt;            // User's current savings amount
    public $savings_name;                  // Name of the savings for the report
    public $current_monthly_savings;                 // User's monthly savings
    public $current_savings_interest_rate;  // User's current annual interest rate for savings
    public $what_if_scenario;               // User's chosen what-if scenario for debt type
    public $debt_new_interest_rate;              // New interest rate for 'interest-rate-change' scenario
    public $new_monthly_debt_payment;       // New monthly payment for 'payment-change' scenario
    public $savings_new_annual_interest_rate; // New annual interest rate for 'interest-rate-change' scenario
    public $new_monthly_savings;            // New monthly savings for 'savings-change' scenario
    public $months_to_save;                 // Number of months to save (optional)
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
                    ->required() // Required for both analysis types
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
                    ->visible(fn ($get) => $get('analysis_type') === 'debt'), // Only show if 'debt' is selected

                
                // Required field to determine if the user wants to analyze a goal.
                Checkbox::make('saving_for_goal')
                    ->label('Are you saving for a goal?')
                    ->helperText('If checked, you will be able to select a goal for your analysis.')
                    ->default(true)
                    ->live()
                    ->visible(fn ($get) => $get('analysis_type') === 'savings'), // Only show if 'savings' is selected

                // Optional field to select a financial goal for debt what-if.  Required field for savings what-if.
                Select::make('financial_goal_id')
                    ->label('Financial Goal')
                    ->options(fn () => $this->getCurrentUserGoals())
                    ->nullable() // Nullable if debt analysis is selected or user is not saving for a goal
                    ->placeholder('Select a goal')
                    ->required(fn ($get) => $get('analysis_type') === 'savings' && $get('saving_for_goal')) // Required if savings analysis is selected and user is saving for a goal
                    ->visible(fn ($get) => $get('analysis_type') === 'debt' || ($get('analysis_type') === 'savings' && $get('saving_for_goal'))), // Shows if debt or savings analysis is selected
                
                //Required field to input name of savings for report.
                TextInput::make('savings_name')
                    ->label('Savings Name')
                    ->placeholder('Name your savings analysis for the report.  Is there anything you are saving for?')
                    ->required() // Required if savings analysis is selected and user is not saving for a goal
                    ->visible(fn ($get) => $get('analysis_type') === 'savings' && ! $get('saving_for_goal')), // Only show if 'savings' is selected

                //Required field to input user's current savings amount.
                TextInput::make('current_savings_amt') 
                    ->label('Current Savings Amount')
                    ->type('number')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->maxValue(9999999)
                    ->placeholder('How much do you currently have saved?')
                    ->required() // Required for non goal savings analysis
                    ->visible(fn ($get) => $get('analysis_type') === 'savings' && ! $get('saving_for_goal')), // Only show if 'savings' is selected

                //Required field to input user's current monthly savings rate.
                TextInput::make('current_monthly_savings')
                    ->label('Current Monthly Savings Rate')
                    ->type('number')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->maxValue(9999999)
                    ->placeholder('How much are you currently saving per month?')
                    ->required() // Required for savings analysis
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
                    ->required() // Required for savings analysis
                    ->visible(fn ($get) => $get('analysis_type') === 'savings'), // Only show if 'savings' is selected

                // Required field to choose a debt what-if scenario - controls visibility of the following fields.  Uses the same name as saving to make it simple.
                Select::make('what_if_scenario')
                    ->label('Debt Scenario')
                    ->placeholder('Select a scenario')
                    ->required(fn ($get) => $get('analysis_type') === 'debt')                  
                    ->options([
                        'debt-interest-rate-change' => 'What if my debt interest rate changes?',
                        'debt-payment-change' => 'What if I change my monthly payment?',
                    ])
                    ->live()
                    ->visible(fn ($get) => $get('analysis_type') === 'debt'), // Only show if 'debt' is selected

                // Required field to choose a savings what-if scenario.  Uses same name as debt to make it simple.
                Select::make('what_if_scenario')
                    ->label('Saving Scenario')
                    ->placeholder('Select a scenario')
                    ->required(fn ($get) => $get('analysis_type') === 'savings')                  
                    ->options([
                        'saving-interest-rate-change' => 'What if my saving interest rate changes?',
                        'savings-change' => 'What if I change my monthly savings?',
                    ])
                    ->required()
                    ->live()
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
                    ->visible(fn ($get) => $get('what_if_scenario') === 'debt-interest-rate-change')
                    ->required()
                    ->helperText(fn ($get) => ($debt = Debt::find($get('debt_id'))) ? "Current rate: " . number_format($debt->interest_rate, 2) . "%" : null),

                // Required field for the new monthly debt payment (visible for 'payment-change)'.
                TextInput::make('new_monthly_debt_payment')
                    ->type('number')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->maxValue(9999999)
                    ->placeholder('Input a new monthly payment')
                    ->visible(fn ($get) => $get('what_if_scenario') === 'debt-payment-change')
                    ->required()
                    ->helperText(fn ($get) => ($debt = Debt::find($get('debt_id'))) ? "Current monthly payment: $" . number_format($debt->monthly_payment, 2) : null),             

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
                    ->visible(fn ($get) => $get('what_if_scenario') === 'saving-interest-rate-change')
                    ->required()
                    ->helperText(fn ($get) => $get('current_savings') ? "Current rate: " . number_format($get('current_savings'), 2) . "%" : null),

                // Required field for new monthly savings amount.  (visible for savings amount change)
                TextInput::make('new_monthly_savings') 
                    ->type('number')
                    ->numeric()
                    ->prefix('$')
                    ->minValue(0)
                    ->maxValue(9999999)
                    ->placeholder('Input a new monthly savings amount')
                    ->visible(fn ($get) => $get('what_if_scenario') === 'savings-change')
                    ->required()
                    ->helperText(fn ($get) => $get('current_monthly_savings') ? "Current monthly savings: $" . number_format($get('current_monthly_savings'), 2) : null),

                // Optional field for the user to input the amount of months they want to save money. (Visible for both interest rate change and savings amount change.)
                TextInput::make('months_to_save')
                    ->type('number')
                    ->numeric()
                    ->postfix('months')
                    ->minValue(0)
                    ->maxValue(9999999)
                    ->default(12)
                    ->placeholder('How many months do you want to save?')
                    ->visible(fn ($get) => ! $get('saving_for_goal') && ($get('what_if_scenario') === 'saving-interest-rate-change' || $get('what_if_scenario') === 'savings-change'))
                    ->helperText('If you do not provide a value, then the analysis will project up to 12 months into the future.'),
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
        if ($state['analysis_type'] === 'debt') {
            if ($state['what_if_scenario'] === 'debt-interest-rate-change') {
                $result = (new WhatIfAnalysisService)->debtInterestRateChangeScenario(
                    $state['debt_id'],                                            
                    $state['debt_new_interest_rate'],                                   
                    $state['monthly_income'],
                    $total_monthly_expenses,
                    $state['financial_goal_id']
                );
            }
            // Call the payment-change algorithm.
            elseif ($state['what_if_scenario'] === 'debt-payment-change') {
                $result = (new WhatIfAnalysisService)->changeMonthlyPaymentScenario(
                    $state['debt_id'],                                            
                    $state['new_monthly_debt_payment'],                                   
                    $state['monthly_income'],
                    $total_monthly_expenses,
                    $state['financial_goal_id']
                );
            }
        }
        elseif ($state['analysis_type'] === 'savings') {
            if ($state['saving_for_goal'] && $state['what_if_scenario'] === 'saving-interest-rate-change') {
                $result = (new WhatIfAnalysisService)->goalSavingsInterestRateChangeScenario(
                    $state['financial_goal_id'],  
                    $state['current_monthly_savings'],
                    $state['current_savings_interest_rate'],                                 
                    $state['savings_new_annual_interest_rate'],                                   
                    $state['monthly_income'],
                    $total_monthly_expenses
                );
            }
            elseif (! $state['saving_for_goal'] && $state['what_if_scenario'] === 'saving-interest-rate-change') {
                // Change algorithm to new saving for goal interest rate change algorithm.
                $result = (new WhatIfAnalysisService)->noGoalSavingsInterestRateChangeScenario(
                    $state['current_savings_amt'],
                    $state['current_monthly_savings'],  
                    $state['current_savings_interest_rate'],                                 
                    $state['savings_new_annual_interest_rate'],                                   
                    $state['monthly_income'],
                    $total_monthly_expenses,
                    null
                );

            }
            elseif ($state['saving_for_goal'] && $state['what_if_scenario'] === 'savings-change') {
                // Change algorithm to new saving for goal monthly savings change algorithm.
                $result = (new WhatIfAnalysisService)->goalMonthlySavingsChangeScenario(
                    $state['financial_goal_id'],  
                    $state['current_monthly_savings'],
                    $state['current_savings_interest_rate'],                                 
                    $state['new_monthly_savings'],                                   
                    $state['monthly_income'],
                    $total_monthly_expenses
                );
            }
            elseif (! $state['saving_for_goal'] && $state['what_if_scenario'] === 'savings-change') {
                $result = (new WhatIfAnalysisService)->noGoalMonthlySavingsChangeScenario(
                    $state['current_savings_amt'],
                    $state['current_monthly_savings'],  
                    $state['current_savings_interest_rate'],                                 
                    $state['new_monthly_savings'],                                   
                    $state['months_to_save'],
                    $state['monthly_income'],
                    $total_monthly_expenses,
                    null
                );
            }
        }

        // If result returned an error, save it to $this->what_if_report.
        if (isset($result['error'])) $this->what_if_report = $result;

        elseif ($state['analysis_type'] === 'savings') {
            $this->what_if_report = $this->saveSavingsWhatIfReport($state, $result);
        }

        elseif ($state['analysis_type'] === 'debt') {
            $this->what_if_report = $this->saveWhatIfReport($state, $result);
        }
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
    public function saveWhatIfReport(array $state, array $result): WhatIfReport {
        return WhatIfReport::create([
            // Identifiers and scenario choice         
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
            'new_interest_rate' => $state['debt_new_interest_rate'] ?? null,
            'new_monthly_debt_payment' => $state['new_monthly_debt_payment'] ?? null,

            // Scenario outcomes
            'total_months' => $result['total_months'],
            'total_interest_paid' => $result['total_interest_paid'],
            'timeline' => $result['timeline'],
            'goal_impact' => $result['goal_impact'] ?? null,
        ]);
    }

    public function saveSavingsWhatIfReport(array $state, array $result): SavingsWhatIfReport {
        return SavingsWhatIfReport::create([
            //Identifiers and scenario choice
            'user_id' => Auth::id(),
            'financial_goal_id' => $state['financial_goal_id'] ?? null,
            'savings_name' => $state['savings_name'] ?? $result['savings_name'],
            'what_if_scenario' => $state['what_if_scenario'],

            // Original savings state
            'original_savings' => $result['original_savings_amount'],
            'original_interest_rate' => $result['original_interest_rate'],
            'current_monthly_savings' => $result['original_monthly_savings'],

            // Scenario inputs
            'new_interest_rate' => $state['savings_new_annual_interest_rate'] ?? null,
            'new_monthly_savings_rate' => $state['new_monthly_savings'] ?? null,              // New monthly savings rate
            'months_to_save' => $state['months_to_save'] ?? null,                     // The total months to reach the savings goal

            // Scenario outcomes
            'total_interest_earned' => $result['total_interest_earned'],     // The total interest earned over the time period
            'total_months' => $result['total_months'],
            'timeline' => $result['timeline'] ?? null,                         // A Json array holding the results of each month.
            'goal_impact' => $result['goal_impact'] ?? null

        ]);

    }
}