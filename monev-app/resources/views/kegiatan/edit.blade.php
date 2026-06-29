<x-app-layout>
    <x-slot name="pageTitle">Edit Kegiatan</x-slot>
    <x-slot name="pageSubtitle">{{ Str::limit($kegiatan->judul, 60) }}</x-slot>

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('kegiatan.update', $kegiatan->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="bg-white border border-slate-200 rounded-[8px] p-6 space-y-5">
                <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">
                    Informasi Kegiatan
                </p>

                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Judul Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul', $kegiatan->judul) }}"
                           class="input-field @error('judul') border-red-400 @enderror" required>
                    @error('judul')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Kategori <span class="text-red-500">*</span></label>
                        <select name="kategori_id" id="kategori_id"
                                class="input-field" onchange="filterSkema(this.value)" required>
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id }}"
                                    {{ old('kategori_id', $kegiatan->kategori_id) == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Skema <span class="text-red-500">*</span></label>
                        <select name="skema_id" id="skema_id" class="input-field" required>
                            @foreach($skema as $s)
                                <option value="{{ $s->id }}"
                                        data-kategori="{{ $s->kategori_id }}"
                                        {{ old('skema_id', $kegiatan->skema_id) == $s->id ? 'selected' : '' }}>
                                    {{ $s->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Ketua Kegiatan <span class="text-red-500">*</span></label>
                        <select name="ketua_dosen_id" class="input-field" required>
                            @foreach($dosen as $d)
                                <option value="{{ $d->id }}"
                                    {{ old('ketua_dosen_id', $kegiatan->ketua_dosen_id) == $d->id ? 'selected' : '' }}>
                                    {{ $d->nama }} ({{ $d->nidn }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Tahun Pelaksanaan <span class="text-red-500">*</span></label>
                        <input type="number" name="tahun" value="{{ old('tahun', $kegiatan->tahun) }}"
                               min="2000" max="2099" class="input-field" required>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-[8px] p-6 space-y-5">
                <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">
                    Pendanaan & Jadwal
                </p>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Sumber Dana</label>
                        <input type="text" name="sumber_dana" value="{{ old('sumber_dana', $kegiatan->sumber_dana) }}"
                               class="input-field">
                    </div>
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Jumlah Dana (Rp)</label>
                        <input type="number" name="jumlah_dana" value="{{ old('jumlah_dana', $kegiatan->jumlah_dana) }}"
                               min="0" step="100000" class="input-field font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai"
                               value="{{ old('tanggal_mulai', $kegiatan->tanggal_mulai?->format('Y-m-d')) }}"
                               class="input-field">
                    </div>
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai"
                               value="{{ old('tanggal_selesai', $kegiatan->tanggal_selesai?->format('Y-m-d')) }}"
                               class="input-field">
                    </div>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-[8px] p-6 space-y-5">
                <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">
                    Status & Catatan
                </p>

                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Status</label>
                    <select name="status" class="input-field">
                        @foreach(['TERDAFTAR','BERJALAN','LAPORAN_MASUK','DINILAI','SELESAI'] as $st)
                            <option value="{{ $st }}" {{ old('status', $kegiatan->status) === $st ? 'selected' : '' }}>
                                {{ $kegiatan->statusLabel() === $st ? $kegiatan->statusLabel() : Str::title(str_replace('_',' ',$st)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Catatan Admin</label>
                    <textarea name="catatan_admin" rows="3" class="input-field resize-none">{{ old('catatan_admin', $kegiatan->catatan_admin) }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary">
                    <i class="ti ti-device-floppy mr-1"></i> Simpan Perubahan
                </button>
                <a href="{{ route('kegiatan.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>

    <script>
        function filterSkema(kategoriId) {
            const select = document.getElementById('skema_id');
            const allOptions = @json($skema->map(fn($s) => ['id'=>$s->id,'nama'=>$s->nama,'kategori_id'=>$s->kategori_id]));
            select.innerHTML = '';
            allOptions.filter(s => s.kategori_id == kategoriId).forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.nama;
                if (s.id == {{ old('skema_id', $kegiatan->skema_id) }}) opt.selected = true;
                select.appendChild(opt);
            });
        }
    </script>
</x-app-layout>
