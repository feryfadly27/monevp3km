<x-app-layout>
    <x-slot name="pageTitle">Edit Dosen</x-slot>
    <x-slot name="pageSubtitle">{{ $dosen->nama }}</x-slot>

    <div class="max-w-lg">
        <form method="POST" action="{{ route('dosen.update', $dosen->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Data Pribadi --}}
            <div class="bg-white border border-slate-200 rounded-[8px] p-6 space-y-4">
                <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">Data Pribadi</p>

                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama', $dosen->nama) }}"
                           class="input-field @error('nama') border-red-400 @enderror" required>
                    @error('nama')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">NIDN <span class="text-red-500">*</span></label>
                    <input type="text" name="nidn" value="{{ old('nidn', $dosen->nidn) }}"
                           class="input-field font-mono @error('nidn') border-red-400 @enderror" required maxlength="20">
                    @error('nidn')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Email</label>
                        <input type="email" name="email" value="{{ old('email', $dosen->email) }}"
                               class="input-field @error('email') border-red-400 @enderror"
                               placeholder="email@kampus.ac.id">
                        @error('email')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">No. HP</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp', $dosen->no_hp) }}"
                               class="input-field font-mono @error('no_hp') border-red-400 @enderror"
                               placeholder="08xxxxxxxxxx" maxlength="20">
                        @error('no_hp')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            {{-- Prodi --}}
            <div class="bg-white border border-slate-200 rounded-[8px] p-6 space-y-4">
                <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">Unit Akademik</p>

                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Program Studi <span class="text-red-500">*</span></label>
                    <select name="prodi_id" class="input-field @error('prodi_id') border-red-400 @enderror" required>
                        @foreach($fakultasAll as $fak)
                            <optgroup label="{{ $fak->nama }}">
                                @foreach($fak->prodi as $p)
                                    <option value="{{ $p->id }}"
                                        {{ old('prodi_id', $dosen->prodi_id) == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('prodi_id')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Link Akun --}}
            <div class="bg-white border border-slate-200 rounded-[8px] p-6 space-y-3">
                <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">
                    Akun User Terhubung
                    <span class="font-normal text-slate-400" style="font-size:13px;">(opsional)</span>
                </p>
                <select name="user_id" class="input-field @error('user_id') border-red-400 @enderror">
                    <option value="">-- Tidak dihubungkan --</option>
                    @foreach($usersAvailable as $u)
                        <option value="{{ $u->id }}"
                            {{ old('user_id', $dosen->user_id) == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ $u->email }})
                            @if($u->id === $dosen->user_id) — terhubung saat ini @endif
                        </option>
                    @endforeach
                </select>
                @error('user_id')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
            </div>

            {{-- Info kegiatan --}}
            @php
                $jmlKetua   = $dosen->kegiatan()->count();
                $jmlAnggota = $dosen->kegiatanAnggota()->count();
            @endphp
            @if($jmlKetua > 0 || $jmlAnggota > 0)
            <div class="bg-slate-50 border border-slate-200 rounded-[8px] px-5 py-4 flex items-center gap-4" style="font-size:13px;">
                <i class="ti ti-info-circle text-slate-400" style="font-size:20px;"></i>
                <span class="text-slate-500">
                    Dosen ini terlibat dalam
                    <span class="font-medium text-navy">{{ $jmlKetua }} kegiatan</span> sebagai ketua
                    @if($jmlAnggota > 0)
                        dan <span class="font-medium text-navy">{{ $jmlAnggota }} kegiatan</span> sebagai anggota.
                    @else.@endif
                    Data tidak bisa dihapus selama masih ada kegiatan aktif.
                </span>
            </div>
            @endif

            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary">
                    <i class="ti ti-device-floppy mr-1"></i> Simpan Perubahan
                </button>
                <a href="{{ route('dosen.index') }}" class="btn-secondary">Batal</a>
                @if($jmlKetua === 0)
                <button type="button"
                    onclick="document.getElementById('form-hapus-dosen').submit()"
                    class="flex items-center gap-1.5 px-4 py-2.5 text-red-500 border border-red-200 rounded-[8px] hover:bg-red-50 transition ml-auto"
                    style="font-size:14px;">
                    <i class="ti ti-trash" style="font-size:15px;"></i> Hapus
                </button>
                @endif
            </div>
        </form>

        {{-- Form hapus di luar form update agar tidak bersarang --}}
        @if($jmlKetua === 0)
        <form id="form-hapus-dosen" method="POST" action="{{ route('dosen.destroy', $dosen->id) }}"
              onsubmit="return confirm('Yakin hapus dosen {{ addslashes($dosen->nama) }}? Tindakan ini tidak dapat dibatalkan.')">
            @csrf @method('DELETE')
        </form>
        @endif
    </div>
</x-app-layout>
