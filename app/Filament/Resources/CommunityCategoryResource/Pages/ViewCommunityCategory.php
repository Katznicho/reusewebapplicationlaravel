<?php

namespace App\Filament\Resources\CommunityCategoryResource\Pages;

use App\Filament\Resources\CommunityCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCommunityCategory extends ViewRecord
{
    protected static string $resource = CommunityCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
