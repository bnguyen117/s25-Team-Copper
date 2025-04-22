<?php

namespace App\Filament\Resources\DebtResource\Pages;

use App\Filament\Resources\DebtResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Services\Rewards\BadgeService;
use App\Gamify\Points\DebtCreated;
use QCod\Gamify\Gamify;
use QCod\Gamify\HasReputations;



class CreateDebt extends CreateRecord
{
    protected static string $resource = DebtResource::class;

    protected function afterCreate(): void  //triggers after a debt is created
    {
        $debt = $this->record;
        $user = $debt->user;

        // Check and sync debt-related badge
       // app(BadgeService::class)->syncDebtRelatedBadges($user);

       app(\App\Services\Rewards\BadgeService::class)->syncDebtRelatedBadges($user);

       
    }
}

