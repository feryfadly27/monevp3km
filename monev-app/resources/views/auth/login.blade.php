<x-guest-layout>

    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-2 mb-6 lg:hidden">
            <div class="w-8 h-8 rounded-lg bg-navy flex items-center justify-center">
                <i class="ti ti-chart-bar text-white" style="font-size:16px;"></i>
            </div>
            <span class="text-navy font-heading font-semibold" style="font-size:16px;">Monev P3KM</span>
        </div>
        <h2 class="font-heading font-bold text-navy" style="font-size:26px;">Masuk ke Sistem</h2>
        <p class="text-slate-400 mt-1" style="font-size:14px;">Gunakan akun yang telah diberikan oleh Admin P3KM.</p>
    </div>

    {{-- Session status (misal: setelah reset password) --}}
    @if (session('status'))
        <div class="mb-5 flex items-center gap-2.5 bg-sage/10 border border-sage/20 rounded-lg px-4 py-3">
            <i class="ti ti-circle-check text-sage" style="font-size:18px;"></i>
            <p class="text-sage font-medium" style="font-size:14px;">{{ session('status') }}</p>
        </div>
    @endif

    {{-- Error umum --}}
    @if ($errors->any())
        <div class="mb-5 flex items-start gap-2.5 bg-red-50 border border-red-100 rounded-lg px-4 py-3">
            <i class="ti ti-alert-circle text-red-500 mt-0.5" style="font-size:18px;"></i>
            <p class="text-red-600" style="font-size:14px;">Email atau password tidak sesuai. Silakan coba lagi.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block font-medium text-slate-700 mb-1.5" style="font-size:14px;">
                Alamat Email
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <i class="ti ti-mail text-slate-400" style="font-size:17px;"></i>
                </span>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="nama@lppm.ac.id"
                    class="input-field pl-10 @error('email') border-red-300 focus:border-red-400 focus:ring-red-100 @enderror"
                >
            </div>
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block font-medium text-slate-700 mb-1.5" style="font-size:14px;">
                Kata Sandi
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <i class="ti ti-lock text-slate-400" style="font-size:17px;"></i>
                </span>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="input-field pl-10 pr-10"
                >
                <button type="button"
                    onclick="togglePassword()"
                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 transition-colors">
                    <i id="eye-icon" class="ti ti-eye" style="font-size:17px;"></i>
                </button>
            </div>
        </div>

        {{-- Remember me + Lupa password --}}
        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" name="remember" id="remember_me"
                    class="rounded border-slate-300 text-navy focus:ring-navy/20">
                <span class="text-slate-600" style="font-size:14px;">Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-navy hover:text-navy/70 font-medium transition-colors"
                   style="font-size:14px;">
                    Lupa password?
                </a>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit"
            class="w-full bg-navy text-white font-heading font-semibold py-3 rounded-lg hover:bg-navy-dark transition-colors flex items-center justify-center gap-2"
            style="font-size:15px;">
            <i class="ti ti-login-2" style="font-size:18px;"></i>
            Masuk
        </button>
    </form>

    <p class="text-center text-slate-400 mt-8" style="font-size:13px;">
        Dosen baru?
        <a href="{{ route('register.dosen') }}" class="text-navy font-medium hover:underline">Daftar di sini</a>
    </p>
    <p class="text-center text-slate-300 mt-2" style="font-size:12px;">
        Hubungi Admin P3KM jika mengalami kendala akses.
    </p>

    <script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'ti ti-eye-off';
        } else {
            input.type = 'password';
            icon.className = 'ti ti-eye';
        }
    }
    </script>

</x-guest-layout>
