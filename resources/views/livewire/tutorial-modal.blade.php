<div>
    @if ($isOpen)
        <!-- Container to center the content window and apply background opacity. -->
        <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50" 
            wire:keydown.escape="close"
        >

            <!-- The content window -->
            <div  class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6 relative">

                <!-- Close button in the top right of the content window -->
                <button 
                    wire:click="close"
                    class="absolute top-3 right-3 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 focus:outline-none"
                >
                    <!-- X icon from Heroicons -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>

                <!-- Title for the content window -->
                <div class="text-xl text-center font-bold text-gray-900 dark:text-gray-100 mb-2">

                    <!-- Flipped cash icon from Heroicons -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-500 inline-block align-middle mr-1 scale-x-[-1]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                    </svg>

                    Welcome to CreditTrax! 
                    
                    <!-- Cash icon from Heroicons -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-500 inline-block align-middle ml-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                    </svg>
                </div>

                <!-- Step counter -->
                <div class="text-sm text-center text-gray-500 dark:text-gray-400 mb-4">
                    Step {{ $step }} of {{ $totalSteps }}
                </div>

                <!-- Displays the content window's body text based on the current step -->
                <div class="text-gray-700 dark:text-gray-300  mb-6 text-base leading-relaxed">

                    <!-- Step 1: Introduction -->
                    @if ($step === 1) 
                    <p class="text-center"> 
                        Let's get started with a quick overview! 
                        <!-- Rocket icon from Heroicons -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-blue-500 inline-block align-middle ml-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.493 4.493 0 0 0 4.306-1.758M16.5 9a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z" />
                        </svg>
                    </p>


                    <!-- Step 2: Finance -->
                    @elseif ($step === 2)
                        <!-- Intro paragraph -->
                        <p class="mb-2">
                            A great place to start is the
                            <!-- Dollar sign icon from Heroicons -->
                            <strong class="text-blue-600 dark:text-blue-400">Finance</strong><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline-block align-middle ml-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg> 
                            page.
                        </p>
                        <!-- Paragraph 2 -->
                        <p class="mb-2">
                            From here, you can create a monthly 
                            <strong class="text-teal-600 dark:text-teal-400">Budget</strong> 
                            and start tracking your financial 
                            <strong class="text-green-600 dark:text-green-400">Goals</strong> 
                            and 
                            <strong class="text-red-600 dark:text-red-400">Debts</strong>!
                        </p>


                    <!-- Step 3: What-If Analysis -->
                    @elseif ($step === 3)
                        <!-- Intro paragraph -->
                        <p class="mb-2">
                            Next, head over to 
                            <strong class="text-blue-600 dark:text-blue-400">What-If Analysis</strong>
                            <!-- Chart icon from Heroicons -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline-block align-middle ml-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                            </svg> 
                            page.
                        </p>
                        <!-- Paragraph 2 -->
                        <p class="mb-2">
                            From here, you can generate what-if scenario reports on your 
                            <strong class="text-red-600 dark:text-red-400">Debts</strong> 
                            and 
                            <strong class="text-green-600 dark:text-green-400">Goals</strong>! 
                        </p>
                        <!-- Paragraph 3 -->
                        <p class="mb-2">
                            Giving you the insight to make the financial choices that are right for 
                            <!-- Right curved arrow from Heroicons -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline-block align-middle ml-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m15 15 6-6m0 0-6-6m6 6H9a6 6 0 0 0 0 12h3" />
                            </svg>
                            <strong>You</strong>
                            <!-- Left curved arrow from Heroicons -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline-block align-middle ml-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                            </svg>
                        </p>


                    <!-- Step 4: Dashboard -->
                    @elseif ($step === 4)
                        <!-- Intro paragraph -->
                        <p class="mb-2">
                            Next, check out your  
                            <strong class="text-blue-600 dark:text-blue-400">Dashboard</strong>
                            <!-- House icon from Heroicons -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline-block align-middle ml-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                        </p>
                        <!-- Paragraph 2 -->
                        <p class="mb-2">
                            From here, you'll see a summary of your current financial situation dynamically represented through graphs and charts
                            <!-- Chart icon from Heroicons -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-yellow-600 dark:text-yellow-500 inline-block align-middle ml-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
                            </svg>
                        </p>


                    <!-- Step 5: Rewards -->
                    @elseif ($step === 5)
                        <!-- Intro pragraph -->
                        <p class="mb-2">
                            Next, move over to your  
                            <strong class="text-blue-600 dark:text-blue-400">Rewards</strong>
                            <!-- Trophy icon from Heroicons -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline-block align-middle ml-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                            </svg>
                            page.
                        </p>
                        <!-- Paragraph 2 -->
                        <p class="mb-2">
                            From here, you'll see the badges and rewards you've earned by achieving your <strong class="text-green-600 dark:text-green-400">Goals</strong> and paying off <strong class="text-red-600 dark:text-red-400">Debts</strong>! 
                        </p>


                    <!-- Step 6: Community -->
                    @elseif ($step === 6)
                        <!-- Intro paragraph -->
                        <p class="mb-2">
                            Finally, visit the 
                            <strong class="text-blue-600 dark:text-blue-400">Community</strong> 
                            <!-- Users icon from Herocions -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 inline-block align-middle ml-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                            page.
                        </p>
                        <!-- Paragraph 2 -->
                        <p class="mb-2">
                            Where you will find the CreditTrax community! Join groups and make friends to accomplish 
                            <strong class="text-green-600 dark:text-green-400">Goals</strong> 
                            and talk strategy.
                        </p>
                    @endif
                </div>
                
                <!-- FOOTER -->
                <div class="flex justify-end space-x-3">
                    <!-- Previous step button; disabled on first step -->
                    <button
                        wire:click="previousStep"
                        @if($step === 1) disabled @endif
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                    >
                    Previous
                    </button>

                    <!-- Next step button; displays up to the final step -->
                    @if ($step < $totalSteps)
                        <button
                            wire:click="nextStep"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
                        >
                        Next
                        </button>

                    <!-- Finish button; shown only on the final step -->
                    @else
                        <button
                            wire:click="close"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
                        >
                            Finish
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

