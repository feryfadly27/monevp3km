<x-app-layout>
    <x-slot name="pageTitle">Edit User</x-slot>
    <x-slot name="pageSubtitle">{{ $user->name }}</x-slot>

    <div class="max-w-lg">
        <form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="bg-white border border-slate-200 rounded-[8px] p-6 space-y-4">
                <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">Informasi Akun</p>

                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="input-field @error('name') border-red-400 @enderror" required>
                    @error('name')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="input-field @error('email') border-red-400 @enderror" required>
                    @error('email')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Password Baru</label>
                    <input type="password" name="password"
                           class="input-field @error('password') border-red-400 @enderror"
                           placeholder="Kosongkan jika tidak ingin mengubah">
                    @error('password')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                    <p class="mt-1 text-slate-400" style="font-size:12px;">Minimal 8 karakter. Biarkan kosong untuk mempertahankan password lama.</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">NIP</label>
                        <input type="text" name="nip" value="{{ old('nip', $user->nip) }}"
                               class="input-field @error('nip') border-red-400 @enderror"
                               placeholder="Nomor Induk Pegawai">
                        @error('nip')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">NIDN</label>
                        <input type="text" name="nidn" value="{{ old('nidn', $user->nidn) }}"
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
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}"
                                {{ old('role', $user->roles->first()?->name) === $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Profil dosen --}}
            <div id="dosen-field" class="bg-white border border-slate-200 rounded-[8px] p-6 space-y-3">
                <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">
                    Hubungkan ke Profil Dosen
                    <span class="font-normal text-slate-400" style="font-size:13px;">(opsional)</span>
                </p>
                <select name="dosen_id" class="input-field">
                    <option value="">-- Tidak dihubungkan --</option>
                    @if($user->dosen)
                        <option value="{{ $user->dosen->id }}" selected>
                            {{ $user->dosen->nama }} ({{ $user->dosen->nidn }}) — terhubung saat ini
                        </option>
                    @endif
                    @foreach($dosenTanpaAkun as $d)
                        <option value="{{ $d->id }}" {{ old('dosen_id') == $d->id ? 'selected' : '' }}>
                            {{ $d->nama }} ({{ $d->nidn }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary">
                    <i class="ti ti-device-floppy mr-1"></i> Simpan Perubahan
                </button>
                <a href="{{ route('users.index') }}" class="btn-secondary">Batal</a>
                @if($user->id !== auth()->id())
                <button type="button" onclick="document.getElementById('form-hapus-user').submit()"
                    class="flex items-center gap-1.5 px-4 py-2.5 text-red-500 border border-red-200 rounded-[8px] hover:bg-red-50 transition ml-auto"
                    style="font-size:14px;"
                    onclick="return confirm('Yakin hapus user ini?')">
                    <i class="ti ti-trash" style="font-size:15px;"></i> Hapus User
                </button>
                @endif
            </div>
        </form>

        {{-- Form hapus di luar form update agar tidak bersarang --}}
        @if($user->id !== auth()->id())
        <form id="form-hapus-user" method="POST" action="{{ route('users.destroy', $user->id) }}"
              onsubmit="return confirm('Yakin ingin menghapus user ini? Tindakan ini tidak dapat dibatalkan.')">
            @csrf @method('DELETE')
        </form>
        @endif
    </div>

    <script>
    function toggleDosenField(role) {
        const show = role === 'dosen' || role === 'reviewer';
        document.getElementById('dosen-field').style.display = show ? 'block' : 'none';
    }
    toggleDosenField(document.getElementById('role_select').value);
    </script>
</x-app-layout>
