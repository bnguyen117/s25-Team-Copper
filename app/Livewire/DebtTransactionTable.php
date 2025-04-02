<?php

namespace App\Livewire;

use App\Models\DebtTransaction;
use App\Models\Debt;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Panel;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class DebtTransactionTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    /* Stores the original principal paid before editing a transaction */
    private $originalPrincipal = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(DebtTransaction::with('debt')->whereHas('debt', fn ($q) => $q->where('user_id', Auth::id())))
            ->heading ('Debt Transactions')
            ->columns($this->getColumns())
            ->paginated(false)
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Transaction')
                    ->button()
                    ->slideover()
                    ->form($this->getFormFields())
                    ->after(
                        function (DebtTransaction $record) {
                        $this->updateDebt($record);             // Update debt with new transaction
                        $this->dispatch('refreshDebtTable');    // Refresh debt table in real-time
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->button()
                    ->slideOver()
                    ->form($this->getFormFields())
                    ->before(fn (DebtTransaction $record) => $this->originalPrincipal = $record->principal_paid)
                    ->after(function (DebtTransaction $record) {
                        $debt = $record->debt->fresh();
                        $debt->amount += $this->originalPrincipal;  // Reverse the original transaction's impact
                        $debt->save();
                        $this->updateDebt($record);                 // Update debt with new transaction details
                        $this->originalPrincipal = null;            // Reset for the next edit
                        $this->dispatch('refreshDebtTable');        // Refresh debt table UI
                    }),
                Tables\Actions\DeleteAction::make()
                    ->button()
                    ->after(function (DebtTransaction $record) {
                        $debt = $record->debt->fresh();
                        $debt->amount += $record->principal_paid;   // Reverse the transaction's debt impact
                        $debt->save();
                        $this->dispatch('refreshDebtTable');        // Refresh debt table UI
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function (Collection $records) {
                            foreach($records as $record) {
                                $debt = $record->debt->fresh();
                                $debt->amount += $record->principal_paid; // Reverse each transaction's debt impact
                                $debt->save();
                            }
                            $this->dispatch('refreshDebtTable');     // Refresh debt table UI
                        }),
                ]),
            ]);
    }

    /**
     * Update a debt and transaction record based on a payment transaction
     */
    private function updateDebt(DebtTransaction $record): void {
        $debt = $record->debt->fresh();
        $split = $this->calculatePaymentSplit($debt, $record->amount);
        $record->interest_paid = $split['interest_paid'];
        $record->principal_paid = $split['principal_paid'];
        $debt->amount -= $split['principal_paid'];                       // Reduce the debt by principal paid
        $record->save();
        $debt->save();
    }

    /**
     * Calculates the split between interest paid and principal paid for a payment 
     */
    private function calculatePaymentSplit (Debt $debt, float $paymentAmount): array {
        $monthlyRate = $debt->interest_rate / 1200;                         // Convert annual rate to monthly
        $interestPaid = $debt->amount * $monthlyRate;                       // Interest paid based on current debt amount
        $principalPaid = min($paymentAmount - $interestPaid, $debt->amount);// Principal paid based on interest paid
        return ['interest_paid' => $interestPaid, 'principal_paid' => $principalPaid];
    }

    /**
     * Returns an array defining the table's columns
     */
   private function getColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('debt.debt_name')
                ->label('Debt Name')
                ->searchable()
                ->size('md')
                ->weight(FontWeight::Bold)
                ->formatStateUsing(fn (DebtTransaction $record) => "{$record->debt->debt_name} - {$record->transaction_type} - {$record->transaction_date->format('F Y')}"),
            Panel::make([
                Split::make([
                    Tables\Columns\TextColumn::make('amount')
                        ->numeric()
                        ->money('usd')
                        ->sortable()
                        ->weight(FontWeight::Medium)
                        ->description('Amount', position: 'above'),
                    Tables\Columns\TextColumn::make('interest_paid')
                        ->numeric()
                        ->money('usd')
                        ->sortable()
                        ->weight(FontWeight::Medium)
                        ->description('Interest Paid', position: 'above'),
                    Tables\Columns\TextColumn::make('principal_paid')
                        ->numeric()
                        ->money('usd')
                        ->sortable()
                        ->weight(FontWeight::Medium)
                        ->description('Principal Paid', position: 'above'),
                    Tables\Columns\TextColumn::make('transaction_type')
                        ->weight(FontWeight::Medium)
                        ->description('Transaction Type', position: 'above'),
                    Tables\Columns\TextColumn::make('transaction_date')
                        ->date('Y-m-d')
                        ->sortable()
                        ->weight(FontWeight::Medium)
                        ->description('Transaction Date', position: 'above'),
                    Tables\Columns\TextColumn::make('description')
                        ->searchable()
                        ->limit(50)
                        ->weight(FontWeight::Medium)
                        ->description('Description', position: 'above'),
                ])->from('lg'),
            ])->collapsible(),
        ];
    }

    /**
     * Returns an array defining the form's fields
     */
    protected function getFormFields(): array
    {
        return [
            Forms\Components\Select::make('debt_id')
                ->label('Debt')
                ->relationship('debt', 'debt_name', fn ($query) => $query->where('user_id', Auth::id()))
                ->preload()
                ->required(),
            Forms\Components\TextInput::make('amount')
                ->label('Amount')
                ->numeric()
                ->prefix('$')
                ->minValue(0)
                ->maxValue(99999999.99)
                ->step(0.01)
                ->required(),
            Forms\Components\Select::make('transaction_type')
                ->label('Transaction Type')
                ->options(DebtTransaction::OPTIONS)
                ->default('payment')
                ->required(),
            Forms\Components\TextInput::make('description')
                ->label('Description')
                ->placeholder('Optional transaction details')
                ->columnSpanFull(),
            Forms\Components\DatePicker::make('transaction_date')
                ->label('Transaction Date')
                ->displayFormat('Y-m-d')
                ->default(now())
                ->required(),
        ];
    }

    public function render(): View
    {
        return view('livewire.debt-transaction-table');
    }
}