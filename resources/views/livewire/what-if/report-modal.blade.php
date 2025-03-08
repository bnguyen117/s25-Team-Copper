<div class="p-4">
    <!-- Include algorithm-specific report header and summary grid -->
    @include('livewire.what-if.scenarios.' . $result['algorithm'])

    <!-- Include shared timeline table -->
    @include('livewire.what-if.partials.timeline-table')

    <!-- Chart displaying repayment trends over time -->
    <div class="mt-4">
        @livewire(\App\Filament\Widgets\DebtRepaymentChart::class, ['chartData' => $result])
    </div>
</div>