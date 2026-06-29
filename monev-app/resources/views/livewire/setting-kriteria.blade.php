<div>
    {{-- Flash --}}
    @if(session('success_k'))
    <div data-flash class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-[8px] flex items-center gap-2" style="font-size:14px;">
        <i class="ti ti-circle-check text-lg"></i> {{ session('success_k') }}
    </div>
    @endif

    {{-- Header + total bobot --}}
    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <div class="px-3 py-1.5 rounded-[6px] border {{ abs($totalBobot - 100) < 0.01 ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-amber-50 border-amber-200 text-amber-700' }}"
                 style="font-size:13px;">
                <span class="font-semibold">Total bobot aktif: {{ number_format($totalBobot, 1) }}%</span>
                @if(abs($totalBobot - 100) < 0.01)
                    <i class="ti ti-circle-check ml-1"></i>
                @else
                    <span class="ml-1">— ideal 100%</span>
                @endif
            </div>
        </div>
        <button wire:click="openModal()"
                class="flex items-center gap-2 bg-navy text-white px-4 py-2 rounded-[8px] hover:bg-navy-dark transition"
                style="font-size:14px;">
            <i class="ti ti-plus" style="font-size:16px;"></i> Tambah Kriteria
        </button>
    </div>

    {{-- Tabel --}}
    <div class="bg-white border border-slate-200 rounded-[8px]">
        @if($kriteria->isEmpty())
            <div class="py-16 text-center text-slate-400">
                <i class="ti ti-list-details block mb-2" style="font-size:36px;"></i>
                <p style="font-size:15px;">Belum ada kriteria penilaian</p>
            </div>
        @else
        <table class="data-table">
            <thead>
                <tr>
                    <th class="pl-5 w-8">No</th>
                    <th>Nama Kriteria</th>
                    <th class="text-center w-28">Scope</th>
                    <th class="text-center w-20">Bobot</th>
                    <th class="text-center w-28">Rentang Skor</th>
                    <th class="text-center w-20">Status</th>
                    <th class="pr-5 text-right w-24">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kriteria as $i => $k)
                <tr class="hover:bg-slate-50 transition-colors {{ !$k->aktif ? 'opacity-50' : '' }}">
                    <td class="pl-5 text-slate-400" style="font-size:13px;">{{ $k->urutan ?: $i+1 }}</td>
                    <td>
                        <p class="font-medium text-navy" style="font-size:14px;">{{ $k->nama }}</p>
                        @if($k->scope !== 'GLOBAL')
                            <p class="text-slate-400" style="font-size:12px;">
                                {{ $k->scope === 'KATEGORI' ? $k->kategori?->nama : $k->skema?->nama }}
                            </p>
                        @endif
                    </td>
                    <td class="text-center">
                        <span class="chip {{ match($k->scope) { 'GLOBAL'=>'chip-selesai','KATEGORI'=>'chip-laporan','SKEMA'=>'chip-berjalan', default=>'' } }}"
                              style="font-size:11px;">
                            {{ $k->scope }}
                        </span>
                    </td>
                    <td class="text-center font-semibold text-navy" style="font-size:14px;">
                        {{ number_format($k->bobot, 1) }}%
                    </td>
                    <td class="text-center text-slate-500 font-mono" style="font-size:13px;">
                        {{ $k->skor_min }}–{{ $k->skor_max }}
                    </td>
                    <td class="text-center">
                        <button wire:click="toggleAktif({{ $k->id }})"
                                class="chip cursor-pointer hover:opacity-80 transition {{ $k->aktif ? 'chip-berjalan' : '' }}"
                                style="font-size:11px; {{ !$k->aktif ? 'background:#f1f5f9;color:#94a3b8;' : '' }}"
                                title="{{ $k->aktif ? 'Klik untuk nonaktifkan' : 'Klik untuk aktifkan' }}">
                            {{ $k->aktif ? 'Aktif' : 'Nonaktif' }}
                        </button>
                    </td>
                    <td class="pr-5 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <button wire:click="openModal({{ $k->id }})"
                                    class="p-1.5 text-slate-400 hover:text-navy hover:bg-slate-100 rounded transition" title="Edit">
                                <i class="ti ti-edit" style="font-size:16px;"></i>
                            </button>
                            <button wire:click="delete({{ $k->id }})"
                                    wire:confirm="Yakin hapus kriteria '{{ addslashes($k->nama) }}'?"
                                    class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded transition" title="Hapus">
                                <i class="ti ti-trash" style="font-size:16px;"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <p class="mt-3 text-slate-400" style="font-size:13px;">
        Total bobot aktif yang ideal adalah 100%. Kriteria nonaktif tidak dihitung dalam skor akhir.
    </p>

    {{-- Modal Tambah/Edit Kriteria --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="bg-white rounded-[10px] shadow-xl w-full max-w-md p-6 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between">
                <p class="font-semibold font-heading text-navy" style="font-size:15px;">
                    {{ $editId ? 'Edit Kriteria' : 'Tambah Kriteria Penilaian' }}
                </p>
                <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-navy">
                    <i class="ti ti-x" style="font-size:18px;"></i>
                </button>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Nama Kriteria <span class="text-red-500">*</span></label>
                    <input wire:model="nama" type="text"
                           class="input-field @error('nama') border-red-400 @enderror"
                           placeholder="Contoh: Kesesuaian pelaksanaan dengan proposal">
                    @error('nama')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Scope <span class="text-red-500">*</span></label>
                    <select wire:model.live="scope" class="input-field @error('scope') border-red-400 @enderror">
                        <option value="GLOBAL">Global — berlaku untuk semua kegiatan</option>
                        <option value="KATEGORI">Per Kategori — Penelitian / Pengmas</option>
                        <option value="SKEMA">Per Skema — skema tertentu</option>
                    </select>
                </div>

                @if($scope === 'KATEGORI')
                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Kategori <span class="text-red-500">*</span></label>
                    <select wire:model="kategoriId" class="input-field @error('kategoriId') border-red-400 @enderror">
                        <option value="">Pilih kategori...</option>
                        @foreach($kategoriAll as $kat)
                            <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                    @error('kategoriId')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                </div>
                @endif

                @if($scope === 'SKEMA')
                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Skema <span class="text-red-500">*</span></label>
                    <select wire:model="skemaId" class="input-field @error('skemaId') border-red-400 @enderror">
                        <option value="">Pilih skema...</option>
                        @foreach($skemaAll as $s)
                            <option value="{{ $s->id }}">{{ $s->nama }}</option>
                        @endforeach
                    </select>
                    @error('skemaId')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Bobot (%) <span class="text-red-500">*</span></label>
                        <input wire:model="bobot" type="number" step="0.01" min="0.01" max="100"
                               class="input-field font-mono @error('bobot') border-red-400 @enderror"
                               placeholder="25.00">
                        @error('bobot')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Urutan</label>
                        <input wire:model="urutan" type="number" min="0"
                               class="input-field font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Skor Min <span class="text-red-500">*</span></label>
                        <input wire:model="skorMin" type="number" min="0" max="99"
                               class="input-field font-mono @error('skorMin') border-red-400 @enderror">
                        @error('skorMin')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Skor Max <span class="text-red-500">*</span></label>
                        <input wire:model="skorMax" type="number" min="1" max="100"
                               class="input-field font-mono @error('skorMax') border-red-400 @enderror">
                        @error('skorMax')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input wire:model="aktif" type="checkbox" class="w-4 h-4 accent-navy">
                        <span class="text-navy" style="font-size:14px;">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
                <button wire:click="save" class="btn-primary">
                    <i class="ti ti-device-floppy mr-1"></i> Simpan
                </button>
                <button wire:click="$set('showModal', false)" class="btn-secondary">Batal</button>
            </div>
        </div>
    </div>
    @endif
</div>
