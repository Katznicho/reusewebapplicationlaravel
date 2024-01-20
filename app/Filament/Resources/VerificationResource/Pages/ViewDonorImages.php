<?php

namespace App\Filament\Resources\VerificationResource\Pages;

use App\Filament\Resources\VerificationResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class ViewDonorImages extends Page
{
    use InteractsWithRecord;

    protected static string $resource = VerificationResource::class;

    protected static string $view = 'filament.resources.verification-resource.pages.view-donor-images';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        static::authorizeResourceAccess();
    }
}
