<!-- The base HTML view that defines the overall page structure of the app -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="flex flex-col min-h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Navbar -->
        @include('layouts.navigation', ['user' => Auth::user()])

        <!-- Header -->
        @isset($header)
        <header class="bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endisset

        <!-- Main content -->
        <main class="pb-24">
            {{ $slot }}
        </main>

        <!-- Footer -->
        @include('layouts.footer')
    </div>

    <!-- Scripts -->
    @filamentScripts
    @livewire('notifications')

        <!-- Script to remove friend requests from the dropdown -->
        <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".accept-request, .decline-request").forEach(button => {
                button.addEventListener("submit", function (e) {
                    e.preventDefault();
                
                    let requestId = this.dataset.requestId;
                    let requestRow = document.querySelector(".request-" + requestId);

                    fetch(this.action, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: new FormData(this)
                    }).then(response => {
                        if (response.ok) {
                            requestRow.remove(); // Remove request from dropdown
                            updateFriendRequestCount();
                        }
                    }).catch(error => console.error("Error:", error));
                });
            });

            function updateFriendRequestCount() {
                let requestList = document.getElementById("friendRequestsList");
                let countSpan = document.getElementById("friendRequestsCount");

                // Count remaining requests
                let remainingRequests = requestList.querySelectorAll(".request-").length;

                if (remainingRequests > 0) {
                    countSpan.textContent = remainingRequests; // Update count
                } else {
                    countSpan.remove(); // Remove notification badge if 0
                }
            }
        });
    </script>

</body>

</html>