<?php

namespace App\Filament\Resources\FinancialGoalResource\Pages;

use App\Filament\Resources\FinancialGoalResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFinancialGoal extends CreateRecord
{
    protected static string $resource = FinancialGoalResource::class;
}
