<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Transactions</h2>
            <a href="{{ route('transactions.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                + Add Transaction
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Filters --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
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
                        <select id="type" name="type" class="mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All</option>
                            <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="account_id" :value="__('Account')" />
                        <select id="account_id" name="account_id" class="mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All Accounts</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="category_id" :value="__('Category')" />
                        <select id="category_id" name="category_id" class="mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                    <div class="flex gap-2">
                        <x-primary-button class="px-4 py-2 text-sm">Filter</x-primary-button>
                        <a href="{{ route('transactions.index') }}" class="px-4 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">Clear</a>
                    </div>
                </form>
            </div>

            @if ($transactions->isEmpty())
                <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-200">
                    <p class="text-gray-500">No transactions found.</p>
                </div>
            @else
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Category</th>
                                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 uppercase">Account</th>
                                <th class="text-right px-4 py-3 text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($transactions as $transaction)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $transaction->date->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $transaction->description ?: '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium" style="background-color: {{ $transaction->category->color ?? '#6B7280' }}20; color: {{ $transaction->category->color ?? '#6B7280' }}">
                                            {{ $transaction->category->name }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $transaction->account->name }}</td>
                                    <td class="px-4 py-3 text-sm font-bold text-right {{ $transaction->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->type === 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex gap-2 justify-end">
                                            <a href="{{ route('transactions.edit', $transaction) }}" class="text-gray-400 hover:text-indigo-600 transition-colors">
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
