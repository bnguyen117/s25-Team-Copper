
<!-- 
    Displays the slide-over modal content for a WhatIfReport.
    Triggered by the 'View Report' button on the WhatIfReport table.
-->

<div class="p-4">

    <!-- Include the report-display partial and pass the WhatIfReport details as 'report' -->
    @include('livewire.what-if.partials.report-display', ['report' => $report])

</div>