<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            {{ Auth::user()->name }}'s Dashboard
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto mt-8 space-y-6">
        <!-- Income and Budgeting Chart, and Debt Breakdown Chart Side by Side -->
        <div class="flex flex-col md:flex-row justify-between space-y-4 md:space-y-0 md:space-x-4">
            @include('livewire.dashboard.income-budgeting-chart')

            @include('livewire.dashboard.debt-donut-chart')
        </div>

        <!-- Debt Stats Bar Chart -->
         @include('livewire.dashboard.debt-stats-bar-chart')
    </div>

    <!-- Rewards & Inspiration and Goal Progress Side by Side -->
    <div class="flex flex-col md:flex-row justify-between space-y-4 md:space-y-0 md:space-x-4">
        @include('livewire.dashboard.rewards-inspiration')

        @include('livewire.dashboard.goal-progress')
    </div>

    <div>
        @include('livewire.dashboard.debt-payment-history-chart')
    </div>
    
</x-app-layout>
