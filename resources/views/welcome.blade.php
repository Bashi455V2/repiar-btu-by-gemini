<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Btu Repair - ระบบแจ้งซ่อมอาคาร</title> {{-- เปลี่ยนชื่อแอป --}}

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>
    <body class="font-sans antialiased bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 flex flex-col min-h-screen">

        <nav class="bg-white dark:bg-slate-800/60 backdrop-blur-md border-b border-slate-200 dark:border-slate-700 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <a href="{{ url('/') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
                        {{-- โลโก้ SVG --}}
                        <svg class="h-10 w-10 text-sky-600 dark:text-sky-500" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L3 7V17L12 22L21 17V7L12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 7L12 12L21 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 12V22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16.5 9.75L12 12L7.5 9.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <ellipse cx="12" cy="6.5" rx="1.5" ry="1" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                        <span class="self-center text-2xl font-semibold whitespace-nowrap text-slate-700 dark:text-white">Btu Repair</span> {{-- เปลี่ยนชื่อแอป --}}
                    </a>

                    <div class="flex items-center space-x-2 sm:space-x-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="px-4 py-2 sm:px-5 sm:py-2.5 text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 focus:ring-4 focus:ring-sky-300 rounded-lg dark:bg-sky-500 dark:hover:bg-sky-600 dark:focus:ring-sky-800 transition ease-in-out duration-150">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="px-4 py-2 sm:px-5 sm:py-2.5 text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-sky-600 dark:hover:text-sky-400 focus:outline-none transition ease-in-out duration-150 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700">
                                    เข้าสู่ระบบ
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-4 py-2 sm:px-5 sm:py-2.5 text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 focus:ring-4 focus:ring-sky-300 rounded-lg dark:bg-sky-500 dark:hover:bg-sky-600 dark:focus:ring-sky-800 transition ease-in-out duration-150">
                                        ลงทะเบียน
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <main class="flex-grow">
            <section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-sky-800 to-teal-700 text-white py-28 sm:py-36 px-4 sm:px-6 lg:px-8 text-center">
                <div class="absolute inset-0 opacity-10">
                    {{-- Optional: Add subtle background pattern or shapes --}}
                </div>
                <div class="relative max-w-3xl mx-auto">
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold mb-6 leading-tight tracking-tight">
                        ยกระดับการแจ้งซ่อม<span class="block sm:inline">สู่ระบบดิจิทัล</span>
                    </h1>
                    <p class="text-lg sm:text-xl mb-10 text-slate-300 max-w-2xl mx-auto">
                        "Btu Repair" ทำให้การแจ้งปัญหา ติดตามงานซ่อม และการจัดการสำหรับทุกฝ่าย เป็นเรื่องง่าย สะดวก และทันสมัย
                    </p>
                    <div class="mt-10">
                        <p class="text-md sm:text-lg font-medium text-slate-200">
                            พร้อมเริ่มต้นหรือยัง? <a href="{{ route('login') }}" class="font-semibold text-sky-400 hover:text-sky-300 underline underline-offset-4 transition">เข้าสู่ระบบ</a> หรือ <a href="{{ route('register') }}" class="font-semibold text-sky-400 hover:text-sky-300 underline underline-offset-4 transition">สร้างบัญชีใหม่</a> เพื่อใช้งานระบบ
                        </p>
                    </div>
                </div>
            </section>

            <section class="py-16 sm:py-24 bg-slate-100 dark:bg-slate-800">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center mb-12 sm:mb-16">
                        <h2 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-white">
                            ออกแบบมาเพื่อทุกคน
                        </h2>
                        <p class="mt-3 text-lg text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                            ไม่ว่าคุณจะเป็นผู้แจ้งซ่อม ช่างเทคนิค หรือผู้ดูแลระบบ "Btu Repair" มีเครื่องมือที่ตอบโจทย์
                        </p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-10">
                        <div class="flex flex-col items-center text-center p-6 sm:p-8 bg-white dark:bg-slate-700/50 rounded-xl shadow-lg hover:shadow-sky-500/10 transition-all duration-300 transform hover:-translate-y-1">
                            <div class="p-4 bg-sky-100 dark:bg-sky-800/50 rounded-full mb-5 ring-4 ring-sky-200 dark:ring-sky-700/50">
                                <svg class="w-10 h-10 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </div>
                            <h3 class="text-xl font-semibold mb-2 text-slate-900 dark:text-white">ผู้ใช้งานทั่วไป</h3>
                            <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                                แจ้งปัญหาได้รวดเร็ว ระบุตำแหน่งชัดเจน พร้อมติดตามสถานะการซ่อมได้ตลอดเวลา
                            </p>
                        </div>
                        <div class="flex flex-col items-center text-center p-6 sm:p-8 bg-white dark:bg-slate-700/50 rounded-xl shadow-lg hover:shadow-sky-500/10 transition-all duration-300 transform hover:-translate-y-1">
                            <div class="p-4 bg-sky-100 dark:bg-sky-800/50 rounded-full mb-5 ring-4 ring-sky-200 dark:ring-sky-700/50">
                                <svg class="w-10 h-10 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z"></path></svg>
                            </div>
                            <h3 class="text-xl font-semibold mb-2 text-slate-900 dark:text-white">ทีมช่างเทคนิค</h3>
                            <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                                รับการแจ้งเตือนงานใหม่ จัดการตารางงาน อัปเดตสถานะ และปิดงานผ่านระบบได้อย่างง่ายดาย
                            </p>
                        </div>
                        <div class="flex flex-col items-center text-center p-6 sm:p-8 bg-white dark:bg-slate-700/50 rounded-xl shadow-lg hover:shadow-sky-500/10 transition-all duration-300 transform hover:-translate-y-1">
                            <div class="p-4 bg-sky-100 dark:bg-sky-800/50 rounded-full mb-5 ring-4 ring-sky-200 dark:ring-sky-700/50">
                                <svg class="w-10 h-10 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <h3 class="text-xl font-semibold mb-2 text-slate-900 dark:text-white">ผู้ดูแลระบบ</h3>
                            <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                                เข้าถึงภาพรวมของระบบ, จัดการข้อมูลผู้ใช้, กำหนดบทบาท, และดูรายงานสรุปผลการดำเนินงาน
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="bg-slate-800 dark:bg-black text-slate-400 dark:text-slate-500 py-8 text-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <p>&copy; {{ date('Y') }} Btu Repair. All rights reserved.</p> {{-- เปลี่ยนชื่อแอป --}}
                <p class="mt-1">ออกแบบและพัฒนาโดย [Btu Team หรือชื่อทีม/หน่วยงานของคุณ]</p>
            </div>
        </footer>

    </body>
</html>