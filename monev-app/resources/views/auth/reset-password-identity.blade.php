<x-guest-layout>

    <div class="mb-8">
        <div class="flex items-center gap-2 mb-6 lg:hidden">
            <div class="w-8 h-8 rounded-lg bg-navy flex items-center justify-center">
                <i class="ti ti-chart-bar text-white" style="font-size:16px;"></i>
            </div>
            <span class="text-navy font-heading font-semibold" style="font-size:16px;">Monev P3KM</span>
        </div>

        {{-- Step indicator --}}
        <div class="flex items-center gap-2 mb-6">
            <div class="flex items-center justify-center w-7 h-7 rounded-full bg-sage text-white font-semibold" style="font-size:12px;">
                <i class="ti ti-check" style="font-size:14px;"></i>
            </div>
            <div class="h-px flex-1 bg-sage/40"></div>
            <div class="flex items-center justify-center w-7 h-7 rounded-full bg-sage text-white font-semibold" style="font-size:12px;">
                <i class="ti ti-check" style="font-size:14px;"></i>
            </div>
            <div class="h-px flex-1 bg-sage/40"></div>
            <div class="flex items-center justify-center w-7 h-7 rounded-full bg-navy text-white font-semibold" style="font-size:12px;">3</div>
        </div>

        <h2 class="font-heading font-bold text-navy" style="font-size:26px;">Buat Password Baru</h2>
        <p class="text-slate-400 mt-1" style="font-size:14px;">
            Identitas terverifikasi. Silakan buat password baru untuk akun
            <span class="text-navy font-medium">{{ $email }}</span>.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-5 flex items-start gap-2.5 bg-red-50 border border-red-100 rounded-lg px-4 py-3">
            <i class="ti ti-alert-circle text-red-500 mt-0.5" style="font-size:18px;"></i>
            <p class="text-red-600" style="font-size:14px;">{{ $errors->first() }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('password.identity.reset.post') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Password baru --}}
        <div>
            <label for="password" class="block font-medium text-slate-700 mb-1.5" style="font-size:14px;">
                Password Baru
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
                    autofocus
                    placeholder="Minimal 8 karakter"
                    class="input-field pl-10 pr-10 @error('password') border-red-300 @enderror"
                >
                <button type="button" onclick="togglePass('password','eye1')"
                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 transition-colors">
                    <i id="eye1" class="ti ti-eye" style="font-size:17px;"></i>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-red-500" style="font-size:13px;">{{ $message }}</p>
            @enderror
        </div>

        {{-- Konfirmasi password --}}
        <div>
            <label for="password_confirmation" class="block font-medium text-slate-700 mb-1.5" style="font-size:14px;">
                Konfirmasi Password
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <i class="ti ti-lock-check text-slate-400" style="font-size:17px;"></i>
                </span>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    placeholder="Ulangi password baru"
                    class="input-field pl-10 pr-10"
                >
                <button type="button" onclick="togglePass('password_confirmation','eye2')"
                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 transition-colors">
                    <i id="eye2" class="ti ti-eye" style="font-size:17px;"></i>
                </button>
            </div>
        </div>

        {{-- Indikator kekuatan password --}}
        <div id="strength-bar" class="h-1 rounded-full bg-slate-100 overflow-hidden hidden">
            <div id="strength-fill" class="h-full rounded-full transition-all duration-300 w-0"></div>
        </div>
        <p id="strength-label" class="text-slate-400 hidden" style="font-size:12px;"></p>

        <button type="submit"
            class="w-full bg-sage text-white font-heading font-semibold py-3 rounded-lg hover:bg-sage/90 transition-colors flex items-center justify-center gap-2"
            style="font-size:15px;">
            <i class="ti ti-lock-check" style="font-size:18px;"></i>
            Simpan Password Baru
        </button>
    </form>

    <script>
    function togglePass(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'ti ti-eye-off';
        } else {
            input.type = 'password';
            icon.className = 'ti ti-eye';
        }
    }

    document.getElementById('password').addEventListener('input', function () {
        const val = this.value;
        const bar  = document.getElementById('strength-bar');
        const fill = document.getElementById('strength-fill');
        const lbl  = document.getElementById('strength-label');

        if (!val) { bar.classList.add('hidden'); lbl.classList.add('hidden'); return; }
        bar.classList.remove('hidden'); lbl.classList.remove('hidden');

        let score = 0;
        if (val.length >= 8)  score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const levels = [
            { w: '25%', color: '#ef4444', label: 'Sangat lemah' },
            { w: '50%', color: '#f59e0b', label: 'Lemah' },
            { w: '75%', color: '#3b82f6', label: 'Cukup kuat' },
            { w: '100%', color: '#10b981', label: 'Kuat' },
        ];
        const lvl = levels[score - 1] || levels[0];
        fill.style.width = lvl.w;
        fill.style.backgroundColor = lvl.color;
        lbl.textContent = lvl.label;
        lbl.style.color = lvl.color;
    });
    </script>

</x-guest-layout>
