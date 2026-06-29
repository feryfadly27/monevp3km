<x-app-layout>
    <x-slot name="pageTitle">Tambah User</x-slot>
    <x-slot name="pageSubtitle">Buat akun baru dan tentukan role-nya</x-slot>

    <div class="max-w-lg">
        <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
            @csrf

            <div class="bg-white border border-slate-200 rounded-[8px] p-6 space-y-4">
                <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">Informasi Akun</p>

                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="input-field @error('name') border-red-400 @enderror" required autofocus>
                    @error('name')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="input-field @error('email') border-red-400 @enderror" required>
                    @error('email')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Password <span class="text-red-500">*</span></label>
                    <input type="password" name="password"
                           class="input-field @error('password') border-red-400 @enderror"
                           placeholder="Minimal 8 karakter" required>
                    @error('password')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">NIP</label>
                        <input type="text" name="nip" value="{{ old('nip') }}"
                               class="input-field @error('nip') border-red-400 @enderror"
                               placeholder="Nomor Induk Pegawai">
                        @error('nip')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">NIDN</label>
                        <input type="text" name="nidn" value="{{ old('nidn') }}"
                               class="input-field @error('nidn') border-red-400 @enderror"
                               placeholder="Nomor Induk Dosen Nasional">
                        @error('nidn')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Role <span class="text-red-500">*</span></label>
                    <select name="role" id="role_select"
                            class="input-field @error('role') border-red-400 @enderror"
                            onchange="toggleDosenField(this.value)" required>
                        <option value="">Pilih role...</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') === $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Link ke profil dosen (muncul jika role = dosen/reviewer) --}}
            <div id="dosen-field" class="bg-white border border-slate-200 rounded-[8px] p-6 space-y-3" style="display:none;">
                <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">
                    Hubungkan ke Profil Dosen
                    <span class="font-normal text-slate-400" style="font-size:13px;">(opsional)</span>
                </p>
                <p class="text-slate-400" style="font-size:13px;">
                    Jika user ini adalah dosen yang sudah terdaftar di sistem, pilih profilnya agar data kegiatan tersambung.
                </p>
                <select name="dosen_id" class="input-field">
                    <option value="">-- Tidak dihubungkan --</option>
                    @foreach($dosenTanpaAkun as $d)
                        <option value="{{ $d->id }}" {{ old('dosen_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->nama }} ({{ $d->nidn }})
                        </option>
                    @endforeach
                </select>
                @if($dosenTanpaAkun->isEmpty())
                    <p class="text-amber-600" style="font-size:13px;">Semua dosen sudah memiliki akun.</p>
                @endif
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary">
                    <i class="ti ti-user-plus mr-1"></i> Buat User
                </button>
                <a href="{{ route('users.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>

    <script>
    function toggleDosenField(role) {
        const show = role === 'dosen' || role === 'reviewer';
        document.getElementById('dosen-field').style.display = show ? 'block' : 'none';
    }
    toggleDosenField(document.getElementById('role_select').value);
    </script>
</x-app-layout>
