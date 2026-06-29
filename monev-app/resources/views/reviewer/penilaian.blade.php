<x-app-layout>
    <x-slot name="pageTitle">Form Penilaian</x-slot>
    <x-slot name="pageSubtitle">{{ Str::limit($kegiatan->judul, 60) }}</x-slot>

    <div class="max-w-2xl space-y-5">

        {{-- Info kegiatan --}}
        <div class="bg-white border border-slate-200 rounded-[8px] p-5">
            <div class="grid grid-cols-2 gap-x-6 gap-y-2" style="font-size:13px;">
                <div class="flex gap-3"><span class="text-slate-400 w-24">Skema</span><span class="font-medium text-navy">{{ $kegiatan->skema?->nama }}</span></div>
                <div class="flex gap-3"><span class="text-slate-400 w-24">Kategori</span><span class="font-medium text-navy">{{ $kegiatan->kategori?->nama }}</span></div>
                <div class="flex gap-3"><span class="text-slate-400 w-24">Ketua</span><span class="font-medium text-navy">{{ $kegiatan->ketua?->nama }}</span></div>
                <div class="flex gap-3"><span class="text-slate-400 w-24">Tahun</span><span class="font-medium text-navy">{{ $kegiatan->tahun }}</span></div>
            </div>
        </div>

        @if($penilaian->status === 'FINAL')
        <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-[8px] flex items-center gap-2" style="font-size:14px;">
            <i class="ti ti-lock text-lg"></i>
            Penilaian sudah disubmit final. Skor: <strong>{{ number_format($penilaian->skor_akhir, 1) }}</strong> — Rekomendasi: <strong>{{ $penilaian->rekomendasi }}</strong>
        </div>
        @endif

        <form method="POST" action="{{ route('penilaian.simpan', $penugasan->id) }}" class="space-y-5">
            @csrf

            {{-- Kriteria --}}
            <div class="bg-white border border-slate-200 rounded-[8px] p-5 space-y-5">
                <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">
                    Penilaian Kriteria
                    <span class="font-normal text-slate-400" style="font-size:13px;">(total bobot: {{ number_format($kriteria->sum('bobot'), 0) }}%)</span>
                </p>

                @if($kriteria->isEmpty())
                <p class="text-amber-600" style="font-size:13px;">Belum ada kriteria penilaian aktif. Tambahkan di Pengaturan → Kriteria Penilaian.</p>
                @endif

                @foreach($kriteria as $k)
                <div class="space-y-2">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <label class="font-medium text-navy" style="font-size:14px;">
                                {{ $loop->iteration }}. {{ $k->nama }}
                            </label>
                            <p class="text-slate-400" style="font-size:12px;">
                                Bobot {{ number_format($k->bobot, 0) }}% &nbsp;·&nbsp; Rentang {{ $k->skor_min }}–{{ $k->skor_max }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <input type="number"
                                   name="skor[{{ $k->id }}]"
                                   id="skor_{{ $k->id }}"
                                   value="{{ old("skor.{$k->id}", $detailMap[$k->id] ?? '') }}"
                                   min="{{ $k->skor_min }}" max="{{ $k->skor_max }}" step="1"
                                   class="w-20 text-center font-mono input-field @error("skor.{$k->id}") border-red-400 @enderror"
                                   {{ $penilaian->status === 'FINAL' ? 'disabled' : '' }}
                                   oninput="hitungPreview()">
                            <span class="text-slate-400" style="font-size:13px;">/ {{ $k->skor_max }}</span>
                        </div>
                    </div>
                    {{-- Progress bar bobot × skor --}}
                    <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-sage rounded-full transition-all" id="bar_{{ $k->id }}"
                             style="width:{{ isset($detailMap[$k->id]) ? ($detailMap[$k->id] / $k->skor_max * 100) : 0 }}%"></div>
                    </div>
                    @error("skor.{$k->id}")<p class="text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                </div>
                @endforeach

                {{-- Preview skor akhir --}}
                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <span class="text-slate-500" style="font-size:14px;">Estimasi Skor Akhir</span>
                    <span id="preview_skor" class="font-bold text-navy" style="font-size:22px;">—</span>
                </div>
            </div>

            {{-- Rekomendasi + Catatan --}}
            <div class="bg-white border border-slate-200 rounded-[8px] p-5 space-y-4">
                <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">Rekomendasi & Catatan</p>
                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Rekomendasi <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach(['LANJUT' => ['chip-berjalan','Lanjut'], 'PERBAIKAN' => ['chip-laporan','Perbaikan'], 'DIHENTIKAN' => ['chip-dihentikan','Dihentikan']] as $val => [$cls, $lbl])
                        <label class="cursor-pointer">
                            <input type="radio" name="rekomendasi" value="{{ $val }}"
                                   {{ old('rekomendasi', $penilaian->rekomendasi) === $val ? 'checked' : '' }}
                                   {{ $penilaian->status === 'FINAL' ? 'disabled' : '' }}
                                   class="peer sr-only">
                            <div class="border-2 border-slate-200 rounded-[8px] px-4 py-3 text-center transition
                                        peer-checked:border-navy peer-checked:bg-navy/5">
                                <span class="chip {{ $cls }} block mx-auto mb-1" style="font-size:12px;">{{ $val }}</span>
                                <p class="text-navy font-medium" style="font-size:13px;">{{ $lbl }}</p>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('rekomendasi')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Catatan Reviewer</label>
                    <textarea name="catatan" rows="4" class="input-field"
                              placeholder="Komentar, saran, atau temuan penting..."
                              {{ $penilaian->status === 'FINAL' ? 'disabled' : '' }}>{{ old('catatan', $penilaian->catatan) }}</textarea>
                </div>
            </div>

            @if($penilaian->status !== 'FINAL')
            <div class="flex items-center gap-3">
                <button type="submit" name="submit_draft" class="btn-secondary">
                    <i class="ti ti-device-floppy mr-1"></i> Simpan Draft
                </button>
                <button type="submit" name="submit_final"
                        onclick="return confirm('Submit final? Penilaian tidak bisa diubah setelah disubmit.')"
                        class="btn-primary">
                    <i class="ti ti-send mr-1"></i> Submit Final
                </button>
                <a href="{{ route('tugas.index') }}" class="ml-auto text-slate-400 hover:text-navy" style="font-size:14px;">Kembali</a>
            </div>
            @else
            <div class="flex items-center gap-3">
                <a href="{{ route('tugas.index') }}" class="btn-secondary inline-flex items-center gap-2">
                    <i class="ti ti-arrow-left"></i> Kembali ke Daftar Tugas
                </a>
                <button type="button" onclick="window.print()" class="btn-secondary inline-flex items-center gap-2">
                    <i class="ti ti-printer"></i> Cetak
                </button>
            </div>
            @endif
        </form>
    </div>

    @php
        $kriteriaJs = $kriteria->map(fn($k) => ['id'=>$k->id,'bobot'=>$k->bobot,'skor_max'=>$k->skor_max,'skor_min'=>$k->skor_min])->values();
    @endphp
    <script>
    const kriteria = @json($kriteriaJs);
    const totalBobot = kriteria.reduce((s, k) => s + k.bobot, 0) || 100;

    function hitungPreview() {
        let skor = 0;
        kriteria.forEach(k => {
            const inp = document.getElementById('skor_' + k.id);
            const bar = document.getElementById('bar_' + k.id);
            const val = parseFloat(inp?.value) || 0;
            skor += (k.bobot / totalBobot) * val;
            if (bar) bar.style.width = Math.min(100, (val / k.skor_max) * 100) + '%';
        });
        document.getElementById('preview_skor').textContent = skor > 0 ? skor.toFixed(1) : '—';
    }
    hitungPreview();
    </script>
</x-app-layout>
