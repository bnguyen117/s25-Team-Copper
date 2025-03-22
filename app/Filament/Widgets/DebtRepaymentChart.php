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
        // If the `$chartData` is null or has no timeline key
        if (!$this->chartData || !isset($this->chartData['timeline'])) {
            return [ // Return an empty chart
                'datasets' => [],
                'labels' => [],
            ];
        }

        // Else, extract the `$chartData` into `$timeline`
        $timeline = $this->chartData['timeline'];

        return [
            'datasets' => [
                [   // Dataset for displaying remaining balance monthly over time
                    'label' => 'Remaining Balance',                                     // Label displayed in the legend
                    'data' => array_map(fn ($entry) => $entry['balance'], $timeline),   // Monthly balance values
                    'borderColor' => '#2196F3',                                         // Blue line color
                    'fill' => false,                                                    // Do not fill under the line
                ],
            ],
            'labels' => array_map(fn ($entry) => "Month {$entry['month']}", $timeline), // Monthly labels on the x-axis of the chart
        ];
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