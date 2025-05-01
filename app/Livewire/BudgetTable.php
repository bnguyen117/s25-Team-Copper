<?php

namespace App\Livewire;

use App\Models\Budget;
use Livewire\Component;

// Form
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

// Table
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Panel;

// Illuminate
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
            ->columns(
                [
                TextColumn::make('income')
                    ->numeric()
                    ->label('Monthly Income')
                    ->money('usd')
                    ->weight(FontWeight::Medium),

                TextColumn::make('budgeted_needs')
                    ->numeric()
                    ->label('Budgeted Needs')
                    ->money('usd')
                    ->weight(FontWeight::Medium),

                TextColumn::make('budgeted_wants')
                    ->numeric()
                    ->label('Budgeted Wants')
                    ->money('usd')
                    ->weight(FontWeight::Medium),

                TextColumn::make('budgeted_savings')
                    ->numeric()
                    ->label('Budgeted Savings')
                    ->money('usd')
                    ->weight(FontWeight::Medium),
                ]
            )
            ->paginated(false);
    }

    public function render(): View
    {
        return view('livewire.budget-table');
    }
}
