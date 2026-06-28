<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Internal Transfer</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-6">
                <form method="POST" action="{{ route('transfers.store') }}">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="from_account_id" :value="__('From Account')" />
                        <select id="from_account_id" name="from_account_id" class="input-field mt-1" required>
                            <option value="">Select source account...</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('from_account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }} ({{ number_format($account->current_balance, 2) }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('from_account_id')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="to_account_id" :value="__('To Account')" />
                        <select id="to_account_id" name="to_account_id" class="input-field mt-1" required>
                            <option value="">Select destination account...</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('to_account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }} ({{ number_format($account->current_balance, 2) }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('to_account_id')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="amount" :value="__('Amount')" />
                        <x-text-input id="amount" class="input-field mt-1" type="number" step="0.01" min="0.01" name="amount" :value="old('amount')" required placeholder="0.00" />
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="description" :value="__('Description (optional)')" />
                        <x-text-input id="description" class="input-field mt-1" type="text" name="description" :value="old('description')" placeholder="e.g. Savings transfer" />
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="date" :value="__('Date')" />
                        <x-text-input id="date" class="input-field mt-1" type="date" name="date" :value="old('date', now()->format('Y-m-d'))" />
                        <x-input-error :messages="$errors->get('date')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4 mt-6">
                        <button type="submit" class="btn-primary">
                            {{ __('Transfer') }}
                        </button>
                        <a href="{{ route('transactions.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white text-sm">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
