<x-guest-layout>
    {{-- อาจจะมีการตั้งชื่อ Title ของหน้าที่นี่ ถ้า Guest Layout รองรับ Slot ชื่อ title --}}
    {{-- <x-slot name="title">เข้าสู่ระบบ - Btu Repair</x-slot> --}}

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
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
                เข้าสู่ระบบ Btu Repair
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                ยินดีต้อนรับกลับมา! กรุณากรอกข้อมูลเพื่อเข้าใช้งาน
            </p>
        </div>


        <div>
            <x-input-label for="email" :value="__('อีเมล')" class="text-slate-700 dark:text-slate-300" />
            <x-text-input id="email" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('รหัสผ่าน')" class="text-slate-700 dark:text-slate-300" />
            <x-text-input id="password" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 dark:border-slate-600 text-sky-600 shadow-sm focus:ring-sky-500 dark:focus:ring-offset-slate-800" name="remember">
                <span class="ms-2 text-sm text-slate-600 dark:text-slate-400">{{ __('จดจำฉันไว้ในระบบ') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="underline text-sm text-sky-600 hover:text-sky-500 dark:text-sky-400 dark:hover:text-sky-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 dark:focus:ring-offset-slate-800" href="{{ route('password.request') }}">
                    {{ __('ลืมรหัสผ่าน?') }}
                </a>
            @endif
        </div>

        <div class="mt-8">
            {{-- <x-primary-button class="w-full justify-center text-lg py-3 bg-sky-600 hover:bg-sky-700 focus:bg-sky-700 active:bg-sky-800 dark:bg-sky-500 dark:hover:bg-sky-600 dark:focus:bg-sky-600 dark:active:bg-sky-700">
                {{ __('เข้าสู่ระบบ') }}
            </x-primary-button> --}}
            <button type="submit"
                    class="w-full inline-flex items-center justify-center px-4 py-3 bg-sky-600 border border-transparent rounded-lg font-semibold text-base text-white tracking-widest hover:bg-sky-700 focus:bg-sky-700 active:bg-sky-800 dark:bg-sky-500 dark:hover:bg-sky-600 dark:focus:bg-sky-600 dark:active:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                {{ __('เข้าสู่ระบบ') }}
            </button>
        </div>

        <div class="mt-6 text-center">
            <p class="text-sm text-slate-600 dark:text-slate-400">
                ยังไม่มีบัญชี?
                <a href="{{ route('register') }}" class="font-medium text-sky-600 hover:text-sky-500 dark:text-sky-400 dark:hover:text-sky-300 underline">
                    ลงทะเบียนที่นี่
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>