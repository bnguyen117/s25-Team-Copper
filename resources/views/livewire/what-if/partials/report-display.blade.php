

<!-- 
    Renders the:
    1. WhatIfReport header and grid summary.
    2. timeline table displaying remaining balance, and interest paid monthly.
    3. A Filament chart displaying remaining balance, and interest paid monthly.
-->

<!-- Include report header and record grid summary based on the user's chosen $report->algorithm -->
@include('livewire.what-if.scenarios.'.$report->algorithm)

<!-- Include timeline table -->
@include('livewire.what-if.partials.timeline-table')

<!-- Chart displaying repayment trends over time -->
<div class="mt-4">
    <div class="chart-wrapper">
        @livewire(\App\Filament\Widgets\DebtRepaymentChart::class, ['chartData' => $report])
        <style>
            .chart-wrapper canvas { height: 50vh !important; }
        </style>
    </div>
</div>