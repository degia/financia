<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Currency Preference -->
        <div class="mt-4">
            <x-input-label for="currency_preference" :value="__('Currency')" />
            <select id="currency_preference" name="currency_preference" class="block mt-1 w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                <option value="USD" {{ old('currency_preference') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                <option value="IDR" {{ old('currency_preference') == 'IDR' ? 'selected' : '' }}>IDR - Indonesian Rupiah</option>
                <option value="EUR" {{ old('currency_preference') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                <option value="SGD" {{ old('currency_preference') == 'SGD' ? 'selected' : '' }}>SGD - Singapore Dollar</option>
                <option value="MYR" {{ old('currency_preference') == 'MYR' ? 'selected' : '' }}>MYR - Malaysian Ringgit</option>
            </select>
            <x-input-error :messages="$errors->get('currency_preference')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-gray-400" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
