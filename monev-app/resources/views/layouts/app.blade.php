<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'Monev P3KM') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=DM+Sans:wght@400;500&family=Fira+Code:wght@400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/dist/tabler-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-page font-sans antialiased">

<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="w-[210px] flex-shrink-0 bg-navy flex flex-col fixed inset-y-0 left-0 z-30">
        <div class="px-5 py-5 border-b border-white/10">
            <span class="block font-semibold text-white font-heading" style="font-size:17px;">Monev P3KM</span>
            <span class="text-white/40" style="font-size:13px;">P3KM — {{ auth()->user()->getRoleNames()->first() ?? 'User' }}</span>
        </div>

        <nav class="flex-1 py-2">
            @include('layouts.sidebar-nav')
        </nav>

        <div class="px-5 py-4 border-t border-white/10">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-sage flex items-center justify-center font-medium text-white flex-shrink-0" style="font-size:12px;">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </div>
                <div>
                    <div class="text-white font-medium truncate w-28" style="font-size:14px;">{{ auth()->user()->name }}</div>
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="text-white/40 hover:text-white/70 transition-colors" style="font-size:12px;">Keluar</a>
                </div>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="ml-[210px] flex-1 flex flex-col min-h-screen">

        <header class="h-14 bg-white border-b border-slate-200 flex items-center justify-between px-7 sticky top-0 z-20">
            <div>
                <h1 class="font-semibold font-heading text-navy leading-tight" style="font-size:21px;">{{ $pageTitle ?? 'Dashboard' }}</h1>
                @isset($pageSubtitle)
                    <p class="text-slate-400 mt-0.5" style="font-size:13px;">{{ $pageSubtitle }}</p>
                @endisset
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('profile.edit') }}" title="Profil Saya"
                   class="w-8 h-8 rounded-full bg-navy flex items-center justify-center font-medium text-white hover:bg-navy/80 transition-colors" style="font-size:13px;">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </a>
            </div>
        </header>

        <main class="flex-1 p-7">
            {{ $slot }}
        </main>
    </div>

</div>

@livewireScripts
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-flash]').forEach(el => {
        setTimeout(() => el.style.transition = 'opacity .5s', 4000);
        setTimeout(() => el.remove(), 4500);
    });
});
document.addEventListener('livewire:navigated', () => {
    document.querySelectorAll('[data-flash]').forEach(el => {
        setTimeout(() => el.style.transition = 'opacity .5s', 4000);
        setTimeout(() => el.remove(), 4500);
    });
});
</script>
</body>
</html>
