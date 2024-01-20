<?php

namespace App\Filament\Resources\CommunityCategoryResource\Pages;

use App\Filament\Resources\CommunityCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCommunityCategory extends EditRecord
{
    protected static string $resource = CommunityCategoryResource::class;

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
