<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">Institutions</h2>
            <a href="{{ route('institutions.create') }}" class="btn-primary">+ New Institution</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="card p-4 mb-6 border-l-4 border-l-green-500 bg-green-50 dark:bg-green-900/20">
                    <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="card p-4 mb-6 border-l-4 border-l-red-500 bg-red-50 dark:bg-red-900/20">
                    <p class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</p>
                </div>
            @endif

            @if ($institutions->isEmpty())
                <div class="text-center py-12 card">
                    <p class="text-gray-500 dark:text-gray-400 mb-4">No institutions yet.</p>
                    <a href="{{ route('institutions.create') }}" class="btn-primary">+ Create Institution</a>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @foreach ($institutions as $inst)
                        <div class="card p-4 hover:shadow-md dark:hover:border-gray-700 transition-all">
                            <div class="flex flex-col items-center text-center gap-3">
                                @if ($inst->logo_url)
                                    <img src="{{ $inst->logo_url }}" alt="{{ $inst->name }}" class="w-14 h-14 object-contain rounded-lg">
                                @else
                                    <svg width="56" height="56" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                                        <rect width="40" height="40" rx="8" fill="{{ $inst->color }}"/>
                                        <text x="20" y="27" text-anchor="middle" fill="#fff" font-size="14" font-weight="bold" font-family="Arial,sans-serif">{{ substr($inst->name, 0, 2) }}</text>
                                    </svg>
                                @endif
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $inst->name }}</h3>
                                    @php
                                        $labels = ['cash'=>'Cash','bank'=>'Bank Account','ewallet'=>'E-Wallet','credit_card'=>'Credit Card','savings'=>'Savings','other'=>'Other'];
                                        $colors = ['cash'=>'text-green-600','bank'=>'text-blue-600','ewallet'=>'text-orange-600','credit_card'=>'text-red-600','savings'=>'text-violet-600','other'=>'text-gray-500'];
                                    @endphp
                                    <span class="text-xs font-medium {{ $colors[$inst->type] ?? 'text-gray-500' }}">{{ $labels[$inst->type] ?? $inst->type }}</span>
                                    @if (!$inst->is_active)
                                        <span class="text-xs text-gray-400 ml-1">(inactive)</span>
                                    @endif
                                </div>
                                <div class="flex gap-2 mt-1">
                                    <a href="{{ route('institutions.edit', $inst) }}" class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white underline">Edit</a>
                                    <form method="POST" action="{{ route('institutions.destroy', $inst) }}" onsubmit="return confirm('Delete this institution?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 underline">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
