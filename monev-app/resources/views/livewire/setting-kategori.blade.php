<div>
    @if(session('success_kategori'))
    <div data-flash class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-[8px] flex items-center gap-2" style="font-size:14px;">
        <i class="ti ti-circle-check text-lg"></i> {{ session('success_kategori') }}
    </div>
    @endif

    <div class="max-w-lg">
        <div class="bg-white border border-slate-200 rounded-[8px]">
            <div class="px-5 py-4 border-b border-slate-100">
                <p class="font-semibold font-heading text-navy" style="font-size:15px;">Kategori Kegiatan</p>
                <p class="text-slate-400 mt-0.5" style="font-size:13px;">
                    Kategori digunakan untuk mengelompokkan kegiatan (Penelitian / Pengabdian Masyarakat). Nama kategori dapat disesuaikan.
                </p>
            </div>

            @if($kategoriAll->isEmpty())
                <div class="py-10 text-center text-slate-400" style="font-size:14px;">Belum ada data kategori</div>
            @else
            <ul class="divide-y divide-slate-100">
                @foreach($kategoriAll as $kat)
                <li class="flex items-center justify-between px-5 py-4 hover:bg-slate-50 transition">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-mono font-medium
                                     {{ $kat->kode === 'PENELITIAN' ? 'bg-blue-50 text-blue-700' : 'bg-purple-50 text-purple-700' }}"
                              style="font-size:11px;">
                            {{ $kat->kode }}
                        </span>
                        <p class="font-medium text-navy" style="font-size:14px;">{{ $kat->nama }}</p>
                    </div>
                    <button wire:click="openModal({{ $kat->id }})"
                            class="p-1.5 text-slate-400 hover:text-navy hover:bg-slate-100 rounded transition" title="Edit Nama">
                        <i class="ti ti-edit" style="font-size:15px;"></i>
                    </button>
                </li>
                @endforeach
            </ul>
            @endif

            <div class="px-5 py-3 border-t border-slate-100 bg-slate-50 rounded-b-[8px]">
                <p class="text-slate-400" style="font-size:12px;">
                    <i class="ti ti-info-circle mr-1"></i>
                    Kode kategori bersifat tetap (PENELITIAN / PENGMAS). Hanya nama tampilan yang dapat diubah.
                </p>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="bg-white rounded-[10px] shadow-xl w-full max-w-sm p-6 space-y-4">
            <div class="flex items-center justify-between">
                <p class="font-semibold font-heading text-navy" style="font-size:15px;">Edit Nama Kategori</p>
                <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-navy">
                    <i class="ti ti-x" style="font-size:18px;"></i>
                </button>
            </div>
            <div>
                <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Nama Kategori <span class="text-red-500">*</span></label>
                <input wire:model="nama" type="text" class="input-field @error('nama') border-red-400 @enderror"
                       placeholder="Contoh: Penelitian Dasar" autofocus>
                @error('nama')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-3 pt-1">
                <button wire:click="save" class="btn-primary">
                    <i class="ti ti-device-floppy mr-1"></i> Simpan
                </button>
                <button wire:click="$set('showModal', false)" class="btn-secondary">Batal</button>
            </div>
        </div>
    </div>
    @endif
</div>
