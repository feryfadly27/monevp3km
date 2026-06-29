<x-app-layout>
    <x-slot name="pageTitle">Tambah Skema</x-slot>
    <x-slot name="pageSubtitle">Buat skema baru beserta target luaran wajib</x-slot>

    <div class="max-w-2xl">
        <form method="POST" action="{{ route('skema.store') }}" class="space-y-5">
            @csrf

            <div class="bg-white border border-slate-200 rounded-[8px] p-6 space-y-5">
                <p class="font-semibold font-heading text-navy border-b border-slate-100 pb-3" style="font-size:15px;">Informasi Skema</p>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Kategori <span class="text-red-500">*</span></label>
                        <select name="kategori_id" class="input-field @error('kategori_id') border-red-400 @enderror" required>
                            <option value="">Pilih kategori...</option>
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                            @endforeach
                        </select>
                        @error('kategori_id')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Kode Skema <span class="text-red-500">*</span></label>
                        <input type="text" name="kode" value="{{ old('kode') }}"
                               class="input-field font-mono uppercase @error('kode') border-red-400 @enderror"
                               placeholder="mis. PDP, PKM" required>
                        @error('kode')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Nama Skema <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}"
                           class="input-field @error('nama') border-red-400 @enderror"
                           placeholder="Nama lengkap skema..." required>
                    @error('nama')<p class="mt-1 text-red-500" style="font-size:12px;">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Dana Maksimal (Rp)</label>
                        <input type="number" name="dana_maksimal" value="{{ old('dana_maksimal', 0) }}"
                               min="0" step="1000000" class="input-field font-mono">
                    </div>
                    <div>
                        <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Durasi (bulan)</label>
                        <input type="number" name="durasi_bulan" value="{{ old('durasi_bulan', 12) }}"
                               min="1" max="60" class="input-field">
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-navy mb-1.5" style="font-size:14px;">Deskripsi</label>
                    <textarea name="deskripsi" rows="2" class="input-field resize-none"
                              placeholder="Deskripsi singkat skema...">{{ old('deskripsi') }}</textarea>
                </div>
            </div>

            {{-- Target Luaran --}}
            <div class="bg-white border border-slate-200 rounded-[8px] p-6">
                <div class="flex justify-between items-center border-b border-slate-100 pb-3 mb-4">
                    <p class="font-semibold font-heading text-navy" style="font-size:15px;">Target Luaran</p>
                    <button type="button" onclick="addLuaran()"
                            class="flex items-center gap-1.5 text-sage border border-sage px-3 py-1.5 rounded-[8px] hover:bg-sage/5 transition"
                            style="font-size:13px;">
                        <i class="ti ti-plus" style="font-size:14px;"></i> Tambah Luaran
                    </button>
                </div>
                <div id="luaran-list" class="space-y-3"></div>
                <p id="luaran-empty" class="text-slate-400 text-center py-4" style="font-size:14px;">
                    Belum ada target luaran. Klik "Tambah Luaran" untuk menambahkan.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="btn-primary">
                    <i class="ti ti-device-floppy mr-1"></i> Simpan Skema
                </button>
                <a href="{{ route('skema.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>

    <script>
    let luaranCount = 0;
    const jenisOptions = {
        PUBLIKASI: "Publikasi", HKI: "HKI/Paten", PRODUK: "Produk",
        LAPORAN: "Laporan", LAINNYA: "Lainnya"
    };

    function addLuaran(data = {}) {
        const i = luaranCount++;
        document.getElementById("luaran-empty").style.display = "none";
        const div = document.createElement("div");
        div.id = "luaran-" + i;
        div.className = "flex items-start gap-3 p-3 bg-slate-50 rounded-[8px] border border-slate-200";
        div.innerHTML = `
            <div class="grid grid-cols-[120px_1fr_80px_auto] gap-3 flex-1 items-start">
                <div>
                    <select name="luaran[${i}][jenis]" class="input-field" style="font-size:13px;">
                        ${Object.entries(jenisOptions).map(([v,l]) =>
                            `<option value="${v}" ${data.jenis===v?"selected":""}>${l}</option>`
                        ).join("")}
                    </select>
                </div>
                <div>
                    <input type="text" name="luaran[${i}][deskripsi]"
                           value="${data.deskripsi||}"
                           placeholder="mis. Artikel terindeks Sinta..."
                           class="input-field" style="font-size:13px;" required>
                </div>
                <div>
                    <input type="number" name="luaran[${i}][jumlah_minimal]"
                           value="${data.jumlah_minimal||1}"
                           min="1" class="input-field text-center font-mono" style="font-size:13px;">
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <label class="flex items-center gap-1.5 text-slate-600 whitespace-nowrap" style="font-size:13px;">
                        <input type="checkbox" name="luaran[${i}][wajib]" ${data.wajib!==false?"checked":""} class="rounded">
                        Wajib
                    </label>
                </div>
            </div>
            <button type="button" onclick="removeLuaran(${i})"
                    class="mt-1 p-1 text-slate-400 hover:text-red-500 transition flex-shrink-0">
                <i class="ti ti-x" style="font-size:16px;"></i>
            </button>
        `;
        document.getElementById("luaran-list").appendChild(div);
    }

    function removeLuaran(i) {
        document.getElementById("luaran-" + i)?.remove();
        if (!document.getElementById("luaran-list").children.length) {
            document.getElementById("luaran-empty").style.display = "block";
        }
    }

    // Init old input
    @if(old("luaran"))
        @foreach(old("luaran") as $i => $l)
            addLuaran({ jenis: "{{ $l["jenis"] }}", deskripsi: "{{ addslashes($l["deskripsi"]) }}", jumlah_minimal: {{ $l["jumlah_minimal"] ?? 1 }}, wajib: {{ isset($l["wajib"]) ? "true" : "false" }} });
        @endforeach
    @endif
    </script>
</x-app-layout>
