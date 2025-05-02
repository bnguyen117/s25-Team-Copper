<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Group') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <section>
                <header>
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        {{ __('Group Information') }}
                    </h2>
                    
                    <form method = "post" action = "{{ route('groups.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                        @csrf
                        <div> 
                            <!--Group Name-->
                            <x-input-label for="name" :value="__('Group Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div> 
           
                        <!--Group Description-->
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <x-text-input id="description" name="description" type="text" class="mt-1 block w-full" required />
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>
                        <!--Invite Group Members-->
                        <div>
                            <x-input-label for="members" :value="__('Invite Members (IDs/Email)')" />
                            <textarea id="members-search" name="members" rows="2" class="w-half mt-1 p-2 rounded border dark:bg-gray-800 dark:text-white" placeholder="e.g. 2, 2, user@email.com"></textarea>
                            <div id="suggestions" class="absolute w-full bg-white dark:bg-gray-800 border rounded mt-1 z-10 hidden shadow-lg max-h-40 overflow-y-auto"></div>
                            <x-input-error class="mt-2" :messages="$errors->get('members')" />
                        </div>
            
                        <!--Group Image-->
                        <div>
                            <x-input-label for="image" :value="__('Image')" />
                            <x-text-input id="image" name="image" type="file" class="mt-1 block w-full" />
                            <x-input-error class="mt-2" :messages="$errors->get('image')" />
                        </div>
            
                        <!--Private Button-->
                        <div>
                            <x-input-label for="private" :value="__('Private')" />
                            <input type="checkbox" id="private" name="private" class="mt-1">
                            <x-input-error class="mt-2" :messages="$errors->get('private')" />
                        </div>
                        
                        <!--Save Button-->
                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save') }}</x-primary-button>
                        </div>
                    </form>
                </header>
            </section>
        </div>
    </div>
</x-app-layout>