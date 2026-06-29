<div>
    {{-- Flash --}}
    @if(session('success'))
    <div data-flash class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-[8px] flex items-center gap-2" style="font-size:14px;">
        <i class="ti ti-circle-check text-lg"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div data-flash class="mb-4 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-[8px] flex items-center gap-2" style="font-size:14px;">
        <i class="ti ti-alert-circle text-lg"></i> {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Daftar Fakultas --}}
        <div class="bg-white border border-slate-200 rounded-[8px]">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <p class="font-semibold font-heading text-navy" style="font-size:15px;">Fakultas</p>
                <button wire:click="openFakModal()"
                        class="flex items-center gap-1.5 bg-navy text-white px-3 py-1.5 rounded-[6px] hover:bg-navy-dark transition"
                        style="font-size:13px;">
                    <i class="ti ti-plus" style="font-size:14px;"></i> Tambah
                </button>
            </div>
            @if($fakultasAll->isEmpty())
                <div class="py-10 text-center text-slate-400" style="font-size:14px;">Belum ada data</div>
            @else
            <ul class="divide-y divide-slate-100">
                @foreach($fakultasAll as $fak)
                <li class="flex items-center justify-between px-5 py-3 hover:bg-slate-50 transition">
                    <div>
                        <p class="font-medium text-navy" style="font-size:14px;">{{ $fak->nama }}</p>
                        <p class="text-slate-400" style="font-size:12px;">{{ $fak->prodi->count() }} program studi</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <button wire:click="openFakModal({{ $fak->id }})"
                                class="p-1.5 text-slate-400 hover:text-navy hover:bg-slate-100 rounded transition" title="Edit">
                            <i class="ti ti-edit" style="font-size:15px;"></i>
                        </button>
                        <button wire:click="deleteFakultas({{ $fak->id }})"
                                wire:confirm="Yakin hapus fakultas {{ addslashes($fak->nama) }}?"
                                class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded transition" title="Hapus">
                            <i class="ti ti-trash" style="font-size:15px;"></i>
                        </button>
                    </div>
                </li>
                @endforeach
            </ul>
            @endif
        </div>

        {{-- Daftar Prodi --}}
        <div class="bg-white border border-slate-200 rounded-[8px]">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <p class="font-semibold font-heading text-navy" style="font-size:15px;">Program Studi</p>
                <button wire:click="openProdiModal()"
                        class="flex items-center gap-1.5 bg-navy text-white px-3 py-1.5 rounded-[6px] hover:bg-navy-dark transition"
                        style="font-size:13px;">
                    <i class="ti ti-plus" style="font-size:14px;"></i> Tambah
                </button>
            </div>
            @php $allProdi = $fakultasAll->flatMap->prodi @endphp
            @if($allProdi->isEmpty())
                <div class="py-10 text-center text-slate-400" style="font-size:14px;">Belum ada data</div>
            @else
            @foreach($fakultasAll as $fak)
                @if($fak->prodi->isNotEmpty())
                <div class="px-5 pt-3 pb-1">
                    <p class="font-medium text-slate-400 uppercase tracking-widest" style="font-size:11px;">{{ $fak->nama }}</p>
                </div>
                <ul class="divide-y divide-slate-100">
                    @foreach($fak->prodi as $p)
                    <li class="flex items-center justify-between px-5 py-2.5 hover:bg-slate-50 transition">
                        <p class="text-navy" style="font-size:14px;">{{ $p->nama }}</p>
                        <div class="flex items-center gap-1">
                            <button wire:click="openProdiModal({{ $p->id }})"
                                    class="p-1.5 text-slate-400 hover:text-navy hover:bg-slate-100 rounded transition" title="Edit">
                                <i class="ti ti-edit" style="font-size:15px;"></i>
                            </button>
                            <button wire:click="deleteProdi({{ $p->id }})"
                                    wire:confirm="Yakin hapus prodi {{ addslashes($p->nama) }}?"
                                    class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded transition" title="Hapus">
                                <i class="ti ti-trash" style="font-size:15px;"></i>
                            </button>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
            @endforeach
            @endif
        </div>
    </div>

    {{-- Modal Fakultas --}}
    @if($showFakModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="bg-white rounded-[10px] shadow-xl w-full max-w-sm p-6 space-y-4">
            <div class="flex items-center justify-between">
                <p class="font-semibold font-heading text-navy" style="font-size:15px;">
                    {{ $editFakId ? 'Edit Fakultas' : 'Tambah Fakultas' }}
                </p>
                <button wire:click="$set('showFakModal', false)" class="text-slate-400 hover:text-navy">
                    <i class="ti ti-x" style="font-size:18px;"></i>
                </button>
            </div>
            <div>
                <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Nama Fakultas <span class="text-red-500">*</span></label>
                <input wire:model="fakNama" type="text" class="input-field @error('fakNama') border-red-400 @enderror"
                       placeholder="Contoh: Fakultas Teknik" autofocus>
                @error('fakNama')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-3 pt-1">
                <button wire:click="saveFakultas" class="btn-primary">
                    <i class="ti ti-device-floppy mr-1"></i> Simpan
                </button>
                <button wire:click="$set('showFakModal', false)" class="btn-secondary">Batal</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Prodi --}}
    @if($showProdiModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="bg-white rounded-[10px] shadow-xl w-full max-w-sm p-6 space-y-4">
            <div class="flex items-center justify-between">
                <p class="font-semibold font-heading text-navy" style="font-size:15px;">
                    {{ $editProdiId ? 'Edit Program Studi' : 'Tambah Program Studi' }}
                </p>
                <button wire:click="$set('showProdiModal', false)" class="text-slate-400 hover:text-navy">
                    <i class="ti ti-x" style="font-size:18px;"></i>
                </button>
            </div>
            <div>
                <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Fakultas <span class="text-red-500">*</span></label>
                <select wire:model="prodiAFak" class="input-field @error('prodiAFak') border-red-400 @enderror">
                    <option value="">Pilih fakultas...</option>
                    @foreach($fakultasAll as $fak)
                        <option value="{{ $fak->id }}">{{ $fak->nama }}</option>
                    @endforeach
                </select>
                @error('prodiAFak')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Nama Program Studi <span class="text-red-500">*</span></label>
                <input wire:model="prodiNama" type="text" class="input-field @error('prodiNama') border-red-400 @enderror"
                       placeholder="Contoh: Teknik Informatika">
                @error('prodiNama')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-3 pt-1">
                <button wire:click="saveProdi" class="btn-primary">
                    <i class="ti ti-device-floppy mr-1"></i> Simpan
                </button>
                <button wire:click="$set('showProdiModal', false)" class="btn-secondary">Batal</button>
            </div>
        </div>
    </div>
    @endif
</div>
