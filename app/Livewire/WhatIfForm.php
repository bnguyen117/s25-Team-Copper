<?php
 
namespace App\Livewire;
 
use App\Services\WhatIfAnalysisService;
use App\Models\WhatIfReport;
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

    public $monthly_income;                 // User's monthly income
    public $monthly_debt_expenses;          // User's monthly debt expenses (calculated from the user's debts)
    public $monthly_non_debt_expenses;      // User's monthly non-debt expenses (e.g. groceries, utility bills, etc)
    public $debt_id;                        // Selected Debt ID for analysis
    public $financial_goal_id;              // Selected Financial goal ID (optional)
    public $what_if_scenario;               // User's chosen what-if scenario
    public $new_interest_rate;              // New interest rate for 'interest-rate-change' scenario
    public $new_monthly_debt_payment;       // New monthly payment for 'payment-change' scenario
    public $what_if_report;                 // Stores the what-if analysis result; passed to the view for rendering


    /**
     * On component mount:
     *  - Resets the form and what_if_scenario with clearForm().
     *  - Sets $this->monthly_debt_expenses to the sum of the user's debt payments.
     */
    public function mount(): void
    {
        $this->clearForm();
        $this->monthly_debt_expenses = $this->calculateTotalDebtPayments();
    }

    /**
     * Defines the form's field structure.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([

                // Required field for the user's monthly income.
                TextInput::make('monthly_income')
                ->type('number')
                ->required()
                ->prefix('$')
                ->placeholder('Enter your monthly income'),

                // Disabled field showing the total of the user's monthly debt payments.
                TextInput::make('monthly_debt_expenses')
                ->type('number')
                ->label('Monthly Debt Expenses')
                ->prefix('$')
                ->disabled()
                ->default($this->monthly_debt_expenses)
                ->helperText('The sum of your monthly debt payments.'),

                // Optional field for additonal non-debt expenses.
                TextInput::make('monthly_non_debt_expenses')
                ->type('number')
                ->label('Non-Debt Monthly Expenses')
                ->prefix('$')
                ->default(0)
                ->minValue(0)
                ->helperText('Expenses like groceries, utilities, etc.'),

                // Required field to select a debt for analysis.
                Select::make('debt_id')
                ->label('Debt')
                ->options(fn () => $this->getCurrentUserDebts())
                ->placeholder('Select a debt for analysis')
                ->required(),

                // Optional field to select a financial goal for analysis.
                Select::make('financial_goal_id')
                ->label('Financial Goal')
                ->options(fn () => $this->getCurrentUserGoals())
                ->nullable()
                ->placeholder('Select a financial goal (optional)'),

                // Required field to choose a what-if scenario - controls visibility of the following fields.
                Select::make('what_if_scenario')
                ->label('Scenario')
                ->placeholder('Select a scenario')
                ->required()
                ->reactive()                   
                ->options([
                    'interest-rate-change' => 'What if my interest rate changes?',
                    'payment-change' => 'What if I change my monthly payment?',
                ]),

                /**
                 * What-If Scenario Specific Fields.
                 */
                // Required field for the new annual interest rate (visible for 'interest-rate-change').
                TextInput::make('new_interest_rate')
                ->type('number')
                ->label('New Interest Rate (%)')
                ->suffix('%')
                ->visible(fn ($get) => $get('what_if_scenario') === 'interest-rate-change')
                ->required(fn ($get) => $get('what_if_scenario') === 'interest-rate-change')
                ->minValue(0)
                ->maxValue(100)
                ->placeholder('Input a new annual interest rate'),

                // Required field for the new monthly debt payment (visible for 'payment-change)'.
                TextInput::make('new_monthly_debt_payment')
                ->type('number')
                ->prefix('$')
                ->visible(fn ($get) => $get('what_if_scenario') === 'payment-change')
                ->required(fn ($get) => $get('what_if_scenario') === 'payment-change')
                ->minValue(0)
                ->placeholder('Input a new monthly payment'),                                                 
                ]);
    }

    /**
     * Called on form submission and runs the selected what-if scenario:
     *  - Captures the form state and calculates total monthly expenses (debt + non-debt).
     *  - Executes the chosen scenario algorithm via WhatIfAnalysisService.
     *  - Stores the result in $this-what_if_report.
     *  - Saves to the database if successful.
     */
    public function analyze(): void
    {
        $state = $this->form->getState();
        $total_monthly_expenses = $this->monthly_debt_expenses + ($state['monthly_non_debt_expenses'] ?? 0);
        $service = new WhatIfAnalysisService();

        if ($state['what_if_scenario'] === 'interest-rate-change') {
            $result = $service->interestRateChangeScenario(
                $state['debt_id'],                                            
                $state['new_interest_rate'],                                   
                $state['monthly_income'],
                $total_monthly_expenses,
            );
        } 
        
        elseif ($state['what_if_scenario'] === 'payment-change') {
            $result = $service->changeMonthlyPaymentScenario(
                $state['debt_id'],                                            
                $state['new_monthly_debt_payment'],                                   
                $state['monthly_income'],
                $total_monthly_expenses,
            );
        }

        if (isset($result['error'])) {
            $this->what_if_report = $result;
        } else {
            $this->what_if_report = WhatIfReport::create([                        
                'user_id' => Auth::id(),
                'debt_id' => $state['debt_id'],
                'what_if_scenario' => $state['what_if_scenario'],
                'original_debt_amount' => $result['original_debt_amount'],
                'current_monthly_debt_payment' => $result['current_monthly_debt_payment'],
                'minimum_monthly_debt_payment' => $result['minimum_monthly_debt_payment'] ?? null,
                'new_interest_rate' => $result['new_interest_rate'] ?? null,
                'new_monthly_debt_payment' => $result['new_monthly_debt_payment'] ?? null,
                'total_months' => $result['total_months'],
                'total_interest_paid' => $result['total_interest_paid'],
                'timeline' => $result['timeline'],
            ]);
        }
    }

    /**
     * Render the component's view, passes $this->what_if_report as 'report'.
     */
    public function render(): View {
        return view('livewire.what-if.form', ['report' => $this->what_if_report]);
    }

    /**
     * Resets the form and its data.
     *  - Clears the form's state.
     *  - Resets $this->what_if_report to null.
     *  - Refreshes $this->monthly_expenses with the latest debt totals.
     */
    public function clearForm(): void{
        $this->form->fill();
        $this->what_if_report = null;
        $this->monthly_debt_expenses = $this->calculateTotalDebtPayments();
    }

    /**
     * Calculates the total of the currently authenticated user's monthly debt payments.
     */
    private function calculateTotalDebtPayments(): float
    {
        return \App\Models\Debt::where('user_id', Auth::id())
                    ->sum('monthly_payment');
    }

    /**
     * Gets the currently authenticated user's debts.
     */
    private function getCurrentUserDebts(): array
    {
        return \App\Models\Debt::where('user_id', Auth::id())                 // Get debts from the currently authenicated user
                    ->pluck('debt_name', 'id')                                // Extract debt_name as value, id as key
                    ->toArray();                                              // Convert to an array
    }

    /**
     * Gets the current authenticated user's goals
     */
    private function getCurrentUserGoals(): array
    {
        return \App\Models\FinancialGoal::where('user_id', Auth::id())        // Get goals from the currently authenicated user
                            ->pluck('goal_name', 'id')                        // Extract goal_name as value, id as key
                            ->toArray();                                      // Convert to an array
    }
}