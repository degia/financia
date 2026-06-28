<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Budgets</h2>
            <a href="{{ route('budgets.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                + New Budget
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Period Filter --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                <form method="GET" class="flex gap-4 items-end">
                    <div>
                        <x-input-label for="month" :value="__('Month')" />
                        <select id="month" name="month" class="mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach (range(1, 12) as $m)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ Carbon\Carbon::create()->month($m)->format('F') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="year" :value="__('Year')" />
                        <select id="year" name="year" class="mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach (range(now()->year - 2, now()->year + 1) as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-primary-button class="text-sm px-3 py-2">View</x-primary-button>
                    </div>
                </form>
            </div>

            @if ($budgets->isEmpty())
                <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-200">
                    <p class="text-gray-500">No budgets set for this period.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($budgets as $item)
                        @php
                            $spent = $item['spent'];
                            $percentage = $item['percentage'];
                            $budget = $item['budget'];
                            $status = $item['status'];
                            $statusColors = [
                                'good' => 'bg-green-500',
                                'warning' => 'bg-yellow-500',
                                'danger' => 'bg-orange-500',
                                'exceeded' => 'bg-red-500',
                            ];
                        @endphp
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center text-white" style="background-color: {{ $budget->category->color ?? '#6B7280' }}">
                                        {{ substr($budget->category->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $budget->category->name }}</h3>
                                        <p class="text-sm text-gray-500">Budget: {{ number_format($budget->amount, 2) }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('budgets.edit', $budget) }}" class="text-gray-400 hover:text-indigo-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('budgets.destroy', $budget) }}" onsubmit="return confirm('Delete this budget?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="mt-3">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">{{ number_format($spent, 2) }} spent</span>
                                    <span class="{{ $status == 'exceeded' ? 'text-red-600 font-semibold' : 'text-gray-500' }}">{{ $percentage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="h-3 rounded-full transition-all duration-500 {{ $statusColors[$status] }}" style="width: {{ min(100, $percentage) }}%"></div>
                                </div>
                                @if ($status == 'exceeded')
                                    <p class="text-red-500 text-sm mt-1">Exceeded by {{ number_format($spent - $budget->amount, 2) }}</p>
                                @else
                                    <p class="text-gray-400 text-sm mt-1">{{ number_format($budget->amount - $spent, 2) }} remaining</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
