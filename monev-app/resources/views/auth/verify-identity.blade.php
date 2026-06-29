<x-guest-layout>

    <div class="mb-8">
        <div class="flex items-center gap-2 mb-6 lg:hidden">
            <div class="w-8 h-8 rounded-lg bg-navy flex items-center justify-center">
                <i class="ti ti-chart-bar text-white" style="font-size:16px;"></i>
            </div>
            <span class="text-navy font-heading font-semibold" style="font-size:16px;">Monev P3KM</span>
        </div>

        <a href="{{ route('password.request') }}" class="inline-flex items-center gap-1.5 text-slate-400 hover:text-slate-600 transition-colors mb-5" style="font-size:14px;">
            <i class="ti ti-arrow-left" style="font-size:15px;"></i>
            Kembali
        </a>

        {{-- Step indicator --}}
        <div class="flex items-center gap-2 mb-6">
            <div class="flex items-center justify-center w-7 h-7 rounded-full bg-sage text-white font-semibold" style="font-size:12px;">
                <i class="ti ti-check" style="font-size:14px;"></i>
            </div>
            <div class="h-px flex-1 bg-sage/40"></div>
            <div class="flex items-center justify-center w-7 h-7 rounded-full bg-navy text-white font-semibold" style="font-size:12px;">2</div>
            <div class="h-px flex-1 bg-slate-200"></div>
            <div class="flex items-center justify-center w-7 h-7 rounded-full bg-slate-200 text-slate-400 font-semibold" style="font-size:12px;">3</div>
        </div>

        <h2 class="font-heading font-bold text-navy" style="font-size:26px;">Verifikasi Identitas</h2>
        <p class="text-slate-400 mt-1 leading-relaxed" style="font-size:14px;">
            Masukkan <strong class="text-slate-600">NIP</strong> atau <strong class="text-slate-600">NIDN</strong> yang terdaftar pada akun
            <span class="text-navy font-medium">{{ $email }}</span>.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-5 flex items-start gap-2.5 bg-red-50 border border-red-100 rounded-lg px-4 py-3">
            <i class="ti ti-alert-circle text-red-500 mt-0.5" style="font-size:18px;"></i>
            <p class="text-red-600" style="font-size:14px;">{{ $errors->first() }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('password.identity.verify.post') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="identity" class="block font-medium text-slate-700 mb-1.5" style="font-size:14px;">
                NIP atau NIDN
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <i class="ti ti-id-badge text-slate-400" style="font-size:17px;"></i>
                </span>
                <input
                    id="identity"
                    type="text"
                    name="identity"
                    value="{{ old('identity') }}"
                    required
                    autofocus
                    placeholder="Masukkan NIP atau NIDN Anda"
                    class="input-field pl-10 @error('identity') border-red-300 @enderror"
                    autocomplete="off"
                >
            </div>
            @error('identity')
                <p class="mt-1.5 text-red-500" style="font-size:13px;">{{ $message }}</p>
            @enderror
            <p class="mt-1.5 text-slate-400" style="font-size:13px;">
                Masukkan salah satu: NIP (18 digit) atau NIDN (10 digit) yang sesuai dengan akun Anda.
            </p>
        </div>

        <button type="submit"
            class="w-full bg-navy text-white font-heading font-semibold py-3 rounded-lg hover:bg-navy-dark transition-colors flex items-center justify-center gap-2"
            style="font-size:15px;">
            <i class="ti ti-shield-check" style="font-size:18px;"></i>
            Verifikasi
        </button>
    </form>

    <div class="mt-8 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 flex items-start gap-2.5">
        <i class="ti ti-clock text-amber-500 mt-0.5 flex-shrink-0" style="font-size:17px;"></i>
        <p class="text-amber-700" style="font-size:13px;">
            Sesi verifikasi ini berlaku selama <strong>30 menit</strong>. Lewat dari itu, Anda perlu mengulang dari awal.
        </p>
    </div>

</x-guest-layout>
