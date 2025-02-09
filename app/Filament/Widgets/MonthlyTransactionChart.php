<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class MonthlyTransactionChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Monthly Transaction';

    protected function getData(): array
    {
        $data = Trend::model(Transaction::class)
            ->between(now()->startOfMonth(), now()->endOfMonth())
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Transactions Created',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate)->toArray(),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date)->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public function getDescription(): string
    {
        return 'This chart shows the number of transactions made each month.';
    }
}
