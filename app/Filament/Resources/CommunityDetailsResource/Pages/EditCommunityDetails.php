<?php

namespace App\Filament\Resources\CommunityDetailsResource\Pages;

use App\Filament\Resources\CommunityDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommunityDetails extends EditRecord
{
    protected static string $resource = CommunityDetailsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
