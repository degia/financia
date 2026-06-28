<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Goals</h2>
            <a href="{{ route('goals.create') }}" class="bg-gray-900 hover:bg-gray-800 text-white dark:bg-white dark:text-black dark:hover:bg-gray-200 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                + New Goal
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if ($goals->isEmpty())
                <div class="text-center py-12 bg-white dark:bg-gray-900 rounded-xl shadow-sm dark:shadow-none border border-gray-200 dark:border-gray-800">
                    <p class="text-gray-500 dark:text-gray-400">No goals yet. Set a savings goal!</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($goals as $goal)
                        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm dark:shadow-none border border-gray-200 dark:border-gray-800 p-6 {{ $goal->is_achieved ? 'ring-2 ring-green-400' : '' }}">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white" style="background-color: {{ $goal->color ?? '#6366F1' }}">
                                        {{ substr($goal->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $goal->name }}</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Target: {{ number_format($goal->target_amount, 2) }}</p>
                                    </div>
                                </div>
                                @if (!$goal->is_achieved)
                                <div class="flex gap-2">
                                    <a href="{{ route('goals.edit', $goal) }}" class="text-gray-400 dark:text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('goals.destroy', $goal) }}" onsubmit="return confirm('Delete this goal?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </div>

                            @php $progress = $goal->target_amount > 0 ? min(100, round(($goal->current_amount / $goal->target_amount) * 100, 1)) : 0; @endphp

                            <div class="mt-3">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">{{ number_format($goal->current_amount, 2) }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">{{ $progress }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                    <div class="h-3 rounded-full transition-all duration-500 {{ $goal->is_achieved ? 'bg-green-500' : 'bg-gray-900 dark:bg-gray-200' }}" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>

                            <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                                @if ($goal->is_achieved)
                                    <span class="text-green-600 font-semibold">Achieved!</span>
                                @else
                                    Target: {{ $goal->target_date->format('M d, Y') }}
                                @endif
                            </div>

                            @if (!$goal->is_achieved)
                            <form method="POST" action="{{ route('goals.contribute', $goal) }}" class="mt-4 flex gap-2">
                                @csrf
                                <input type="number" step="0.01" min="0" name="amount" placeholder="Amount" class="flex-1 border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm text-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400" required>
                                <x-primary-button class="text-sm px-3 py-2">Add</x-primary-button>
                            </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
