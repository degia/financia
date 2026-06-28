<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Edit Category</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            {{-- Main Category Form --}}
            <div class="card p-6">
                <form method="POST" action="{{ route('categories.update', $category) }}">
                    @csrf @method('PUT')

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Category Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $category->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="type" :value="__('Type')" />
                        <select id="type" name="type" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                            <option value="expense" {{ old('type', $category->type) == 'expense' ? 'selected' : '' }}>Expense</option>
                            <option value="income" {{ old('type', $category->type) == 'income' ? 'selected' : '' }}>Income</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="color" :value="__('Color')" />
                        <input id="color" type="color" name="color" value="{{ old('color', $category->color ?? '#6366F1') }}" class="block mt-1 w-16 h-10 rounded border-gray-300 dark:border-gray-700 dark:bg-gray-800 cursor-pointer">
                        <x-input-error :messages="$errors->get('color')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4 mt-6">
                        <x-primary-button>{{ __('Update Category') }}</x-primary-button>
                        <a href="{{ route('categories.index') }}" class="text-gray-600 dark:text-gray-400 dark:hover:text-white text-sm">Cancel</a>
                    </div>
                </form>
            </div>

            {{-- Sub-Categories --}}
            <div class="card p-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Sub-Categories</h3>

                @if ($category->subCategories->isNotEmpty())
                    <div class="space-y-2 mb-4">
                        @foreach ($category->subCategories as $sub)
                            <div class="flex items-center justify-between py-2 px-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $sub->name }}</span>
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('sub-categories.update', $sub) }}" class="flex items-center gap-1">
                                        @csrf @method('PUT')
                                        <input type="text" name="name" value="{{ $sub->name }}" required
                                            class="text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 focus:ring-gray-500 w-32">
                                        <button type="submit" class="btn-primary text-xs px-2 py-1">Rename</button>
                                    </form>
                                    <form method="POST" action="{{ route('sub-categories.destroy', $sub) }}" onsubmit="return confirm('Delete &quot;{{ $sub->name }}&quot;?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">No sub-categories yet.</p>
                @endif

                <form method="POST" action="{{ route('sub-categories.store') }}" class="flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="category_id" value="{{ $category->id }}">
                    <input type="text" name="name" placeholder="New sub-category name..." required
                        class="flex-1 text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 focus:ring-gray-500">
                    <button type="submit" class="btn-primary text-sm">Add</button>
                </form>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
        </div>
    </div>
</x-app-layout>
