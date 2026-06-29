<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Monev P3KM') }} — Login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/dist/tabler-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-page">
    <div class="min-h-screen flex">

        {{-- Panel kiri: branding --}}
        <div class="hidden lg:flex lg:w-[480px] xl:w-[520px] flex-col justify-between bg-navy flex-shrink-0 p-12">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-sage flex items-center justify-center">
                        <i class="ti ti-chart-bar text-white" style="font-size:18px;"></i>
                    </div>
                    <span class="text-white font-heading font-semibold" style="font-size:18px;">Monev P3KM</span>
                </div>

                <div class="mt-16">
                    <h1 class="text-white font-heading font-bold leading-tight" style="font-size:36px;">
                        Sistem Monitoring<br>& Evaluasi Kegiatan
                    </h1>
                    <p class="text-white/50 mt-4 leading-relaxed" style="font-size:15px;">
                        Platform pengelolaan dan penilaian kegiatan penelitian, pengabdian, dan kreativitas mahasiswa P3KM.
                    </p>
                </div>

                <div class="mt-12 space-y-4">
                    @foreach([
                        ['ti-file-check', 'Kelola kegiatan & pengajuan'],
                        ['ti-users-group', 'Manajemen reviewer & dosen'],
                        ['ti-chart-dots-3', 'Rekap & laporan penilaian'],
                    ] as $feat)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0">
                            <i class="ti {{ $feat[0] }} text-sage" style="font-size:16px;"></i>
                        </div>
                        <span class="text-white/60" style="font-size:14px;">{{ $feat[1] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <p class="text-white/25" style="font-size:13px;">© {{ date('Y') }} P3KM — Monev P3KM</p>
        </div>

        {{-- Panel kanan: form --}}
        <div class="flex-1 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-[400px]">
                {{ $slot }}
            </div>
        </div>

    </div>
</body>
</html>
