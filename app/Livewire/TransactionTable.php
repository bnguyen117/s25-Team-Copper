<?php
 
 namespace App\Livewire;
 
 use App\Models\Transaction;
 use App\Models\Debt;
 use App\Services\Budgeting\BudgetService;
 use App\Services\Debt\DebtService;
 use Filament\Forms;
 use Filament\Forms\Concerns\InteractsWithForms;
 use Filament\Forms\Contracts\HasForms;
 use Filament\Tables;
 use Filament\Tables\Concerns\InteractsWithTable;
 use Filament\Tables\Contracts\HasTable;
 use Filament\Tables\Table;
 use Filament\Support\Enums\FontWeight;
 use Filament\Notifications\Notification;
 use Livewire\Component;
 use Illuminate\Contracts\View\View;
 use Illuminate\Support\Facades\Auth;
 
 class TransactionTable extends Component implements HasForms, HasTable
 {
     use InteractsWithForms, InteractsWithTable;
 
     // Store original transaction values for reverting budget/debt impacts during an edit.
     private $originalCategory = null;
     private $originalAmount = null;
     private $originalPrincipal = null;
     private $originalDebtId = null;
 
     // Listeners to refresh table's UI
     protected $listeners = ['refreshBudgetTable' => '$refresh', 'refreshBudgetTransactionTable' => '$refresh'];
 
     // Initialize Services
     private BudgetService $budgetService;
     private DebtService $debtService;
     public function __construct()
     {
         $this->budgetService = new BudgetService();
         $this->debtService = new DebtService();
     }
 
     public function table(Table $table): Table
     {
         return $table
             ->query(Transaction::with('debt')->where('user_id', Auth::id()))
             ->heading('Your Transactions')
             ->columns($this->getTableFields())
             ->defaultSort('transaction_date', 'desc')
             // Allow filtering transactions by category and debt.
             ->filters([
                 Tables\Filters\SelectFilter::make('category')
                     ->options(['needs' => 'Needs', 'wants' => 'Wants', 'savings' => 'Savings']),
                 Tables\Filters\SelectFilter::make('debt_id')
                     ->label('Debt')
                     ->relationship('debt', 'debt_name', fn ($query) => $query->where('user_id', Auth::id())),
             ])
             ->headerActions([
                 Tables\Actions\CreateAction::make()
                     ->label('Add Transaction')
                     ->button()
                     ->slideOver()
                     ->form($this->getFormFields())
                     // Set user_id for all transactions and category/name for debt transactions.
                     ->mutateFormDataUsing(function ($data) {
                         $data['user_id'] = Auth::id();
                         if ($data['transaction_type'] === 'debt') {
                             $debt = Debt::find($data['debt_id']);
                             $data['category'] = 'needs';
                             $data['name'] = $debt->debt_name;
                         }
                         return $data;
                     })
                     ->action(function ($data, $action) {
                         // Ensure the submitted data is valid for the user's budget and debt.
                         $this->budgetService->validateBudget($data, $this->notify(...));
                         $this->debtService->validateDebt($data, $this->notify(...));
 
                         // Create the new transaction record.
                         $record = Transaction::create($data);
 
                         // Udate Debt/Budget records to reflect the transaction's impact.
                         $this->handleTransaction($record);
                         $action->success();
                     }),
             ])
             ->actions([
                 Tables\Actions\EditAction::make()
                     ->button()
                     ->slideOver()
                     ->form($this->getFormFields())
                     
                     // Auto fill form with current transaction values.
                     ->fillForm(function (Transaction $record) {
                         return [
                             'transaction_type' => $record->transaction_type,
                             'debt_id' => $record->debt_id,
                             'category' => $record->category,
                             'name' => $record->name,
                             'amount' => $record->amount,
                             'description' => $record->description,
                             'transaction_date' => $record->transaction_date->format('Y-m-d'),
                         ];
                     })
                     ->action(function ($data, $record, $action) {
                         // Store original transaction's values for reverting its budget/debt impact.
                         $this->originalCategory = $record->category;
                         $this->originalAmount = $record->amount;
                         $this->originalPrincipal = $record->principal_paid;
                         $this->originalDebtId = $record->debt_id;
 
                         // Ensure the submitted data is valid for the user's budget and debt.
                         $this->budgetService->validateBudget($data, $this->notify(...), $this->originalAmount);
                         $this->debtService->validateDebt($data, $this->notify(...));
 
                         // Update transaction record with the new data.
                         $record->update($data);
 
                         // Initialize the original transaction.
                         $originalTransaction = new Transaction([
                             'category' => $this->originalCategory,
                             'amount' => $this->originalAmount,
                             'user_id' => Auth::id(),
                             'debt_id' => $this->originalDebtId,
                         ]);
                         $originalTransaction->principal_paid = $this->originalPrincipal;
 
                         // Revert the original transaction's budget and debt impact.
                         $this->handleTransaction($originalTransaction, true);
 
                         // Apply updated transaction's budget and debt impact.
                         $this->handleTransaction($record);
 
                         // Clear original values.
                         $this->originalCategory = null;
                         $this->originalAmount = null;
                         $this->originalPrincipal = null;
                         $action->success();
                     }),
                 // Delete transaction and revert its debt/budget impact.
                 Tables\Actions\DeleteAction::make()
                     ->button()
                     ->after(fn ($record) => $this->handleTransaction($record, true)),
             ])
             ->bulkActions([
                 // Delete all selected transactions and revert their debt/budget impact.
                 Tables\Actions\BulkActionGroup::make([
                     Tables\Actions\DeleteBulkAction::make()
                         ->after(fn ($records) => $records->each(fn ($record) => $this->handleTransaction($record, true))),
                 ]),
             ])
             ->paginated(false);
     }
 
     /**
      * Defines the columns of the UI table.
      */
     protected function getTableFields(): array
     {
         return [
             Tables\Columns\TextColumn::make('transaction_type')
                 ->label('Type')
                 ->formatStateUsing(fn ($state) => $state === 'debt' ? 'Debt Payment' : 'Other Expense')
                 ->searchable()
                 ->sortable()
                 ->weight(FontWeight::Medium),
             Tables\Columns\TextColumn::make('name')
                 ->label('Name')
                 ->searchable()
                 ->sortable()
                 ->weight(FontWeight::Medium),
             Tables\Columns\TextColumn::make('category')
                 ->label('Category')
                 ->formatStateUsing(fn ($state) => ucfirst($state))
                 ->badge()
                 ->searchable()
                 ->sortable()
                 ->weight(FontWeight::Medium),
             Tables\Columns\TextColumn::make('amount')
                 ->money('usd')
                 ->sortable()
                 ->weight(FontWeight::Medium)
                 ->description('Amount', position: 'above'),
             Tables\Columns\TextColumn::make('interest_paid')
                 ->money('usd')
                 ->sortable()
                 ->weight(FontWeight::Medium)
                 ->description('Interest Paid', position: 'above'),
             Tables\Columns\TextColumn::make('principal_paid')
                 ->money('usd')
                 ->sortable()
                 ->weight(FontWeight::Medium)
                 ->description('Principal Paid', position: 'above'),
             Tables\Columns\TextColumn::make('transaction_date')
                 ->date('Y-m-d')
                 ->sortable()
                 ->weight(FontWeight::Medium)
                 ->description('Transaction Date', position: 'above'),
         ];
     }
 
     /**
      * Defines the fields of the slide-over form.
      */
     protected function getFormFields(): array
     {
         return [
             Forms\Components\Select::make('transaction_type')
                 ->label('Transaction Type')
                 ->options(['debt' => 'Debt Payment', 'expense' => 'Other Expense'])
                 ->reactive()
                 ->required()
                 ->default('expense'),
             
             // Only displays on debt transaction type.
             Forms\Components\Select::make('debt_id')
                 ->label('Debt')
                 ->relationship('debt', 'debt_name', fn ($query) => $query->where('user_id', Auth::id()))
                 ->required()
                 ->visible(fn ($get) => $get('transaction_type') === 'debt'),
             
             // Only displays on expense transaction type.
             Forms\Components\Select::make('category')
                 ->label('Category')
                 ->options(['needs' => 'Needs', 'wants' => 'Wants', 'savings' => 'Savings Contribution'])
                 ->required()
                 ->visible(fn ($get) => $get('transaction_type') === 'expense'),
             Forms\Components\TextInput::make('name')
                 ->label('Name')
                 ->maxLength(100)
                 ->placeholder('e.g., Groceries')
                 ->required()
                 ->visible(fn ($get) => $get('transaction_type') === 'expense'),
             
             // Displays on both transaction types.
             Forms\Components\TextInput::make('amount')
                 ->label('Amount')
                 ->numeric()
                 ->placeholder('Debt payment amounts are added to your budgeted needs')
                 ->prefix('$')
                 ->minValue(0)
                 ->maxValue(99999999.99)
                 ->step(0.01)
                 ->required(),
             Forms\Components\DatePicker::make('transaction_date')
                 ->label('Transaction Date')
                 ->displayFormat('Y-m-d')
                 ->default(now())
                 ->required(),
         ];
     }
 
     /**
      * Handles updating the user's budget and debt records to apply a transaction's impact or revert it.
      */
     private function handleTransaction(Transaction $record, bool $reverse = false): void
     {
         if (!$record) return;
         $this->budgetService->updateBudget($record, $reverse, $this->notify(...));
         $this->debtService->updateDebt($record, $reverse);
 
         // Update UI.
         $this->dispatch('refreshBudgetTransactionTable');
         $this->dispatch('refreshBudgetingChat');
         $this->dispatch('refreshDebtTable');
     }
 
     /**
      * Sends a notification to the user.
      */
     private function notify(string $title, string $body): void
     {
         Notification::make()->title($title)->body($body)->danger()->send();
     }
 
     /**
      * Renders the view and passes budget summary to the frontend.
      */
     public function render(): View
     {
         $summary = $this->budgetService->getBudgetSummary();
         return view('livewire.transaction-table', [
             'spendingSummary' => $summary['spendingSummary'],
             'percentageSummary' => $summary['percentageSummary'],
         ]);
     }
 }