@php
    $role = auth()->user()->getRoleNames()->first() ?? '';
@endphp

@if($role === 'admin')
    <div class="pt-2 pb-1">
        <p class="px-5 font-medium text-white/30 uppercase tracking-widest mb-1" style="font-size:12px;">Utama</p>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="ti ti-layout-dashboard text-base"></i> Dashboard
        </a>
        <a href="{{ route('kegiatan.index') }}" class="nav-item {{ request()->routeIs('kegiatan.index','kegiatan.create','kegiatan.edit','kegiatan.show') ? 'active' : '' }}">
            <i class="ti ti-list-details text-base"></i> Kegiatan
        </a>
        <a href="{{ route('kegiatan.trash') }}" class="nav-item {{ request()->routeIs('kegiatan.trash') ? 'active' : '' }}">
            <i class="ti ti-trash text-base"></i> Tong Sampah
        </a>
        <a href="{{ route('skema.index') }}" class="nav-item {{ request()->routeIs('skema.*') ? 'active' : '' }}">
            <i class="ti ti-file-text text-base"></i> Skema
        </a>
    </div>
    <div class="pt-2 pb-1">
        <p class="px-5 font-medium text-white/30 uppercase tracking-widest mb-1" style="font-size:12px;">Penilaian</p>
        <a href="{{ route('reviewer.assign') }}" class="nav-item {{ request()->routeIs('reviewer.*') ? 'active' : '' }}">
            <i class="ti ti-user-check text-base"></i> Assign Reviewer
        </a>
        <a href="{{ route('rekap.index') }}" class="nav-item {{ request()->routeIs('rekap.*') ? 'active' : '' }}">
            <i class="ti ti-chart-bar text-base"></i> Rekap & Laporan
        </a>
    </div>
    <div class="pt-2 pb-1">
        <p class="px-5 font-medium text-white/30 uppercase tracking-widest mb-1" style="font-size:12px;">Sistem</p>
        <a href="{{ route('dosen.index') }}" class="nav-item {{ request()->routeIs('dosen.*') ? 'active' : '' }}">
            <i class="ti ti-school text-base"></i> Data Dosen
        </a>
        <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="ti ti-users text-base"></i> Kelola User
        </a>
        <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="ti ti-settings text-base"></i> Pengaturan
        </a>
    </div>

@elseif($role === 'reviewer')
    <div class="pt-2 pb-1">
        <p class="px-5 font-medium text-white/30 uppercase tracking-widest mb-1" style="font-size:12px;">Penilaian</p>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="ti ti-layout-dashboard text-base"></i> Dashboard
        </a>
        <a href="{{ route('tugas.index') }}" class="nav-item {{ request()->routeIs('tugas.*') ? 'active' : '' }}">
            <i class="ti ti-clipboard-check text-base"></i> Daftar Tugas
        </a>
        <a href="{{ route('kegiatan.index') }}" class="nav-item {{ request()->routeIs('kegiatan.*') ? 'active' : '' }}">
            <i class="ti ti-list-details text-base"></i> Semua Kegiatan
        </a>
    </div>

@elseif($role === 'dosen')
    <div class="pt-2 pb-1">
        <p class="px-5 font-medium text-white/30 uppercase tracking-widest mb-1" style="font-size:12px;">Kegiatan Saya</p>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="ti ti-layout-dashboard text-base"></i> Dashboard
        </a>
        <a href="{{ route('kegiatan-saya.index') }}" class="nav-item {{ request()->routeIs('kegiatan-saya.*') ? 'active' : '' }}">
            <i class="ti ti-list-details text-base"></i> Kegiatan Saya
        </a>
    </div>
@endif
