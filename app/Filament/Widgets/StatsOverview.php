<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Listing;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    private function getPercentage(float $from, float $to): float
    {
        return (($to - $from) / ($to + $from / 2)) * 100;
    }

    protected function getStats(): array
    {
        $newlisting = Listing::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $transaction = Transaction::where('status', 'approved')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $prevTransaction = Transaction::where('status', 'approved')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();

        // Menghitung total revenue per bulan
        $revenue = Transaction::where('status', 'approved')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_price');

        $prevRevenue = Transaction::where('status', 'approved')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->sum('total_price');

        $transactionPercentage = $this->getPercentage($prevTransaction, $transaction);
        $revenuePercentage = $this->getPercentage($prevRevenue, $revenue);

        return [
            Stat::make('New Listings This Month', $newlisting),
            Stat::make('Transactions This Month', $transaction)
                ->description($transactionPercentage > 0
                    ? number_format($transactionPercentage, 2) . "% increase"
                    : number_format(abs($transactionPercentage), 2) . "% decrease")
                ->color($transactionPercentage >= 0 ? 'success' : 'danger'),
            Stat::make('Revenue This Month', '$' . number_format($revenue, 2))
                ->description($revenuePercentage > 0
                    ? number_format($revenuePercentage, 2) . "% increase"
                    : number_format(abs($revenuePercentage), 2) . "% decrease")
                ->color($revenuePercentage >= 0 ? 'success' : 'danger'),
        ];
    }
}
