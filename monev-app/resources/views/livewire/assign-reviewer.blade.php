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

    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center gap-3 mb-5">
        <div class="relative flex-1 min-w-[200px]">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:16px;"></i>
            <input wire:model.live.debounce.300ms="search"
                   type="text" placeholder="Cari judul kegiatan..."
                   class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-[8px] focus:outline-none focus:border-navy transition"
                   style="font-size:14px;">
        </div>
        <select wire:model.live="filterStatus"
                class="border border-slate-200 rounded-[8px] px-3 py-2 text-navy focus:outline-none focus:border-navy"
                style="font-size:14px;">
            <option value="">Semua Status</option>
            <option value="TERDAFTAR">Terdaftar</option>
            <option value="BERJALAN">Berjalan</option>
            <option value="LAPORAN_MASUK">Laporan Masuk</option>
            <option value="DINILAI">Dinilai</option>
        </select>
        <select wire:model.live="filterTahun"
                class="border border-slate-200 rounded-[8px] px-3 py-2 text-navy focus:outline-none focus:border-navy"
                style="font-size:14px;">
            <option value="0">Semua Tahun</option>
            @foreach($tahunList as $t)
                <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
        </select>
    </div>

    {{-- Cards kegiatan --}}
    @if($kegiatan->isEmpty())
        <div class="py-16 text-center text-slate-400">
            <i class="ti ti-user-check block mb-2" style="font-size:36px;"></i>
            <p style="font-size:15px;">Tidak ada kegiatan ditemukan</p>
        </div>
    @else
    <div class="space-y-3">
        @foreach($kegiatan as $k)
        <div class="bg-white border border-slate-200 rounded-[8px] p-5">
            <div class="flex items-start justify-between gap-4">

                {{-- Info kegiatan --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="chip {{ $k->statusChip() }}">{{ $k->statusLabel() }}</span>
                        <span class="chip {{ $k->kategori?->kode === 'PENELITIAN' ? 'chip-terdaftar' : 'chip-selesai' }}" style="font-size:11px;">
                            {{ $k->kategori?->kode === 'PENELITIAN' ? 'Penelitian' : 'Pengmas' }}
                        </span>
                        <span class="text-slate-400 font-mono" style="font-size:12px;">{{ $k->tahun }}</span>
                    </div>
                    <h3 class="font-semibold text-navy font-heading truncate" style="font-size:15px;">{{ $k->judul }}</h3>
                    <p class="text-slate-400 mt-0.5" style="font-size:13px;">
                        {{ $k->skema->nama ?? '—' }} &nbsp;·&nbsp; Ketua: {{ $k->ketua->nama ?? '—' }}
                    </p>
                </div>

                {{-- Tombol assign --}}
                <button wire:click="openModal({{ $k->id }})"
                        class="flex-shrink-0 flex items-center gap-1.5 px-3 py-2 border border-navy text-navy rounded-[8px] hover:bg-navy hover:text-white transition"
                        style="font-size:13px;">
                    <i class="ti ti-user-plus" style="font-size:15px;"></i> Assign
                </button>
            </div>

            {{-- Reviewer yang sudah di-assign --}}
            @if($k->penugasanReviewer->isNotEmpty())
            <div class="mt-4 pt-3 border-t border-slate-100">
                <p class="text-slate-400 mb-2" style="font-size:12px;">REVIEWER DITUGASKAN</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($k->penugasanReviewer as $p)
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-[8px] border
                        {{ $p->status === 'SELESAI' ? 'border-emerald-200 bg-emerald-50' :
                           ($p->status === 'DALAM_PENILAIAN' ? 'border-purple-200 bg-purple-50' : 'border-slate-200 bg-slate-50') }}">

                        <div class="w-6 h-6 rounded-full bg-navy flex items-center justify-center text-white flex-shrink-0" style="font-size:10px;">
                            {{ strtoupper(substr($p->reviewer->name ?? 'R', 0, 2)) }}
                        </div>

                        <span style="font-size:13px;
                            color: {{ $p->status === 'SELESAI' ? '#0F6E56' : ($p->status === 'DALAM_PENILAIAN' ? '#3C3489' : '#0F172A') }}">
                            {{ $p->reviewer->name ?? '—' }}
                        </span>

                        <span class="chip {{ $p->status === 'SELESAI' ? 'chip-selesai' : ($p->status === 'DALAM_PENILAIAN' ? 'chip-dinilai' : 'chip-terdaftar') }}"
                              style="font-size:10px;">
                            {{ match($p->status) { 'MENUNGGU'=>'Menunggu','DALAM_PENILAIAN'=>'Menilai','SELESAI'=>'Selesai', default=>$p->status } }}
                        </span>

                        @if($p->status === 'MENUNGGU')
                        <button wire:click="remove({{ $p->id }})"
                                wire:confirm="Hapus penugasan reviewer ini?"
                                class="ml-1 text-slate-300 hover:text-red-400 transition"
                                title="Hapus penugasan">
                            <i class="ti ti-x" style="font-size:13px;"></i>
                        </button>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center gap-2 text-slate-400" style="font-size:13px;">
                <i class="ti ti-user-off" style="font-size:15px;"></i>
                Belum ada reviewer ditugaskan
            </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($kegiatan->hasPages())
    <div class="mt-4">{{ $kegiatan->links() }}</div>
    @endif
    @endif

    {{-- Modal Assign --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center"
         style="background: rgba(15,23,42,0.45);">
        <div class="bg-white rounded-[12px] w-full max-w-md mx-4 shadow-lg" wire:click.stop>

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h2 class="font-semibold font-heading text-navy" style="font-size:16px;">Assign Reviewer</h2>
                <button wire:click="closeModal" class="text-slate-400 hover:text-navy transition">
                    <i class="ti ti-x" style="font-size:18px;"></i>
                </button>
            </div>

            @if($activeKegiatanId)
            @php $kg = $kegiatan->firstWhere('id', $activeKegiatanId); @endphp
            @if($kg)
            <div class="px-6 py-3 bg-slate-50 border-b border-slate-100">
                <p class="font-medium text-navy truncate" style="font-size:14px;">{{ $kg->judul }}</p>
                <p class="text-slate-400" style="font-size:12px;">{{ $kg->skema->nama ?? '—' }} · {{ $kg->tahun }}</p>
            </div>
            @endif
            @endif

            <div class="px-6 py-5">
                <label class="block font-medium text-navy mb-2" style="font-size:14px;">
                    Pilih Reviewer <span class="text-red-500">*</span>
                </label>
                <select wire:model="selectedReviewer"
                        class="w-full border border-slate-200 rounded-[8px] px-3 py-2.5 focus:outline-none focus:border-navy transition"
                        style="font-size:14px;">
                    <option value="0">-- Pilih reviewer --</option>
                    @foreach($reviewerList as $rev)
                        <option value="{{ $rev->id }}"
                                {{ $assignedIds->contains($rev->id) ? 'disabled' : '' }}>
                            {{ $rev->name }}
                            {{ $assignedIds->contains($rev->id) ? '(sudah ditugaskan)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('selectedReviewer')
                    <p class="mt-1.5 text-red-500" style="font-size:13px;">{{ $message }}</p>
                @enderror

                @if($reviewerList->isEmpty())
                <p class="mt-2 text-amber-600" style="font-size:13px;">
                    <i class="ti ti-alert-triangle"></i>
                    Belum ada user dengan role reviewer. Tambahkan dulu di menu Kelola User.
                </p>
                @endif
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100">
                <button wire:click="closeModal"
                        class="px-4 py-2 border border-slate-200 text-slate-600 rounded-[8px] hover:bg-slate-50 transition"
                        style="font-size:14px;">Batal</button>
                <button wire:click="assign"
                        class="px-4 py-2 bg-navy text-white rounded-[8px] hover:bg-navy-dark transition flex items-center gap-1.5"
                        style="font-size:14px;">
                    <i class="ti ti-user-check" style="font-size:15px;"></i> Tugaskan
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
