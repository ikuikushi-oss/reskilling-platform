<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                    name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                    href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
    <div class="mt-6 border-t pt-4">
        <p class="text-xs text-gray-500 mb-2 text-center">Development Login Helpers (Remove in Prod)</p>
        <div class="grid grid-cols-1 gap-2">
            <button type="button" onclick="fillLogin('admin@example.com', 'password')"
                style="background-color: #1e293b; color: white;"
                class="w-full px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium rounded-md transition transform hover:scale-[1.02]">
                管理者 (Admin)
            </button>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" onclick="fillLogin('teacher1@example.com', 'password')"
                    class="w-full px-2 py-2 bg-blue-100 hover:bg-blue-200 text-blue-800 text-xs font-semibold rounded-md transition border border-blue-200">
                    講師:山田(Web)
                </button>
                <button type="button" onclick="fillLogin('teacher2@example.com', 'password')"
                    class="w-full px-2 py-2 bg-indigo-100 hover:bg-indigo-200 text-indigo-800 text-xs font-semibold rounded-md transition border border-indigo-200">
                    講師:鈴木(AI)
                </button>
                <button type="button" onclick="fillLogin('teacher3@example.com', 'password')"
                    class="w-full px-2 py-2 bg-teal-100 hover:bg-teal-200 text-teal-800 text-xs font-semibold rounded-md transition border border-teal-200">
                    講師:田中(DX)
                </button>
            </div>
            <button type="button" onclick="fillLogin('student_1_1@example.com', 'password')"
                class="w-full px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 text-sm font-medium rounded-md transition">
                生徒 (Student 1)
            </button>
        </div>
    </div>

    <script>
        function fillLogin(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
        }
    </script>
</x-guest-layout>