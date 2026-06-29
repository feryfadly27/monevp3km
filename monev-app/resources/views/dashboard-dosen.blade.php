<x-app-layout>
    <x-slot name="pageTitle">Dashboard</x-slot>
    <x-slot name="pageSubtitle">Selamat datang, {{ auth()->user()->name }} · Tahun {{ $tahun }}</x-slot>

    @if(!$dosen)
    <div class="max-w-lg">
        <div class="bg-amber-50 border border-amber-200 rounded-[8px] px-5 py-6 text-center space-y-2">
            <i class="ti ti-user-question text-amber-500 block" style="font-size:36px;"></i>
            <p class="font-semibold text-amber-700" style="font-size:15px;">Akun belum terhubung ke profil dosen</p>
            <p class="text-amber-600" style="font-size:13px;">Hubungi admin untuk menghubungkan akun Anda ke data dosen di sistem.</p>
        </div>
    </div>
    @else

    {{-- Filter tahun --}}
    @if($tahunList->count() > 1)
    <div class="flex justify-end mb-4">
        <form method="GET" action="{{ route('dashboard') }}">
            <select name="tahun" onchange="this.form.submit()"
                class="border border-slate-200 rounded px-3 py-1.5 text-navy focus:outline-none focus:border-navy"
                style="font-size:14px;">
                @foreach($tahunList as $t)
                    <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </form>
    </div>
    @endif

    {{-- Stat cards --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="stat-card">
            <p class="text-slate-400 mb-1" style="font-size:13px;">Sebagai Ketua</p>
            <p class="font-bold text-navy" style="font-size:30px;">{{ $sebagaiKetua->count() }}</p>
            <p class="text-slate-300 mt-0.5" style="font-size:13px;">kegiatan</p>
        </div>
        <div class="stat-card">
            <p class="text-slate-400 mb-1" style="font-size:13px;">Sebagai Anggota</p>
            <p class="font-bold text-navy" style="font-size:30px;">{{ $sebagaiAnggota->count() }}</p>
            <p class="text-slate-300 mt-0.5" style="font-size:13px;">kegiatan</p>
        </div>
        <div class="stat-card border-sage/40">
            <p class="text-slate-400 mb-1" style="font-size:13px;">Selesai Dinilai</p>
            <p class="font-bold text-sage" style="font-size:30px;">
                {{ $sebagaiKetua->where('status', 'SELESAI')->count() }}
            </p>
            <p class="text-slate-300 mt-0.5" style="font-size:13px;">kegiatan</p>
        </div>
    </div>

    {{-- Kegiatan sebagai ketua --}}
    <div class="bg-white border border-slate-200 rounded-[8px] mb-5">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <p class="font-semibold font-heading text-navy" style="font-size:15px;">
                Kegiatan sebagai Ketua
                <span class="font-normal text-slate-400" style="font-size:13px;">({{ $sebagaiKetua->count() }})</span>
            </p>
            <a href="{{ route('kegiatan-saya.index') }}" class="text-sage hover:underline" style="font-size:14px;">
                Lihat detail <i class="ti ti-arrow-right text-xs"></i>
            </a>
        </div>

        @if($sebagaiKetua->isEmpty())
            <div class="py-12 text-center text-slate-400">
                <i class="ti ti-clipboard-x block mb-2" style="font-size:36px;"></i>
                <p style="font-size:15px;">Tidak ada kegiatan sebagai ketua untuk tahun {{ $tahun }}</p>
            </div>
        @else
        <table class="data-table">
            <thead>
                <tr>
                    <th class="pl-5">Judul Kegiatan</th>
                    <th>Skema</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Skor</th>
                    <th class="pr-5 text-right w-16">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sebagaiKetua as $k)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="pl-5 font-medium text-navy" style="font-size:14px;">{{ Str::limit($k->judul, 52) }}</td>
                    <td class="text-slate-500" style="font-size:13px;">{{ $k->skema?->nama }}</td>
                    <td class="text-center"><span class="chip {{ $k->statusChip() }}">{{ $k->statusLabel() }}</span></td>
                    <td class="text-center font-semibold {{ $k->skor_final ? 'text-sage' : 'text-slate-300' }}" style="font-size:15px;">
                        {{ $k->skor_final ? number_format($k->skor_final, 1) : '—' }}
                    </td>
                    <td class="pr-5 text-right">
                        <a href="{{ route('kegiatan.show', $k->id) }}"
                           class="p-1.5 text-slate-400 hover:text-navy hover:bg-slate-100 rounded transition" title="Detail">
                            <i class="ti ti-eye" style="font-size:16px;"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- Kegiatan sebagai anggota --}}
    @if($sebagaiAnggota->isNotEmpty())
    <div class="bg-white border border-slate-200 rounded-[8px]">
        <div class="px-5 py-4 border-b border-slate-100">
            <p class="font-semibold font-heading text-navy" style="font-size:15px;">
                Kegiatan sebagai Anggota
                <span class="font-normal text-slate-400" style="font-size:13px;">({{ $sebagaiAnggota->count() }})</span>
            </p>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th class="pl-5">Judul Kegiatan</th>
                    <th>Ketua</th>
                    <th>Skema</th>
                    <th class="text-center">Status</th>
                    <th class="pr-5 text-right w-16">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sebagaiAnggota as $k)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="pl-5 font-medium text-navy" style="font-size:14px;">{{ Str::limit($k->judul, 52) }}</td>
                    <td class="text-slate-500" style="font-size:13px;">{{ $k->ketua?->nama }}</td>
                    <td class="text-slate-500" style="font-size:13px;">{{ $k->skema?->nama }}</td>
                    <td class="text-center"><span class="chip {{ $k->statusChip() }}">{{ $k->statusLabel() }}</span></td>
                    <td class="pr-5 text-right">
                        <a href="{{ route('kegiatan.show', $k->id) }}"
                           class="p-1.5 text-slate-400 hover:text-navy hover:bg-slate-100 rounded transition" title="Detail">
                            <i class="ti ti-eye" style="font-size:16px;"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @endif
</x-app-layout>
