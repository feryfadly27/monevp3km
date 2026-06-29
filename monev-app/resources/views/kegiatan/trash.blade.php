<x-app-layout>
    <x-slot name="pageTitle">Tong Sampah Kegiatan</x-slot>
    <x-slot name="pageSubtitle">Kegiatan yang telah dihapus — bisa dipulihkan atau dihapus permanen</x-slot>

    @if(session('success'))
    <div data-flash class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-[8px] flex items-center gap-2" style="font-size:14px;">
        <i class="ti ti-circle-check text-lg"></i> {{ session('success') }}
    </div>
    @endif

    <div class="flex items-center justify-between mb-4">
        <a href="{{ route('kegiatan.index') }}" class="flex items-center gap-1.5 text-slate-400 hover:text-navy" style="font-size:14px;">
            <i class="ti ti-arrow-left"></i> Kembali ke Daftar Kegiatan
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-[8px]">
        @if($kegiatan->isEmpty())
            <div class="py-16 text-center text-slate-400">
                <i class="ti ti-trash-off block mb-2" style="font-size:36px;"></i>
                <p style="font-size:15px;">Tong sampah kosong</p>
            </div>
        @else
        <table class="data-table">
            <thead>
                <tr>
                    <th class="pl-5">Judul Kegiatan</th>
                    <th>Skema</th>
                    <th>Ketua</th>
                    <th class="text-center w-24">Tahun</th>
                    <th class="text-center w-36">Dihapus</th>
                    <th class="pr-5 text-right w-32">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kegiatan as $k)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="pl-5">
                        <p class="font-medium text-navy" style="font-size:14px;">{{ Str::limit($k->judul, 50) }}</p>
                        <p class="text-slate-400" style="font-size:12px;">{{ $k->kategori?->nama }}</p>
                    </td>
                    <td class="text-slate-500" style="font-size:13px;">{{ $k->skema?->nama }}</td>
                    <td class="text-slate-500" style="font-size:13px;">{{ $k->ketua?->nama }}</td>
                    <td class="text-center font-mono text-slate-500" style="font-size:13px;">{{ $k->tahun }}</td>
                    <td class="text-center text-slate-400" style="font-size:12px;">{{ $k->deleted_at?->format('d M Y H:i') }}</td>
                    <td class="pr-5 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <form method="POST" action="{{ route('kegiatan.restore', $k->id) }}">
                                @csrf
                                <button type="submit"
                                        class="p-1.5 text-slate-400 hover:text-sage hover:bg-emerald-50 rounded transition"
                                        title="Pulihkan">
                                    <i class="ti ti-restore" style="font-size:15px;"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('kegiatan.force-delete', $k->id) }}"
                                  onsubmit="return confirm('Hapus permanen kegiatan ini? Tidak bisa dibatalkan.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded transition"
                                        title="Hapus permanen">
                                    <i class="ti ti-trash-x" style="font-size:15px;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-slate-100">
            {{ $kegiatan->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
