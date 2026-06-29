<x-app-layout>
    <x-slot name="pageTitle">Rekap & Laporan</x-slot>
    <x-slot name="pageSubtitle">Ringkasan kegiatan dan hasil penilaian</x-slot>

    {{-- Filter --}}
    <form method="GET" action="{{ route('rekap.index') }}" class="flex flex-wrap items-center gap-3 mb-6">
        <select name="tahun" class="border border-slate-200 rounded-[8px] px-3 py-2 text-navy focus:outline-none focus:border-navy" style="font-size:14px;">
            <option value="">Semua Tahun</option>
            @foreach($tahunList as $t)
                <option value="{{ $t }}" {{ $tahun == $t ? 'selected' : '' }}>{{ $t }}</option>
            @endforeach
        </select>
        <select name="skema" class="border border-slate-200 rounded-[8px] px-3 py-2 text-navy focus:outline-none focus:border-navy" style="font-size:14px;">
            <option value="">Semua Skema</option>
            @foreach($skemaAll as $s)
                <option value="{{ $s->id }}" {{ $skema == $s->id ? 'selected' : '' }}>{{ $s->nama }}</option>
            @endforeach
        </select>
        <select name="status" class="border border-slate-200 rounded-[8px] px-3 py-2 text-navy focus:outline-none focus:border-navy" style="font-size:14px;">
            <option value="">Semua Status</option>
            @foreach(['TERDAFTAR','BERJALAN','LAPORAN_MASUK','DINILAI','SELESAI'] as $s)
                <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ str_replace('_',' ',$s) }}</option>
            @endforeach
        </select>
        <input type="text" name="ketua" value="{{ $ketua ?? '' }}" placeholder="Cari nama ketua..." class="border border-slate-200 rounded-[8px] px-3 py-2 focus:outline-none focus:border-navy transition" style="font-size:14px; min-width:180px;">
        <button type="submit" class="btn-primary">
            <i class="ti ti-filter mr-1"></i> Filter
        </button>
        <a href="{{ route('rekap.export', request()->query()) }}"
           class="ml-auto flex items-center gap-1.5 border border-slate-200 text-navy px-4 py-2 rounded-[8px] hover:bg-slate-50 transition"
           style="font-size:14px;">
            <i class="ti ti-download" style="font-size:16px;"></i> Export CSV
        </a>
    </form>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-slate-200 rounded-[8px] px-5 py-4">
            <p class="text-slate-400" style="font-size:13px;">Total Kegiatan</p>
            <p class="font-bold text-navy mt-0.5" style="font-size:26px;">{{ $total }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-[8px] px-5 py-4">
            <p class="text-slate-400" style="font-size:13px;">Selesai Dinilai</p>
            <p class="font-bold text-sage mt-0.5" style="font-size:26px;">{{ $selesai }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-[8px] px-5 py-4">
            <p class="text-slate-400" style="font-size:13px;">Rata-rata Skor</p>
            <p class="font-bold text-navy mt-0.5" style="font-size:26px;">{{ $rataaSkor ? number_format($rataaSkor, 1) : '—' }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-[8px] px-5 py-4">
            <p class="text-slate-400" style="font-size:13px;">Total Dana</p>
            <p class="font-bold text-navy mt-0.5" style="font-size:18px;">Rp {{ number_format($totalDana / 1000000, 0) }} jt</p>
        </div>
    </div>

    {{-- Distribusi per skema --}}
    @if($perSkema->isNotEmpty())
    <div class="bg-white border border-slate-200 rounded-[8px] p-5 mb-6">
        <p class="font-semibold font-heading text-navy mb-4 border-b border-slate-100 pb-3" style="font-size:15px;">Distribusi per Skema</p>
        <div class="space-y-3">
            @foreach($perSkema as $ps)
            <div>
                <div class="flex items-center justify-between mb-1" style="font-size:13px;">
                    <span class="text-navy font-medium">{{ $ps['nama'] }}</span>
                    <span class="text-slate-400">{{ $ps['jumlah'] }} kegiatan ({{ $ps['selesai'] }} selesai)</span>
                </div>
                <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full bg-navy rounded-full" style="width:{{ $total > 0 ? ($ps['jumlah'] / $total * 100) : 0 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Tabel kegiatan --}}
    <div class="bg-white border border-slate-200 rounded-[8px]">
        @if($kegiatan->isEmpty())
            <div class="py-16 text-center text-slate-400">
                <i class="ti ti-database-off block mb-2" style="font-size:36px;"></i>
                <p style="font-size:15px;">Tidak ada data untuk filter ini</p>
            </div>
        @else
        <table class="data-table">
            <thead>
                <tr>
                    <th class="pl-5 w-8">#</th>
                    <th>Judul Kegiatan</th>
                    <th>Skema</th>
                    <th>Ketua</th>
                    <th class="text-center w-20">Tahun</th>
                    <th class="text-center w-28">Status</th>
                    <th class="text-center w-20">Skor</th>
                    <th class="text-center w-32">Rekomendasi</th>
                    <th class="pr-5 text-right w-16">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kegiatan as $i => $k)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="pl-5 text-slate-400" style="font-size:13px;">{{ $i + 1 }}</td>
                    <td>
                        <p class="font-medium text-navy" style="font-size:14px;">{{ Str::limit($k->judul, 45) }}</p>
                        <p class="text-slate-400" style="font-size:12px;">{{ $k->kategori?->nama }}</p>
                    </td>
                    <td class="text-slate-500" style="font-size:13px;">{{ $k->skema?->nama }}</td>
                    <td>
                        <p class="text-navy" style="font-size:13px;">{{ $k->ketua?->nama }}</p>
                        <p class="text-slate-400 font-mono" style="font-size:12px;">{{ $k->ketua?->nidn }}</p>
                    </td>
                    <td class="text-center font-mono text-slate-500" style="font-size:13px;">{{ $k->tahun }}</td>
                    <td class="text-center"><span class="chip {{ $k->statusChip() }}">{{ $k->statusLabel() }}</span></td>
                    <td class="text-center font-semibold {{ $k->skor_final ? 'text-sage' : 'text-slate-300' }}" style="font-size:15px;">
                        {{ $k->skor_final ? number_format($k->skor_final, 1) : '—' }}
                    </td>
                    <td class="text-center">
                        @if($k->rekomendasi_final)
                        <span class="chip {{ match($k->rekomendasi_final) { 'LANJUT'=>'chip-berjalan','PERBAIKAN'=>'chip-laporan','DIHENTIKAN'=>'chip-dihentikan', default=>'' } }}">
                            {{ $k->rekomendasi_final }}
                        </span>
                        @else <span class="text-slate-300">—</span> @endif
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
        <div class="px-5 py-3 border-t border-slate-100 text-slate-400" style="font-size:13px;">
            Menampilkan {{ $kegiatan->count() }} kegiatan
            @if($lanjut > 0) · <span class="text-emerald-600 font-medium">{{ $lanjut }} Lanjut</span> @endif
            @if($dihentikan > 0) · <span class="text-red-500 font-medium">{{ $dihentikan }} Dihentikan</span> @endif
        </div>
        @endif
    </div>
</x-app-layout>
