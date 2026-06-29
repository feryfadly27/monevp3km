<x-app-layout>
    <x-slot name="pageTitle">Kelola Skema</x-slot>
    <x-slot name="pageSubtitle">Atur skema penelitian dan pengabdian masyarakat beserta target luaran</x-slot>

    @if(session('success'))
    <div class="mb-5 px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-[8px] flex items-center gap-2" style="font-size:14px;">
        <i class="ti ti-circle-check text-lg"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-5 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-[8px] flex items-center gap-2" style="font-size:14px;">
        <i class="ti ti-alert-circle text-lg"></i> {{ session('error') }}
    </div>
    @endif

    <div class="flex justify-end mb-5">
        <a href="{{ route('skema.create') }}"
           class="flex items-center gap-2 bg-navy text-white px-4 py-2 rounded-[8px] hover:bg-navy-dark transition"
           style="font-size:14px;">
            <i class="ti ti-plus" style="font-size:16px;"></i> Tambah Skema
        </a>
    </div>

    <div class="space-y-6">
        @foreach($kategoriList as $kat)
        <div>
            {{-- Header kategori --}}
            <div class="flex items-center gap-3 mb-3">
                <div class="h-px flex-1 bg-slate-200"></div>
                <span class="flex items-center gap-2 px-3 py-1 rounded-full text-white font-medium"
                      style="font-size:12px; background: {{ $kat->kode === 'PENELITIAN' ? '#185FA5' : '#0F6E56' }};">
                    <i class="ti ti-{{ $kat->kode === 'PENELITIAN' ? 'microscope' : 'users' }}" style="font-size:13px;"></i>
                    {{ $kat->nama }}
                </span>
                <div class="h-px flex-1 bg-slate-200"></div>
            </div>

            @if($kat->skema->isEmpty())
            <div class="bg-white border border-dashed border-slate-300 rounded-[8px] py-8 text-center text-slate-400" style="font-size:14px;">
                Belum ada skema untuk kategori ini.
            </div>
            @else
            <div class="grid grid-cols-1 gap-3">
                @foreach($kat->skema as $s)
                <div class="bg-white border border-slate-200 rounded-[8px] p-5 {{ !$s->aktif ? 'opacity-60' : '' }}">
                    <div class="flex items-start justify-between gap-4">

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1.5">
                                <span class="font-mono text-white px-2 py-0.5 rounded text-[11px]"
                                      style="background: {{ $kat->kode === 'PENELITIAN' ? '#185FA5' : '#0F6E56' }};">
                                    {{ $s->kode }}
                                </span>
                                @if(!$s->aktif)
                                    <span class="chip chip-dihentikan">Non-aktif</span>
                                @endif
                            </div>
                            <h3 class="font-semibold text-navy font-heading" style="font-size:15px;">{{ $s->nama }}</h3>

                            <div class="flex flex-wrap items-center gap-4 mt-2">
                                <span class="flex items-center gap-1.5 text-slate-500" style="font-size:13px;">
                                    <i class="ti ti-currency-dollar" style="font-size:15px;"></i>
                                    Maks. Rp {{ number_format($s->dana_maksimal / 1000000, 0) }} jt
                                </span>
                                <span class="flex items-center gap-1.5 text-slate-500" style="font-size:13px;">
                                    <i class="ti ti-clock" style="font-size:15px;"></i>
                                    {{ $s->durasi_bulan }} bulan
                                </span>
                                <span class="flex items-center gap-1.5 text-slate-500" style="font-size:13px;">
                                    <i class="ti ti-list-check" style="font-size:15px;"></i>
                                    {{ $s->kegiatan_count }} kegiatan
                                </span>
                            </div>

                            {{-- Luaran target --}}
                            @if($s->luaran->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5 mt-3">
                                @foreach($s->luaran as $l)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded border border-slate-200 text-slate-600"
                                      style="font-size:12px;">
                                    <i class="ti ti-{{ match($l->jenis) {
                                        'PUBLIKASI'=>'book', 'HKI'=>'certificate', 'PRODUK'=>'package',
                                        'LAPORAN'=>'file-text', default=>'paperclip'
                                    } }}" style="font-size:12px;"></i>
                                    {{ $l->deskripsi }}
                                    @if($l->wajib)
                                        <span class="text-red-400 font-bold">*</span>
                                    @endif
                                </span>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-1 flex-shrink-0">
                            <a href="{{ route('skema.edit', $s->id) }}"
                               class="p-1.5 text-slate-400 hover:text-navy hover:bg-slate-100 rounded transition"
                               title="Edit">
                                <i class="ti ti-edit" style="font-size:17px;"></i>
                            </a>
                            @if($s->kegiatan_count === 0)
                            <form method="POST" action="{{ route('skema.destroy', $s->id) }}"
                                  onsubmit="return confirm('Yakin hapus skema {{ addslashes($s->nama) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded transition" title="Hapus">
                                    <i class="ti ti-trash" style="font-size:17px;"></i>
                                </button>
                            </form>
                            @else
                            <button disabled class="p-1.5 text-slate-200 cursor-not-allowed rounded" title="Tidak bisa hapus — ada kegiatan terdaftar">
                                <i class="ti ti-trash" style="font-size:17px;"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>
</x-app-layout>
