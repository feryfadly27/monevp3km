<div>
    {{-- Flash message --}}
    @if(session('success'))
    <div data-flash class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-[8px] flex items-center gap-2" style="font-size:14px;">
        <i class="ti ti-circle-check text-lg"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center gap-3 mb-5">

        {{-- Search --}}
        <div class="relative flex-1 min-w-[200px]">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:16px;"></i>
            <input wire:model.live.debounce.300ms="search"
                   type="text" placeholder="Cari judul atau ketua..."
                   class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-[8px] focus:outline-none focus:border-navy transition" wire:loading.class="opacity-50"
                   style="font-size:14px;">
        </div>

        {{-- Filter Status --}}
        <select wire:model.live="filterStatus"
                class="border border-slate-200 rounded-[8px] px-3 py-2 text-navy focus:outline-none focus:border-navy transition"
                style="font-size:14px;">
            <option value="">Semua Status</option>
            <option value="TERDAFTAR">Terdaftar</option>
            <option value="BERJALAN">Berjalan</option>
            <option value="LAPORAN_MASUK">Laporan Masuk</option>
            <option value="DINILAI">Dinilai</option>
            <option value="SELESAI">Selesai</option>
        </select>

        {{-- Filter Skema --}}
        <select wire:model.live="filterSkema"
                class="border border-slate-200 rounded-[8px] px-3 py-2 text-navy focus:outline-none focus:border-navy transition"
                style="font-size:14px;">
            <option value="">Semua Skema</option>
            @foreach($skemaList as $s)
                <option value="{{ $s->id }}">{{ $s->nama }}</option>
            @endforeach
        </select>

        {{-- Filter Tahun --}}
        <select wire:model.live="filterTahun"
                class="border border-slate-200 rounded-[8px] px-3 py-2 text-navy focus:outline-none focus:border-navy transition"
                style="font-size:14px;">
            <option value="0">Semua Tahun</option>
            @foreach($tahunList as $t)
                <option value="{{ $t }}">{{ $t }}</option>
            @endforeach
        </select>

        <div class="ml-auto flex items-center gap-2">
            <a href="{{ route('kegiatan.import') }}"
               class="flex items-center gap-2 border border-slate-200 text-navy px-4 py-2 rounded-[8px] hover:bg-slate-50 transition"
               style="font-size:14px;">
                <i class="ti ti-file-upload" style="font-size:16px;"></i> Import CSV
            </a>
            <a href="{{ route('kegiatan.create') }}"
               class="flex items-center gap-2 bg-navy text-white px-4 py-2 rounded-[8px] hover:bg-navy-dark transition"
               style="font-size:14px;">
                <i class="ti ti-plus" style="font-size:16px;"></i> Tambah Kegiatan
            </a>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white border border-slate-200 rounded-[8px]">
        @if($kegiatan->isEmpty())
            <div class="py-16 text-center text-slate-400">
                <i class="ti ti-database-off block mb-2" style="font-size:36px;"></i>
                <p style="font-size:15px;">Belum ada kegiatan ditemukan</p>
                @if($search || $filterStatus || $filterSkema || $filterTahun)
                    <button wire:click="$set('search',''); $set('filterStatus',''); $set('filterSkema',''); $set('filterTahun',0)"
                            class="mt-3 text-sage hover:underline" style="font-size:14px;">Reset filter</button>
                @endif
            </div>
        @else
        <div class="overflow-x-auto">
            <table class="data-table px-5">
                <thead>
                    <tr class="px-5">
                        <th class="pl-5 w-[30%]">Judul</th>
                        <th>Kategori</th>
                        <th>Skema</th>
                        <th>Ketua</th>
                        <th>Tahun</th>
                        <th>Dana</th>
                        <th>Status</th>
                        <th>Skor</th>
                        <th class="pr-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($kegiatan as $k)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="pl-5 font-medium" style="font-size:14px;">
                            {{ Str::limit($k->judul, 48) }}
                        </td>
                        <td>
                            <span class="chip {{ $k->kategori?->kode === 'PENELITIAN' ? 'chip-terdaftar' : 'chip-selesai' }}" style="font-size:11px;">
                                {{ $k->kategori?->kode === 'PENELITIAN' ? 'Penelitian' : 'Pengmas' }}
                            </span>
                        </td>
                        <td class="text-slate-400" style="font-size:13px;">{{ $k->skema->nama ?? '—' }}</td>
                        <td class="text-slate-400" style="font-size:13px;">{{ $k->ketua->nama ?? '—' }}</td>
                        <td class="font-mono text-slate-500" style="font-size:13px;">{{ $k->tahun }}</td>
                        <td class="font-mono text-slate-400" style="font-size:13px;">
                            Rp {{ number_format($k->jumlah_dana / 1000000, 0) }} jt
                        </td>
                        <td><span class="chip {{ $k->statusChip() }}">{{ $k->statusLabel() }}</span></td>
                        <td class="font-mono font-medium {{ $k->skor_final ? 'text-sage' : 'text-slate-300' }}" style="font-size:14px;">
                            {{ $k->skor_final ? number_format($k->skor_final, 1) : '—' }}
                        </td>
                        <td class="pr-5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('kegiatan.show', $k->id) }}"
                                   class="p-1.5 text-slate-400 hover:text-navy hover:bg-slate-100 rounded transition"
                                   title="Detail">
                                    <i class="ti ti-eye" style="font-size:16px;"></i>
                                </a>
                                <a href="{{ route('kegiatan.edit', $k->id) }}"
                                   class="p-1.5 text-slate-400 hover:text-navy hover:bg-slate-100 rounded transition"
                                   title="Edit">
                                    <i class="ti ti-edit" style="font-size:16px;"></i>
                                </a>
                                <button wire:click="delete({{ $k->id }})"
                                        wire:confirm="Yakin hapus kegiatan ini?"
                                        class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded transition"
                                        title="Hapus">
                                    <i class="ti ti-trash" style="font-size:16px;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($kegiatan->hasPages())
        <div class="px-5 py-3 border-t border-slate-100">
            {{ $kegiatan->links() }}
        </div>
        @endif
        @endif
    </div>

    {{-- Info total --}}
    <div class="mt-3 text-slate-400" style="font-size:13px;">
        Total: <span class="font-medium text-navy">{{ $kegiatan->total() }}</span> kegiatan
        @if($search || $filterStatus || $filterSkema || $filterTahun)
            &nbsp;·&nbsp; Filter aktif
        @endif
    </div>
</div>
