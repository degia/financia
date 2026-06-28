<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Edit Loan</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-6">
                <form method="POST" action="{{ route('loans.update', $loan) }}">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Loan Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $loan->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <x-input-label for="type" :value="__('Type')" />
                            <select id="type" name="type" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                                <option value="borrow" {{ old('type', $loan->type) == 'borrow' ? 'selected' : '' }}>I Borrow (I owe money)</option>
                                <option value="lend" {{ old('type', $loan->type) == 'lend' ? 'selected' : '' }}>I Lend (Someone owes me)</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="lender_name" :value="__('Lender / Borrower Name')" />
                            <x-text-input id="lender_name" class="block mt-1 w-full" type="text" name="lender_name" :value="old('lender_name', $loan->lender_name)" />
                            <x-input-error :messages="$errors->get('lender_name')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <x-input-label for="amount" :value="__('Amount')" />
                            <x-text-input id="amount" class="block mt-1 w-full" type="number" step="0.01" min="0" name="amount" :value="old('amount', $loan->amount)" required />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Paid: {{ number_format($loan->paid_amount, 2) }} | Remaining: {{ number_format($loan->remaining_amount, 2) }}</p>
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="interest_rate" :value="__('Interest Rate (%)')" />
                            <x-text-input id="interest_rate" class="block mt-1 w-full" type="number" step="0.01" min="0" max="100" name="interest_rate" :value="old('interest_rate', $loan->interest_rate)" />
                            <x-input-error :messages="$errors->get('interest_rate')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <x-input-label for="account_id" :value="__('Linked Account')" />
                            <select id="account_id" name="account_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                                <option value="">-- No account --</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('account_id', $loan->account_id) == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('account_id')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                                <option value="active" {{ old('status', $loan->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ old('status', $loan->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="defaulted" {{ old('status', $loan->status) == 'defaulted' ? 'selected' : '' }}>Defaulted</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="mb-4">
                            <x-input-label for="start_date" :value="__('Start Date')" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="old('start_date', $loan->start_date->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="due_date" :value="__('Due Date')" />
                            <x-text-input id="due_date" class="block mt-1 w-full" type="date" name="due_date" :value="old('due_date', $loan->due_date?->format('Y-m-d'))" />
                            <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="notes" :value="__('Notes')" />
                        <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">{{ old('notes', $loan->notes) }}</textarea>
                        <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4 mt-6">
                        <x-primary-button>{{ __('Update Loan') }}</x-primary-button>
                        <a href="{{ route('loans.index') }}" class="text-gray-600 dark:text-gray-400 dark:hover:text-white text-sm">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
