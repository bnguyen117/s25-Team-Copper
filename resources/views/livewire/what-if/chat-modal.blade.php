<!-- 
    Triggered by clicking the 'Chat with AI' button 
    on a WhatIfReport record. Launches the 
    WhatIfChatModal component and passes the $report data.
-->
<div>
    @livewire('what-if-chat-modal', ['report' => $report])
</div>