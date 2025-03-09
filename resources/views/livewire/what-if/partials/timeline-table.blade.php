
<!--
    A view that holds the design for a timeline table that
    that displays Remaining balance and interest paid monthly
    over a debt repayment period.
-->

<!-- Timeline Table -->
<div class="mb-6">

    <!-- Table Header -->
    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Repayment Timeline</h3>
  
    <!-- Table wrapper -->
    <div class="max-h-64 overflow-y-auto rounded-lg border border-gray-200 dark:border-gray-700">

      <!-- The table -->
      <table class="w-full text-sm text-gray-700 dark:text-gray-300">

          <!-- The Header Row of the Table -->
          <thead class="sticky top-0 bg-gray-200 dark:bg-gray-800 text-gray-900 dark:text-gray-100">
              <tr class="hidden md:table-row">
                  <th class="px-4 py-2 text-left">Month</th>
                  <th class="px-4 py-2 text-right">Remaining Balance</th>
                  <th class="px-4 py-2 text-right">Principal Paid</th>
                  <th class="px-4 py-2 text-right">Interest Paid</th>
              </tr>
          </thead>
          
          <!-- Iterates over each entry in the timline array -->
          <tbody>
              @foreach($report->timeline as $entry)
              <tr class="md:table-row block md:border-b hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="md:table-cell block px-2 py-1 border-t md:border-t-0">
                    <span class="md:hidden font-semibold">Month: </span>{{ $entry['month'] }}
                </td>
                <td class="md:table-cell block px-2 py-1 text-right md:text-right">
                    <span class="md:hidden font-semibold">Balance: </span>${{ number_format($entry['balance'], 2) }}
                </td>
                <td class="md:table-cell block px-2 py-1 text-right md:text-right">
                    <span class="md:hidden font-semibold">Principal Paid: </span>${{ number_format($entry['principal_paid'], 2) }}
                </td>
                <td class="md:table-cell block px-2 py-1 text-right md:text-right">
                    <span class="md:hidden font-semibold">Interest Paid: </span>${{ number_format($entry['interest_paid'], 2) }}
                </td>
            </tr>
              @endforeach
          </tbody>
      </table>
    </div>
</div>