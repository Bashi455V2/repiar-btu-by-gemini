<x-guest-layout>
    {{-- อาจจะมีการตั้งชื่อ Title ของหน้าที่นี่ ถ้า Guest Layout รองรับ Slot ชื่อ title --}}
    {{-- <x-slot name="title">สร้างบัญชีผู้ใช้ - Btu Repair</x-slot> --}}

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- <div class="flex justify-center mb-6">
            <a href="/">
                -- SVG Logo Btu Repair ที่เราใช้ใน Welcome Page --
                <svg class="h-12 w-auto text-sky-600 dark:text-sky-500" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2L3 7V17L12 22L21 17V7L12 2Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3 7L12 12L21 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 12V22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16.5 9.75L12 12L7.5 9.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <ellipse cx="12" cy="6.5" rx="1.5" ry="1" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </a>
        </div> --}}
        <div class="mb-6 text-center">
            <h1 class="text-2xl font-bold text-slate-700 dark:text-slate-200">
                สร้างบัญชีผู้ใช้ใหม่
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                เข้าร่วมระบบแจ้งซ่อม Btu Repair วันนี้!
            </p>
        </div>

        <div>
            <x-input-label for="name" :value="__('ชื่อ-นามสกุล')" class="text-slate-700 dark:text-slate-300" />
            <x-text-input id="name" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('อีเมล')" class="text-slate-700 dark:text-slate-300" />
            <x-text-input id="email" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('รหัสผ่าน')" class="text-slate-700 dark:text-slate-300" />
            <x-text-input id="password" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('ยืนยันรหัสผ่าน')" class="text-slate-700 dark:text-slate-300" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- เพิ่มส่วนสำหรับเลือก Role ถ้าต้องการให้ผู้ใช้เลือกตอนลงทะเบียน หรือ Admin กำหนดทีหลัง --}}
        {{-- <div class="mt-4">
            <x-input-label for="role" :value="__('ประเภทผู้ใช้งาน')" class="text-slate-700 dark:text-slate-300" />
            <select id="role" name="role" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200">
                <option value="user">ผู้ใช้งานทั่วไป</option>
                -- <option value="technician">ช่างเทคนิค</option> --
                -- <option value="admin">ผู้ดูแลระบบ</option> --  // ปกติ admin ไม่ควรให้ลงทะเบียนเอง
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div> --}}


        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-sky-600 hover:text-sky-500 dark:text-sky-400 dark:hover:text-sky-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 dark:focus:ring-offset-slate-800" href="{{ route('login') }}">
                {{ __('เป็นสมาชิกอยู่แล้ว?') }}
            </a>

            {{-- <x-primary-button class="ms-4 bg-sky-600 hover:bg-sky-700 focus:bg-sky-700 active:bg-sky-800 dark:bg-sky-500 dark:hover:bg-sky-600 dark:focus:bg-sky-600 dark:active:bg-sky-700">
                {{ __('ลงทะเบียน') }}
            </x-primary-button> --}}
            <button type="submit"
                    class="ms-4 inline-flex items-center px-4 py-2.5 bg-sky-600 border border-transparent rounded-lg font-semibold text-base text-white tracking-widest hover:bg-sky-700 focus:bg-sky-700 active:bg-sky-800 dark:bg-sky-500 dark:hover:bg-sky-600 dark:focus:bg-sky-600 dark:active:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                {{ __('ลงทะเบียน') }}
            </button>
        </div>
    </form>
</x-guest-layout>