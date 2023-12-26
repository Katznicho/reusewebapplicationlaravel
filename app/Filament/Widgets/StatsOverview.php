<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->icon('heroicon-o-arrow-trending-up')
                ->description('Total number of customers')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 9])
                ->url(route('filament.admin.resources.users.index'))
                ->extraAttributes([
                    'class' => 'text-white text-lg cursor-pointer',
                ]),
            Stat::make('Total Transactions', Payment::count())
                ->icon('heroicon-o-arrow-trending-up')
                ->description('Total number of transactions')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 9])
                ->url(route('filament.admin.resources.payments.index'))
                ->extraAttributes([
                    'class' => 'text-white text-lg cursor-pointer',
                ]),
            Stat::make('Total Donations', User::count())
                ->icon('heroicon-o-arrow-trending-up')
                ->description('Total number of users')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 9])
                ->url(route('filament.admin.resources.donations.index'))
                ->extraAttributes([
                    'class' => 'text-white text-lg cursor-pointer',
                ]),
            Stat::make('Total Categories', Category::count())
                ->icon('heroicon-o-arrow-trending-up')
                ->description('Total number of branches')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 9])
                ->url(route('filament.admin.resources.categories.index'))
                ->extraAttributes([
                    'class' => 'text-white text-lg cursor-pointer',
                ]),
            Stat::make('Total Products', Product::count())
                ->icon('heroicon-o-arrow-trending-up')
                ->description('Total number of cards')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                // ->chart([7, 2, 10, 3, 15, 4, 9])
                ->url(route('filament.admin.resources.products.index'))
                ->extraAttributes([
                    'class' => 'text-white text-lg cursor-pointer',
                ]),
            Stat::make('Total Deliveries', Product::count())
                ->icon('heroicon-o-arrow-trending-up')
                ->description('Total number of cards')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                // ->chart([7, 2, 10, 3, 15, 4, 9])
                ->url(route('filament.admin.resources.deliveries.index'))
                ->extraAttributes([
                    'class' => 'text-white text-lg cursor-pointer',
                ]),

        ];
    }
}
