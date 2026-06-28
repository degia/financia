<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
            <form method="GET" class="flex gap-2 items-center">
                <select name="month" class="border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ Carbon\Carbon::create()->month($m)->format('F') }}</option>
                    @endforeach
                </select>
                <select name="year" class="border-gray-300 rounded-md shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach (range(now()->year - 2, now()->year + 1) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <x-primary-button class="text-sm px-3 py-2">Go</x-primary-button>
            </form>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <p class="text-sm text-gray-500 mb-1">Total Balance</p>
                    <p class="text-2xl font-bold {{ $summary['totalBalance'] >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                        {{ number_format($summary['totalBalance'], 2) }}
                    </p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <p class="text-sm text-gray-500 mb-1">Income ({{ Carbon\Carbon::create()->month($month)->format('M') }})</p>
                    <p class="text-2xl font-bold text-green-600">+{{ number_format($summary['monthlyIncome'], 2) }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <p class="text-sm text-gray-500 mb-1">Expense ({{ Carbon\Carbon::create()->month($month)->format('M') }})</p>
                    <p class="text-2xl font-bold text-red-600">-{{ number_format($summary['monthlyExpense'], 2) }}</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <p class="text-sm text-gray-500 mb-1">Net Savings</p>
                    <p class="text-2xl font-bold {{ $summary['netSavings'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $summary['netSavings'] >= 0 ? '+' : '' }}{{ number_format($summary['netSavings'], 2) }}
                    </p>
                </div>
            </div>

            {{-- Charts Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-900 mb-4">Income vs Expense ({{ $year }})</h3>
                    <div class="h-64">
                        <canvas id="monthlyChart"
                            data-labels='@json($monthlyChart["labels"])'
                            data-income='@json($monthlyChart["income"])'
                            data-expense='@json($monthlyChart["expense"])'>
                        </canvas>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                    <h3 class="font-semibold text-gray-900 mb-4">Expense by Category ({{ Carbon\Carbon::create()->month($month)->format('M Y') }})</h3>
                    <div class="h-64">
                        <canvas id="categoryChart"
                            data-labels='@json($categoryBreakdown["labels"])'
                            data-data='@json($categoryBreakdown["data"])'
                            data-colors='@json($categoryBreakdown["colors"])'>
                        </canvas>
                    </div>
                </div>
            </div>

            {{-- Budget Progress --}}
            @if (!empty($budgetProgress))
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
                <h3 class="font-semibold text-gray-900 mb-4">Budget Progress</h3>
                <div class="space-y-4">
                    @foreach ($budgetProgress as $bp)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-700">{{ $bp['category'] }}</span>
                                <span class="text-gray-500">{{ number_format($bp['spent'], 0) }} / {{ number_format($bp['budgeted'], 0) }} ({{ $bp['percentage'] }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="h-2.5 rounded-full transition-all duration-300 {{ $bp['percentage'] > 100 ? 'bg-red-500' : ($bp['percentage'] > 80 ? 'bg-orange-500' : ($bp['percentage'] > 50 ? 'bg-yellow-500' : 'bg-green-500')) }}" style="width: {{ min(100, $bp['percentage']) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Recent Transactions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-900">Recent Transactions</h3>
                    <a href="{{ route('transactions.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View All</a>
                </div>
                @if ($recentTransactions->isEmpty())
                    <p class="text-gray-500 text-sm text-center py-4">No transactions yet.</p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($recentTransactions as $t)
                            <div class="flex justify-between items-center py-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $t->description ?: $t->category->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $t->date->format('M d, Y') }} · {{ $t->account->name }}</p>
                                </div>
                                <span class="text-sm font-bold {{ $t->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $t->type === 'income' ? '+' : '-' }}{{ number_format($t->amount, 2) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        @vite(['resources/js/charts.js'])
    @endpush
</x-app-layout>
