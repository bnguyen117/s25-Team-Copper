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

}

