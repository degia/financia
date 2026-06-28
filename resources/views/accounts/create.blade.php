<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Create Account</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm dark:shadow-none border border-gray-200 dark:border-gray-800 p-6">
                <form method="POST" action="{{ route('accounts.store') }}">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Account Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required placeholder="e.g. Bank Mandiri" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="type" :value="__('Account Type')" />
                        <select id="type" name="type" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                            <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank" {{ old('type') == 'bank' ? 'selected' : '' }}>Bank Account</option>
                            <option value="ewallet" {{ old('type') == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                            <option value="credit_card" {{ old('type') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="initial_balance" :value="__('Initial Balance')" />
                        <x-text-input id="initial_balance" class="block mt-1 w-full" type="number" step="0.01" name="initial_balance" :value="old('initial_balance', '0')" />
                        <x-input-error :messages="$errors->get('initial_balance')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="currency" :value="__('Currency')" />
                        <select id="currency" name="currency" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                            <option value="USD" {{ old('currency', Auth::user()->currency_preference) == 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="IDR" {{ old('currency', Auth::user()->currency_preference) == 'IDR' ? 'selected' : '' }}>IDR</option>
                            <option value="EUR" {{ old('currency', Auth::user()->currency_preference) == 'EUR' ? 'selected' : '' }}>EUR</option>
                            <option value="SGD" {{ old('currency', Auth::user()->currency_preference) == 'SGD' ? 'selected' : '' }}>SGD</option>
                            <option value="MYR" {{ old('currency', Auth::user()->currency_preference) == 'MYR' ? 'selected' : '' }}>MYR</option>
                        </select>
                        <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="color" :value="__('Color')" />
                        <input id="color" type="color" name="color" value="{{ old('color', '#6366F1') }}" class="block mt-1 w-16 h-10 rounded border-gray-300 dark:border-gray-700 dark:bg-gray-800 cursor-pointer">
                        <x-input-error :messages="$errors->get('color')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4 mt-6">
                        <x-primary-button>{{ __('Save Account') }}</x-primary-button>
                        <a href="{{ route('accounts.index') }}" class="text-gray-600 dark:text-gray-400 dark:hover:text-white text-sm">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
