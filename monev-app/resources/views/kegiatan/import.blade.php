<x-app-layout>
    <x-slot name="pageTitle">Import Kegiatan</x-slot>
    <x-slot name="pageSubtitle">Unggah file CSV untuk menambahkan banyak kegiatan sekaligus</x-slot>

    <div class="max-w-2xl space-y-5">

        {{-- Notifikasi hasil import --}}
        @if(session('import_success'))
        <div class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-[8px]" style="font-size:14px;">
            <div class="flex items-center gap-2 font-medium mb-1">
                <i class="ti ti-circle-check text-lg"></i>
                {{ session('import_success') }}
            </div>
            @if(session('import_skipped'))
            <ul class="mt-2 space-y-0.5 list-disc list-inside text-amber-600" style="font-size:13px;">
                @foreach(session('import_skipped') as $skip)
                    <li>{{ $skip }}</li>
                @endforeach
            </ul>
            @endif
        </div>
        @endif

        @if($errors->any())
        <div class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-[8px]" style="font-size:14px;">
            <div class="flex items-center gap-2 font-medium"><i class="ti ti-alert-circle text-lg"></i> Gagal mengimpor</div>
            <ul class="mt-1 list-disc list-inside" style="font-size:13px;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        {{-- Form upload --}}
        <form method="POST" action="{{ route('kegiatan.import.post') }}" enctype="multipart/form-data">
            @csrf
            <div class="bg-white border border-slate-200 rounded-[8px] p-6 space-y-5">
                <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">Upload File CSV</p>

                <div id="drop-zone"
                     class="border-2 border-dashed border-slate-300 rounded-[8px] p-8 text-center cursor-pointer hover:border-navy hover:bg-slate-50 transition-colors"
                     onclick="document.getElementById('csv_file').click()">
                    <i class="ti ti-file-upload block text-slate-400 mb-2" style="font-size:40px;"></i>
                    <p class="font-medium text-navy" style="font-size:15px;" id="drop-label">Klik atau seret file CSV ke sini</p>
                    <p class="text-slate-400 mt-1" style="font-size:13px;">Format: .csv — Maks. 2 MB</p>
                </div>
                <input type="file" id="csv_file" name="file" accept=".csv,text/csv" class="hidden"
                       onchange="showFileName(this)">
                @error('file')<p class="text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                <p id="row-preview" class="text-sage font-medium mt-1" style="font-size:13px;"></p>
            </div>

            <div class="flex items-center gap-3 mt-5">
                <button type="submit" class="btn-primary">
                    <i class="ti ti-upload mr-1"></i> Proses Import
                </button>
                <a href="{{ route('kegiatan.index') }}" class="btn-secondary">Batal</a>
                <a href="{{ route('kegiatan.import.template') }}"
                   class="ml-auto flex items-center gap-1.5 text-navy border border-slate-200 px-4 py-2.5 rounded-[8px] hover:bg-slate-50 transition"
                   style="font-size:14px;">
                    <i class="ti ti-download" style="font-size:16px;"></i> Unduh Template
                </a>
            </div>
        </form>

        {{-- Panduan format --}}
        <div class="bg-white border border-slate-200 rounded-[8px] p-6 space-y-4">
            <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">Format CSV</p>

            <p class="text-slate-500" style="font-size:14px;">
                Baris pertama adalah header (wajib ada). Urutan kolom harus persis seperti berikut:
            </p>

            <div class="bg-slate-50 border border-slate-200 rounded-[6px] px-4 py-3 font-mono overflow-x-auto whitespace-nowrap" style="font-size:12px;">
                judul, skema, ketua_nidn, tahun, kategori, sumber_dana, jumlah_dana, tanggal_mulai, tanggal_selesai, status, catatan_admin
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" style="font-size:13px;">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="py-2 pr-4 font-semibold text-navy w-36">Kolom</th>
                            <th class="py-2 pr-4 font-semibold text-navy w-20">Wajib</th>
                            <th class="py-2 font-semibold text-navy">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-500">
                        <tr class="border-b border-slate-100">
                            <td class="py-1.5 pr-4 font-mono text-navy">judul</td>
                            <td class="pr-4"><span class="chip chip-dinilai" style="font-size:11px;">Ya</span></td>
                            <td>Judul lengkap kegiatan</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-1.5 pr-4 font-mono text-navy">skema</td>
                            <td class="pr-4"><span class="chip chip-dinilai" style="font-size:11px;">Ya</span></td>
                            <td>
                                Nama skema sesuai sistem
                                @if($skemaAll->isNotEmpty())
                                <details class="mt-0.5 inline-block">
                                    <summary class="text-navy cursor-pointer" style="font-size:12px;">Lihat daftar skema</summary>
                                    <ul class="mt-1 space-y-0.5 pl-2" style="font-size:12px;">
                                        @foreach($skemaAll as $s)
                                            <li class="font-mono text-slate-500">{{ $s->nama }}
                                                <span class="text-slate-400">({{ $s->kategori?->nama }})</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </details>
                                @endif
                            </td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-1.5 pr-4 font-mono text-navy">ketua_nidn</td>
                            <td class="pr-4"><span class="chip chip-dinilai" style="font-size:11px;">Ya</span></td>
                            <td>NIDN dosen ketua (harus sudah terdaftar di Data Dosen)</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-1.5 pr-4 font-mono text-navy">tahun</td>
                            <td class="pr-4"><span class="chip chip-dinilai" style="font-size:11px;">Ya</span></td>
                            <td>Tahun pelaksanaan, contoh: <span class="font-mono">2026</span></td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-1.5 pr-4 font-mono text-navy">kategori</td>
                            <td class="pr-4"><span class="text-slate-300">Tidak</span></td>
                            <td>
                                Nama atau kode kategori
                                @if($kategoriAll->isNotEmpty())
                                    ({{ $kategoriAll->map(fn($k) => $k->kode)->implode(', ') }}).
                                @endif
                                Boleh kosong — otomatis dari skema
                            </td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-1.5 pr-4 font-mono text-navy">sumber_dana</td>
                            <td class="pr-4"><span class="text-slate-300">Tidak</span></td>
                            <td>Sumber pendanaan, contoh: <span class="font-mono">DIPA</span></td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-1.5 pr-4 font-mono text-navy">jumlah_dana</td>
                            <td class="pr-4"><span class="text-slate-300">Tidak</span></td>
                            <td>Angka tanpa titik/koma, contoh: <span class="font-mono">15000000</span></td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-1.5 pr-4 font-mono text-navy">tanggal_mulai</td>
                            <td class="pr-4"><span class="text-slate-300">Tidak</span></td>
                            <td>Format <span class="font-mono">YYYY-MM-DD</span>, contoh: <span class="font-mono">2026-01-15</span></td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-1.5 pr-4 font-mono text-navy">tanggal_selesai</td>
                            <td class="pr-4"><span class="text-slate-300">Tidak</span></td>
                            <td>Format <span class="font-mono">YYYY-MM-DD</span></td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-1.5 pr-4 font-mono text-navy">status</td>
                            <td class="pr-4"><span class="text-slate-300">Tidak</span></td>
                            <td>
                                Default: <span class="font-mono chip chip-terdaftar" style="font-size:11px;">TERDAFTAR</span>.
                                Pilihan: TERDAFTAR, BERJALAN, LAPORAN_MASUK, DINILAI, SELESAI
                            </td>
                        </tr>
                        <tr>
                            <td class="py-1.5 pr-4 font-mono text-navy">catatan_admin</td>
                            <td class="pr-4"><span class="text-slate-300">Tidak</span></td>
                            <td>Catatan tambahan dari admin</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-[6px] px-4 py-3 text-amber-700 flex gap-2" style="font-size:13px;">
                <i class="ti ti-info-circle shrink-0 mt-0.5" style="font-size:15px;"></i>
                <div class="space-y-1">
                    <p>Kegiatan diimpor dengan status awal sesuai kolom <span class="font-mono">status</span> (default TERDAFTAR).</p>
                    <p>Pastikan NIDN ketua sudah terdaftar di <a href="{{ route('dosen.index') }}" class="underline">Data Dosen</a> sebelum import.</p>
                    <p>Baris komentar yang diawali <span class="font-mono">#</span> dalam template akan diabaikan.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    function showFileName(input) {
        const label = document.getElementById('drop-label');
        const preview = document.getElementById('row-preview');
        if (input.files.length > 0) {
            label.textContent = input.files[0].name;
            document.getElementById('drop-zone').classList.add('border-navy', 'bg-slate-50');
            // Count rows
            const reader = new FileReader();
            reader.onload = e => {
                const lines = e.target.result.split('
').filter(l => l.trim() && !l.startsWith('#'));
                const dataRows = lines.length - 1; // minus header
                if (preview) preview.textContent = dataRows + ' baris data terdeteksi';
            };
            reader.readAsText(input.files[0]);
        }
    }

    const zone = document.getElementById('drop-zone');
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('border-navy', 'bg-slate-50'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('bg-slate-50'));
    zone.addEventListener('drop', e => {
        e.preventDefault();
        const file = e.dataTransfer.files[0];
        if (file) {
            const dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById('csv_file').files = dt.files;
            showFileName(document.getElementById('csv_file'));
        }
    });
    </script>
</x-app-layout>
