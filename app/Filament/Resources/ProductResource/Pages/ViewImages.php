<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class ViewImages extends Page
{
    use InteractsWithRecord;

    protected static string $resource = ProductResource::class;

    protected static string $view = 'filament.resources.product-resource.pages.view-images';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        static::authorizeResourceAccess();
    }
}
