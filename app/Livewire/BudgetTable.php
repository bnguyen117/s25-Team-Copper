<?php

namespace App\Livewire;

use App\Models\Budget;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class BudgetTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            ->query(Budget::where('user_id', Auth::id()))
            ->heading('Budgets')
            ->columns([
                Tables\Columns\TextColumn::make('income')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expenses')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('savings')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('remaining_balance')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.budget-table');
    }
}
