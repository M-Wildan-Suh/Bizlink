<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <link rel="icon" href="{{ asset('/assets/images/icon.png') }}" type="image/x-icon">

    <!-- Scripts -->
    <!-- Scripts -->
    @if (env('APP_DEPLOY') == true)
        <link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>

<body x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 1000)" @beforeunload.window="loading = true"
    @load.window="setTimeout(() => loading = false, 1000)">
    <!-- Loading overlay -->
    <div x-show="loading" class="fixed inset-0 z-50 flex items-center justify-center bg-white"
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
    <div class=" w-screen h-screen flex items-center justify-center bg-neutral-100 px-4">
        <div class=" w-full max-w-4xl mx-auto">
            <div
                class=" w-full md:aspect-[3/2] p-4 grid grid-cols-1 md:grid-cols-2 gap-4 shadow-md shadow-black/20 rounded-md overflow-hidden bg-white">
                <div class=" rounded-md overflow-hidden relative">
                    <div style="background-image: url({{ asset('assets/images/login.png') }})"
                        class=" w-full h-full bg-cover"></div>
                    {{-- <div class=" absolute w-full h-full top-0 left-0"></div> --}}
                </div>
                <div class=" w-full h-full flex flex-col gap-3 items-center justify-center py-4 md:py-0 px-4 md:px-10">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>
{{-- Script --}}
@if (env('APP_DEPLOY') == true)
    <script src="{{ asset('build/assets/app.js') }}" defer></script>
@endif

</html>
