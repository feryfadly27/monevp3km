<div>
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
                   type="text" placeholder="Cari nama, NIDN, atau email..."
                   class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-[8px] focus:outline-none focus:border-navy transition" wire:loading.class="opacity-50"
                   style="font-size:14px;">
        </div>

        <select wire:model.live="filterProdi"
                class="border border-slate-200 rounded-[8px] px-3 py-2 text-navy focus:outline-none focus:border-navy"
                style="font-size:14px;">
            <option value="">Semua Prodi</option>
            @foreach($prodiAll as $p)
                <option value="{{ $p->id }}">{{ $p->nama }} — {{ $p->fakultas?->nama }}</option>
            @endforeach
        </select>

        <div class="ml-auto flex items-center gap-2">
            <a href="{{ route('dosen.import') }}"
               class="flex items-center gap-2 border border-slate-200 text-navy px-4 py-2 rounded-[8px] hover:bg-slate-50 transition"
               style="font-size:14px;">
                <i class="ti ti-file-upload" style="font-size:16px;"></i> Import CSV
            </a>
            <a href="{{ route('dosen.create') }}"
               class="flex items-center gap-2 bg-navy text-white px-4 py-2 rounded-[8px] hover:bg-navy-dark transition"
               style="font-size:14px;">
                <i class="ti ti-user-plus" style="font-size:16px;"></i> Tambah Dosen
            </a>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white border border-slate-200 rounded-[8px]">
        @if($dosenList->isEmpty())
            <div class="py-16 text-center text-slate-400">
                <i class="ti ti-user-off block mb-2" style="font-size:36px;"></i>
                <p style="font-size:15px;">Tidak ada dosen ditemukan</p>
            </div>
        @else
        <table class="data-table">
            <thead>
                <tr>
                    <th class="pl-5 w-10">#</th>
                    <th>Nama / NIDN</th>
                    <th>Prodi</th>
                    <th>Email</th>
                    <th>No. HP</th>
                    <th class="text-center">Akun</th>
                    <th class="text-center">Kegiatan</th>
                    <th class="pr-5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dosenList as $d)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="pl-5">
                        <div class="w-9 h-9 rounded-full bg-navy flex items-center justify-center font-semibold text-white"
                             style="font-size:13px;">
                            {{ strtoupper(substr($d->nama, 0, 2)) }}
                        </div>
                    </td>
                    <td>
                        <div class="font-medium text-navy" style="font-size:14px;">{{ $d->nama }}</div>
                        <div class="text-slate-400 font-mono" style="font-size:12px;">{{ $d->nidn }}</div>
                    </td>
                    <td>
                        <div class="text-navy" style="font-size:14px;">{{ $d->prodi?->nama ?? '—' }}</div>
                        <div class="text-slate-400" style="font-size:12px;">{{ $d->prodi?->fakultas?->nama ?? '' }}</div>
                    </td>
                    <td class="text-slate-500" style="font-size:13px;">
                        {{ $d->email ?? '—' }}
                    </td>
                    <td class="text-slate-500 font-mono" style="font-size:13px;">
                        {{ $d->no_hp ?? '—' }}
                    </td>
                    <td class="text-center">
                        @if($d->user_id)
                            <span class="chip chip-berjalan">
                                <i class="ti ti-check mr-0.5" style="font-size:12px;"></i>
                                {{ $d->user?->name ?? 'Terhubung' }}
                            </span>
                        @else
                            <span class="chip" style="background:#f1f5f9;color:#94a3b8;">Belum ada akun</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="flex items-center justify-center gap-2">
                            <span class="font-medium text-navy" style="font-size:14px;" title="Ketua kegiatan">
                                {{ $d->kegiatan_count }}
                                <span class="text-slate-400 font-normal" style="font-size:12px;">ketua</span>
                            </span>
                            @if($d->kegiatan_anggota_count > 0)
                            <span class="text-slate-400" style="font-size:12px;">
                                + {{ $d->kegiatan_anggota_count }} anggota
                            </span>
                            @endif
                        </div>
                    </td>
                    <td class="pr-5 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('dosen.edit', $d->id) }}"
                               class="p-1.5 text-slate-400 hover:text-navy hover:bg-slate-100 rounded transition"
                               title="Edit">
                                <i class="ti ti-edit" style="font-size:16px;"></i>
                            </a>
                            @if($d->kegiatan_count === 0)
                            <button wire:click="delete({{ $d->id }})"
                                    wire:confirm="Yakin hapus dosen {{ addslashes($d->nama) }}?"
                                    class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded transition"
                                    title="Hapus">
                                <i class="ti ti-trash" style="font-size:16px;"></i>
                            </button>
                            @else
                            <div class="p-1.5 text-slate-200 cursor-not-allowed" title="Tidak bisa hapus — masih memiliki kegiatan">
                                <i class="ti ti-trash" style="font-size:16px;"></i>
                            </div>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($dosenList->hasPages())
        <div class="px-5 py-3 border-t border-slate-100">
            {{ $dosenList->links() }}
        </div>
        @endif
        @endif
    </div>

    <div class="mt-3 text-slate-400" style="font-size:13px;">
        Total: <span class="font-medium text-navy">{{ $dosenList->total() }}</span> dosen
    </div>
</div>
