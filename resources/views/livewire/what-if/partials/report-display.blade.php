

<!-- Renders the core what-if report: summary grid, timeline table, and chart -->
@include('livewire.what-if.scenarios.'.$result['algorithm'])

<!-- Include timeline table -->
@include('livewire.what-if.partials.timeline-table')

<!-- Chart displaying repayment trends over time -->
<div class="mt-4">
    @livewire(\App\Filament\Widgets\DebtRepaymentChart::class, ['chartData' => $result])
</div>