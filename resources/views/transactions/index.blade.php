<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Transactions</h2>
            <a href="{{ route('transactions.create') }}" class="btn-primary">
                + Add Transaction
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Filters --}}
            <div class="card p-4 mb-6">
                <form method="GET" action="{{ route('transactions.index') }}" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <x-input-label for="start_date" :value="__('From')" />
                        <x-text-input id="start_date" type="date" name="start_date" :value="request('start_date')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="end_date" :value="__('To')" />
                        <x-text-input id="end_date" type="date" name="end_date" :value="request('end_date')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="type" :value="__('Type')" />
                        <select id="type" name="type" class="mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                            <option value="">All</option>
                            <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="account_id" :value="__('Account')" />
                        <select id="account_id" name="account_id" class="mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                            <option value="">All Accounts</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="category_id" :value="__('Category')" />
                        <select id="category_id" name="category_id" class="mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                            <option value="">All Categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="search" :value="__('Search')" />
                        <x-text-input id="search" type="text" name="search" :value="request('search')" placeholder="Description..." class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="is_recurring" :value="__('Recurring')" />
                        <select id="is_recurring" name="is_recurring" class="mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                            <option value="">All</option>
                            <option value="1" {{ request('is_recurring') == '1' ? 'selected' : '' }}>Recurring Templates</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <x-primary-button class="px-4 py-2 text-sm">Filter</x-primary-button>
                        <a href="{{ route('transactions.index') }}" class="btn-secondary">Clear</a>
                    </div>
                </form>
            </div>

            @if ($transactions->isEmpty())
                <div class="text-center py-12 card">
                    <p class="text-gray-500 dark:text-gray-400">No transactions found.</p>
                </div>
            @else
                <div class="card overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800">
                            <tr>
                                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>
                                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Category</th>
                                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Sub</th>
                                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Account</th>
                                <th class="text-right px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Amount</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @foreach ($transactions as $transaction)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $transaction->date->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $transaction->description ?: '-' }}
                                        @if ($transaction->is_recurring)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 ml-1">
                                                {{ $transaction->recurring_interval ? ucfirst($transaction->recurring_interval) : 'Recurring' }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium" style="background-color: {{ $transaction->category->color ?? '#6B7280' }}20; color: {{ $transaction->category->color ?? '#6B7280' }}">
                                            {{ $transaction->category->name }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $transaction->subCategory->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $transaction->account->name }}</td>
                                    <td class="px-4 py-3 text-sm font-bold text-right {{ $transaction->transfer_id ? ($transaction->is_savings ? 'text-purple-600 dark:text-purple-400' : 'text-gray-500') : ($transaction->type === 'income' ? 'text-green-600' : 'text-red-600') }}">
                                        @if ($transaction->transfer_id)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium mr-1 {{ $transaction->is_savings ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                                {{ $transaction->is_savings ? '💰 Savings' : '↔ Transfer' }}
                                            </span>
                                        @endif
                                        {{ $transaction->type === 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex gap-2 justify-end">
                                            <a href="{{ route('transactions.edit', $transaction) }}" class="text-gray-400 dark:text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" onsubmit="return confirm('Delete this transaction? Account balance will be adjusted.')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $transactions->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
