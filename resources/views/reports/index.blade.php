<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Reports</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Filters --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm dark:shadow-none border border-gray-200 dark:border-gray-800 p-4 mb-6">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <x-input-label for="start_date" :value="__('From')" />
                        <x-text-input id="start_date" type="date" name="start_date" :value="request('start_date', now()->startOfMonth()->format('Y-m-d'))" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="end_date" :value="__('To')" />
                        <x-text-input id="end_date" type="date" name="end_date" :value="request('end_date', now()->format('Y-m-d'))" class="mt-1" />
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
                            <option value="">All</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="category_id" :value="__('Category')" />
                        <select id="category_id" name="category_id" class="mt-1 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                            <option value="">All</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <x-primary-button class="px-4 py-2 text-sm">Generate</x-primary-button>
                        <a href="{{ route('reports.export.csv', request()->query()) }}" class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">CSV</a>
                        <a href="{{ route('reports.export.pdf', request()->query()) }}" class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">PDF</a>
                    </div>
                </form>
            </div>

            {{-- Summary --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm dark:shadow-none border border-gray-200 dark:border-gray-800 p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Income</p>
                    <p class="text-2xl font-bold text-green-600">+{{ number_format($totalIncome, 2) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm dark:shadow-none border border-gray-200 dark:border-gray-800 p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Expense</p>
                    <p class="text-2xl font-bold text-red-600">-{{ number_format($totalExpense, 2) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm dark:shadow-none border border-gray-200 dark:border-gray-800 p-5">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Net</p>
                    <p class="text-2xl font-bold {{ ($totalIncome - $totalExpense) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ ($totalIncome - $totalExpense) >= 0 ? '+' : '' }}{{ number_format($totalIncome - $totalExpense, 2) }}
                    </p>
                </div>
            </div>

            {{-- Category Breakdown --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm dark:shadow-none border border-gray-200 dark:border-gray-800 p-5">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Expense by Category</h3>
                    @if ($categoryReport['expenses']->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400 text-sm">No expenses in this period.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($categoryReport['expenses'] as $expense)
                                @php $pct = $totalExpense > 0 ? round(($expense->total / $totalExpense) * 100, 1) : 0; @endphp
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-700 dark:text-gray-300">{{ $expense->category->name }}</span>
                                        <span class="text-gray-500 dark:text-gray-400">{{ number_format($expense->total, 2) }} ({{ $pct }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="h-2 rounded-full" style="width: {{ $pct }}%; background-color: {{ $expense->category->color ?? '#6B7280' }}"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm dark:shadow-none border border-gray-200 dark:border-gray-800 p-5">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Income by Category</h3>
                    @if ($categoryReport['incomes']->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400 text-sm">No income in this period.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($categoryReport['incomes'] as $income)
                                @php $pct = $totalIncome > 0 ? round(($income->total / $totalIncome) * 100, 1) : 0; @endphp
                                <div>
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-gray-700 dark:text-gray-300">{{ $income->category->name }}</span>
                                        <span class="text-gray-500 dark:text-gray-400">{{ number_format($income->total, 2) }} ({{ $pct }}%)</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="h-2 rounded-full bg-green-500" style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Transactions Table --}}
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm dark:shadow-none border border-gray-200 dark:border-gray-800 overflow-hidden">
                <div class="p-5 border-b border-gray-200 dark:border-gray-800">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Transaction Details</h3>
                </div>
                @if ($transactions->isEmpty())
                    <div class="p-5 text-gray-500 dark:text-gray-400 text-sm text-center">No transactions found.</div>
                @else
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800">
                            <tr>
                                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>
                                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Category</th>
                                <th class="text-left px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Account</th>
                                <th class="text-right px-4 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            @foreach ($transactions as $t)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-800">
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $t->date->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $t->description ?: '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $t->category->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $t->account->name }}</td>
                                    <td class="px-4 py-3 text-sm font-bold text-right {{ $t->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $t->type === 'income' ? '+' : '-' }}{{ number_format($t->amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
