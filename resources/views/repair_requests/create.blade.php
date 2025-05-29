<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('แจ้งซ่อมใหม่') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8"> {{-- ปรับ max-w ให้แคบลงเล็กน้อยสำหรับฟอร์ม --}}
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-xl sm:rounded-lg"> {{-- เพิ่ม shadow-xl --}}
                <div class="p-6 sm:p-8"> {{-- เพิ่ม padding สำหรับจอ sm ขึ้นไป --}}
                    {{-- <h3 class="text-lg font-medium text-slate-900 dark:text-slate-100 mb-6">
                        กรอกรายละเอียดการแจ้งซ่อม
                    </h3> --}}
                    <form method="POST" action="{{ route('repair_requests.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mt-4">
                            <x-input-label for="title" :value="__('เรื่อง/อาการเบื้องต้น')" class="text-slate-700 dark:text-slate-300" />
                            <x-text-input id="title" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" type="text" name="title" :value="old('title')" required autofocus />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" :value="__('รายละเอียดปัญหา')" class="text-slate-700 dark:text-slate-300" />
                            <textarea id="description" name="description" rows="5" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" required>{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="location_id" :value="__('สถานที่/อาคาร')" class="text-slate-700 dark:text-slate-300" />
                            <select id="location_id" name="location_id" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" required>
                                <option value="">-- เลือกสถานที่ --</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }} {{ $location->building ? '('.$location->building.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('location_id')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="category_id" :value="__('หมวดหมู่ปัญหา')" class="text-slate-700 dark:text-slate-300" />
                            <select id="category_id" name="category_id" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" required>
                                <option value="">-- เลือกหมวดหมู่ --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="requester_phone" :value="__('เบอร์โทรศัพท์ติดต่อ (ถ้ามี)')" class="text-slate-700 dark:text-slate-300" />
                            <x-text-input id="requester_phone" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" type="text" name="requester_phone" :value="old('requester_phone', Auth::user()->phone_number ?? '')" />
                            <x-input-error :messages="$errors->get('requester_phone')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="image" :value="__('รูปภาพประกอบ (ถ้ามี)')" class="text-slate-700 dark:text-slate-300" />
                            <input id="image" name="image" type="file" class="block w-full text-sm text-slate-500 dark:text-slate-400
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-lg file:border-0
                                file:text-sm file:font-semibold
                                file:bg-sky-100 dark:file:bg-sky-700/50 file:text-sky-700 dark:file:text-sky-200
                                hover:file:bg-sky-200 dark:hover:file:bg-sky-600/50
                                mt-1 border border-slate-300 dark:border-slate-600 rounded-lg cursor-pointer
                                focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"/>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        {{-- Buttons: Save and Cancel --}}
                        <div class="flex items-center justify-end mt-8 space-x-4">
                            <a href="{{ route('repair_requests.index') }}" class="text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 dark:focus:ring-offset-slate-800">
                                {{ __('ยกเลิก') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-sky-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-sky-700 focus:bg-sky-700 active:bg-sky-900 dark:bg-sky-500 dark:hover:bg-sky-400 dark:focus:bg-sky-400 dark:active:bg-sky-300 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                                {{ __('ส่งเรื่องแจ้งซ่อม') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>