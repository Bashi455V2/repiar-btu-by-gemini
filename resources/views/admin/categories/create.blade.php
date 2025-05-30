<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('เพิ่มหมวดหมู่ปัญหาใหม่') }}
        </h2>
    </x-slot>
    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8">
                    <form method="POST" action="{{ route('admin.categories.store') }}">
                        @csrf
                        <div>
                            <x-input-label for="name" :value="__('ชื่อหมวดหมู่')" class="text-slate-700 dark:text-slate-300" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" :value="old('name')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" :value="__('คำอธิบาย (ถ้ามี)')" class="text-slate-700 dark:text-slate-300" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200">{{ old('description') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-4">
                            <a href="{{ route('admin.categories.index') }}" class="text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 dark:focus:ring-offset-slate-800">
                                {{ __('ยกเลิก') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-sky-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-sky-700 focus:bg-sky-700 active:bg-sky-900 dark:bg-sky-500 dark:hover:bg-sky-400 dark:focus:bg-sky-400 dark:active:bg-sky-300 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                                {{ __('เพิ่มหมวดหมู่') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>