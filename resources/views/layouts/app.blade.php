<!DOCTYPE html>

@props(['title' => null, 'head' => null])

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{ asset('/assets/images/icon.png') }}" type="image/x-icon">

    <title>{{ $title ?? '' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/nano.min.css" />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Scripts -->
    @if (env('APP_DEPLOY') == true)
        <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body class="font-sans antialiased" x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 1000)"
    @beforeunload.window="loading = true" @load.window="setTimeout(() => loading = false, 1000)"
    @pageshow.window="loading = false">
    <!-- Loading overlay -->
    <div x-show="loading" class="fixed inset-0 z-[200] flex items-center justify-center bg-white"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        <!-- Animasi tiga titik meloncat -->
        <div class="flex space-x-2">
            <div class="dot w-4 h-4 bg-byolink-1 rounded-full animate-bounce delay-0"></div>
            <div class="dot w-4 h-4 bg-byolink-2 rounded-full animate-bounce delay-200"></div>
            <div class="dot w-4 h-4 bg-byolink-3 rounded-full animate-bounce delay-400"></div>
        </div>
    </div>

    <style>
        /* Menambahkan delay pada animasi */
        .animate-bounce {
            animation: bounce 0.6s infinite alternate;
        }

        .delay-0 {
            animation-delay: 0s;
        }

        .delay-200 {
            animation-delay: 0.2s;
        }

        .delay-400 {
            animation-delay: 0.4s;
        }

        /* Keyframes untuk animasi bounce */
        @keyframes bounce {
            0% {
                transform: translateY(0);
            }

            100% {
                transform: translateY(-8px);
            }
        }
    </style>
    <div class="min-h-screen bg-neutral-100">
        @include('components.admin.navbar')
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

@if (env('APP_DEPLOY') == true)
    <script src="{{ asset('build/assets/app.js') }}" defer></script>
@endif

<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/pickr.min.js"></script>

</html>
