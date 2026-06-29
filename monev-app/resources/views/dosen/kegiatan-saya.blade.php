<x-app-layout>
    <x-slot name="pageTitle">Kegiatan Saya</x-slot>
    <x-slot name="pageSubtitle">Riwayat keterlibatan Anda dalam kegiatan penelitian & pengabdian</x-slot>

    @if(!$dosenAda)
    <div class="max-w-lg">
        <div class="bg-amber-50 border border-amber-200 rounded-[8px] px-5 py-6 text-center space-y-2">
            <i class="ti ti-user-question text-amber-500 block" style="font-size:36px;"></i>
            <p class="font-semibold text-amber-700" style="font-size:15px;">Akun belum terhubung ke profil dosen</p>
            <p class="text-amber-600" style="font-size:13px;">Hubungi admin untuk menghubungkan akun Anda ke data dosen di sistem.</p>
        </div>
    </div>
    @else

    @php $tab = request('tab', 'ketua'); @endphp
    <div class="flex items-center gap-1 mb-5 border-b border-slate-200">
        @foreach(['ketua' => 'Sebagai Ketua (' . $sebagaiKetua->count() . ')', 'anggota' => 'Sebagai Anggota (' . $sebagaiAnggota->count() . ')'] as $key => $label)
        <a href="{{ request()->fullUrlWithQuery(['tab' => $key]) }}"
           class="px-4 py-2.5 rounded-t-[6px] border-b-2 transition-colors -mb-px
               {{ $tab === $key ? 'border-navy text-navy bg-white font-semibold' : 'border-transparent text-slate-400 hover:text-navy' }}"
           style="font-size:14px;">{{ $label }}</a>
        @endforeach
    </div>

    @php $list = $tab === 'ketua' ? $sebagaiKetua : $sebagaiAnggota; @endphp
    <div class="bg-white border border-slate-200 rounded-[8px]">
        @if($list->isEmpty())
            <div class="py-16 text-center text-slate-400">
                <i class="ti ti-database-off block mb-2" style="font-size:36px;"></i>
                <p style="font-size:15px;">Tidak ada kegiatan</p>
            </div>
        @else
        <table class="data-table">
            <thead>
                <tr>
                    <th class="pl-5">Judul Kegiatan</th>
                    <th>Skema</th>
                    <th class="text-center">Tahun</th>
                    <th class="text-center">Status</th>
                    @if($tab === 'ketua')
                    <th class="text-center">Skor</th>
                    <th class="text-center">Rekomendasi</th>
                    @else
                    <th>Ketua</th>
                    @endif
                    <th class="pr-5 text-right w-20">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($list as $k)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="pl-5 font-medium text-navy" style="font-size:14px;">{{ Str::limit($k->judul, 48) }}</td>
                    <td class="text-slate-500" style="font-size:13px;">{{ $k->skema?->nama }}</td>
                    <td class="text-center font-mono text-slate-500" style="font-size:13px;">{{ $k->tahun }}</td>
                    <td class="text-center"><span class="chip {{ $k->statusChip() }}">{{ $k->statusLabel() }}</span></td>
                    @if($tab === 'ketua')
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
                    @else
                    <td class="text-slate-500" style="font-size:13px;">{{ $k->ketua?->nama }}</td>
                    @endif
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
    @endif
</x-app-layout>
