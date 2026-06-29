<x-app-layout>
    <x-slot name="pageTitle">Dashboard Reviewer</x-slot>
    <x-slot name="pageSubtitle">Selamat datang, {{ auth()->user()->name }} · Tahun {{ $tahun }}</x-slot>

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
    <div class="grid grid-cols-4 gap-4 mb-6">
        @foreach([
            ['label' => 'Total Tugas',     'value' => $total,       'color' => 'text-navy'],
            ['label' => 'Menunggu',        'value' => $menunggu,    'color' => 'text-amber-600'],
            ['label' => 'Dalam Penilaian', 'value' => $dalamProses, 'color' => 'text-blue-600'],
            ['label' => 'Selesai',         'value' => $selesai,     'color' => 'text-sage'],
        ] as $card)
        <div class="stat-card">
            <p class="text-slate-400 mb-1" style="font-size:13px;">{{ $card['label'] }}</p>
            <p class="font-bold {{ $card['color'] }}" style="font-size:30px;">{{ $card['value'] }}</p>
            <p class="text-slate-300 mt-0.5" style="font-size:13px;">kegiatan</p>
        </div>
        @endforeach
    </div>

    {{-- Daftar tugas --}}
    <div class="bg-white border border-slate-200 rounded-[8px]">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <p class="font-semibold font-heading text-navy" style="font-size:15px;">Tugas Penilaian Saya</p>
            <a href="{{ route('tugas.index') }}" class="text-sage hover:underline" style="font-size:14px;">
                Lihat semua <i class="ti ti-arrow-right text-xs"></i>
            </a>
        </div>

        @if($tugas->isEmpty())
            <div class="py-16 text-center text-slate-400">
                <i class="ti ti-clipboard-check block mb-2" style="font-size:36px;"></i>
                <p style="font-size:15px;">Tidak ada tugas untuk tahun {{ $tahun }}</p>
            </div>
        @else
        <table class="data-table">
            <thead>
                <tr>
                    <th class="pl-5">Judul Kegiatan</th>
                    <th>Skema</th>
                    <th>Ketua</th>
                    <th class="text-center w-32">Status Tugas</th>
                    <th class="text-center w-20">Skor</th>
                    <th class="pr-5 text-right w-20">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tugas->take(8) as $t)
                @php
                    $statusChip = match($t->status) {
                        'MENUNGGU'       => 'chip-terdaftar',
                        'DALAM_PENILAIAN'=> 'chip-berjalan',
                        'SELESAI'        => 'chip-selesai',
                        default          => '',
                    };
                    $statusLabel = match($t->status) {
                        'MENUNGGU'       => 'Menunggu',
                        'DALAM_PENILAIAN'=> 'Dalam Penilaian',
                        'SELESAI'        => 'Selesai',
                        default          => $t->status,
                    };
                @endphp
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="pl-5 font-medium text-navy" style="font-size:14px;">
                        {{ Str::limit($t->kegiatan->judul, 50) }}
                    </td>
                    <td class="text-slate-500" style="font-size:13px;">{{ $t->kegiatan->skema?->nama }}</td>
                    <td class="text-slate-500" style="font-size:13px;">{{ $t->kegiatan->ketua?->nama }}</td>
                    <td class="text-center"><span class="chip {{ $statusChip }}">{{ $statusLabel }}</span></td>
                    <td class="text-center font-semibold {{ $t->penilaian?->skor_akhir ? 'text-sage' : 'text-slate-300' }}" style="font-size:15px;">
                        {{ $t->penilaian?->skor_akhir ? number_format($t->penilaian->skor_akhir, 1) : '—' }}
                    </td>
                    <td class="pr-5 text-right">
                        @if($t->status !== 'SELESAI')
                        <a href="{{ route('penilaian.form', $t->id) }}"
                           class="inline-flex items-center gap-1 text-sage hover:underline" style="font-size:13px;">
                            <i class="ti ti-edit"></i> Nilai
                        </a>
                        @else
                        <a href="{{ route('penilaian.form', $t->id) }}"
                           class="inline-flex items-center gap-1 text-slate-400 hover:text-navy" style="font-size:13px;">
                            <i class="ti ti-eye"></i> Lihat
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($tugas->count() > 8)
        <div class="px-5 py-3 border-t border-slate-100 text-center">
            <a href="{{ route('tugas.index') }}" class="text-sage hover:underline" style="font-size:13px;">
                Tampilkan {{ $tugas->count() - 8 }} tugas lainnya →
            </a>
        </div>
        @endif
        @endif
    </div>
</x-app-layout>
