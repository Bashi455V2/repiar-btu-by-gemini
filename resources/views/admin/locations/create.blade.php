<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ isset($location) ? __('แก้ไขสถานที่') : __('เพิ่มสถานที่ใหม่') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8">
                    <form method="POST" action="{{ isset($location) ? route('admin.locations.update', $location->id) : route('admin.locations.store') }}">
                        @csrf
                        @if (isset($location))
                            @method('PUT')
                        @endif

                        {{-- Name --}}
                        <div>
                            <x-input-label for="name" :value="__('ชื่อสถานที่')" class="text-slate-700 dark:text-slate-300" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full ..." :value="old('name', $location->name ?? '')" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        {{-- Building --}}
                        <div class="mt-4">
                            <x-input-label for="building" :value="__('อาคาร (ถ้ามี)')" class="text-slate-700 dark:text-slate-300" />
                            <x-text-input id="building" name="building" type="text" class="mt-1 block w-full ..." :value="old('building', $location->building ?? '')" />
                            <x-input-error class="mt-2" :messages="$errors->get('building')" />
                        </div>

                        {{-- Floor --}}
                        <div class="mt-4">
                            <x-input-label for="floor" :value="__('ชั้น (ถ้ามี)')" class="text-slate-700 dark:text-slate-300" />
                            <x-text-input id="floor" name="floor" type="text" class="mt-1 block w-full ..." :value="old('floor', $location->floor ?? '')" />
                            <x-input-error class="mt-2" :messages="$errors->get('floor')" />
                        </div>

                        {{-- Room Number --}}
                        <div class="mt-4">
                            <x-input-label for="room_number" :value="__('เลขห้อง (ถ้ามี)')" class="text-slate-700 dark:text-slate-300" />
                            <x-text-input id="room_number" name="room_number" type="text" class="mt-1 block w-full ..." :value="old('room_number', $location->room_number ?? '')" />
                            <x-input-error class="mt-2" :messages="$errors->get('room_number')" />
                        </div>

                        {{-- Details --}}
                        <div class="mt-4">
                            <x-input-label for="details" :value="__('รายละเอียดเพิ่มเติม (ถ้ามี)')" class="text-slate-700 dark:text-slate-300" />
                            <textarea id="details" name="details" rows="3" class="block mt-1 w-full border-slate-300 ...">{{ old('details', $location->details ?? '') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('details')" />
                        </div>

                        <div class="flex items-center justify-end mt-6 space-x-4">
                            <a href="{{ route('admin.locations.index') }}" class="text-sm text-slate-600 ...">{{ __('ยกเลิก') }}</a>
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-sky-600 ...">
                                {{ isset($location) ? __('บันทึกการเปลี่ยนแปลง') : __('เพิ่มสถานที่') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>