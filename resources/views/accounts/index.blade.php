<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Accounts</h2>
            <a href="{{ route('accounts.create') }}" class="btn-primary">
                + New Account
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="card p-4 mb-6 border-l-4 border-l-green-500 bg-green-50 dark:bg-green-900/20">
                    <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
                </div>
            @endif

            @if ($accounts->isNotEmpty())
                <div class="flex justify-end mb-4">
                    <form method="POST" action="{{ route('accounts.reconcile') }}">
                        @csrf
                        <button type="submit" class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white underline">Reconcile Balances</button>
                    </form>
                </div>
            @endif

            @if ($accounts->isEmpty())
                <div class="text-center py-12 card">
                    <div class="text-gray-400 dark:text-gray-500 mb-4">
                        <svg class="mx-auto h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No accounts yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Create your first account to start tracking your finances.</p>
                    <a href="{{ route('accounts.create') }}" class="btn-primary">
                        + Create Account
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($accounts as $account)
                        <div class="card p-6 hover:shadow-md dark:hover:shadow-none dark:hover:border-gray-700 transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <x-institution-logo :slug="$account->icon" :size="40" :fallback-name="$account->name" :fallback-color="$account->color ?? '#6366F1'" />
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $account->name }}</h3>
                                        @php
                                            $typeLabels = ['cash' => 'Cash', 'bank' => 'Bank', 'ewallet' => 'E-Wallet', 'credit_card' => 'Credit Card', 'savings' => 'Savings'];
                                            $typeColors = ['cash' => 'text-green-600', 'bank' => 'text-blue-600', 'ewallet' => 'text-orange-600', 'credit_card' => 'text-red-600', 'savings' => 'text-violet-600'];
                                        @endphp
                                        <span class="text-xs font-medium {{ $typeColors[$account->type] ?? 'text-gray-500' }}">{{ $typeLabels[$account->type] ?? $account->type }}</span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium {{ $account->category === 'savings' ? 'bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300' : ($account->category === 'subscriptions' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400') }}">
                                            {{ ucfirst($account->category) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('accounts.edit', $account) }}" class="text-gray-400 dark:text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('accounts.destroy', $account) }}" onsubmit="return confirm('Delete this account? All transactions will be deleted too.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="border-t border-gray-100 dark:border-gray-800 pt-4">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Current Balance</p>
                                <p class="text-2xl font-bold {{ $account->current_balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($account->current_balance, 2) }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
