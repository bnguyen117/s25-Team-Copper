<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class DebtRepaymentChart extends ChartWidget
{
    protected static ?string $heading = 'Debt Repayment Timeline';
    
    // Holds the result of the WhatIf Analysis
    public $chartData;

    protected function getData(): array
    {
        // If the `$chartData` is null return an empty chart
        if (!$this->chartData) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        // Store timeline; Retrieve month count until full repayment.
        $timeline = $this->chartData['timeline'];
        $monthCount = count($timeline);

        // If timeline is less than 36 months, plot points monthly.
        if ($monthCount < 36) {
            return [
                'datasets' => [
                    [
                        'label' => 'Remaining Balance',                                     
                        'data' => array_map(fn ($entry) => round($entry['balance'], 2), $timeline),   
                        'borderColor' => '#2196F3',                                         
                        'fill' => true,                                                    
                        'backgroundColor' => 'rgba(33, 150, 243, 0.2)',
                    ],
                ],
                'labels' => array_map(fn ($entry) => "Month {$entry['month']}", $timeline),
            ];
        }
        // For 36+ months, plot points yearly.
        else {
            $yearlyData = [];
            foreach ($timeline as $entry) {
                $year = ceil($entry['month'] / 12);
                $yearlyData[$year] = round($entry['balance'], 2);
            }
            return [
                'datasets' => [
                    [
                        'label' => 'Remaining Balance',                                     
                        'data' => array_values($yearlyData),
                        'borderColor' => '#2196F3',                                         
                        'fill' => true,                                                    
                        'backgroundColor' => 'rgba(33, 150, 243, 0.2)',
                    ],
                ],
                'labels' => array_map(fn($year) => "Year {$year}", array_keys($yearlyData)),
            ];
        }
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'font' => [
                            'size' => 12,
                        ],
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'ticks' => [
                        'maxRotation' => 45,
                        'minRotation' => 45,
                        'font' => [
                            'size' => 10,
                        ],
                    ],
                ],
                'y' => [
                    'ticks' => [
                        'font' => [
                            'size' => 10,
                        ],
                    ],
                ],
            ],
        ];
    }
}