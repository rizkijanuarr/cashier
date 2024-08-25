<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use App\Enums\OrderStatus;
use Flowframe\Trend\Trend;
use Illuminate\Support\Carbon;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Resources\OrderResource\Pages\ListOrders;

class OrderStats extends BaseWidget
{
    use InteractsWithPageTable;

    public static function canView(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'manager']);
    }

    protected function getTablePage(): string
    {
        return ListOrders::class;
    }

    protected function getStats(): array
    {
        $createdFrom = new Carbon($this->tableFilters['created_at']['created_from']) ?? now()->startOfMonth();
        $createdTo = new Carbon($this->tableFilters['created_at']['created_until']) ?? now()->endOfMonth();

        $count = Trend::model(Order::class)->between(start: $createdFrom, end: $createdTo)->perDay()->count();

        $profit = Trend::query(Order::query()->where('status', OrderStatus::COMPLETED))
            ->between(start: now()->startOfYear(), end: now()->endOfYear())
            ->perMonth()
            ->sum('profit');

        $total = Trend::query(Order::query()->where('status', OrderStatus::COMPLETED))
            ->between(start: now()->startOfYear(), end: now()->endOfYear())
            ->perMonth()
            ->sum('total');

        return [
            Stat::make('Orders', $this->getPageTableQuery()->count())
                ->chart($count->map(fn(TrendValue $item) => $item->aggregate)->toArray())
                ->icon('heroicon-o-shopping-bag')
                ->description('Orders this month so far.')
                ->descriptionColor('gray')
                ->color('success'),

            Stat::make(
                'Total',
                'Rp ' . number_format(
                    Order::query()
                        ->where('status', OrderStatus::COMPLETED)
                        ->when(
                            $this->tableFilters['created_at']['created_from'] && $this->tableFilters['created_at']['created_until'],
                            fn($query) => $query->whereDate('created_at', '>=', $createdFrom)->whereDate('created_at', '<=', $createdTo)
                        )
                        ->sum('total'),
                    0,
                    ',',
                    '.'
                )
            )
                ->chart($total->map(fn(TrendValue $item) => $item->aggregate)->toArray())
                ->icon('heroicon-o-banknotes')
                ->description('Profit this month so far.')
                ->descriptionColor('gray')
                ->color('success'),

            Stat::make(
                'Profit',
                'Rp ' . number_format(
                    Order::query()
                        ->where('status', OrderStatus::COMPLETED)
                        ->when(
                            $this->tableFilters['created_at']['created_from'] && $this->tableFilters['created_at']['created_until'],
                            fn($query) => $query->whereDate('created_at', '>=', $createdFrom)->whereDate('created_at', '<=', $createdTo)
                        )
                        ->sum('profit'),
                    0,
                    ',',
                    '.'
                )
            )
                ->chart($profit->map(fn(TrendValue $item) => $item->aggregate)->toArray())
                ->icon('heroicon-o-banknotes')
                ->description('Profit this month so far.')
                ->descriptionColor('gray')
                ->color('success'),
        ];
    }
}
