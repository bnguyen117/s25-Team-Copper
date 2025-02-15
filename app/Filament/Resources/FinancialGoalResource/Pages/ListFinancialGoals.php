<?php

namespace App\Filament\Resources\FinancialGoalResource\Pages;

use App\Filament\Resources\FinancialGoalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFinancialGoals extends ListRecords
{
    protected static string $resource = FinancialGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
