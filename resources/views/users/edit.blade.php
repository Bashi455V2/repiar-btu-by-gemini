{{-- resources/views/users/edit.blade.php (ตัวอย่าง) --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('แก้ไขผู้ใช้: ') }} {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 shadow-xl sm:rounded-lg">
                <div class="p-4 sm:p-8">
                    {{-- <h3 class="text-lg font-medium text-slate-900 dark:text-slate-100 mb-6">
                        ฟอร์มแก้ไขข้อมูลผู้ใช้
                    </h3> --}}

                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- Name --}}
                        <div class="mt-4">
                            <x-input-label for="name" :value="__('ชื่อ-นามสกุล')" class="text-slate-700 dark:text-slate-300" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" :value="old('name', $user->name)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        {{-- Email --}}
                        <div class="mt-4">
                            <x-input-label for="email" :value="__('อีเมล')" class="text-slate-700 dark:text-slate-300" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" :value="old('email', $user->email)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        {{-- Password (Optional) --}}
                        <div class="mt-4">
                            <x-input-label for="password" :value="__('รหัสผ่านใหม่ (กรอกเมื่อต้องการเปลี่ยน)')" class="text-slate-700 dark:text-slate-300" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" autocomplete="new-password" />
                            <x-input-error class="mt-2" :messages="$errors->get('password')" />
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mt-4">
                            <x-input-label for="password_confirmation" :value="__('ยืนยันรหัสผ่านใหม่')" class="text-slate-700 dark:text-slate-300" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" autocomplete="new-password" />
                            <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                        </div>

                        {{-- Roles --}}
                        <div class="mt-6">
                            <h4 class="text-md font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('บทบาท') }}</h4>
                            <div class="space-y-2">
                                <label for="is_admin" class="flex items-center">
                                    <input id="is_admin" name="is_admin" type="checkbox" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }} class="rounded border-slate-300 dark:border-slate-600 text-sky-600 shadow-sm focus:ring-sky-500 dark:focus:ring-offset-slate-800">
                                    <span class="ms-2 text-sm text-slate-600 dark:text-slate-400">{{ __('แอดมิน (Admin)') }}</span>
                                </label>
                                <label for="is_technician" class="flex items-center">
                                    <input id="is_technician" name="is_technician" type="checkbox" value="1" {{ old('is_technician', $user->is_technician) ? 'checked' : '' }} class="rounded border-slate-300 dark:border-slate-600 text-sky-600 shadow-sm focus:ring-sky-500 dark:focus:ring-offset-slate-800">
                                    <span class="ms-2 text-sm text-slate-600 dark:text-slate-400">{{ __('ช่าง (Technician)') }}</span>
                                </label>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('is_admin')" />
                            <x-input-error class="mt-2" :messages="$errors->get('is_technician')" />
                        </div>


                        {{-- Buttons: Save and Cancel --}}
                        <div class="flex items-center justify-end mt-8 space-x-4">
                            <a href="{{ route('admin.users.index') }}" class="text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 dark:focus:ring-offset-slate-800">
                                {{ __('ยกเลิก') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-sky-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-sky-700 focus:bg-sky-700 active:bg-sky-900 dark:bg-sky-500 dark:hover:bg-sky-400 dark:focus:bg-sky-400 dark:active:bg-sky-300 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                                {{ __('บันทึกการเปลี่ยนแปลง') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>