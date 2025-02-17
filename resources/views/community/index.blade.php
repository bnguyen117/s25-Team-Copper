
<!-- Extend app.blade.php Page layout -->
<x-app-layout>

    <!-- Header -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Community') }}
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                <a href="{{ route('groups.create') }}" class="text-blue-500 hover:text-blue-700">Create Group</a> 
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 grid grid-cols-3 gap-6">
                
                <!-- Left Column: Personal Groups -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Your Groups</h3>
                    <input type="text" id="searchGroups" placeholder="Search your groups..." class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white">
                    <ul id="personalGroupsList" class="space-y-2">
                        @foreach($userGroups as $group)
                            <li class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                <a href="{{ route('groups.show', $group->id) }}" class="text-blue-500">{{ $group->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Middle Column: Public Groups -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Public Groups</h3>
                    <input type="text" id="searchPublicGroups" placeholder="Search public groups..." class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white">
                    <ul id="publicGroupsList" class="space-y-2">
                        @foreach($publicGroups as $group)
                            <li class="p-3 bg-gray-100 dark:bg-gray-700 rounded-lg flex justify-between">
                                <a href="{{ route('groups.show', $group->id) }}" class="text-blue-500">{{ $group->name }}</a>
                                <span class="text-gray-600 dark:text-gray-300">{{ $group->members_count }} members</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Right Column: Friends List -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Friends</h3>
                    <input type="text" id="searchFriends" placeholder="Search friends..." class="w-full px-4 py-2 border rounded-lg dark:bg-gray-700 dark:text-white">
                    <ul id="friendsList" class="space-y-2">
                        @foreach($friends as $friend)
                            <li class="flex items-center space-x-4 p-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                <img src="{{ asset('storage/' . $friend->avatar) }}" class="w-10 h-10 rounded-full" alt="Avatar">
                                <span class="text-gray-900 dark:text-gray-100">{{ $friend->display_name }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
    </div>

</x-app-layout>