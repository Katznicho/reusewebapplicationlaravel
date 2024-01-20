<?php

namespace App\Filament\Resources\CommunityDetailsResource\Pages;

use App\Filament\Resources\CommunityDetailsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCommunityDetails extends ListRecords
{
    protected static string $resource = CommunityDetailsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
