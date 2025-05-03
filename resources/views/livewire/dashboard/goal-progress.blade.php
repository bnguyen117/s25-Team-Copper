<!-- Your Goal Progress -->
<div class="w-full md:w-1/2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 text-center">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 border-b pb-2">Your Goal Progress</h3>

    <!-- Display Goal Name Above the Slider -->
    <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mt-4 mb-2">
        {{ $goalName }} Goal Progress<!-- Display goal name dynamically -->
    </h4>

    <!-- Slider with Dynamic Percentage Display -->
    <div x-data="{ progress: {{ $goalProgress }} }" class="mt-4 flex flex-col items-center w-full">
        <div class="relative w-1/2">
            <div class="absolute w-full h-3 rounded-lg bg-gray-300"></div>
            <div class="absolute h-3 rounded-lg" 
                :class="progress <= 30 ? 'bg-red-600' : (progress <= 70 ? 'bg-yellow-500' : 'bg-green-600')" 
                :style="'width: ' + progress + '%'">
            </div>

            <!-- Read-only Slider -->
            <input type="range" min="0" max="100" x-model="progress"
            class="relative w-full h-3 bg-transparent appearance-none cursor-not-allowed" disabled>
        </div>
        <span class="mt-2 font-medium" 
            :class="progress <= 30 ? 'text-red-600' : (progress <= 70 ? 'text-yellow-500' : 'text-green-600')">
            <span x-text="progress"></span>% of your goal achieved
        </span>
    </div>

    <a href="{{ route('rewards') }}#goals"
    class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg block text-center">Edit Goals</a> 
</div>