<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    @if (app()->environment('local'))
        <div class="mt-6 border-t border-gray-200 pt-6">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-gray-800">
                    Akun Uji Coba
                </h2>
                <span class="text-xs bg-amber-100 text-amber-800 font-semibold px-2 py-0.5 rounded">Demo Evaluasi</span>
            </div>

            <p class="mt-1 text-xs text-gray-600">
                Pilih peran untuk mencoba akses sesuai hak masing-masing.
            </p>

            <div class="mt-4 space-y-2.5">
                {{-- 1. Tombol Login Admin (Akses Penuh) --}}
                <form method="POST" action="{{ route('local.demo-login') }}">
                    @csrf
                    <input type="hidden" name="role" value="admin">
                    <button
                        type="submit"
                        class="w-full flex items-center justify-between rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-red-700 transition"
                    >
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Login sebagai Admin
                        </span>
                        <span class="text-xs bg-red-800 px-2 py-0.5 rounded font-normal">Akses Penuh & Hapus Data</span>
                    </button>
                </form>

                {{-- 2. Tombol Login Staf (Akses Terbatas) --}}
                <form method="POST" action="{{ route('local.demo-login') }}">
                    @csrf
                    <input type="hidden" name="role" value="staf">
                    <button
                        type="submit"
                        class="w-full flex items-center justify-between rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition"
                    >
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Login sebagai Staf
                        </span>
                        <span class="text-xs bg-blue-800 px-2 py-0.5 rounded font-normal">Akses Terbatas (Tanpa Hapus)</span>
                    </button>
                </form>

                <div class="grid grid-cols-2 gap-2 pt-1">
                    {{-- 3. Dosen --}}
                    <form method="POST" action="{{ route('local.demo-login') }}">
                        @csrf
                        <input type="hidden" name="role" value="dosen">
                        <button
                            type="submit"
                            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 text-center"
                        >
                            Akun Dosen
                        </button>
                    </form>

                    {{-- 4. Asisten Dosen --}}
                    <form method="POST" action="{{ route('local.demo-login') }}">
                        @csrf
                        <input type="hidden" name="role" value="asdos">
                        <button
                            type="submit"
                            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50 text-center"
                        >
                            Akun Asdos
                        </button>
                    </form>
                </div>

                {{-- 5. Mahasiswa --}}
                <form method="POST" action="{{ route('local.demo-login') }}">
                    @csrf
                    <input type="hidden" name="role" value="mahasiswa">
                    <button
                        type="submit"
                        class="w-full rounded-md bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700 text-center"
                    >
                        Login sebagai Mahasiswa (Hanif)
                    </button>
                </form>
            </div>
        </div>
    @endif
</x-guest-layout>
