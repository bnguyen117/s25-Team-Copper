<?php

namespace App\Livewire;

use App\Models\Budget;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Auth;

class PercentageTable extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected $listeners =['refreshPercentTable'  => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            ->query(Budget::where('user_id', Auth::id()))
            ->columns([
                TextColumn::make('title')
                    ->label('Income Percentage:'),
                TextColumn::make('needs_percentage')
                    ->numeric()
                    ->label('Needs')
                    ->suffix('%')
                    ->weight(FontWeight::Medium),

                TextColumn::make('wants_percentage')
                    ->numeric()
                    ->label('Wants')
                    ->suffix('%')
                    ->weight(FontWeight::Medium),

                TextColumn::make('savings_percentage')
                    ->numeric()
                    ->label('Savings')
                    ->suffix('%')
                    ->weight(FontWeight::Medium),
            ])
            ->paginated(false);
    }

    public function render(): View
    {
        return view('livewire.percentage-table');
    }
}
