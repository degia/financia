<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Category</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <form method="POST" action="{{ route('categories.update', $category) }}">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Category Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $category->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="type" :value="__('Type')" />
                        <select id="type" name="type" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="expense" {{ old('type', $category->type) == 'expense' ? 'selected' : '' }}>Expense</option>
                            <option value="income" {{ old('type', $category->type) == 'income' ? 'selected' : '' }}>Income</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="color" :value="__('Color')" />
                        <input id="color" type="color" name="color" value="{{ old('color', $category->color ?? '#6366F1') }}" class="block mt-1 w-16 h-10 rounded border-gray-300 cursor-pointer">
                        <x-input-error :messages="$errors->get('color')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4 mt-6">
                        <x-primary-button>{{ __('Update Category') }}</x-primary-button>
                        <a href="{{ route('categories.index') }}" class="text-gray-600 hover:text-gray-900 text-sm">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
