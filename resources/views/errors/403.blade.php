<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - ไม่มีสิทธิ์เข้าถึง</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 flex flex-col items-center justify-center min-h-screen p-4">
    <div class="bg-white dark:bg-slate-800 shadow-2xl rounded-xl p-8 sm:p-12 max-w-lg w-full text-center">
        {{-- ไอคอน Error --}}
        <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 dark:bg-red-800/30 mb-6">
            <svg class="h-10 w-10 text-red-600 dark:text-red-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>
        </div>

        <h1 class="text-6xl font-bold text-sky-600 dark:text-sky-500 mb-2">403</h1>
        <h2 class="text-2xl sm:text-3xl font-semibold text-slate-800 dark:text-slate-100 mb-4">ไม่มีสิทธิ์เข้าถึง</h2>
        <p class="text-slate-600 dark:text-slate-400 mb-8 text-sm sm:text-base">
            ขออภัยค่ะ คุณไม่ได้รับอนุญาตให้เข้าถึงหน้าที่คุณร้องขอ
            กรุณาตรวจสอบสิทธิ์ของคุณ หรือติดต่อผู้ดูแลระบบหากพบปัญหา
        </p>

        <div class="space-y-4 sm:space-y-0 sm:space-x-4 flex flex-col sm:flex-row justify-center">
            <a href="{{ url()->previous(route('welcome')) }}"
               class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 bg-slate-200 hover:bg-slate-300 focus:ring-4 focus:ring-slate-300/50 rounded-lg font-medium text-slate-700 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600 dark:focus:ring-slate-600/50 transition ease-in-out duration-150 text-sm">
                <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                ย้อนกลับ
            </a>

            @auth
                @php
                    $homeForUser = route('welcome'); // Default
                    if (Auth::user()->is_admin) {
                        $homeForUser = route('dashboard');
                    } elseif (Auth::user()->is_technician) {
                        $homeForUser = route('technician.queue.index');
                    } else {
                        $homeForUser = route('profile.edit'); // หรือ route('repair_requests.index')
                    }
                @endphp
                <a href="{{ $homeForUser }}"
                   class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 bg-sky-600 hover:bg-sky-700 focus:ring-4 focus:ring-sky-300/50 rounded-lg font-medium text-white dark:bg-sky-500 dark:hover:bg-sky-600 dark:focus:ring-sky-800/50 transition ease-in-out duration-150 text-sm">
                    กลับสู่หน้าหลักของคุณ
                    <svg class="w-5 h-5 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </a>
            @else
                <a href="{{ route('welcome') }}"
                   class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 bg-sky-600 hover:bg-sky-700 focus:ring-4 focus:ring-sky-300/50 rounded-lg font-medium text-white dark:bg-sky-500 dark:hover:bg-sky-600 dark:focus:ring-sky-800/50 transition ease-in-out duration-150 text-sm">
                    กลับสู่หน้าแรก
                    <svg class="w-5 h-5 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </a>
            @endauth
        </div>
    </div>
</body>
</html>