<!-- 
    Displays the WhatIfReport summary, timeline table, and Filament chart
    based on the selected scenario.
-->

<!-- Includes the scenario specific header and summary cards from $report->what_if_scenario -->
@include('livewire.what-if.scenarios.'.$report->what_if_scenario)

<!-- Includes the timeline table with monthly repayment details -->
@include('livewire.what-if.partials.timeline-table')

<!-- Container for the Filament chart -->
<div class="mt-4">

        <!-- Filament chart showing debt repayment trends over time with $report data passed as 'chartData' -->
        @livewire(\App\Filament\Widgets\DebtRepaymentChart::class, ['chartData' => $report])

        <!-- Set the charts height to half of the viewing screen -->
        <style>canvas { height: 50vh !important; }</style>

</div>