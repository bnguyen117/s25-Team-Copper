<?php

namespace App\Filament\Resources\FriendResource\Pages;

use App\Filament\Resources\FriendResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFriends extends ListRecords
{
    protected static string $resource = FriendResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
