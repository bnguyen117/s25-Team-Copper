<!-- Timeline Table -->
<div class="mb-6">
    <!-- Header above the table -->
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Repayment Timeline</h3>
  
    <!-- Defines the container that will hold the table -->
    <div class="max-h-72 overflow-y-auto rounded-xl shadow-sm bg-white dark:bg-gray-900">

      <!-- Defines the tables structure within its container -->
      <table class="w-full text-sm font-medium text-gray-800 dark:text-gray-200">

          <!-- Defines the container for the Header row of the Table that displays the column labels -->
          <thead class="sticky top-0 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100">

              <!--Header row that is hidden on devices less than 768px wide -->  
              <tr class="hidden md:table-row">
                  <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-300">Month</th>
                  <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Principal Paid</th>
                  <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Interest Paid</th>
                  <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-300">Remaining Balance</th>
              </tr>
          </thead>
          
          <!-- Defines the content rows of the table's body -->
          <tbody>

            <!-- for every entry in the timeline array within report -->
            @foreach($report->timeline as $entry)

                <!-- Defines a table row, block on Mobile, row on Desktop -->
                <tr class="md:table-row block transition-colors hover:bg-gray-50 dark:hover:bg-gray-600">

                  <!-- 
                        Mobile: Single cell stacking all label-value pairs vertically with labels left and values right.
                        Desktop: Hidden
                   -->
                    <td class="block md:hidden px-3 py-2 border-t border-gray-100 dark:border-gray-800">

                        <!-- 
                            Mobile layout for all of the data within this single cell.
                            Hidden on Desktop.
                        -->
                        <div class="flex flex-col gap-2">

                            <!-- Month: label to the left, month value to the right -->
                            <div class="flex justify-between">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Month:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $entry['month'] }}</span>
                            </div>

                            <!-- Principal Paid: label to the left, principal_paid value to the right -->
                            <div class="flex justify-between">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Principal Paid:</span>
                                <span class="text-gray-900 dark:text-gray-100">${{ number_format($entry['principal_paid'], 2) }}</span>
                            </div>

                            <!-- Interest Paid: label to the left, interest_paid value to the right -->
                            <div class="flex justify-between">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Interest Paid:</span>
                                <span class="text-gray-900 dark:text-gray-100">${{ number_format($entry['interest_paid'], 2) }}</span>
                            </div>

                            <!-- Remaining Balance: label to the left, balance value to the right -->
                            <div class="flex justify-between">
                                <span class="font-semibold text-gray-700 dark:text-gray-300">Remaining Balance:</span>
                                <span class="text-gray-900 dark:text-gray-100">${{ number_format($entry['balance'], 2) }}</span>
                            </div>
                            
                        </div>
                    </td>

                  <!-- Desktop-only cells -->
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