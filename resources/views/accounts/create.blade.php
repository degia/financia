<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Create Account</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-6">
                <form method="POST" action="{{ route('accounts.store') }}">
                    @csrf

                    {{-- Institution Picker --}}
                    <div class="mb-6" x-data="institutionPicker()" x-init="init()">
                        <x-input-label value="Brand / Institution" />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Pick a brand below, or enter custom details manually.</p>

                        <input type="text" x-model="search" placeholder="Search institutions..." class="mb-3 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-lg shadow-sm text-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">

                        <input type="hidden" name="institution_id" x-model="selectedInstId">

                        <template x-for="(group, groupKey) in filtered" :key="groupKey">
                            <div class="mb-4">
                                <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2" x-text="group.label"></p>
                                <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                                    <template x-for="inst in group.items" :key="inst.id">
                                        <button type="button" @click="select(inst)"
                                            class="flex flex-col items-center gap-1.5 p-2.5 rounded-lg border-2 transition-all text-center"
                                            :class="selectedInstId === inst.id ? 'border-gray-900 dark:border-white bg-gray-50 dark:bg-gray-800' : 'border-transparent hover:border-gray-200 dark:hover:border-gray-700'">
                                            <div x-html="inst.svg"></div>
                                            <span class="text-[10px] text-gray-600 dark:text-gray-400 leading-tight" x-text="inst.name"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Account fields --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Account Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required placeholder="e.g. My Account" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="type" :value="__('Account Type')" />
                            <select id="type" name="type" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                                <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="bank" {{ old('type') == 'bank' ? 'selected' : '' }}>Bank Account</option>
                                <option value="ewallet" {{ old('type') == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                                <option value="credit_card" {{ old('type') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                <option value="savings" {{ old('type') == 'savings' ? 'selected' : '' }}>Savings</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="category" :value="__('Category')" />
                            <select id="category" name="category" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                                <option value="real" {{ old('category', 'real') == 'real' ? 'selected' : '' }}>Real</option>
                                <option value="savings" {{ old('category') == 'savings' ? 'selected' : '' }}>Savings</option>
                                <option value="subscriptions" {{ old('category') == 'subscriptions' ? 'selected' : '' }}>Subscriptions</option>
                            </select>
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="initial_balance" :value="__('Initial Balance')" />
                            <x-text-input id="initial_balance" class="block mt-1 w-full" type="number" step="0.01" name="initial_balance" :value="old('initial_balance', '0')" />
                            <x-input-error :messages="$errors->get('initial_balance')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="currency" :value="__('Currency')" />
                            <select id="currency" name="currency" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                                <option value="USD" {{ old('currency', Auth::user()->currency_preference) == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="IDR" {{ old('currency', Auth::user()->currency_preference) == 'IDR' ? 'selected' : '' }}>IDR</option>
                                <option value="EUR" {{ old('currency', Auth::user()->currency_preference) == 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="SGD" {{ old('currency', Auth::user()->currency_preference) == 'SGD' ? 'selected' : '' }}>SGD</option>
                                <option value="MYR" {{ old('currency', Auth::user()->currency_preference) == 'MYR' ? 'selected' : '' }}>MYR</option>
                            </select>
                            <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="color" :value="__('Color')" />
                            <input id="color" type="color" name="color" value="{{ old('color', '#6366F1') }}" class="block mt-1 w-16 h-10 rounded border-gray-300 dark:border-gray-700 dark:bg-gray-800 cursor-pointer">
                            <x-input-error :messages="$errors->get('color')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-6">
                        <x-primary-button>{{ __('Save Account') }}</x-primary-button>
                        <a href="{{ route('accounts.index') }}" class="text-gray-600 dark:text-gray-400 dark:hover:text-white text-sm">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('institutionPicker', () => ({
                search: '',
                selectedInstId: {{ old('institution_id') ?: 'null' }},
                institutions: @json($institutions),

                init() {
                    if (this.selectedInstId) {
                        const inst = this.institutions.find(i => i.id === this.selectedInstId);
                        if (inst) this.select(inst);
                    }
                },

                get typeLabels() {
                    return { cash: 'Cash', bank: 'Banks', ewallet: 'E-Wallets', credit_card: 'Credit Cards', savings: 'Savings', other: 'Other' };
                },

                get grouped() {
                    const groups = {};
                    this.institutions.forEach(inst => {
                        const label = this.typeLabels[inst.type] || 'Other';
                        if (!groups[label]) groups[label] = [];
                        groups[label].push(inst);
                    });
                    return groups;
                },

                get filtered() {
                    const query = this.search.toLowerCase();
                    const result = [];
                    Object.entries(this.grouped).forEach(([label, items]) => {
                        const filtered = query
                            ? items.filter(i => i.name.toLowerCase().includes(query))
                            : items;
                        if (filtered.length > 0) {
                            result.push({
                                label,
                                items: filtered.map(i => ({ ...i, svg: this.renderLogo(i) }))
                            });
                        }
                    });
                    return result;
                },

                renderLogo(inst) {
                    if (inst.logo_url) {
                        return `<img src="${inst.logo_url}" alt="${inst.name}" class="w-9 h-9 object-contain rounded-md">`;
                    }
                    const s = inst.name.length > 3 ? inst.name.substring(0, 2) : inst.name;
                    return `<svg width="36" height="36" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg"><rect width="36" height="36" rx="7" fill="${inst.color}"/><text x="18" y="24" text-anchor="middle" fill="#fff" font-size="13" font-weight="bold" font-family="Arial,sans-serif">${s}</text></svg>`;
                },

                select(inst) {
                    this.selectedInstId = inst.id;
                    document.getElementById('name').value = inst.name;
                    document.getElementById('type').value = inst.type;
                    document.getElementById('color').value = inst.color;
                },
            }));
        });
    </script>
    @endpush
</x-app-layout>
