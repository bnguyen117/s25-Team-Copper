<?php

namespace App\Filament\Resources\FriendResource\Pages;

use App\Filament\Resources\FriendResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFriend extends EditRecord
{
    protected static string $resource = FriendResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
