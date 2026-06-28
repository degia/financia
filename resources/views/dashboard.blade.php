<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Dashboard</h2>
            <div class="hidden sm:flex items-center gap-3 px-4 py-1.5 bg-gray-100 dark:bg-gray-800 rounded-lg">
                <span class="text-xs text-gray-500 dark:text-gray-400">Real Balance</span>
                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($summary['realBalance'], 2) }}</span>
                <span class="text-[10px] text-gray-400 dark:text-gray-500">(excl. savings)</span>
            </div>
            <form method="GET" class="flex gap-2 items-center ms-auto">
                <select name="month" class="border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm text-xs focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ Carbon\Carbon::create()->month($m)->format('F') }}</option>
                    @endforeach
                </select>
                <select name="year" class="border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm text-xs focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                    @foreach (range(now()->year - 2, now()->year + 1) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <x-primary-button class="text-xs px-2.5 py-1.5">Go</x-primary-button>
            </form>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Summary Cards - Drag & Drop Reorderable --}}
            @php
                $cards = [
                    'savings_account' => [
                        'label' => 'Savings Account',
                        'value' => number_format($summary['savingsBalance'], 2),
                        'sub' => 'Savings category accounts',
                        'color' => 'text-violet-600 dark:text-violet-400',
                        'border' => 'border-l-violet-500',
                    ],
                    'income' => [
                        'label' => 'Income (' . Carbon\Carbon::create()->month($month)->format('M') . ')',
                        'value' => '+' . number_format($summary['monthlyIncome'], 2),
                        'sub' => '',
                        'color' => 'text-green-600',
                        'border' => '',
                    ],
                    'expense' => [
                        'label' => 'Expense (' . Carbon\Carbon::create()->month($month)->format('M') . ')',
                        'value' => '-' . number_format($summary['monthlyExpense'], 2),
                        'sub' => '',
                        'color' => 'text-red-600',
                        'border' => '',
                    ],
                    'saved' => [
                        'label' => 'Saved',
                        'value' => number_format($savings['totalSaved'], 2),
                        'sub' => '+' . number_format($savings['monthlySavings'], 2) . ' this month',
                        'color' => 'text-violet-600 dark:text-violet-400',
                        'border' => 'border-violet-200 dark:border-violet-900/50',
                    ],
                    'net_savings' => [
                        'label' => 'Net Savings',
                        'value' => ($summary['netSavings'] >= 0 ? '+' : '') . number_format($summary['netSavings'], 2),
                        'sub' => '',
                        'color' => $summary['netSavings'] >= 0 ? 'text-green-600' : 'text-red-600',
                        'border' => '',
                    ],
                ];
                $defaultOrder = ['savings_account', 'income', 'expense', 'saved', 'net_savings'];
            @endphp

            <div x-data='dashboardCards(@json($cards), @json($defaultOrder))' class="mb-6">
                {{-- Toolbar --}}
                <div class="flex items-center justify-end gap-2 mb-3">
                    <button @click="showSettings = !showSettings" class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors px-2 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-800">
                        <svg class="w-4 h-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Customize
                    </button>
                </div>

                {{-- Settings Panel --}}
                <div x-show="showSettings" x-cloak class="card p-4 mb-4">
                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-3">Drag cards to reorder. Toggle visibility.</p>
                    <div class="space-y-2">
                        <template x-for="(id, idx) in order" :key="id">
                            <div class="flex items-center gap-3 px-3 py-2 bg-gray-50 dark:bg-gray-800 rounded-lg"
                                 draggable="true"
                                 @dragstart="dragStart($event, idx)"
                                 @dragover.prevent="dragOver($event, idx)"
                                 @dragend="dragEnd()">
                                <span class="cursor-grab text-gray-400 dark:text-gray-500">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 8h16M4 16h16"/>
                                    </svg>
                                </span>
                                <span class="text-sm text-gray-700 dark:text-gray-300 flex-1" x-text="labels[id]"></span>
                                <button @click="toggleCard(id)" class="text-xs px-2 py-1 rounded transition-colors"
                                    :class="hidden.includes(id) ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' : 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400'"
                                    x-text="hidden.includes(id) ? 'Hidden' : 'Visible'"></button>
                            </div>
                        </template>
                    </div>
                    <button @click="resetOrder()" class="mt-3 text-xs text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white underline">Reset to default</button>
                </div>

                {{-- Cards Grid --}}
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    <template x-for="(id, idx) in order" :key="id">
                        <div x-show="!hidden.includes(id)"
                             x-cloak
                             class="card p-3 border-l-4 transition"
                             :class="cardsData[id].border || ''"
                             draggable="true"
                             @dragstart="dragStart($event, idx)"
                             @dragover.prevent="dragOver($event, idx)"
                             @dragend="dragEnd()">
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mb-0.5" x-text="cardsData[id].label"></p>
                            <p class="text-lg font-bold" :class="cardsData[id].color" x-text="cardsData[id].value"></p>
                            <p x-show="cardsData[id].sub" class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5" x-text="cardsData[id].sub"></p>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Charts Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="card p-5">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Income vs Expense ({{ $year }})</h3>
                    <div class="h-64">
                        <canvas id="monthlyChart"
                            data-labels='@json($monthlyChart["labels"])'
                            data-income='@json($monthlyChart["income"])'
                            data-expense='@json($monthlyChart["expense"])'>
                        </canvas>
                    </div>
                </div>
                <div class="card p-5">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Expense by Category ({{ Carbon\Carbon::create()->month($month)->format('M Y') }})</h3>
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
            <div class="card p-5 mb-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Budget Progress</h3>
                <div class="space-y-4">
                    @foreach ($budgetProgress as $bp)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-700 dark:text-gray-300">{{ $bp['category'] }}</span>
                                <span class="text-gray-500 dark:text-gray-400">{{ number_format($bp['spent'], 0) }} / {{ number_format($bp['budgeted'], 0) }} ({{ $bp['percentage'] }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                <div class="h-2.5 rounded-full transition-all duration-300 {{ $bp['percentage'] > 100 ? 'bg-red-500' : ($bp['percentage'] > 80 ? 'bg-orange-500' : ($bp['percentage'] > 50 ? 'bg-yellow-500' : 'bg-green-500')) }}" style="width: {{ min(100, $bp['percentage']) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Recent Transactions --}}
            <div class="card p-5">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Recent Transactions</h3>
                    <a href="{{ route('transactions.index') }}" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">View All</a>
                </div>
                @if ($recentTransactions->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-sm text-center py-4">No transactions yet.</p>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($recentTransactions as $t)
                            <div class="flex justify-between items-center py-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $t->description ?: $t->category->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $t->date->format('M d, Y') }} · {{ $t->account->name }}</p>
                                </div>
                                <span class="text-sm font-bold {{ $t->transfer_id ? ($t->is_savings ? 'text-purple-600 dark:text-purple-400' : 'text-gray-500') : ($t->type === 'income' ? 'text-green-600' : 'text-red-600') }}">
                                    @if ($t->transfer_id)
                                        <span class="text-xs font-normal mr-1 {{ $t->is_savings ? 'text-purple-500 dark:text-purple-400' : 'text-gray-400 dark:text-gray-500' }}">{{ $t->is_savings ? '💰' : '↔' }}</span>
                                    @endif
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
