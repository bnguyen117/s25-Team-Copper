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

    public $monthly_income;         // User's monthly income
    public $monthly_expenses;       // User's monthly exspenses
    public $debt_name;              // Selected Debt
    public $financial_goal;         // Selected Financial Goal
    public $algorithm;              // User's chosen WhatIf algorithm
    public $new_interest_rate;      // New interest rate input for 'interest-rate-change' algo
    public $new_monthly_payment;    // New monthly payment input for 'payment-change' algo
    public $report;                 // Stores the result of a what-if analysis algorithm.


    /**
     * On component mount:
     *  - clears the form's state
     *  - sets the value of $this->report to null
     *  - refreshes the value of $this->monthly_expenses
     */
    public function mount(): void
    {
        $this->clearForm();
        $this->monthly_expenses = $this->calculateTotalDebtPayments();
    }

    /**
     * Defines the form's field structure
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([

                // Required field for the user's monthly income.
                TextInput::make('monthly_income')
                ->type('number')
                ->required(),

                // Disabled field that displays the sum of the user's monthly debts.
                TextInput::make('monthly_expenses')
                ->type('number')
                ->label('Monthly Debt Expenses')
                ->prefix('$')
                ->disabled()
                ->default($this->monthly_expenses)
                ->helperText('Calculated by summing your monthly debt payments.'),

                // Required field for the user to select one of thier debts for analysis.
                Select::make('debt_name')
                ->label('Debt')
                ->options(fn () => $this->getCurrentUserDebts())
                ->placeholder('Select a debt for analysis')
                ->required(),

                // Optional field for the user to select one of their financial goals for analysis.
                Select::make('financial_goal')
                ->options(fn () => $this->getCurrentUserGoals())
                ->nullable()
                ->placeholder('Select a financial goal (optional)'),

                // Required field for the user to choose a what if analysis scenario.
                Select::make('algorithm')
                ->label('Scenario')
                ->placeholder('Select a scenario')
                ->required()
                ->reactive() // Selected option controls visibility of the following fields.                      
                ->options([
                    'interest-rate-change' => 'What if my interest rate changes?',
                    'payment-change' => 'What if I change my monthly payment?',
                ]),

                /**
                 * Algorithm Specific Fields.
                 */
                // Required field for the user to input their debt's new annual interest rate.
                TextInput::make('new_interest_rate')
                ->type('number')
                ->label('New Interest Rate (%)')
                ->visible(fn ($get) => $get('algorithm') === 'interest-rate-change')
                ->required(fn ($get) => $get('algorithm') === 'interest-rate-change')
                ->minValue(0)
                ->maxValue(100)
                ->placeholder('Input your new annual interest rate'),

                // Required field for the user to input their debt's new monthly payment.
                TextInput::make('new_monthly_payment')
                ->type('number')
                ->visible(fn ($get) => $get('algorithm') === 'payment-change')
                ->required(fn ($get) => $get('algorithm') === 'payment-change')
                ->minValue(0)
                ->placeholder('Input your new monthly payment'),                                                 
                ]);
    }

    /**
     * Called on form submission:
     *  - Stores the form's current state.
     *  - Appends $this->monthly_expenses to the state
     *  - Creates new 'WhatIfAnalysisService' object to call scenario algorithms.
     * 
     *  - Checks the stored $state for the user's chosen scenario algorithm.
     *  - Calls $service->algorithm() and stores the resulting array in $result.
     * 
     *  - Checks if $result returned an error message and stores the message in $this->report.
     *  - Else, save the user's id, relevant state values, and algorithm result to a WhatIfReport record
     *  - Create a copy of this record in $this-report for use later.
     * 
     */
    public function analyze(): void
    {
        $state = $this->form->getState();
        $state['monthly_expenses'] = $this->monthly_expenses;
        $service = new WhatIfAnalysisService();

        if ($state['algorithm'] === 'interest-rate-change') {
            $result = $service->interestRateChangeScenario(
                $state['debt_name'],                                            
                $state['new_interest_rate'],                                   
                $state['monthly_income'],
                $state['monthly_expenses'],
            );
        } 
        
        elseif ($state['algorithm'] === 'payment-change') {
            $result = $service->changeMonthlyPaymentScenario(
                $state['debt_name'],                                            
                $state['new_monthly_payment'],                                   
                $state['monthly_income'],
                $state['monthly_expenses'],
            );
        }

        if (isset($result['error'])) {
            $this->report = $result;
        } else {
            $this->report = WhatIfReport::create([                        
                'user_id' => Auth::id(),
                'debt_id' => $state['debt_name'],
                'algorithm' => $state['algorithm'],
                'original_amount' => $result['original_amount'],
                'current_payment' => $result['current_payment'],
                'minimum_payment' => $result['minimum_payment'] ?? null,
                'new_interest_rate' => $result['new_interest_rate'] ?? null,
                'new_payment' => $result['new_payment'] ?? null,
                'total_months' => $result['total_months'],
                'total_interest_paid' => $result['total_interest_paid'],
                'timeline' => $result['timeline'],
            ]);
        }
    }

    /**
     * Render the component's view.
     *  - passes $this->report to the view for use as 'report'.
     */
    public function render(): View {
        return view('livewire.what-if.form', ['report' => $this->report]);
    }

    /**
     * Resets the form and its data.
     *  - Clears the form's state.
     *  - Resets $this->report to null.
     *  - Refreshes $this->monthly_expenses with the latest debt totals.
     */
    public function clearForm(): void{
        $this->form->fill();
        $this->report = null;
        $this->monthly_expenses = $this->calculateTotalDebtPayments();
    }

    /**
     * Sums all of the user's monthly debts.
     */
    private function calculateTotalDebtPayments(): float
    {
        return \App\Models\Debt::where('user_id', Auth::id())
                    ->sum('monthly_payment');
    }

    /**
     * Gets the currently authenicated user's debts.
     */
    private function getCurrentUserDebts(): array
    {
        return \App\Models\Debt::where('user_id', Auth::id())                 // Get debts from the currently authenicated user
                    ->pluck('debt_name', 'id')                                // Extract debt_name as value, id as key
                    ->toArray();                                              // Convert to an array
    }

    /**
     * Gets the current authenicated user's goals
     */
    private function getCurrentUserGoals(): array
    {
        return \App\Models\FinancialGoal::where('user_id', Auth::id())        // Get goals from the currently authenicated user
                            ->pluck('goal_name', 'id')                        // Extract goal_name as value, id as key
                            ->toArray();                                      // Convert to an array
    }
}