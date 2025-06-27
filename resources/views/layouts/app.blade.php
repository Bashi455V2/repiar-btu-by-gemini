<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        {{-- คอมเมนต์หรือลบบรรทัดนี้ หากไม่ต้องการใช้ Figtree --}}
        {{-- <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" /> --}}

        {{-- เพิ่ม Google Fonts: IBM Plex Sans Thai ที่นี่ (แทนที่ Prompt) --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">


        {{-- VVVVVV  เพิ่ม CSS ของ Font Awesome และ Fancybox ที่นี่  VVVVVV --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
        {{-- ^^^^^^  จบส่วนที่เพิ่ม CSS  ^^^^^^ --}}


        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')

    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-slate-100 dark:bg-slate-900">
            @include('layouts.navigation')

            @if (isset($header))
                <header class="bg-white dark:bg-slate-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main>
                {{ $slot }}
            </main>
        </div>

        {{-- VVVVVV  เพิ่ม JavaScript ของ Fancybox ที่นี่ (ก่อนปิด body) VVVVVV --}}
        <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Fancybox.bind('[data-fancybox]', {
                    // Your custom options
                });
            });
        </script>
        {{-- ^^^^^^  จบส่วนที่เพิ่ม JavaScript  ^^^^^^ --}}

        @stack('scripts')
    </body>
</html>