<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;


/**
 * Renders a line chart showing debt repayment trends over time
 */
class DebtRepaymentChart extends ChartWidget
{
    // Chart's title
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
                [   // Dataset for displaying interest paid monthly over time
                    'label' => 'Interest Paid',                                         // Label displayed inthe legend
                    'data' => array_map(fn ($entry) => $entry['interest_paid'], $timeline), // Monthly Interest values
                    'borderColor' => '#FF9800',                                         // Orange line color
                    'fill' => false,                                                    // Do not fill under the line
                ],
            ],
            'labels' => array_map(fn ($entry) => "Month {$entry['month']}", $timeline), // Monthly labels on the x-axis of the chart
        ];
    }

    protected function getType(): string
    {
        return 'line';                                                                  // The chart is of type line
    }
}