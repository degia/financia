<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Edit Institution</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-6">
                <form method="POST" action="{{ route('institutions.update', $institution) }}">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Institution Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $institution->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="type" :value="__('Type')" />
                        <select id="type" name="type" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                            <option value="bank" {{ old('type', $institution->type) == 'bank' ? 'selected' : '' }}>Bank Account</option>
                            <option value="ewallet" {{ old('type', $institution->type) == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                            <option value="cash" {{ old('type', $institution->type) == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="credit_card" {{ old('type', $institution->type) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                            <option value="savings" {{ old('type', $institution->type) == 'savings' ? 'selected' : '' }}>Savings</option>
                            <option value="other" {{ old('type', $institution->type) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="color" :value="__('Brand Color')" />
                        <input id="color" type="color" name="color" value="{{ old('color', $institution->color ?? '#6366F1') }}" class="block mt-1 w-16 h-10 rounded border-gray-300 dark:border-gray-700 dark:bg-gray-800 cursor-pointer">
                        <x-input-error :messages="$errors->get('color')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 shadow-sm focus:ring-gray-500 dark:focus:ring-gray-400" {{ old('is_active', $institution->is_active) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
                        </label>
                    </div>

                    <div class="flex items-center gap-4 mt-6">
                        <x-primary-button>{{ __('Update Institution') }}</x-primary-button>
                        <a href="{{ route('institutions.index') }}" class="text-gray-600 dark:text-gray-400 dark:hover:text-white text-sm">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
