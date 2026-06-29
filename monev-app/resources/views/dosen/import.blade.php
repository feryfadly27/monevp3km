<x-app-layout>
    <x-slot name="pageTitle">Import Dosen</x-slot>
    <x-slot name="pageSubtitle">Unggah file CSV untuk menambahkan banyak dosen sekaligus</x-slot>

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
        <form method="POST" action="{{ route('dosen.import.post') }}" enctype="multipart/form-data">
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
            </div>

            <div class="flex items-center gap-3 mt-5">
                <button type="submit" class="btn-primary">
                    <i class="ti ti-upload mr-1"></i> Proses Import
                </button>
                <a href="{{ route('dosen.index') }}" class="btn-secondary">Batal</a>
                <a href="{{ route('dosen.import.template') }}"
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
                Baris pertama adalah header (wajib ada). Gunakan nama kolom persis seperti berikut:
            </p>

            <div class="bg-slate-50 border border-slate-200 rounded-[6px] px-4 py-3 font-mono overflow-x-auto" style="font-size:13px;">
                nama,nidn,prodi,email,no_hp,user_email
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse" style="font-size:13px;">
                    <thead>
                        <tr class="border-b border-slate-200">
                            <th class="py-2 pr-4 font-semibold text-navy w-28">Kolom</th>
                            <th class="py-2 pr-4 font-semibold text-navy w-20">Wajib</th>
                            <th class="py-2 font-semibold text-navy">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="text-slate-500">
                        <tr class="border-b border-slate-100">
                            <td class="py-1.5 pr-4 font-mono text-navy">nama</td>
                            <td class="pr-4"><span class="chip chip-dinilai" style="font-size:11px;">Ya</span></td>
                            <td>Nama lengkap dosen beserta gelar</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-1.5 pr-4 font-mono text-navy">nidn</td>
                            <td class="pr-4"><span class="chip chip-dinilai" style="font-size:11px;">Ya</span></td>
                            <td>Nomor Induk Dosen Nasional, unik, maks. 20 karakter</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-1.5 pr-4 font-mono text-navy">prodi</td>
                            <td class="pr-4"><span class="chip chip-dinilai" style="font-size:11px;">Ya</span></td>
                            <td>
                                Nama program studi sesuai yang terdaftar di sistem
                                @if($prodiAll->isNotEmpty())
                                <details class="mt-1 inline-block">
                                    <summary class="text-navy cursor-pointer" style="font-size:12px;">Lihat daftar prodi</summary>
                                    <ul class="mt-1 space-y-0.5 list-none pl-2" style="font-size:12px;">
                                        @foreach($prodiAll->groupBy(fn($p) => $p->fakultas?->nama ?? 'Lainnya') as $fak => $prodis)
                                            <li class="font-medium text-slate-600 mt-1">{{ $fak }}</li>
                                            @foreach($prodis as $p)
                                                <li class="pl-2 font-mono text-slate-500">{{ $p->nama }}</li>
                                            @endforeach
                                        @endforeach
                                    </ul>
                                </details>
                                @endif
                            </td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-1.5 pr-4 font-mono text-navy">email</td>
                            <td class="pr-4"><span class="text-slate-300">Tidak</span></td>
                            <td>Email dosen, boleh kosong</td>
                        </tr>
                        <tr class="border-b border-slate-100">
                            <td class="py-1.5 pr-4 font-mono text-navy">no_hp</td>
                            <td class="pr-4"><span class="text-slate-300">Tidak</span></td>
                            <td>Nomor HP dosen, boleh kosong</td>
                        </tr>
                        <tr>
                            <td class="py-1.5 pr-4 font-mono text-navy">user_email</td>
                            <td class="pr-4"><span class="text-slate-300">Tidak</span></td>
                            <td>Email akun user yang akan dihubungkan ke profil dosen ini (harus sudah ada di sistem)</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-[6px] px-4 py-3 text-amber-700 flex gap-2" style="font-size:13px;">
                <i class="ti ti-info-circle shrink-0 mt-0.5" style="font-size:15px;"></i>
                <div class="space-y-1">
                    <p>Baris dengan NIDN yang sudah terdaftar akan dilewati tanpa menimpa data yang ada.</p>
                    <p>Jika <span class="font-mono">user_email</span> tidak ditemukan atau sudah terhubung ke dosen lain, dosen tetap diimpor — hanya tanpa akun terhubung.</p>
                    <p>Baris komentar yang diawali <span class="font-mono">#</span> dalam template akan diabaikan secara otomatis.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
    function showFileName(input) {
        const label = document.getElementById('drop-label');
        if (input.files.length > 0) {
            label.textContent = input.files[0].name;
            document.getElementById('drop-zone').classList.add('border-navy', 'bg-slate-50');
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
