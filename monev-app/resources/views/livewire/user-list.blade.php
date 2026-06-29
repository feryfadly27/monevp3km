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

    {{-- Tab Status --}}
    <div class="flex items-center gap-1 mb-5 border-b border-slate-200">
        <button wire:click="$set('filterStatus','active')"
            class="px-4 py-2.5 font-medium border-b-2 transition-colors -mb-px {{ $filterStatus === 'active' ? 'border-navy text-navy' : 'border-transparent text-slate-400 hover:text-slate-600' }}"
            style="font-size:14px;">
            <i class="ti ti-users mr-1.5"></i>User Aktif
        </button>
        <button wire:click="$set('filterStatus','pending')"
            class="px-4 py-2.5 font-medium border-b-2 transition-colors -mb-px flex items-center gap-2 {{ $filterStatus === 'pending' ? 'border-amber-500 text-amber-600' : 'border-transparent text-slate-400 hover:text-slate-600' }}"
            style="font-size:14px;">
            <i class="ti ti-clock"></i>Menunggu Aktivasi
            @if($pendingCount > 0)
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-500 text-white font-bold" style="font-size:11px;">
                    {{ $pendingCount > 9 ? '9+' : $pendingCount }}
                </span>
            @endif
        </button>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-wrap items-center gap-3 mb-5">
        <div class="relative flex-1 min-w-[200px]">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" style="font-size:16px;"></i>
            <input wire:model.live.debounce.300ms="search"
                   type="text" placeholder="Cari nama atau email..."
                   class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-[8px] focus:outline-none focus:border-navy transition"
                   wire:loading.class="opacity-50" style="font-size:14px;">
        </div>

        @if($filterStatus === 'active')
        <select wire:model.live="filterRole"
                class="border border-slate-200 rounded-[8px] px-3 py-2 text-navy focus:outline-none focus:border-navy"
                style="font-size:14px;">
            <option value="">Semua Role</option>
            <option value="admin">Admin</option>
            <option value="reviewer">Reviewer</option>
            <option value="dosen">Dosen</option>
        </select>
        @endif

        <div class="ml-auto flex items-center gap-2">
            <a href="{{ route('users.import') }}"
               class="flex items-center gap-2 border border-slate-200 text-navy px-4 py-2 rounded-[8px] hover:bg-slate-50 transition"
               style="font-size:14px;">
                <i class="ti ti-file-upload" style="font-size:16px;"></i> Import CSV
            </a>
            <a href="{{ route('users.create') }}"
               class="flex items-center gap-2 bg-navy text-white px-4 py-2 rounded-[8px] hover:bg-navy-dark transition"
               style="font-size:14px;">
                <i class="ti ti-user-plus" style="font-size:16px;"></i> Tambah User
            </a>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="bg-white border border-slate-200 rounded-[8px]">
        @if($users->isEmpty())
            <div class="py-16 text-center text-slate-400">
                <i class="ti ti-{{ $filterStatus === 'pending' ? 'clock-off' : 'users-off' }} block mb-2" style="font-size:36px;"></i>
                <p style="font-size:15px;">
                    {{ $filterStatus === 'pending' ? 'Tidak ada pendaftaran yang menunggu aktivasi' : 'Tidak ada user ditemukan' }}
                </p>
            </div>
        @else
        <table class="data-table">
            <thead>
                <tr>
                    <th class="pl-5 w-8">#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>{{ $filterStatus === 'pending' ? 'Prodi' : 'Role' }}</th>
                    <th>NIDN / NIP</th>
                    @if($filterStatus === 'pending')
                        <th>Mendaftar</th>
                        <th class="pr-5 text-right">Aktivasi</th>
                    @else
                        <th>Profil Dosen</th>
                        <th>Bergabung</th>
                        <th class="pr-5 text-right">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="pl-5">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-medium text-white text-[12px]"
                             style="background: {{ $filterStatus === 'pending' ? '#D97706' : match($u->roles->first()?->name) { 'admin'=>'#0F172A','reviewer'=>'#3C3489','dosen'=>'#0F6E56', default=>'#64748B' } }};">
                            {{ strtoupper(substr($u->name, 0, 2)) }}
                        </div>
                    </td>
                    <td class="font-medium text-navy" style="font-size:14px;">
                        {{ $u->name }}
                        @if($u->id === auth()->id())
                            <span class="ml-1 chip chip-berjalan" style="font-size:10px;">Anda</span>
                        @endif
                    </td>
                    <td class="text-slate-400" style="font-size:13px;">{{ $u->email }}</td>
                    <td>
                        @if($filterStatus === 'pending')
                            <span class="text-slate-600" style="font-size:13px;">
                                {{ $u->dosen?->prodi?->nama ?? '—' }}
                            </span>
                        @else
                            @forelse($u->roles as $role)
                                <span class="chip {{ match($role->name) { 'admin'=>'chip-dinilai','reviewer'=>'chip-laporan','dosen'=>'chip-berjalan', default=>'' } }}">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @empty
                                <span class="text-slate-400" style="font-size:13px;">—</span>
                            @endforelse
                        @endif
                    </td>
                    <td style="font-size:13px;">
                        @if($u->nidn || $u->nip)
                            <div class="space-y-0.5">
                                @if($u->nidn)
                                    <div class="text-navy font-mono">{{ $u->nidn }}</div>
                                @endif
                                @if($u->nip)
                                    <div class="text-slate-400 font-mono">{{ $u->nip }}</div>
                                @endif
                            </div>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>

                    @if($filterStatus === 'pending')
                    <td class="text-slate-400 font-mono" style="font-size:13px;">
                        {{ $u->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="pr-5 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <button wire:click="aktivasi({{ $u->id }})"
                                wire:confirm="Aktifkan akun {{ addslashes($u->name) }}?"
                                class="flex items-center gap-1.5 px-3 py-1.5 bg-sage text-white rounded-[6px] hover:bg-sage/90 transition"
                                style="font-size:13px;">
                                <i class="ti ti-check" style="font-size:14px;"></i> Aktifkan
                            </button>
                            <button wire:click="tolak({{ $u->id }})"
                                wire:confirm="Tolak & hapus pendaftaran {{ addslashes($u->name) }}? Data tidak bisa dikembalikan."
                                class="flex items-center gap-1.5 px-3 py-1.5 border border-red-200 text-red-500 rounded-[6px] hover:bg-red-50 transition"
                                style="font-size:13px;">
                                <i class="ti ti-x" style="font-size:14px;"></i> Tolak
                            </button>
                        </div>
                    </td>
                    @else
                    <td class="text-slate-400" style="font-size:13px;">
                        @if($u->dosen)
                            <span class="flex items-center gap-1 text-emerald-600">
                                <i class="ti ti-check" style="font-size:14px;"></i>
                                {{ $u->dosen->nidn }}
                            </span>
                        @else
                            <span class="text-slate-300">—</span>
                        @endif
                    </td>
                    <td class="text-slate-400 font-mono" style="font-size:13px;">
                        {{ $u->created_at->format('d M Y') }}
                    </td>
                    <td class="pr-5 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('users.edit', $u->id) }}"
                               class="p-1.5 text-slate-400 hover:text-navy hover:bg-slate-100 rounded transition" title="Edit">
                                <i class="ti ti-edit" style="font-size:16px;"></i>
                            </a>
                            @if($u->id !== auth()->id())
                            <button wire:click="delete({{ $u->id }})"
                                    wire:confirm="Yakin hapus user {{ addslashes($u->name) }}?"
                                    class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded transition" title="Hapus">
                                <i class="ti ti-trash" style="font-size:16px;"></i>
                            </button>
                            @else
                            <div class="p-1.5 text-slate-200 cursor-not-allowed" title="Tidak bisa hapus akun sendiri">
                                <i class="ti ti-trash" style="font-size:16px;"></i>
                            </div>
                            @endif
                        </div>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($users->hasPages())
        <div class="px-5 py-3 border-t border-slate-100">
            {{ $users->links() }}
        </div>
        @endif
        @endif
    </div>

    <div class="mt-3 text-slate-400" style="font-size:13px;">
        Total: <span class="font-medium text-navy">{{ $users->total() }}</span>
        {{ $filterStatus === 'pending' ? 'pendaftar menunggu' : 'user' }}
    </div>
</div>
