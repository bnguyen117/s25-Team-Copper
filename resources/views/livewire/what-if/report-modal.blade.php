
<!-- 
    Renders the content of the slide-over modal displayed when clicking
    the 'View Report' Button on a WhatIfReport record.
-->
<div class="p-4">
    @include('livewire.what-if.partials.report-display', ['report' => $report])
</div>