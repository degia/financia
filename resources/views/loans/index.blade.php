<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Loans</h2>
            <a href="{{ route('loans.create') }}" class="btn-primary">+ New Loan</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="card p-4 mb-6 border-l-4 border-l-green-500 bg-green-50 dark:bg-green-900/20">
                    <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Summary --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="card p-4 border-l-4 border-l-red-500">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Total Borrowed</p>
                    <p class="text-xl font-bold text-red-600">{{ number_format($totalBorrow, 2) }}</p>
                </div>
                <div class="card p-4 border-l-4 border-l-green-500">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Total Lent</p>
                    <p class="text-xl font-bold text-green-600">{{ number_format($totalLend, 2) }}</p>
                </div>
                <div class="card p-4 border-l-4 border-l-blue-500">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Active Loans</p>
                    <p class="text-xl font-bold text-blue-600">{{ $activeCount }}</p>
                </div>
            </div>

            @if ($loans->isEmpty())
                <div class="text-center py-12 card">
                    <p class="text-gray-500 dark:text-gray-400 mb-4">No loans yet.</p>
                    <a href="{{ route('loans.create') }}" class="btn-primary">+ Create Loan</a>
                </div>
            @else
                <div class="card overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-800">
                                    <th class="table-header">Name</th>
                                    <th class="table-header">Type</th>
                                    <th class="table-header">Lender</th>
                                    <th class="table-header">Amount</th>
                                    <th class="table-header">Remaining</th>
                                    <th class="table-header">Status</th>
                                    <th class="table-header">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach ($loans as $loan)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                        <td class="table-cell font-medium text-gray-900 dark:text-white">
                                            <a href="{{ route('loans.show', $loan) }}" class="hover:underline">{{ $loan->name }}</a>
                                        </td>
                                        <td class="table-cell">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $loan->type === 'borrow' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' : 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' }}">
                                                {{ $loan->type === 'borrow' ? 'Borrow' : 'Lend' }}
                                            </span>
                                        </td>
                                        <td class="table-cell">{{ $loan->lender_name ?: '-' }}</td>
                                        <td class="table-cell">{{ number_format($loan->amount, 2) }}</td>
                                        <td class="table-cell font-medium {{ $loan->remaining_amount > 0 ? 'text-gray-900 dark:text-white' : 'text-green-600' }}">
                                            {{ number_format($loan->remaining_amount, 2) }}
                                        </td>
                                        <td class="table-cell">
                                            @php
                                                $statusColors = ['active' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300', 'completed' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300', 'defaulted' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300'];
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusColors[$loan->status] }}">
                                                {{ ucfirst($loan->status) }}
                                            </span>
                                        </td>
                                        <td class="table-cell">
                                            <div class="flex gap-2">
                                                <a href="{{ route('loans.show', $loan) }}" class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" title="View">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                </a>
                                                <a href="{{ route('loans.edit', $loan) }}" class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                </a>
                                                <form method="POST" action="{{ route('loans.destroy', $loan) }}" onsubmit="return confirm('Delete this loan?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors" title="Delete">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
