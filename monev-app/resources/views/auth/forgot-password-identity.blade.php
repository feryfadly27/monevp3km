<x-guest-layout>

    <div class="mb-8">
        <div class="flex items-center gap-2 mb-6 lg:hidden">
            <div class="w-8 h-8 rounded-lg bg-navy flex items-center justify-center">
                <i class="ti ti-chart-bar text-white" style="font-size:16px;"></i>
            </div>
            <span class="text-navy font-heading font-semibold" style="font-size:16px;">Monev P3KM</span>
        </div>

        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-slate-400 hover:text-slate-600 transition-colors mb-5" style="font-size:14px;">
            <i class="ti ti-arrow-left" style="font-size:15px;"></i>
            Kembali ke Login
        </a>

        <h2 class="font-heading font-bold text-navy" style="font-size:26px;">Lupa Password</h2>
        <p class="text-slate-400 mt-1 leading-relaxed" style="font-size:14px;">
            Masukkan email akun Anda. Kami akan meminta verifikasi NIP atau NIDN sebelum reset password.
        </p>
    </div>

    @if (session('status'))
        <div class="mb-5 flex items-start gap-2.5 bg-sage/10 border border-sage/20 rounded-lg px-4 py-3">
            <i class="ti ti-info-circle text-sage mt-0.5" style="font-size:18px;"></i>
            <p class="text-sage" style="font-size:14px;">{{ session('status') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

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
                    placeholder="nama@p3km.ac.id"
                    class="input-field pl-10 @error('email') border-red-300 @enderror"
                >
            </div>
            @error('email')
                <p class="mt-1.5 text-red-500" style="font-size:13px;">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="w-full bg-navy text-white font-heading font-semibold py-3 rounded-lg hover:bg-navy-dark transition-colors flex items-center justify-center gap-2"
            style="font-size:15px;">
            <i class="ti ti-shield-check" style="font-size:18px;"></i>
            Lanjut Verifikasi
        </button>
    </form>

    {{-- Petunjuk --}}
    <div class="mt-8 bg-slate-50 border border-slate-200 rounded-lg px-4 py-4">
        <p class="font-medium text-slate-600 mb-2" style="font-size:13px;">Langkah reset password:</p>
        <ol class="space-y-1.5 text-slate-400" style="font-size:13px; list-style: decimal; padding-left: 16px;">
            <li>Masukkan email akun Anda</li>
            <li>Verifikasi dengan NIP atau NIDN yang terdaftar</li>
            <li>Buat password baru</li>
        </ol>
    </div>

</x-guest-layout>
