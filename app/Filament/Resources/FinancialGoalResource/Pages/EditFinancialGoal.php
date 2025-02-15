<?php

namespace App\Filament\Resources\FinancialGoalResource\Pages;

use App\Filament\Resources\FinancialGoalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFinancialGoal extends EditRecord
{
    protected static string $resource = FinancialGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
