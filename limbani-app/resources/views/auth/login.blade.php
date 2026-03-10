<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Correo Electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-orange-500 outline-none @error('email') border-red-500 @enderror">
            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Password -->
        <div class="mt-6 space-y-2">
            <label for="password" class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Contraseña</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="w-full bg-gray-50 dark:bg-[#111] border border-gray-200 dark:border-white/10 rounded-xl p-4 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-orange-500 outline-none @error('password') border-red-500 @enderror">
            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Remember Me -->
        <div class="block mt-6">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded-sm bg-transparent border-gray-600 text-orange-600 shadow-sm focus:ring-orange-500" name="remember">
                <span class="ml-2 text-sm text-gray-400">{{ __('Recordarme') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-8">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-500 hover:text-gray-300 rounded-md" href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif

            <button type="submit" class="ml-4 py-3 px-8 rounded-xl bg-orange-600 text-white font-bold text-xs uppercase tracking-[0.2em] hover:bg-orange-700 transition-all shadow-lg shadow-orange-900/20">
                Ingresar
            </button>
        </div>
    </form>
</x-guest-layout>