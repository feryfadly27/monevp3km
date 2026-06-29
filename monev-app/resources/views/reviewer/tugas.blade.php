<x-app-layout>
    <x-slot name="pageTitle">Daftar Tugas</x-slot>
    <x-slot name="pageSubtitle">Kegiatan yang ditugaskan untuk Anda nilai</x-slot>

    @if(session('success'))
    <div data-flash class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-[8px] flex items-center gap-2" style="font-size:14px;">
        <i class="ti ti-circle-check text-lg"></i> {{ session('success') }}
    </div>
    @endif

    {{-- Stats mini --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        @php
        $menunggu = $penugasan->where('status', 'MENUNGGU')->count();
        $proses   = $penugasan->where('status', 'DALAM_PENILAIAN')->count();
        $selesai  = $penugasan->where('status', 'SELESAI')->count();
        @endphp
        <div class="bg-white border border-slate-200 rounded-[8px] px-5 py-4">
            <p class="text-slate-400" style="font-size:13px;">Menunggu</p>
            <p class="font-bold text-navy mt-0.5" style="font-size:24px;">{{ $menunggu }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-[8px] px-5 py-4">
            <p class="text-slate-400" style="font-size:13px;">Dalam Penilaian</p>
            <p class="font-bold text-navy mt-0.5" style="font-size:24px;">{{ $proses }}</p>
        </div>
        <div class="bg-white border border-slate-200 rounded-[8px] px-5 py-4">
            <p class="text-slate-400" style="font-size:13px;">Selesai</p>
            <p class="font-bold text-sage mt-0.5" style="font-size:24px;">{{ $selesai }}</p>
        </div>
    </div>

    <div class="bg-white border border-slate-200 rounded-[8px]">
        @if($penugasan->isEmpty())
            <div class="py-20 text-center text-slate-400">
                <i class="ti ti-clipboard-check block mb-2" style="font-size:40px;"></i>
                <p style="font-size:16px;">Belum ada kegiatan yang ditugaskan</p>
            </div>
        @else
        <table class="data-table">
            <thead>
                <tr>
                    <th class="pl-5">Kegiatan</th>
                    <th>Skema</th>
                    <th class="text-center">Tahun</th>
                    <th class="text-center">Status Tugas</th>
                    <th class="text-center">Skor</th>
                    <th class="pr-5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penugasan as $p)
                @php $pen = $p->penilaian; @endphp
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="pl-5">
                        <p class="font-medium text-navy" style="font-size:14px;">{{ Str::limit($p->kegiatan?->judul, 50) }}</p>
                        <p class="text-slate-400" style="font-size:12px;">Ketua: {{ $p->kegiatan?->ketua?->nama }}</p>
                    </td>
                    <td class="text-slate-500" style="font-size:13px;">{{ $p->kegiatan?->skema?->nama }}</td>
                    <td class="text-center font-mono text-slate-500" style="font-size:13px;">{{ $p->kegiatan?->tahun }}</td>
                    <td class="text-center">
                        <span class="chip {{ match($p->status) { 'MENUNGGU'=>'','DALAM_PENILAIAN'=>'chip-berjalan','SELESAI'=>'chip-selesai', default=>'' } }}"
                              style="{{ $p->status==='MENUNGGU' ? 'background:#f1f5f9;color:#475569;' : '' }}">
                            {{ str_replace('_', ' ', $p->status) }}
                        </span>
                    </td>
                    <td class="text-center font-semibold {{ $pen?->skor_akhir ? 'text-sage' : 'text-slate-300' }}" style="font-size:15px;">
                        {{ $pen?->skor_akhir !== null ? number_format($pen->skor_akhir, 1) : '—' }}
                    </td>
                    <td class="pr-5 text-right">
                        @if($p->status !== 'SELESAI')
                        <a href="{{ route('penilaian.form', $p->id) }}"
                           class="inline-flex items-center gap-1.5 bg-navy text-white px-3 py-1.5 rounded-[6px] hover:bg-navy-dark transition"
                           style="font-size:13px;">
                            <i class="ti ti-pencil" style="font-size:14px;"></i>
                            {{ $p->status === 'DALAM_PENILAIAN' ? 'Lanjut Nilai' : 'Mulai Nilai' }}
                        </a>
                        @else
                        <a href="{{ route('penilaian.form', $p->id) }}"
                           class="inline-flex items-center gap-1.5 border border-slate-200 text-slate-500 px-3 py-1.5 rounded-[6px] hover:bg-slate-50 transition"
                           style="font-size:13px;">
                            <i class="ti ti-eye" style="font-size:14px;"></i> Lihat
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</x-app-layout>
