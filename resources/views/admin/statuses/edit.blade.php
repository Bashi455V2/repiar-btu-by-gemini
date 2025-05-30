<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('แก้ไขสถานะ: ') }} {{ $status->name }}
        </h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8">
                    <form method="POST" action="{{ route('admin.statuses.update', $status->id) }}">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" :value="__('ชื่อสถานะ')" class="text-slate-700 dark:text-slate-300" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" :value="old('name', $status->name)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="color_class" :value="__('CSS Class สำหรับสี (เช่น bg-yellow-100 text-yellow-800)')" class="text-slate-700 dark:text-slate-300" />
                            <x-text-input id="color_class" name="color_class" type="text" class="mt-1 block w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" :value="old('color_class', $status->color_class)" placeholder="เช่น bg-sky-100 text-sky-800 dark:bg-sky-700 dark:text-sky-100" />
                             <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">ใช้สำหรับแสดงสี Badge ของสถานะในตาราง</p>
                            <x-input-error class="mt-2" :messages="$errors->get('color_class')" />
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-4">
                            <a href="{{ route('admin.statuses.index') }}" class="text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 dark:focus:ring-offset-slate-800">
                                {{ __('ยกเลิก') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-sky-600 ...">
                                {{ __('บันทึกการเปลี่ยนแปลง') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>