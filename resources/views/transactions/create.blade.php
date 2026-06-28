<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Add Transaction</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-6">
                <form method="POST" action="{{ route('transactions.store') }}"
                      x-data="{
                          catId: '{{ old('category_id') }}',
                          transactionType: '{{ old('type', 'expense') }}',
                          filterSubs() {
                              this.$nextTick(() => {
                                  document.querySelectorAll('[data-sub-cat]').forEach(el => {
                                      el.style.display = el.dataset.categoryId == this.catId ? '' : 'none'
                                  })
                              })
                          }
                      }"
                      x-init="filterSubs()">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="type" :value="__('Type')" />
                        <div class="mt-1 flex gap-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="type" value="expense" class="text-red-500 focus:ring-red-500" {{ old('type', 'expense') == 'expense' ? 'checked' : '' }} @change="transactionType = 'expense'">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Expense</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="type" value="income" class="text-green-500 focus:ring-green-500" {{ old('type') == 'income' ? 'checked' : '' }} @change="transactionType = 'income'">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Income</span>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="account_id" :value="__('Account')" />
                        <select id="account_id" name="account_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                            <option value="">Select account...</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }} ({{ number_format($account->current_balance, 0) }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('account_id')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="category_id" :value="__('Category')" />
                        <select id="category_id" name="category_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400"
                                x-model="catId"
                                @change="filterSubs()">
                            <option value="">Select category...</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" data-type="{{ $category->type }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="sub_category_id" :value="__('Sub-Category')" />
                        <select id="sub_category_id" name="sub_category_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                            <option value="">None</option>
                            @foreach ($categories as $category)
                                @foreach ($category->subCategories as $sub)
                                    <option value="{{ $sub->id }}"
                                        data-sub-cat
                                        data-category-id="{{ $category->id }}"
                                        {{ old('sub_category_id') == $sub->id ? 'selected' : '' }}>
                                        {{ $sub->name }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('sub_category_id')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="amount" :value="__('Amount')" />
                        <x-text-input id="amount" class="block mt-1 w-full" type="number" step="0.01" min="0" name="amount" :value="old('amount')" required placeholder="0.00" />
                        <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="description" :value="__('Description')" />
                        <x-text-input id="description" class="block mt-1 w-full" type="text" name="description" :value="old('description')" placeholder="e.g. Grocery shopping" />
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="date" :value="__('Date')" />
                        <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" :value="old('date', date('Y-m-d'))" required />
                        <x-input-error :messages="$errors->get('date')" class="mt-2" />
                    </div>

                    <div class="mb-4" x-show="transactionType === 'expense'" x-cloak>
                        <x-input-label for="loan_id" :value="__('Loan Payment')" />
                        <select id="loan_id" name="loan_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                            <option value="">-- Not a loan payment --</option>
                            @foreach (Auth::user()->loans()->where('type', 'borrow')->where('status', 'active')->orderBy('name')->get() as $loan)
                                <option value="{{ $loan->id }}" {{ old('loan_id') == $loan->id ? 'selected' : '' }}>
                                    {{ $loan->name }} (remaining: {{ number_format($loan->remaining_amount, 2) }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Only for expense transactions paying off a loan.</p>
                        <x-input-error :messages="$errors->get('loan_id')" class="mt-2" />
                    </div>

                    <div class="flex items-center gap-4 mt-6">
                        <x-primary-button>{{ __('Save Transaction') }}</x-primary-button>
                        <a href="{{ route('transactions.index') }}" class="text-gray-600 dark:text-gray-400 dark:hover:text-white text-sm">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
