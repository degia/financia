<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">{{ $loan->name }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $loan->type === "borrow" ? "Borrow" : "Lend" }}
                    @if ($loan->lender_name)
                        &middot; {{ $loan->lender_name }}
                    @endif
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route("loans.edit", $loan) }}" class="btn-secondary text-sm">Edit</a>
                <a href="{{ route("loans.index") }}" class="text-gray-600 dark:text-gray-400 dark:hover:text-white text-sm px-3 py-2">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session("success"))
                <div class="card p-4 mb-6 border-l-4 border-l-green-500 bg-green-50 dark:bg-green-900/20">
                    <p class="text-sm text-green-700 dark:text-green-300">{{ session("success") }}</p>
                </div>
            @endif

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="card p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Amount</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($loan->amount, 2) }}</p>
                </div>
                <div class="card p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Paid</p>
                    <p class="text-lg font-bold text-green-600">{{ number_format($loan->paid_amount, 2) }}</p>
                </div>
                <div class="card p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Remaining</p>
                    <p class="text-lg font-bold {{ $loan->remaining_amount > 0 ? "text-red-600" : "text-green-600" }}">{{ number_format($loan->remaining_amount, 2) }}</p>
                </div>
                <div class="card p-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-0.5">Status</p>
                    <p class="text-lg font-bold {{ $loan->status === "active" ? "text-blue-600" : ($loan->status === "completed" ? "text-green-600" : "text-red-600") }}">{{ ucfirst($loan->status) }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Start Date</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $loan->start_date->format("M d, Y") }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Due Date</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $loan->due_date?->format("M d, Y") ?: "-" }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Interest Rate</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $loan->interest_rate > 0 ? $loan->interest_rate . "%" : "-" }}</p>
                </div>
                <div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">Linked Account</span>
                    <p class="text-sm text-gray-900 dark:text-white">{{ $loan->account?->name ?: "-" }}</p>
                </div>
            </div>

            @if ($loan->notes)
                <div class="card p-4 mb-6">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Notes</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $loan->notes }}</p>
                </div>
            @endif
            @if ($loan->status === "active")
                <div class="card p-5 mb-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Record Payment</h3>
                    <form method="POST" action="{{ route("loans.payment", $loan) }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        @csrf
                        <div>
                            <x-input-label for="payment_amount" :value="__("Amount")" />
                            <x-text-input id="payment_amount" class="block mt-1 w-full" type="number" step="0.01" min="0.01" :max="$loan->remaining_amount" name="amount" required placeholder="0.00" />
                        </div>
                        <div>
                            <x-input-label for="payment_account_id" :value="__("Account")" />
                            <select id="payment_account_id" name="account_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm text-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                                <option value="">-- Select --</option>
                                @foreach (Auth::user()->accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="payment_date" :value="__("Date")" />
                            <x-text-input id="payment_date" class="block mt-1 w-full" type="date" name="payment_date" value="{{ now()->format("Y-m-d") }}" required />
                        </div>
                        <div>
                            <x-primary-button class="w-full justify-center">Record Payment</x-primary-button>
                        </div>
                    </form>
                </div>
            @endif
            {{-- Payment History --}}
            <div class="card p-5">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Payment History</h3>

                @if ($loan->payments->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="pb-2 font-medium text-gray-500 dark:text-gray-400">Date</th>
                                    <th class="pb-2 font-medium text-gray-500 dark:text-gray-400">Account</th>
                                    <th class="pb-2 font-medium text-gray-500 dark:text-gray-400">Amount</th>
                                    <th class="pb-2 font-medium text-gray-500 dark:text-gray-400">Notes</th>
                                    <th class="pb-2 font-medium text-gray-500 dark:text-gray-400"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loan->payments->sortByDesc("payment_date") as $payment)
                                    <tr class="border-b border-gray-100 dark:border-gray-800">
                                        <td class="py-2 text-gray-900 dark:text-white">{{ $payment->payment_date->format("M d, Y") }}</td>
                                        <td class="py-2 text-gray-600 dark:text-gray-400">{{ $payment->account?->name ?: "-" }}</td>
                                        <td class="py-2 font-medium text-green-600">{{ number_format($payment->amount, 2) }}</td>
                                        <td class="py-2 text-gray-600 dark:text-gray-400">{{ $payment->notes ?: "-" }}</td>
                                        <td class="py-2">
                                            <form method="POST" action="{{ route("loans.payment.destroy", [$loan, $payment]) }}" class="inline" onsubmit="return confirm('Delete this payment?')">
                                                @csrf @method("DELETE")
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">Total paid: {{ number_format($loan->paid_amount, 2) }} / {{ number_format($loan->amount, 2) }}</p>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">No payments recorded yet.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
