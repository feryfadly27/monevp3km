<x-app-layout>
    <x-slot name="pageTitle">Pengaturan</x-slot>
    <x-slot name="pageSubtitle">Konfigurasi sistem Monev P3KM</x-slot>

    @php $tab = request('tab', 'fakultas'); @endphp

    {{-- Tab navigation --}}
    <div class="flex items-center gap-1 mb-6 border-b border-slate-200">
        @php
        $tabs = [
            'fakultas' => ['icon' => 'ti-building',    'label' => 'Fakultas & Prodi'],
            'kriteria' => ['icon' => 'ti-list-check',  'label' => 'Kriteria Penilaian'],
            'kategori' => ['icon' => 'ti-folder-open', 'label' => 'Kategori Kegiatan'],
        ];
        @endphp
        @foreach($tabs as $key => $meta)
        <a href="{{ request()->fullUrlWithQuery(['tab' => $key]) }}"
           class="flex items-center gap-1.5 px-4 py-2.5 rounded-t-[6px] border-b-2 transition-colors -mb-px
               {{ $tab === $key
                   ? 'border-navy text-navy bg-white font-semibold'
                   : 'border-transparent text-slate-400 hover:text-navy hover:bg-slate-50' }}"
           style="font-size:14px;">
            <i class="ti {{ $meta['icon'] }}" style="font-size:16px;"></i>
            {{ $meta['label'] }}
        </a>
        @endforeach
    </div>

    {{-- Tab content --}}
    @if($tab === 'fakultas')
        @livewire('setting-fakultas')
    @elseif($tab === 'kriteria')
        @livewire('setting-kriteria')
    @elseif($tab === 'kategori')
        @livewire('setting-kategori')
    @endif

</x-app-layout>
