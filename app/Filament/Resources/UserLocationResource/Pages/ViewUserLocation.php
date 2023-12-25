<?php

namespace App\Filament\Resources\UserLocationResource\Pages;

use App\Filament\Resources\UserLocationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUserLocation extends ViewRecord
{
    protected static string $resource = UserLocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
