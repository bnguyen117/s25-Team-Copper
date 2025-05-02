<!-- 
    Triggered by clicking the 'Chat with AI' button 
    on a SavingsWhatIfReport record. Launches the 
    SavingsWhatIfChatModal component and passes the $report data.
-->
<div>
    @livewire('savings-what-if-chat-modal', ['report' => $report])
</div>