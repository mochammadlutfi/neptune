<?php

namespace App\Services\Dashboard;

use App\Repositories\Dashboard\DashboardOverviewRepository;
use Carbon\Carbon;

class DashboardOverviewService
{
    protected $dashboardRepository;

    public function __construct(DashboardOverviewRepository $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function getOverviewKpi(): array
    {
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $currentData = $this->dashboardRepository->getMonthlyFinancialData($currentMonth);
        $lastMonthData = $this->dashboardRepository->getMonthlyFinancialData($lastMonth);

        return [
            'totalRevenue' => $currentData['revenue'],
            'totalExpenses' => $currentData['expenses'],
            'netProfit' => $currentData['revenue'] - $currentData['expenses'],
            'cashFlow' => $currentData['cash_flow'],
            'revenueGrowth' => $this->calculateGrowth($lastMonthData['revenue'], $currentData['revenue']),
            'expensesGrowth' => $this->calculateGrowth($lastMonthData['expenses'], $currentData['expenses']),
            'profitGrowth' => $this->calculateGrowth(
                $lastMonthData['revenue'] - $lastMonthData['expenses'],
                $currentData['revenue'] - $currentData['expenses']
            )
        ];
    }

    public function getDocumentStatus(): array
    {
        return $this->dashboardRepository->getDocumentStatus();
    }

    public function getOperationalMetrics(): array
    {
        return $this->dashboardRepository->getOperationalMetrics();
    }

    public function getRecentSalesOrders(): array
    {
        return $this->dashboardRepository->getRecentSalesOrders();
    }

    public function getRecentPurchaseOrders(): array
    {
        return $this->dashboardRepository->getRecentPurchaseOrders();
    }

    public function getPendingInvoices(): array
    {
        return $this->dashboardRepository->getPendingInvoices();
    }

    public function getFinancialTrend(array $dateRange): array
    {
        $startDate = Carbon::parse($dateRange[0]);
        $endDate = Carbon::parse($dateRange[1]);

        return $this->dashboardRepository->getFinancialTrend($startDate, $endDate);
    }

    private function calculateGrowth(float $lastValue, float $currentValue): float
    {
        if ($lastValue === 0) return 0;
        return round((($currentValue - $lastValue) / $lastValue) * 100, 1);
    }
} 