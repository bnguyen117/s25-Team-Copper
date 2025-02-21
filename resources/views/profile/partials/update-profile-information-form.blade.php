<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information, display name, and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Display Name -->
        <div>
            <x-input-label for="display_name" :value="__('Display Name')" />
            <x-text-input id="display_name" name="display_name" type="text" class="mt-1 block w-full"
                :value="old('display_name', $user->display_name)" required />
            <x-input-error class="mt-2" :messages="$errors->get('display_name')" />
        </div>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Birthdate -->
        <div>
            <x-input-label for="birthdate" :value="__('Birthdate')" />
            <x-text-input id="birthdate" name="birthdate" type="date" class="mt-1 block w-full"
                :value="old('birthdate', $user->birthdate)" required />
            <x-input-error class="mt-2" :messages="$errors->get('birthdate')" />
        </div>

        <!-- Avatar Upload -->
        <div>
            <x-input-label for="avatar" :value="__('Avatar')" />
            <input id="avatar" name="avatar" type="file" class="mt-1 block w-full" />
            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />

            @if ($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-20 h-20 rounded-full mt-2">
            @endif
        </div>

        <!-- Joined Date (Non-Editable) -->
        <div>
            <x-input-label :value="__('Joined On')" />
            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $user->created_at->format('F d, Y') }}</p>
        </div>

        <!-- Save Button -->
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>