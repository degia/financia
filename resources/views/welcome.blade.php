<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="theme()"
      x-init="init()"
      :class="{ 'dark': isDark }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Financia') }} - Personal Finance Manager</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-white dark:bg-black overflow-hidden">
            <!-- Dark Mode Toggle -->
            <div class="fixed top-4 right-4 z-50">
                <button @click="toggle" class="p-2.5 rounded-xl bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border border-gray-200 dark:border-gray-800 shadow-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200" title="Toggle theme">
                    <svg x-show="!isDark" class="w-5 h-5 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="isDark" class="w-5 h-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>
            </div>

            <!-- Nav -->
            <nav class="relative z-40 border-b border-gray-100 dark:border-gray-900 bg-white/70 dark:bg-black/70 backdrop-blur-xl">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <div class="flex items-center gap-3">
                            <x-application-logo class="w-8 h-8 fill-current text-gray-900 dark:text-white" />
                            <span class="text-lg font-bold text-gray-900 dark:text-white">Financia</span>
                        </div>
                        <div class="flex items-center gap-4">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="btn-primary text-sm">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white font-medium transition-colors">Sign in</a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="btn-primary text-sm">Get started</a>
                                    @endif
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Hero -->
            <section class="relative">
                <div class="absolute inset-0 bg-gradient-to-b from-gray-50 to-white dark:from-black dark:to-black pointer-events-none"></div>
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[600px] bg-gradient-to-b from-gray-200/40 to-transparent dark:from-white/[0.02] dark:to-transparent rounded-full blur-3xl pointer-events-none"></div>
                <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 sm:pt-24 pb-20 sm:pb-28">
                    <div class="text-center max-w-3xl mx-auto">
                        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 mb-8">
                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                            Your finances, under control
                        </div>
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 dark:text-white leading-tight tracking-tight">
                            Take control of your
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-gray-600 to-gray-900 dark:from-gray-400 dark:to-white">financial future</span>
                        </h1>
                        <p class="mt-6 text-lg sm:text-xl text-gray-500 dark:text-gray-400 leading-relaxed max-w-2xl mx-auto">
                            Track expenses, manage budgets, set savings goals, and gain insights into your spending habits — all in one place.
                        </p>
                        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="btn-primary px-8 py-3 text-base">Go to Dashboard</a>
                                @else
                                    <a href="{{ route('register') }}" class="btn-primary px-8 py-3 text-base">Start for free</a>
                                    <a href="{{ route('login') }}" class="btn-secondary px-8 py-3 text-base">Sign in</a>
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features -->
            <section class="relative py-20 sm:py-28">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white">Everything you need</h2>
                        <p class="mt-4 text-gray-500 dark:text-gray-400 text-lg max-w-2xl mx-auto">Powerful tools to help you manage your personal finances with ease.</p>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                        <div class="group card p-6 sm:p-8 hover:shadow-md dark:hover:shadow-black/20 transition-all duration-200">
                            <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-5 group-hover:bg-gray-200 dark:group-hover:bg-gray-700 transition-colors">
                                <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Multi-Account Tracking</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">Manage multiple accounts — checking, savings, credit cards — all in one dashboard with real-time balance updates.</p>
                        </div>
                        <div class="group card p-6 sm:p-8 hover:shadow-md dark:hover:shadow-black/20 transition-all duration-200">
                            <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-5 group-hover:bg-gray-200 dark:group-hover:bg-gray-700 transition-colors">
                                <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Budget Management</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">Set monthly budgets by category, track progress in real-time, and get visual alerts when you're approaching your limits.</p>
                        </div>
                        <div class="group card p-6 sm:p-8 hover:shadow-md dark:hover:shadow-black/20 transition-all duration-200">
                            <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-5 group-hover:bg-gray-200 dark:group-hover:bg-gray-700 transition-colors">
                                <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Reports & Insights</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">Visualize your income and expenses with interactive charts. Export reports as CSV or PDF for deeper analysis.</p>
                        </div>
                        <div class="group card p-6 sm:p-8 hover:shadow-md dark:hover:shadow-black/20 transition-all duration-200">
                            <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-5 group-hover:bg-gray-200 dark:group-hover:bg-gray-700 transition-colors">
                                <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Expense Tracking</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">Log transactions quickly with categorized entries. Filter, search, and review your spending history at a glance.</p>
                        </div>
                        <div class="group card p-6 sm:p-8 hover:shadow-md dark:hover:shadow-black/20 transition-all duration-200">
                            <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-5 group-hover:bg-gray-200 dark:group-hover:bg-gray-700 transition-colors">
                                <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Savings Goals</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">Set and track financial goals. Monitor your progress with visual indicators and stay motivated to reach your targets.</p>
                        </div>
                        <div class="group card p-6 sm:p-8 hover:shadow-md dark:hover:shadow-black/20 transition-all duration-200">
                            <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-5 group-hover:bg-gray-200 dark:group-hover:bg-gray-700 transition-colors">
                                <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Internal Transfers</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">Move money between accounts seamlessly. Mark transfers as savings to track them as expenses for better budgeting.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA -->
            <section class="relative py-20">
                <div class="absolute inset-0 bg-gradient-to-t from-gray-50 to-white dark:from-black dark:to-black pointer-events-none"></div>
                <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <div class="card p-10 sm:p-14">
                        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white mb-4">Ready to take control?</h2>
                        <p class="text-lg text-gray-500 dark:text-gray-400 mb-8 max-w-xl mx-auto">Join Financia today and start managing your finances the smart way. It's free to get started.</p>
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn-primary px-8 py-3 text-base">Go to Dashboard</a>
                            @else
                                <a href="{{ route('register') }}" class="btn-primary px-8 py-3 text-base">Create your free account</a>
                            @endauth
                        @endif
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="border-t border-gray-100 dark:border-gray-900">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-2 text-sm text-gray-400 dark:text-gray-600">
                            <x-application-logo class="w-5 h-5 fill-current text-gray-400 dark:text-gray-600" />
                            <span>&copy; {{ date('Y') }} {{ config('app.name', 'Financia') }}. All rights reserved.</span>
                        </div>
                        <div class="flex items-center gap-6">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="text-sm text-gray-400 dark:text-gray-600 hover:text-gray-600 dark:hover:text-gray-400 transition-colors">Dashboard</a>
                                @else
                                    <a href="{{ route('login') }}" class="text-sm text-gray-400 dark:text-gray-600 hover:text-gray-600 dark:hover:text-gray-400 transition-colors">Sign in</a>
                                    <a href="{{ route('register') }}" class="text-sm text-gray-400 dark:text-gray-600 hover:text-gray-600 dark:hover:text-gray-400 transition-colors">Register</a>
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
