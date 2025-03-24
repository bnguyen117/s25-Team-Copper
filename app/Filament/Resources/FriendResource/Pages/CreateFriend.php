<?php

namespace App\Filament\Resources\FriendResource\Pages;

use App\Filament\Resources\FriendResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFriend extends CreateRecord
{
    protected static string $resource = FriendResource::class;
}
