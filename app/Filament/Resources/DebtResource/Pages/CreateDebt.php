<?php

namespace App\Filament\Resources\DebtResource\Pages;

use App\Filament\Resources\DebtResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Services\Rewards\BadgeService;

class CreateDebt extends CreateRecord
{
    protected static string $resource = DebtResource::class;

    protected function afterCreate(): void  //triggers after a debt is created
    {
        $user = auth()->user(); // get the currently logged-in user

        // Check and sync debt-related badge
        app(BadgeService::class)->syncDebtRelatedBadges($user);
    }
}

