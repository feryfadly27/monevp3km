<x-app-layout>
    <x-slot name="pageTitle">Detail Kegiatan</x-slot>
    <x-slot name="pageSubtitle">{{ Str::limit($kegiatan->judul, 70) }}</x-slot>

    @php $tab = request('tab', 'info'); @endphp

    {{-- Flash --}}
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

    {{-- Header card --}}
    <div class="bg-white border border-slate-200 rounded-[8px] p-5 mb-5 flex flex-wrap items-start justify-between gap-4">
        <div class="space-y-1.5 flex-1">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="chip {{ $kegiatan->statusChip() }}">{{ $kegiatan->statusLabel() }}</span>
                <span class="chip chip-terdaftar" style="background:#f1f5f9;color:#475569;">{{ $kegiatan->tahun }}</span>
                <span class="text-slate-400" style="font-size:13px;">{{ $kegiatan->kategori?->nama }}</span>
            </div>
            <h1 class="font-heading font-bold text-navy leading-snug" style="font-size:18px;">{{ $kegiatan->judul }}</h1>
            <p class="text-slate-500" style="font-size:13px;">
                <i class="ti ti-user mr-1"></i>{{ $kegiatan->ketua?->nama }}
                &nbsp;·&nbsp;
                <i class="ti ti-file-text mr-1"></i>{{ $kegiatan->skema?->nama }}
                @if($kegiatan->jumlah_dana > 0)
                    &nbsp;·&nbsp;
                    <i class="ti ti-coin mr-1"></i>Rp {{ number_format($kegiatan->jumlah_dana, 0, ',', '.') }}
                @endif
            </p>
        </div>
        @role('admin')
        <div class="flex items-center gap-2">
            <a href="{{ route('kegiatan.edit', $kegiatan->id) }}"
               class="flex items-center gap-1.5 border border-slate-200 text-navy px-4 py-2 rounded-[8px] hover:bg-slate-50 transition"
               style="font-size:14px;">
                <i class="ti ti-edit" style="font-size:15px;"></i> Edit
            </a>
            @if($kegiatan->nextStatuses())
            <button onclick="document.getElementById('modal-status').classList.remove('hidden')"
                    class="btn-primary">
                <i class="ti ti-refresh mr-1"></i> Ubah Status
            </button>
            @endif
        </div>
        @endrole
    </div>

    {{-- Tabs --}}
    @php
    $tabs = [
        'info'     => ['icon' => 'ti-info-circle',    'label' => 'Info'],
        'anggota'  => ['icon' => 'ti-users',           'label' => 'Anggota (' . $kegiatan->anggota->count() . ')'],
        'reviewer' => ['icon' => 'ti-user-check',      'label' => 'Reviewer (' . $kegiatan->penugasanReviewer->count() . ')'],
        'luaran'   => ['icon' => 'ti-trophy',          'label' => 'Luaran (' . $kegiatan->luaran->count() . ')'],
        'berkas'   => ['icon' => 'ti-paperclip',       'label' => 'Berkas (' . $kegiatan->berkas->count() . ')'],
        'log'      => ['icon' => 'ti-history',         'label' => 'Log Status'],
    ];
    @endphp
    <div class="flex items-center gap-1 mb-5 border-b border-slate-200 overflow-x-auto">
        @foreach($tabs as $key => $meta)
        <a href="{{ request()->fullUrlWithQuery(['tab' => $key]) }}"
           class="flex items-center gap-1.5 px-4 py-2.5 whitespace-nowrap rounded-t-[6px] border-b-2 transition-colors -mb-px
               {{ $tab === $key ? 'border-navy text-navy bg-white font-semibold' : 'border-transparent text-slate-400 hover:text-navy' }}"
           style="font-size:14px;">
            <i class="ti {{ $meta['icon'] }}" style="font-size:15px;"></i> {{ $meta['label'] }}
        </a>
        @endforeach
    </div>

    {{-- ===== TAB: INFO ===== --}}
    @if($tab === 'info')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="bg-white border border-slate-200 rounded-[8px] p-5 space-y-3">
            <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">Informasi Umum</p>
            @php
            $rows = [
                'Judul'         => $kegiatan->judul,
                'Skema'         => $kegiatan->skema?->nama,
                'Kategori'      => $kegiatan->kategori?->nama,
                'Tahun'         => $kegiatan->tahun,
                'Sumber Dana'   => $kegiatan->sumber_dana ?: '—',
                'Jumlah Dana'   => $kegiatan->jumlah_dana > 0 ? 'Rp ' . number_format($kegiatan->jumlah_dana, 0, ',', '.') : '—',
                'Tanggal Mulai' => $kegiatan->tanggal_mulai?->format('d M Y') ?? '—',
                'Tanggal Selesai' => $kegiatan->tanggal_selesai?->format('d M Y') ?? '—',
            ];
            @endphp
            @foreach($rows as $label => $val)
            <div class="flex gap-3">
                <span class="text-slate-400 w-36 shrink-0" style="font-size:13px;">{{ $label }}</span>
                <span class="text-navy font-medium" style="font-size:14px;">{{ $val }}</span>
            </div>
            @endforeach
        </div>
        <div class="space-y-5">
            <div class="bg-white border border-slate-200 rounded-[8px] p-5 space-y-3">
                <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">Ketua Peneliti</p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-navy flex items-center justify-center text-white font-semibold" style="font-size:13px;">
                        {{ strtoupper(substr($kegiatan->ketua?->nama ?? '?', 0, 2)) }}
                    </div>
                    <div>
                        <p class="font-medium text-navy" style="font-size:14px;">{{ $kegiatan->ketua?->nama }}</p>
                        <p class="text-slate-400 font-mono" style="font-size:12px;">{{ $kegiatan->ketua?->nidn }} · {{ $kegiatan->ketua?->prodi?->nama }}</p>
                    </div>
                </div>
            </div>
            @if($kegiatan->skor_final !== null)
            <div class="bg-white border border-slate-200 rounded-[8px] p-5 space-y-3">
                <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">Hasil Penilaian</p>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400" style="font-size:13px;">Skor Final</span>
                    <span class="font-bold text-navy" style="font-size:22px;">{{ number_format($kegiatan->skor_final, 1) }}</span>
                </div>
                @if($kegiatan->rekomendasi_final)
                <div class="flex items-center justify-between">
                    <span class="text-slate-400" style="font-size:13px;">Rekomendasi</span>
                    <span class="chip {{ match($kegiatan->rekomendasi_final) { 'LANJUT'=>'chip-berjalan','PERBAIKAN'=>'chip-laporan','DIHENTIKAN'=>'chip-dihentikan', default=>'' } }}">
                        {{ $kegiatan->rekomendasi_final }}
                    </span>
                </div>
                @endif
            </div>
            @endif
            @if($kegiatan->catatan_admin)
            <div class="bg-amber-50 border border-amber-200 rounded-[8px] p-4" style="font-size:13px;">
                <p class="font-semibold text-amber-700 mb-1">Catatan Admin</p>
                <p class="text-amber-800">{{ $kegiatan->catatan_admin }}</p>
            </div>
            @endif
        </div>
    </div>

    {{-- ===== TAB: ANGGOTA ===== --}}
    @elseif($tab === 'anggota')
    <div class="max-w-2xl space-y-5">
        <div class="bg-white border border-slate-200 rounded-[8px]">
            @if($kegiatan->anggota->isEmpty())
                <div class="py-10 text-center text-slate-400" style="font-size:14px;"><i class="ti ti-users-off block mb-1" style="font-size:28px;"></i> Belum ada anggota</div>
            @else
            <table class="data-table">
                <thead><tr><th class="pl-5">Nama</th><th>Prodi</th><th>Peran</th><th class="pr-5 text-right">Aksi</th></tr></thead>
                <tbody>
                @foreach($kegiatan->anggota as $a)
                <tr class="hover:bg-slate-50">
                    <td class="pl-5">
                        <p class="font-medium text-navy" style="font-size:14px;">{{ $a->nama }}</p>
                        <p class="text-slate-400 font-mono" style="font-size:12px;">{{ $a->nidn }}</p>
                    </td>
                    <td class="text-slate-500" style="font-size:13px;">{{ $a->prodi?->nama }}</td>
                    <td><span class="chip chip-terdaftar" style="background:#f1f5f9;color:#475569;font-size:12px;">{{ $a->pivot->peran }}</span></td>
                    <td class="pr-5 text-right">
                        @role('admin')
                        <form method="POST" action="{{ route('kegiatan.anggota.hapus', [$kegiatan->id, $a->id]) }}"
                              onsubmit="return confirm('Hapus anggota {{ addslashes($a->nama) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded transition">
                                <i class="ti ti-trash" style="font-size:15px;"></i>
                            </button>
                        </form>
                        @endrole
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
        @role('admin')
        <div class="bg-white border border-slate-200 rounded-[8px] p-5 space-y-4">
            <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">Tambah Anggota</p>
            <form method="POST" action="{{ route('kegiatan.anggota.tambah', $kegiatan->id) }}" class="flex flex-wrap gap-3">
                @csrf
                <select name="dosen_id" class="input-field flex-1 min-w-[200px]" required>
                    <option value="">Pilih dosen...</option>
                    @foreach($dosenAvailable as $d)
                        <option value="{{ $d->id }}">{{ $d->nama }} ({{ $d->nidn }})</option>
                    @endforeach
                </select>
                <input type="text" name="peran" class="input-field w-40" placeholder="Peran (opsional)">
                <button type="submit" class="btn-primary">
                    <i class="ti ti-plus mr-1"></i> Tambah
                </button>
            </form>
        </div>
        @endrole
    </div>

    {{-- ===== TAB: REVIEWER ===== --}}
    @elseif($tab === 'reviewer')
    <div class="max-w-2xl">
        <div class="bg-white border border-slate-200 rounded-[8px]">
            @if($kegiatan->penugasanReviewer->isEmpty())
                <div class="py-10 text-center text-slate-400" style="font-size:14px;"><i class="ti ti-user-off block mb-1" style="font-size:28px;"></i> Belum ada reviewer ditugaskan</div>
            @else
            <table class="data-table">
                <thead><tr><th class="pl-5">Reviewer</th><th class="text-center">Status Tugas</th><th class="text-center">Skor</th><th class="text-center">Rekomendasi</th></tr></thead>
                <tbody>
                @foreach($kegiatan->penugasanReviewer as $pr)
                @php $p = $pr->penilaian; @endphp
                <tr class="hover:bg-slate-50">
                    <td class="pl-5">
                        <p class="font-medium text-navy" style="font-size:14px;">{{ $pr->reviewer?->name }}</p>
                        <p class="text-slate-400" style="font-size:12px;">{{ $pr->reviewer?->email }}</p>
                    </td>
                    <td class="text-center">
                        <span class="chip {{ match($pr->status) { 'MENUNGGU'=>'chip-terdaftar','DALAM_PENILAIAN'=>'chip-berjalan','SELESAI'=>'chip-selesai', default=>'' } }}"
                              style="{{ $pr->status==='MENUNGGU' ? 'background:#f1f5f9;color:#475569;' : '' }}">
                            {{ str_replace('_', ' ', $pr->status) }}
                        </span>
                    </td>
                    <td class="text-center font-semibold text-navy" style="font-size:15px;">
                        {{ $p?->skor_akhir !== null ? number_format($p->skor_akhir, 1) : '—' }}
                    </td>
                    <td class="text-center">
                        @if($p?->rekomendasi)
                        <span class="chip {{ match($p->rekomendasi) { 'LANJUT'=>'chip-berjalan','PERBAIKAN'=>'chip-laporan','DIHENTIKAN'=>'chip-dihentikan', default=>'' } }}">
                            {{ $p->rekomendasi }}
                        </span>
                        @else <span class="text-slate-300">—</span> @endif
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    {{-- ===== TAB: LUARAN ===== --}}
    @elseif($tab === 'luaran')
    <div class="max-w-2xl space-y-5">
        <div class="bg-white border border-slate-200 rounded-[8px]">
            @if($kegiatan->luaran->isEmpty())
                <div class="py-10 text-center text-slate-400" style="font-size:14px;"><i class="ti ti-trophy-off block mb-1" style="font-size:28px;"></i> Belum ada luaran</div>
            @else
            <table class="data-table">
                <thead><tr><th class="pl-5">Judul Luaran</th><th class="text-center w-28">Jenis</th><th class="text-center w-28">Capaian</th><th class="pr-5 text-right w-16">Aksi</th></tr></thead>
                <tbody>
                @foreach($kegiatan->luaran as $l)
                <tr class="hover:bg-slate-50">
                    <td class="pl-5">
                        <p class="font-medium text-navy" style="font-size:14px;">{{ $l->judul_luaran }}</p>
                        @if($l->url_bukti)
                            <a href="{{ $l->url_bukti }}" target="_blank" class="text-sage hover:underline" style="font-size:12px;">
                                <i class="ti ti-link mr-0.5"></i>Bukti
                            </a>
                        @endif
                    </td>
                    <td class="text-center"><span class="chip chip-laporan" style="font-size:11px;">{{ $l->jenis }}</span></td>
                    <td class="text-center">
                        <span class="chip {{ match($l->status_capaian) { 'TERCAPAI'=>'chip-selesai','PROSES'=>'chip-berjalan','RENCANA'=>'', default=>'' } }}"
                              style="{{ $l->status_capaian==='RENCANA' ? 'background:#f1f5f9;color:#475569;' : '' }}">
                            {{ $l->status_capaian }}
                        </span>
                    </td>
                    <td class="pr-5 text-right">
                        @role('admin')
                        <form method="POST" action="{{ route('kegiatan.luaran.hapus', [$kegiatan->id, $l->id]) }}"
                              onsubmit="return confirm('Hapus luaran ini?')">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded transition">
                                <i class="ti ti-trash" style="font-size:15px;"></i>
                            </button>
                        </form>
                        @endrole
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
        @role('admin')
        <div class="bg-white border border-slate-200 rounded-[8px] p-5 space-y-4">
            <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">Tambah Luaran</p>
            <form method="POST" action="{{ route('kegiatan.luaran.tambah', $kegiatan->id) }}" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-navy mb-1" style="font-size:13px;">Jenis <span class="text-red-500">*</span></label>
                        <select name="jenis" class="input-field" required>
                            <option value="">Pilih...</option>
                            @foreach(['PUBLIKASI','HKI','PRODUK','LAPORAN','LAINNYA'] as $j)
                                <option value="{{ $j }}">{{ $j }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-navy mb-1" style="font-size:13px;">Status Capaian <span class="text-red-500">*</span></label>
                        <select name="status_capaian" class="input-field" required>
                            <option value="RENCANA">RENCANA</option>
                            <option value="PROSES">PROSES</option>
                            <option value="TERCAPAI">TERCAPAI</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block font-medium text-navy mb-1" style="font-size:13px;">Judul Luaran <span class="text-red-500">*</span></label>
                    <input type="text" name="judul_luaran" class="input-field" required placeholder="Judul artikel / nama produk / dll">
                </div>
                <div>
                    <label class="block font-medium text-navy mb-1" style="font-size:13px;">URL Bukti</label>
                    <input type="url" name="url_bukti" class="input-field" placeholder="https://...">
                </div>
                <button type="submit" class="btn-primary">
                    <i class="ti ti-plus mr-1"></i> Tambah Luaran
                </button>
            </form>
        </div>
        @endrole
    </div>

    {{-- ===== TAB: BERKAS ===== --}}
    @elseif($tab === 'berkas')
    <div class="max-w-2xl space-y-5">
        <div class="bg-white border border-slate-200 rounded-[8px]">
            @if($kegiatan->berkas->isEmpty())
                <div class="py-10 text-center text-slate-400" style="font-size:14px;"><i class="ti ti-files-off block mb-1" style="font-size:28px;"></i> Belum ada berkas</div>
            @else
            <table class="data-table">
                <thead><tr><th class="pl-5">Nama File</th><th class="text-center w-36">Jenis</th><th class="text-right w-24">Ukuran</th><th class="pr-5 text-right w-16">Aksi</th></tr></thead>
                <tbody>
                @foreach($kegiatan->berkas as $b)
                <tr class="hover:bg-slate-50">
                    <td class="pl-5">
                        <a href="{{ Storage::url($b->path) }}" target="_blank"
                           class="font-medium text-navy hover:text-sage hover:underline flex items-center gap-1.5" style="font-size:14px;">
                            <i class="ti ti-file" style="font-size:15px;"></i> {{ $b->nama_file }}
                        </a>
                        <p class="text-slate-400" style="font-size:12px;">Oleh {{ $b->uploadedBy?->name }} · {{ $b->uploaded_at?->format('d M Y H:i') }}</p>
                    </td>
                    <td class="text-center"><span class="chip chip-laporan" style="font-size:11px;">{{ str_replace('_', ' ', $b->jenis) }}</span></td>
                    <td class="text-right text-slate-400 font-mono" style="font-size:12px;">{{ $b->ukuranLabel() }}</td>
                    <td class="pr-5 text-right">
                        @role('admin')
                        <form method="POST" action="{{ route('kegiatan.berkas.hapus', [$kegiatan->id, $b->id]) }}"
                              onsubmit="return confirm('Hapus berkas ini?')">
                            @csrf @method('DELETE')
                            <button class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded transition">
                                <i class="ti ti-trash" style="font-size:15px;"></i>
                            </button>
                        </form>
                        @endrole
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
        @role('admin')
        <div class="bg-white border border-slate-200 rounded-[8px] p-5 space-y-4">
            <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">Upload Berkas</p>
            <form method="POST" action="{{ route('kegiatan.berkas.upload', $kegiatan->id) }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-navy mb-1" style="font-size:13px;">Jenis <span class="text-red-500">*</span></label>
                        <select name="jenis" class="input-field" required>
                            @foreach(['LAPORAN_KEMAJUAN','LAPORAN_AKHIR','BUKTI_LUARAN','LAMPIRAN'] as $j)
                                <option value="{{ $j }}">{{ str_replace('_', ' ', $j) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-navy mb-1" style="font-size:13px;">File <span class="text-red-500">*</span></label>
                        <input type="file" name="file" class="input-field" required
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.jpg,.png">
                        <p class="text-slate-400 mt-1" style="font-size:11px;">PDF, Word, Excel, ZIP, Gambar — maks. 10 MB</p>
                    </div>
                </div>
                <button type="submit" class="btn-primary">
                    <i class="ti ti-upload mr-1"></i> Upload
                </button>
            </form>
        </div>
        @endrole
    </div>

    {{-- ===== TAB: LOG STATUS ===== --}}
    @elseif($tab === 'log')
    <div class="max-w-lg">
        @if($kegiatan->statusLog->isEmpty())
            <div class="bg-white border border-slate-200 rounded-[8px] py-10 text-center text-slate-400" style="font-size:14px;">
                <i class="ti ti-history block mb-1" style="font-size:28px;"></i> Belum ada riwayat perubahan status
            </div>
        @else
        <div class="space-y-3">
            @foreach($kegiatan->statusLog as $log)
            <div class="bg-white border border-slate-200 rounded-[8px] px-5 py-4 flex items-start gap-4">
                <div class="w-8 h-8 rounded-full bg-navy/10 flex items-center justify-center shrink-0 mt-0.5">
                    <i class="ti ti-refresh text-navy" style="font-size:14px;"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($log->status_lama)
                            <span class="chip chip-{{ strtolower(str_replace('_','-',$log->status_lama)) }}" style="font-size:11px;">{{ $log->status_lama }}</span>
                            <i class="ti ti-arrow-right text-slate-400" style="font-size:13px;"></i>
                        @endif
                        <span class="chip chip-{{ strtolower(str_replace('_','-',$log->status_baru)) }}" style="font-size:11px;">{{ $log->status_baru }}</span>
                    </div>
                    @if($log->catatan)
                        <p class="text-slate-500 mt-1" style="font-size:13px;">{{ $log->catatan }}</p>
                    @endif
                    <p class="text-slate-400 mt-1" style="font-size:12px;">
                        {{ $log->oleh?->name }} · {{ $log->created_at?->format('d M Y H:i') }}
                    </p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endif

    {{-- Modal Ubah Status --}}
    <div id="modal-status" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
        <div class="bg-white rounded-[10px] shadow-xl w-full max-w-sm p-6 space-y-4">
            <div class="flex items-center justify-between">
                <p class="font-semibold font-heading text-navy" style="font-size:15px;">Ubah Status Kegiatan</p>
                <button onclick="document.getElementById('modal-status').classList.add('hidden')" class="text-slate-400 hover:text-navy">
                    <i class="ti ti-x" style="font-size:18px;"></i>
                </button>
            </div>
            <form method="POST" action="{{ route('kegiatan.status', $kegiatan->id) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Status Baru <span class="text-red-500">*</span></label>
                    <select name="status_baru" class="input-field" required>
                        <option value="">Pilih status...</option>
                        @foreach($kegiatan->nextStatuses() as $s)
                        <option value="{{ $s }}">{{ str_replace('_', ' ', $s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Catatan</label>
                    <textarea name="catatan" rows="3" class="input-field" placeholder="Keterangan perubahan status (opsional)"></textarea>
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="submit" class="btn-primary">
                        <i class="ti ti-refresh mr-1"></i> Ubah Status
                    </button>
                    <button type="button" onclick="document.getElementById('modal-status').classList.add('hidden')" class="btn-secondary">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
