<x-app-layout>
    <x-slot name="pageTitle">Tambah Kegiatan</x-slot>
    <x-slot name="pageSubtitle">Input kegiatan baru ke dalam sistem monev</x-slot>

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('kegiatan.store') }}" class="space-y-5">
            @csrf

            <div class="bg-white border border-slate-200 rounded-[8px] p-6 space-y-5">
                <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">
                    Informasi Kegiatan
                </p>

                {{-- Judul --}}
                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Judul Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul') }}"
                           class="input-field @error('judul') border-red-400 @enderror"
                           placeholder="Judul lengkap kegiatan..." required>
                    @error('judul')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                </div>

                {{-- Kategori + Skema --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Kategori <span class="text-red-500">*</span></label>
                        <select name="kategori_id" id="kategori_id"
                                class="input-field @error('kategori_id') border-red-400 @enderror"
                                onchange="filterSkema(this.value)" required>
                            <option value="">Pilih kategori...</option>
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('kategori_id')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Skema <span class="text-red-500">*</span></label>
                        <select name="skema_id" id="skema_id"
                                class="input-field @error('skema_id') border-red-400 @enderror" required>
                            <option value="">Pilih kategori dulu...</option>
                            @foreach($skema as $s)
                                <option value="{{ $s->id }}"
                                        data-kategori="{{ $s->kategori_id }}"
                                        {{ old('skema_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('skema_id')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Ketua + Tahun --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Ketua Kegiatan <span class="text-red-500">*</span></label>
                        <select name="ketua_dosen_id"
                                class="input-field @error('ketua_dosen_id') border-red-400 @enderror" required>
                            <option value="">Pilih dosen...</option>
                            @foreach($dosen as $d)
                                <option value="{{ $d->id }}" {{ old('ketua_dosen_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->nama }} ({{ $d->nidn }})
                                </option>
                            @endforeach
                        </select>
                        @error('ketua_dosen_id')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Tahun Pelaksanaan <span class="text-red-500">*</span></label>
                        <input type="number" name="tahun" value="{{ old('tahun', date('Y')) }}"
                               min="2000" max="2099"
                               class="input-field @error('tahun') border-red-400 @enderror" required>
                        @error('tahun')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-[8px] p-6 space-y-5">
                <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">
                    Pendanaan & Jadwal
                </p>

                {{-- Sumber + Jumlah dana --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Sumber Dana</label>
                        <input type="text" name="sumber_dana" value="{{ old('sumber_dana') }}"
                               class="input-field" placeholder="mis. Internal LPPM, Hibah Kemdikbud...">
                    </div>
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Jumlah Dana (Rp)</label>
                        <input type="number" name="jumlah_dana" value="{{ old('jumlah_dana', 0) }}"
                               min="0" step="100000"
                               class="input-field font-mono @error('jumlah_dana') border-red-400 @enderror">
                        @error('jumlah_dana')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Tanggal --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}"
                               class="input-field">
                    </div>
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}"
                               class="input-field">
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-[8px] p-6 space-y-5">
                <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">
                    Catatan Admin
                </p>
                <textarea name="catatan_admin" rows="3"
                          class="input-field resize-none"
                          placeholder="Catatan internal admin (tidak terlihat oleh dosen)...">{{ old('catatan_admin') }}</textarea>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary">
                    <i class="ti ti-device-floppy mr-1"></i> Simpan Kegiatan
                </button>
                <a href="{{ route('kegiatan.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>

    <script>
        function filterSkema(kategoriId) {
            const select = document.getElementById('skema_id');
            const options = select.querySelectorAll('option');
            select.innerHTML = '<option value="">Pilih skema...</option>';
            options.forEach(opt => {
                if (opt.dataset.kategori == kategoriId) {
                    select.appendChild(opt.cloneNode(true));
                }
            });
        }
        // Init on page load if old value exists
        const oldKategori = "{{ old('kategori_id') }}";
        if (oldKategori) filterSkema(oldKategori);
    </script>
</x-app-layout>
