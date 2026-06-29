<x-guest-layout>

    <div class="mb-7">
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

        <h2 class="font-heading font-bold text-navy" style="font-size:24px;">Daftar sebagai Dosen</h2>
        <p class="text-slate-400 mt-1 leading-relaxed" style="font-size:14px;">
            Akun Anda akan aktif setelah diverifikasi oleh Admin P3KM.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-5 flex items-start gap-2.5 bg-red-50 border border-red-100 rounded-lg px-4 py-3">
            <i class="ti ti-alert-circle text-red-500 mt-0.5 flex-shrink-0" style="font-size:18px;"></i>
            <ul class="text-red-600 space-y-0.5" style="font-size:14px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.dosen.store') }}" class="space-y-4">
        @csrf

        {{-- Nama --}}
        <div>
            <label class="block font-medium text-slate-700 mb-1.5" style="font-size:14px;">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <i class="ti ti-user text-slate-400" style="font-size:17px;"></i>
                </span>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                    placeholder="Dr. Nama Lengkap, M.T."
                    class="input-field pl-10 @error('name') border-red-300 @enderror">
            </div>
        </div>

        {{-- Email --}}
        <div>
            <label class="block font-medium text-slate-700 mb-1.5" style="font-size:14px;">
                Email Institusi <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <i class="ti ti-mail text-slate-400" style="font-size:17px;"></i>
                </span>
                <input type="email" name="email" value="{{ old('email') }}" required
                    placeholder="nama@institusi.ac.id"
                    class="input-field pl-10 @error('email') border-red-300 @enderror">
            </div>
        </div>

        {{-- NIDN & NIP --}}
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block font-medium text-slate-700 mb-1.5" style="font-size:14px;">
                    NIDN <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nidn" value="{{ old('nidn') }}" required
                    placeholder="10 digit"
                    class="input-field @error('nidn') border-red-300 @enderror">
            </div>
            <div>
                <label class="block font-medium text-slate-700 mb-1.5" style="font-size:14px;">
                    NIP <span class="text-slate-400 font-normal">(opsional)</span>
                </label>
                <input type="text" name="nip" value="{{ old('nip') }}"
                    placeholder="18 digit"
                    class="input-field @error('nip') border-red-300 @enderror">
            </div>
        </div>

        {{-- No HP --}}
        <div>
            <label class="block font-medium text-slate-700 mb-1.5" style="font-size:14px;">
                No. HP <span class="text-slate-400 font-normal">(opsional)</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <i class="ti ti-phone text-slate-400" style="font-size:17px;"></i>
                </span>
                <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                    placeholder="08xx-xxxx-xxxx"
                    class="input-field pl-10 @error('no_hp') border-red-300 @enderror">
            </div>
        </div>

        {{-- Program Studi --}}
        <div>
            <label class="block font-medium text-slate-700 mb-1.5" style="font-size:14px;">
                Program Studi <span class="text-red-500">*</span>
            </label>
            <select name="prodi_id" required
                class="input-field @error('prodi_id') border-red-300 @enderror">
                <option value="">-- Pilih Program Studi --</option>
                @foreach($fakultasList as $fakultas)
                    <optgroup label="{{ $fakultas->nama }}">
                        @foreach($fakultas->prodi as $prodi)
                            <option value="{{ $prodi->id }}" {{ old('prodi_id') == $prodi->id ? 'selected' : '' }}>
                                {{ $prodi->nama }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>

        {{-- Password --}}
        <div>
            <label class="block font-medium text-slate-700 mb-1.5" style="font-size:14px;">
                Password <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <i class="ti ti-lock text-slate-400" style="font-size:17px;"></i>
                </span>
                <input type="password" name="password" id="password" required
                    placeholder="Minimal 8 karakter"
                    class="input-field pl-10 pr-10 @error('password') border-red-300 @enderror">
                <button type="button" onclick="togglePass('password','eye1')"
                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 transition-colors">
                    <i id="eye1" class="ti ti-eye" style="font-size:17px;"></i>
                </button>
            </div>
        </div>

        {{-- Konfirmasi Password --}}
        <div>
            <label class="block font-medium text-slate-700 mb-1.5" style="font-size:14px;">
                Konfirmasi Password <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                    <i class="ti ti-lock-check text-slate-400" style="font-size:17px;"></i>
                </span>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    placeholder="Ulangi password"
                    class="input-field pl-10 pr-10">
                <button type="button" onclick="togglePass('password_confirmation','eye2')"
                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 transition-colors">
                    <i id="eye2" class="ti ti-eye" style="font-size:17px;"></i>
                </button>
            </div>
        </div>

        {{-- Tombol daftar --}}
        <button type="submit"
            class="w-full bg-navy text-white font-heading font-semibold py-3 rounded-lg hover:bg-navy-dark transition-colors flex items-center justify-center gap-2 mt-2"
            style="font-size:15px;">
            <i class="ti ti-user-plus" style="font-size:18px;"></i>
            Daftar Sekarang
        </button>

        <p class="text-center text-slate-400" style="font-size:13px;">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-navy font-medium hover:underline">Masuk di sini</a>
        </p>
    </form>

    {{-- Info proses --}}
    <div class="mt-6 bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 flex items-start gap-2.5">
        <i class="ti ti-info-circle text-amber-500 mt-0.5 flex-shrink-0" style="font-size:17px;"></i>
        <p class="text-amber-700 leading-relaxed" style="font-size:13px;">
            Setelah mendaftar, akun Anda akan diverifikasi oleh Admin P3KM. Anda akan dapat login setelah akun diaktifkan.
        </p>
    </div>

    <script>
    function togglePass(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        input.type  = input.type === 'password' ? 'text' : 'password';
        icon.className = input.type === 'password' ? 'ti ti-eye' : 'ti ti-eye-off';
    }
    </script>

</x-guest-layout>
