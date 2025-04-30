<!-- 
    Displays the slide-over modal content for a SavingsWhatIfReport.
    Triggered by the 'View Report' button on the SavingsWhatIfReport table.
-->

<div class="p-4">

    <!-- Includes the scenario specific header and summary cards from $report->what_if_scenario -->
    @include('livewire.what-if.scenarios.'.$report->what_if_scenario)

    <!-- Includes the timeline table with monthly repayment details -->
    @include('livewire.what-if.partials.savings-timeline-table')

    <!-- Container for the Filament chart -->
    <div class="mt-4">
            @livewire(\App\Filament\Widgets\SavingsChart::class, ['chartData' => $report])
            <style>canvas { height: 50vh !important; }</style>
    </div>

    <!-- Include the goal impact section if a goal was provided -->
    @if($report->goal_impact)
        @include('livewire.what-if.partials.goal-impact')
    @endif
</div>