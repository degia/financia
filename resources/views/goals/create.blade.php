<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Goal</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <form method="POST" action="{{ route('goals.store') }}">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Goal Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required placeholder="e.g. Emergency Fund" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="target_amount" :value="__('Target Amount')" />
                        <x-text-input id="target_amount" class="block mt-1 w-full" type="number" step="0.01" min="0" name="target_amount" :value="old('target_amount')" required placeholder="0.00" />
                        <x-input-error :messages="$errors->get('target_amount')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="target_date" :value="__('Target Date')" />
                        <x-text-input id="target_date" class="block mt-1 w-full" type="date" name="target_date" :value="old('target_date')" required />
                        <x-input-error :messages="$errors->get('target_date')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="color" :value="__('Color')" />
                        <input id="color" type="color" name="color" value="{{ old('color', '#6366F1') }}" class="block mt-1 w-16 h-10 rounded border-gray-300 cursor-pointer">
                        <x-input-error :messages="$errors->get('color')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4 mt-6">
                        <x-primary-button>{{ __('Save Goal') }}</x-primary-button>
                        <a href="{{ route('goals.index') }}" class="text-gray-600 hover:text-gray-900 text-sm">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
