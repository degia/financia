<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Budget</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <form method="POST" action="{{ route('budgets.store') }}">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="category_id" :value="__('Category')" />
                        <select id="category_id" name="category_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select category...</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="amount" :value="__('Budget Amount')" />
                        <x-text-input id="amount" class="block mt-1 w-full" type="number" step="0.01" min="0" name="amount" :value="old('amount')" required placeholder="0.00" />
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <x-input-label for="month" :value="__('Month')" />
                            <select id="month" name="month" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ old('month', now()->month) == $m ? 'selected' : '' }}>{{ Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('month')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="year" :value="__('Year')" />
                            <select id="year" name="year" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach (range(now()->year, now()->year + 1) as $y)
                                    <option value="{{ $y }}" {{ old('year', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('year')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-6">
                        <x-primary-button>{{ __('Save Budget') }}</x-primary-button>
                        <a href="{{ route('budgets.index') }}" class="text-gray-600 hover:text-gray-900 text-sm">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
