<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Create Institution</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-6">
                <form method="POST" action="{{ route('institutions.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Institution Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required placeholder="e.g. BCA, GoPay, DANA" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="type" :value="__('Type')" />
                        <select id="type" name="type" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                            <option value="bank" {{ old('type') == 'bank' ? 'selected' : '' }}>Bank Account</option>
                            <option value="ewallet" {{ old('type') == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                            <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="credit_card" {{ old('type') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                            <option value="savings" {{ old('type') == 'savings' ? 'selected' : '' }}>Savings</option>
                            <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="color" :value="__('Brand Color')" />
                        <input id="color" type="color" name="color" value="{{ old('color', '#6366F1') }}" class="block mt-1 w-16 h-10 rounded border-gray-300 dark:border-gray-700 dark:bg-gray-800 cursor-pointer">
                        <x-input-error :messages="$errors->get('color')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="logo" :value="__('Logo')" />
                        <input id="logo" type="file" name="logo" accept="image/png,image/jpg,image/jpeg,image/svg+xml,image/webp" class="block mt-1 w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gray-100 dark:file:bg-gray-800 file:text-gray-700 dark:file:text-gray-300 hover:file:bg-gray-200 dark:hover:file:bg-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">PNG, JPG, SVG, or WebP. Max 1MB.</p>
                        <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4 mt-6">
                        <x-primary-button>{{ __('Save Institution') }}</x-primary-button>
                        <a href="{{ route('institutions.index') }}" class="text-gray-600 dark:text-gray-400 dark:hover:text-white text-sm">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
