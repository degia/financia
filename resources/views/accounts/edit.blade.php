<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Edit Account</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-6">
                <form method="POST" action="{{ route('accounts.update', $account) }}">
                    @csrf @method('PUT')

                    {{-- Institution Picker --}}
                    <div class="mb-6" x-data="institutionPicker()" x-init="init()">
                        <x-input-label value="Brand / Institution" />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Pick a brand, or enter custom details below.</p>

                        <input type="text" x-model="search" placeholder="Search banks &amp; e-wallets..." class="mb-3 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-lg shadow-sm text-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">

                        <input type="hidden" name="icon" x-model="selectedIcon">

                        <template x-for="(group, groupKey) in filtered" :key="groupKey">
                            <div class="mb-4">
                                <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2" x-text="group.label"></p>
                                <div class="grid grid-cols-4 sm:grid-cols-6 gap-2">
                                    <template x-for="(inst, slug) in group.items" :key="slug">
                                        <button type="button" @click="select(slug)"
                                            class="flex flex-col items-center gap-1.5 p-2.5 rounded-lg border-2 transition-all text-center"
                                            :class="selected === slug ? 'border-gray-900 dark:border-white bg-gray-50 dark:bg-gray-800' : 'border-transparent hover:border-gray-200 dark:hover:border-gray-700'">
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
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $account->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="type" :value="__('Account Type')" />
                            <select id="type" name="type" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                                <option value="cash" {{ old('type', $account->type) == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="bank" {{ old('type', $account->type) == 'bank' ? 'selected' : '' }}>Bank Account</option>
                                <option value="ewallet" {{ old('type', $account->type) == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                                <option value="credit_card" {{ old('type', $account->type) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                <option value="savings" {{ old('type', $account->type) == 'savings' ? 'selected' : '' }}>Savings</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="category" :value="__('Category')" />
                            <select id="category" name="category" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                                <option value="real" {{ old('category', $account->category) == 'real' ? 'selected' : '' }}>Real</option>
                                <option value="savings" {{ old('category', $account->category) == 'savings' ? 'selected' : '' }}>Savings</option>
                                <option value="subscriptions" {{ old('category', $account->category) == 'subscriptions' ? 'selected' : '' }}>Subscriptions</option>
                            </select>
                            <x-input-error :messages="$errors->get('category')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="currency" :value="__('Currency')" />
                            <select id="currency" name="currency" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                                <option value="USD" {{ old('currency', $account->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="IDR" {{ old('currency', $account->currency) == 'IDR' ? 'selected' : '' }}>IDR</option>
                                <option value="EUR" {{ old('currency', $account->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="SGD" {{ old('currency', $account->currency) == 'SGD' ? 'selected' : '' }}>SGD</option>
                                <option value="MYR" {{ old('currency', $account->currency) == 'MYR' ? 'selected' : '' }}>MYR</option>
                            </select>
                            <x-input-error :messages="$errors->get('currency')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="color" :value="__('Color')" />
                            <input id="color" type="color" name="color" value="{{ old('color', $account->color ?? '#6366F1') }}" class="block mt-1 w-16 h-10 rounded border-gray-300 dark:border-gray-700 dark:bg-gray-800 cursor-pointer">
                            <x-input-error :messages="$errors->get('color')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-6">
                        <x-primary-button>{{ __('Update Account') }}</x-primary-button>
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
                selected: '{{ old('icon', $account->icon) }}',
                selectedIcon: '{{ old('icon', $account->icon) }}',

                init() {
                    if (this.selectedIcon) {
                        this.selected = this.selectedIcon;
                    }
                },

                groups: @json($institutions),

                get filtered() {
                    const result = [];
                    const query = this.search.toLowerCase();

                    const addGroup = (key, label, items) => {
                        const entries = Object.entries(items);
                        const filtered = query ? entries.filter(([slug, inst]) =>
                            slug.includes(query) || inst.name.toLowerCase().includes(query)
                        ) : entries;
                        if (filtered.length > 0) {
                            result.push({
                                label,
                                items: Object.fromEntries(filtered.map(([slug, inst]) => [
                                    slug,
                                    {...inst, svg: this.renderSvg(inst.color, inst.monogram)}
                                ]))
                            });
                        }
                    };

                    addGroup('banks', 'Banks', this.groups.banks);
                    addGroup('ewallets', 'E-Wallets', this.groups.ewallets);
                    addGroup('cash', 'Cash', this.groups.cash);

                    return result;
                },

                renderSvg(color, text) {
                    const s = text.length > 3 ? text.substring(0, 2) : text;
                    return `<svg width="36" height="36" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg"><rect width="36" height="36" rx="7" fill="${color}"/><text x="18" y="24" text-anchor="middle" fill="#fff" font-size="13" font-weight="bold" font-family="Arial,sans-serif">${s}</text></svg>`;
                },

                select(slug) {
                    this.selected = slug;
                    const all = {...this.groups.banks, ...this.groups.ewallets, ...this.groups.cash};
                    const inst = all[slug];
                    if (inst) {
                        this.selectedIcon = slug;
                        document.getElementById('name').value = inst.name;
                        const type = this.groups.banks[slug] ? 'bank' : (this.groups.ewallets[slug] ? 'ewallet' : 'cash');
                        document.getElementById('type').value = type;
                        document.getElementById('color').value = inst.color;
                    }
                },
            }));
        });
    </script>
    @endpush
</x-app-layout>
