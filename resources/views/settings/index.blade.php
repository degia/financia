<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Settings</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="card p-4 mb-6 border-l-4 border-l-green-500 bg-green-50 dark:bg-green-900/20">
                    <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('settings.update') }}">
                @csrf @method('PATCH')

                {{-- General --}}
                <div class="card p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">General</h3>

                    <div class="mb-4">
                        <x-input-label for="currency_preference" :value="__('Default Currency')" />
                        <select id="currency_preference" name="currency_preference" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                            <option value="USD" {{ old('currency_preference', Auth::user()->currency_preference) == 'USD' ? 'selected' : '' }}>USD</option>
                            <option value="IDR" {{ old('currency_preference', Auth::user()->currency_preference) == 'IDR' ? 'selected' : '' }}>IDR</option>
                            <option value="EUR" {{ old('currency_preference', Auth::user()->currency_preference) == 'EUR' ? 'selected' : '' }}>EUR</option>
                            <option value="SGD" {{ old('currency_preference', Auth::user()->currency_preference) == 'SGD' ? 'selected' : '' }}>SGD</option>
                            <option value="MYR" {{ old('currency_preference', Auth::user()->currency_preference) == 'MYR' ? 'selected' : '' }}>MYR</option>
                        </select>
                        <x-input-error :messages="$errors->get('currency_preference')" class="mt-2" />
                    </div>
                </div>

                {{-- Navigation Menu Visibility --}}
                <div class="card p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Navigation Menu</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Show or hide menu items from the navigation bar.</p>

                    <div class="space-y-3">
                        @php $visible = Auth::user()->preference('menu_visibility', []); @endphp
                        @foreach ($menus as $key => $label)
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="menu_visibility[]" value="{{ $key }}"
                                    class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm focus:ring-gray-500 dark:focus:ring-gray-400"
                                    {{ ($visible[$key] ?? true) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Defaults --}}
                <div class="card p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Defaults</h3>

                    <div class="mb-4">
                        <x-input-label for="default_account_id" :value="__('Default Account')" />
                        <select id="default_account_id" name="default_account_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                            <option value="">-- No default --</option>
                            @foreach ($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('default_account_id', Auth::user()->preference('default_account_id')) == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Pre-selected when creating new transactions</p>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="default_income_category_id" :value="__('Default Income Category')" />
                        <select id="default_income_category_id" name="default_income_category_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                            <option value="">-- No default --</option>
                            @foreach (Auth::user()->categories()->where('type', 'income')->orderBy('name')->get() as $cat)
                                <option value="{{ $cat->id }}" {{ old('default_income_category_id', Auth::user()->preference('default_income_category_id')) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="default_expense_category_id" :value="__('Default Expense Category')" />
                        <select id="default_expense_category_id" name="default_expense_category_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 rounded-md shadow-sm focus:border-gray-500 dark:focus:border-gray-400 focus:ring-gray-500 dark:focus:ring-gray-400">
                            <option value="">-- No default --</option>
                            @foreach (Auth::user()->categories()->where('type', 'expense')->orderBy('name')->get() as $cat)
                                <option value="{{ $cat->id }}" {{ old('default_expense_category_id', Auth::user()->preference('default_expense_category_id')) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- WhatsApp Notification --}}
                <div class="card p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-4">WhatsApp Daily Report</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Kirim laporan keuangan harian otomatis ke WhatsApp setiap pukul 07:00 via <a href="https://fonnte.com" target="_blank" class="underline hover:text-gray-700 dark:hover:text-gray-300">Fonnte</a>.</p>

                    <div class="mb-4">
                        <x-input-label for="fonnte_token" :value="__('Fonnte Token')" />
                        <x-text-input id="fonnte_token" class="block mt-1 w-full" type="password" name="fonnte_token" :value="old('fonnte_token', Auth::user()->preference('fonnte_token'))" placeholder="Masukkan token dari fonnte.com" />
                        <x-input-error :messages="$errors->get('fonnte_token')" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="whatsapp_target" :value="__('WhatsApp Number')" />
                        <x-text-input id="whatsapp_target" class="block mt-1 w-full" type="text" name="whatsapp_target" :value="old('whatsapp_target', Auth::user()->preference('whatsapp_target'))" placeholder="62812xxxx" />
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Nomor tujuan dengan kode negara (62). Contoh: 628123456789</p>
                        <x-input-error :messages="$errors->get('whatsapp_target')" class="mt-2" />
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>{{ __('Save Settings') }}</x-primary-button>
                </div>
            </form>

            {{-- Institutions Management --}}
            <div class="card p-6 mt-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Institutions Management</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Manage bank, e-wallet, and other financial institution brands and logos.</p>
                <a href="{{ route('institutions.index') }}" class="btn-secondary text-sm">
                    Manage Institutions
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
