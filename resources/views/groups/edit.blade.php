<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Group') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="POST" action="{{ route('groups.update', $group->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div>
                    <x-input-label for="name" :value="__('Group Name')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ $group->name }}" required />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="description" :value="__('Description')" />
                    <x-text-input id="description" name="description" type="text" class="mt-1 block w-full" value="{{ $group->description }}" required />
                    <x-input-error class="mt-2" :messages="$errors->get('description')" />
                </div>

                <div>
                    <x-input-label for="image" :value="__('Group Image')" />
                    <input type="file" id="image" name="image" class="mt-1 block w-full">
                    <x-input-error class="mt-2" :messages="$errors->get('image')" />
                </div>

                <div>
                    <x-input-label for="private" :value="__('Private')" />
                    <input type="checkbox" id="private" name="private" class="mt-1" {{ $group->is_private ? 'checked' : '' }}>
                    <x-input-error class="mt-2" :messages="$errors->get('private')" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Update') }}</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
