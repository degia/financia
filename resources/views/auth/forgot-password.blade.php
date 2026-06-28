<div>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Reset your password</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <div class="relative mt-1.5">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </span>
                <x-text-input id="email" class="pl-10 input-field" type="email" name="email" :value="old('email')" required autofocus placeholder="you@example.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit" class="btn-primary w-full justify-center py-3 text-sm font-semibold tracking-wide">
            {{ __('Send Reset Link') }}
        </button>

        <p class="text-center text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('login') }}" class="text-gray-900 dark:text-white font-semibold hover:underline underline-offset-2 transition-colors">Back to sign in</a>
        </p>
    </form>
</div>
