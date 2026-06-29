<x-app-layout>
    <x-slot name="pageTitle">Dashboard</x-slot>
    <x-slot name="pageSubtitle">Periode: Tahun {{ $tahun }} · Per {{ now()->translatedFormat('d F Y') }}</x-slot>

    {{-- Filter tahun --}}
    @if($tahunList->count() > 1)
    <div class="flex justify-end mb-4">
        <form method="GET" action="{{ route('dashboard') }}">
            <select name="tahun" onchange="this.form.submit()"
                class="border border-slate-200 rounded px-3 py-1.5 text-navy focus:outline-none focus:border-navy transition"
                style="font-size:14px;">
                @foreach($tahunList as $t)
                    <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </form>
    </div>
    @endif

    {{-- Stat cards --}}
    <div class="grid grid-cols-5 gap-3 mb-6">
        @php
            $statItems = [
                ['label' => 'Terdaftar',     'value' => $stats->terdaftar    ?? 0, 'color' => 'bg-blue-500',   'accent' => false],
                ['label' => 'Berjalan',      'value' => $stats->berjalan     ?? 0, 'color' => 'bg-green-600',  'accent' => false],
                ['label' => 'Laporan Masuk', 'value' => $stats->laporan_masuk?? 0, 'color' => 'bg-amber-500',  'accent' => false],
                ['label' => 'Dinilai',       'value' => $stats->dinilai      ?? 0, 'color' => 'bg-purple-600', 'accent' => false],
                ['label' => 'Selesai',       'value' => $stats->selesai      ?? 0, 'color' => 'bg-sage',       'accent' => true],
            ];
        @endphp
        @foreach($statItems as $stat)
        <div class="stat-card {{ $stat['accent'] ? 'border-sage/40' : '' }}">
            <div class="flex items-center gap-1.5 mb-2">
                <span class="w-2 h-2 rounded-full {{ $stat['color'] }}"></span>
                <span class="text-slate-500" style="font-size:13px;">{{ $stat['label'] }}</span>
            </div>
            <div class="font-medium font-mono leading-none {{ $stat['accent'] ? 'text-sage' : 'text-navy' }}" style="font-size:30px;">
                {{ $stat['value'] }}
            </div>
            <div class="text-slate-400 mt-1" style="font-size:13px;">kegiatan</div>
        </div>
        @endforeach
    </div>

    {{-- Row 2 --}}
    <div class="grid grid-cols-[1.4fr_1fr] gap-4 mb-4">

        {{-- Terlambat --}}
        <div class="bg-white border border-slate-200 rounded-[8px] p-5">
            <div class="flex justify-between items-center mb-4">
                <span class="font-semibold font-heading text-navy" style="font-size:16px;">Kegiatan terlambat laporan</span>
                <a href="{{ route('kegiatan.index', ['filterStatus' => 'BERJALAN']) }}" class="text-sage hover:underline" style="font-size:14px;">Lihat semua</a>
            </div>

            @if($terlambat->isEmpty())
                <div class="py-8 text-center text-slate-400" style="font-size:14px;">
                    <i class="ti ti-circle-check text-3xl text-emerald-400 block mb-2"></i>
                    Tidak ada kegiatan terlambat
                </div>
            @else
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-[38%]">Judul kegiatan</th>
                        <th>Skema</th>
                        <th>Ketua</th>
                        <th>Batas</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($terlambat as $k)
                    <tr>
                        <td class="font-medium" style="font-size:14px;">{{ Str::limit($k->judul, 45) }}</td>
                        <td class="text-slate-400" style="font-size:13px;">{{ $k->skema->nama ?? '—' }}</td>
                        <td class="text-slate-400" style="font-size:13px;">{{ $k->ketua->nama ?? '—' }}</td>
                        <td>
                            @php $lewat = $k->tanggal_selesai?->diffInDays(now()) ?? 0; @endphp
                            <span class="font-mono flex items-center gap-1 text-red-500" style="font-size:13px;">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                {{ $k->tanggal_selesai?->format('d M') ?? '—' }}
                            </span>
                        </td>
                        <td><span class="chip {{ $k->statusChip() }}">{{ $k->statusLabel() }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        {{-- Kanan --}}
        <div class="flex flex-col gap-4">

            {{-- Distribusi --}}
            <div class="bg-white border border-slate-200 rounded-[8px] p-5">
                <div class="flex justify-between items-center mb-4">
                    <span class="font-semibold font-heading text-navy" style="font-size:16px;">Distribusi per skema</span>
                    <span class="chip chip-berjalan">{{ $tahun }}</span>
                </div>

                @if($distribusiSkema->isEmpty())
                    <p class="text-slate-400 text-center py-4" style="font-size:14px;">Belum ada data</p>
                @else
                @php
                    $warnaPenelitian = ['#378ADD','#185FA5','#0C447C','#042C53'];
                    $warnaPengmas    = ['#1D9E75','#0F6E56','#085041','#04342C'];
                    $idxP = 0; $idxM = 0;
                @endphp
                <div class="space-y-2.5">
                    @foreach($distribusiSkema as $s)
                        @php
                            $isPenelitian = $s->kategori_kode === 'PENELITIAN';
                            $color = $isPenelitian
                                ? ($warnaPenelitian[$idxP++ % 4])
                                : ($warnaPengmas[$idxM++ % 4]);
                            $pct = round(($s->jumlah / $maxSkema) * 100);
                        @endphp
                        <div class="flex items-center gap-2.5">
                            <span class="w-[130px] text-slate-400 truncate" style="font-size:14px;">{{ $s->nama }}</span>
                            <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full" style="width:{{ $pct }}%; background:{{ $color }};"></div>
                            </div>
                            <span class="w-5 text-right font-mono font-medium text-navy" style="font-size:14px;">{{ $s->jumlah }}</span>
                        </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Beban reviewer --}}
            <div class="bg-white border border-slate-200 rounded-[8px] p-5">
                <div class="mb-3">
                    <span class="font-semibold font-heading text-navy" style="font-size:16px;">Beban reviewer aktif</span>
                </div>

                @if($bebanReviewer->isEmpty())
                    <p class="text-slate-400 text-center py-4" style="font-size:14px;">Belum ada reviewer ditugaskan</p>
                @else
                @php
                    $avatarColors = ['#3C3489','#185FA5','#0F6E56','#854F0B','#A32D2D'];
                @endphp
                <div class="divide-y divide-slate-100">
                    @foreach($bebanReviewer as $i => $rev)
                    <div class="flex items-center gap-2.5 py-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-medium text-white flex-shrink-0"
                             style="background:{{ $avatarColors[$i % 5] }}; font-size:12px;">
                            {{ strtoupper(substr($rev->reviewer->name ?? 'R', 0, 2)) }}
                        </div>
                        <span class="text-navy flex-1 truncate" style="font-size:15px;">{{ $rev->reviewer->name ?? '—' }}</span>
                        <span class="text-slate-400 font-mono" style="font-size:14px;">{{ $rev->jumlah_tugas }} kegiatan</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Kegiatan terbaru --}}
    <div class="bg-white border border-slate-200 rounded-[8px] p-5">
        <div class="flex justify-between items-center mb-4">
            <span class="font-semibold font-heading text-navy" style="font-size:16px;">Kegiatan terbaru</span>
            <a href="{{ route('kegiatan.index') }}" class="text-sage hover:underline flex items-center gap-1" style="font-size:14px;">
                Lihat semua <i class="ti ti-arrow-right" style="font-size:13px;"></i>
            </a>
        </div>

        @if($kegiatanTerbaru->isEmpty())
            <div class="py-8 text-center text-slate-400" style="font-size:14px;">
                <i class="ti ti-database-off text-3xl block mb-2"></i>
                Belum ada kegiatan untuk tahun {{ $tahun }}
            </div>
        @else
        <table class="data-table">
            <thead>
                <tr>
                    <th class="w-[32%]">Judul</th>
                    <th>Kategori</th>
                    <th>Skema</th>
                    <th>Ketua</th>
                    <th>Dana</th>
                    <th>Status</th>
                    <th>Skor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kegiatanTerbaru as $k)
                <tr>
                    <td class="font-medium" style="font-size:14px;">{{ Str::limit($k->judul, 50) }}</td>
                    <td>
                        <span class="chip {{ $k->kategori?->kode === 'PENELITIAN' ? 'chip-terdaftar' : 'chip-selesai' }}" style="font-size:12px;">
                            {{ $k->kategori?->kode === 'PENELITIAN' ? 'Penelitian' : 'Pengmas' }}
                        </span>
                    </td>
                    <td class="text-slate-400" style="font-size:13px;">{{ $k->skema->nama ?? '—' }}</td>
                    <td class="text-slate-400" style="font-size:13px;">{{ $k->ketua->nama ?? '—' }}</td>
                    <td class="font-mono text-slate-400" style="font-size:13px;">
                        Rp {{ number_format($k->jumlah_dana / 1000000, 0, ',', '.') }} jt
                    </td>
                    <td><span class="chip {{ $k->statusChip() }}">{{ $k->statusLabel() }}</span></td>
                    <td class="font-mono font-medium {{ $k->skor_final ? 'text-sage' : 'text-slate-300' }}" style="font-size:15px;">
                        {{ $k->skor_final ? number_format($k->skor_final, 1) : '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</x-app-layout>
