<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('เพิ่มผู้ใช้ใหม่') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8">
                    {{-- <h3 class="text-lg font-medium text-slate-900 dark:text-slate-100 mb-6">
                        กรอกข้อมูลผู้ใช้ใหม่
                    </h3> --}}

                    <form method="POST" action="{{ route('admin.users.store') }}">
                        @csrf

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
                            <x-text-input id="password" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" type="password" name="password" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('ยืนยันรหัสผ่าน')" class="text-slate-700 dark:text-slate-300" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" type="password" name="password_confirmation" required autocomplete="new-password" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="mt-6">
                            <x-input-label :value="__('บทบาทผู้ใช้งาน')" class="mb-2 text-slate-700 dark:text-slate-300 font-medium" />
                            <div class="space-y-3">
                                <label for="is_admin" class="flex items-center p-3 border border-slate-300 dark:border-slate-600 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer">
                                    <input type="hidden" name="is_admin" value="0"> {{-- ส่งค่า 0 ถ้า checkbox ไม่ถูกเลือก --}}
                                    <input id="is_admin" type="checkbox" name="is_admin" value="1" class="h-4 w-4 rounded border-slate-300 dark:border-slate-600 text-sky-600 focus:ring-sky-500 dark:focus:ring-offset-slate-800" {{ old('is_admin') ? 'checked' : '' }}>
                                    <span class="ms-3 text-sm text-slate-700 dark:text-slate-300">{{ __('ผู้ดูแลระบบ (Admin)') }}</span>
                                </label>
                                <x-input-error :messages="$errors->get('is_admin')" class="mt-1 text-xs" />

                                <label for="is_technician" class="flex items-center p-3 border border-slate-300 dark:border-slate-600 rounded-md hover:bg-slate-50 dark:hover:bg-slate-700 cursor-pointer">
                                    <input type="hidden" name="is_technician" value="0"> {{-- ส่งค่า 0 ถ้า checkbox ไม่ถูกเลือก --}}
                                    <input id="is_technician" type="checkbox" name="is_technician" value="1" class="h-4 w-4 rounded border-slate-300 dark:border-slate-600 text-sky-600 focus:ring-sky-500 dark:focus:ring-offset-slate-800" {{ old('is_technician') ? 'checked' : '' }}>
                                    <span class="ms-3 text-sm text-slate-700 dark:text-slate-300">{{ __('ช่างเทคนิค (Technician)') }}</span>
                                </label>
                                <x-input-error :messages="$errors->get('is_technician')" class="mt-1 text-xs" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-8 space-x-4">
                            <a href="{{ route('admin.users.index') }}" class="text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 dark:focus:ring-offset-slate-800">
                                {{ __('ยกเลิก') }}
                            </a>
                            {{-- <x-primary-button> --}}
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-sky-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-sky-700 focus:bg-sky-700 active:bg-sky-900 dark:bg-sky-500 dark:hover:bg-sky-400 dark:focus:bg-sky-400 dark:active:bg-sky-300 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                                {{ __('เพิ่มผู้ใช้') }}
                            </button>
                            {{-- </x-primary-button> --}}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>