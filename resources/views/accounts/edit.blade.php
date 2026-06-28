<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Edit Account</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm dark:shadow-none border border-gray-200 dark:border-gray-800 p-6">
                <form method="POST" action="{{ route('accounts.update', $account) }}">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Account Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $account->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="type" :value="__('Account Type')" />
                        <select id="type" name="type" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                            <option value="cash" {{ old('type', $account->type) == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank" {{ old('type', $account->type) == 'bank' ? 'selected' : '' }}>Bank Account</option>
                            <option value="ewallet" {{ old('type', $account->type) == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                            <option value="credit_card" {{ old('type', $account->type) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="currency" :value="__('Currency')" />
                        <select id="currency" name="currency" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                            <option value="USD" {{ old('currency', $account->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="IDR" {{ old('currency', $account->currency) == 'IDR' ? 'selected' : '' }}>IDR</option>
                            <option value="EUR" {{ old('currency', $account->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                            <option value="SGD" {{ old('currency', $account->currency) == 'SGD' ? 'selected' : '' }}>SGD</option>
                            <option value="MYR" {{ old('currency', $account->currency) == 'MYR' ? 'selected' : '' }}>MYR</option>
                        </select>
                        <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="color" :value="__('Color')" />
                        <input id="color" type="color" name="color" value="{{ old('color', $account->color ?? '#6366F1') }}" class="block mt-1 w-16 h-10 rounded border-gray-300 dark:border-gray-700 dark:bg-gray-800 cursor-pointer">
                        <x-input-error :messages="$errors->get('color')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4 mt-6">
                        <x-primary-button>{{ __('Update Account') }}</x-primary-button>
                        <a href="{{ route('accounts.index') }}" class="text-gray-600 dark:text-gray-400 dark:hover:text-white text-sm">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
