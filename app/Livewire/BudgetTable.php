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
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Panel;

// Illuminate
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

// Filament Support
use Filament\Support\Enums\FontWeight;

class BudgetTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        return $table
            // Filter Budget to only values belonging to current user.
            ->query(Budget::where('user_id', Auth::id()))
            ->columns(
                [
                TextColumn::make('income')
                        ->numeric()
                        ->description('Monthly Income', position: 'above')
                        ->money('usd')
                        ->weight(FontWeight::Medium),

                TextColumn::make('budgeted_needs')
                    ->numeric()
                    ->description('Needs', position: 'above')
                    ->money('usd')
                    ->weight(FontWeight::Medium),

                TextColumn::make('budgeted_wants')
                    ->numeric()
                    ->description('Wants', position: 'above')
                    ->money('usd')
                    ->weight(FontWeight::Medium),

                TextColumn::make('budgeted_savings')
                    ->numeric()
                    ->description('Savings', position: 'above')
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
