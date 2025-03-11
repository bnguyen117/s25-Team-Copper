
<!-- Section displaying the repayment timeline table -->
<div class="mb-6">
    <!-- Header for the timeline table -->
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Repayment Timeline</h3>
  
    <!-- Container for the timeline table -->
    <div class="max-h-72 overflow-y-auto bg-gradient-to-br from-gray-200 to-gray-100 dark:from-gray-900 dark:to-gray-700 shadow-xl rounded-xl p-4 md:p-0">

      <!-- Defines the table -->
      <table class="w-full text-sm font-medium text-gray-800 dark:text-gray-200">

          <!-- Table header with sticky column labels -->
          <thead class="sticky top-0 bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 text-gray-900 dark:text-gray-100 shadow-md">

              <!--Desktop-only header row with column labels -->  
              <tr class="hidden md:table-row">
                  <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Month</th>
                  <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Principal Paid</th>
                  <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Interest Paid</th>
                  <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Remaining Balance</th>
              </tr>
          </thead>
          
          <!-- Table's body with timeline entries -->
          <tbody>

            <!-- Iterates over $report->timeline entries -->
            @foreach($report->timeline as $entry)

                <!-- Defines a row: block on Mobile, table row on Desktop -->
                <tr class="md:table-row block transition-colors hover:bg-gradient-to-r hover:from-gray-100 hover:to-gray-50 dark:hover:from-gray-800 dark:hover:to-gray-700">

                  <!-- Mobile-only cell that stacks label-value pairs vertically -->
                    <td class="block md:hidden px-3 py-2 bg-gradient-to-tl from-gray-200 to-gray-50 dark:from-gray-900 dark:to-gray-700 rounded-lg shadow-md mb-4">

                        <!-- Container for mobile label-value layout -->
                        <div class="flex flex-col gap-2">

                            <!-- Month label and value -->
                            <div class="flex justify-between">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Month:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $entry['month'] }}</span>
                            </div>

                            <!-- Principal Paid label and value-->
                            <div class="flex justify-between">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Principal Paid:</span>
                                <span class="text-gray-900 dark:text-gray-100">${{ number_format($entry['principal_paid'], 2) }}</span>
                            </div>

                            <!-- Interest Paid label and value-->
                            <div class="flex justify-between">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Interest Paid:</span>
                                <span class="text-gray-900 dark:text-gray-100">${{ number_format($entry['interest_paid'], 2) }}</span>
                            </div>

                            <!-- Remaining Balance label and value -->
                            <div class="flex justify-between">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Remaining Balance:</span>
                                <span class="text-gray-900 dark:text-gray-100">${{ number_format($entry['balance'], 2) }}</span>
                            </div>
                            
                        </div>
                    </td>

                  <!-- Desktop-only cells for timeline data -->
                  <td class="hidden md:table-cell px-4 py-2 text-left text-gray-900 dark:text-gray-100">
                      {{ $entry['month'] }}
                  </td>  
                  <td class="hidden md:table-cell px-4 py-2 text-right text-gray-900 dark:text-gray-100">
                      ${{ number_format($entry['principal_paid'], 2) }}
                  </td>
                  <td class="hidden md:table-cell px-4 py-2 text-right text-gray-900 dark:text-gray-100">
                      ${{ number_format($entry['interest_paid'], 2) }}
                  </td>
                  <td class="hidden md:table-cell px-4 py-2 text-right text-gray-900 dark:text-gray-100">
                      ${{ number_format($entry['balance'], 2) }}
                  </td>
              </tr>
              @endforeach
          </tbody>
      </table>
    </div>
</div>