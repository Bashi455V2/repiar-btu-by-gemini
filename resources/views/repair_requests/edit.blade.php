<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('แก้ไขรายการแจ้งซ่อม #') }}{{ $repairRequest->id }} - {{ Str::limit($repairRequest->title, 50) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                    <form method="POST" action="{{ route('repair_requests.update', $repairRequest->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- ส่วนข้อมูลทั่วไปที่ User เจ้าของ (ถ้าสถานะอนุญาต), Admin, หรือ Technician (ที่ Assign) อาจจะแก้ไขได้ตาม Policy 'update' --}}
                        <div class="mt-4">
                            <x-input-label for="title" :value="__('เรื่อง/อาการเบื้องต้น')" class="text-slate-700 dark:text-slate-300" />
                            {{-- ถ้าต้องการให้ User ทั่วไปแก้ไขไม่ได้เมื่อสถานะไม่ใช่ 'รอดำเนินการ' อาจจะต้องเพิ่มเงื่อนไข readonly ที่นี่ หรือ Policy จัดการแล้ว --}}
                            <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $repairRequest->title)" required />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" :value="__('รายละเอียดปัญหา')" class="text-slate-700 dark:text-slate-300" />
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" required>{{ old('description', $repairRequest->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="location_id" :value="__('สถานที่/อาคาร')" class="text-slate-700 dark:text-slate-300" />
                            <select id="location_id" name="location_id" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" required>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id', $repairRequest->location_id) == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('location_id')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="category_id" :value="__('หมวดหมู่ปัญหา')" class="text-slate-700 dark:text-slate-300" />
                            <select id="category_id" name="category_id" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" required>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $repairRequest->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="requester_phone" :value="__('เบอร์โทรศัพท์ติดต่อ')" class="text-slate-700 dark:text-slate-300" />
                            <x-text-input id="requester_phone" class="block mt-1 w-full" type="text" name="requester_phone" :value="old('requester_phone', $repairRequest->requester_phone)" />
                            <x-input-error :messages="$errors->get('requester_phone')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="image" :value="__('รูปภาพประกอบ (เลือกใหม่ถ้าต้องการเปลี่ยน)')" class="text-slate-700 dark:text-slate-300" />
                            @if ($repairRequest->image_path)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($repairRequest->image_path) }}" alt="Current Image" class="max-h-48 rounded-md shadow">
                                    <label for="clear_image" class="inline-flex items-center mt-1">
                                        <input type="checkbox" id="clear_image" name="clear_image" value="1" class="rounded border-slate-300 text-sky-600 shadow-sm focus:ring-sky-500">
                                        <span class="ms-2 text-sm text-slate-600 dark:text-slate-400">{{ __('ลบรูปภาพปัจจุบัน') }}</span>
                                    </label>
                                </div>
                            @endif
                            <input id="image" name="image" type="file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 mt-1"/>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </div>

                        {{-- ส่วนสำหรับ Admin และ Technician ที่ได้รับมอบหมาย (ตามสิทธิ์ 'update' ใน Policy) --}}
                        {{-- สิทธิ์ 'update' ใน Policy ของคุณจะกรองว่าใครสามารถเข้าหน้านี้ได้ --}}
                        {{-- User เจ้าของงานถ้าสถานะเป็น 'รอดำเนินการ' ก็อาจจะเข้ามาได้ แต่จะไม่เห็นฟิลด์เหล่านี้ --}}
                        @if (Auth::user()->is_admin || Auth::user()->is_technician)
                            {{-- เงื่อนไขนี้เพื่อให้แน่ใจว่า Technician ที่ไม่ได้ Assign งานนี้ (แต่ Policy update อาจจะยังอนุญาตให้เข้ามาได้ถ้างานยังไม่ Assign) --}}
                            {{-- หรือ Admin จะเห็นส่วนนี้เสมอ --}}
                            @if (Auth::user()->is_admin || ($repairRequest->assigned_to_user_id === Auth::id() || is_null($repairRequest->assigned_to_user_id)))
                                <hr class="my-6 border-slate-300 dark:border-slate-600">
                                <h4 class="text-md font-semibold text-slate-700 dark:text-slate-200 mb-2">ส่วนของผู้ดูแล/ช่าง</h4>

                                <div class="mt-4">
                                    <x-input-label for="status_id" :value="__('สถานะปัจจุบัน')" class="text-slate-700 dark:text-slate-300" />
                                    <select id="status_id" name="status_id" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200">
                                        @foreach ($statuses as $status) {{-- ตรวจสอบว่า Controller ส่ง $statuses มา --}}
                                            <option value="{{ $status->id }}" {{ old('status_id', $repairRequest->status_id) == $status->id ? 'selected' : '' }}>
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('status_id')" class="mt-2" />
                                </div>

                                @if (Auth::user()->is_admin) {{-- "มอบหมายให้ช่าง" แสดงให้ Admin เท่านั้น --}}
                                <div class="mt-4">
                                    <x-input-label for="assigned_to_user_id" :value="__('มอบหมายให้ช่าง')" class="text-slate-700 dark:text-slate-300" />
                                    <select id="assigned_to_user_id" name="assigned_to_user_id" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200">
                                        <option value="">-- ยังไม่มอบหมาย --</option>
                                        @foreach ($technicians as $technician) {{-- ตรวจสอบว่า Controller ส่ง $technicians มา --}}
                                            <option value="{{ $technician->id }}" {{ old('assigned_to_user_id', $repairRequest->assigned_to_user_id) == $technician->id ? 'selected' : '' }}>
                                                {{ $technician->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('assigned_to_user_id')" class="mt-2" />
                                </div>
                                @endif

                                {{-- "หมายเหตุจากช่าง/ผู้ดำเนินการ" แสดงให้ Admin และ Technician ที่ได้รับมอบหมายงานนี้ --}}
                                <div class="mt-4">
                                    <x-input-label for="remarks_by_technician" :value="Auth::user()->is_technician ? __('หมายเหตุจากช่าง') : __('หมายเหตุ (โดยผู้ดำเนินการ/Admin)')" class="text-slate-700 dark:text-slate-300" />
                                    <textarea id="remarks_by_technician" name="remarks_by_technician" rows="3" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200">{{ old('remarks_by_technician', $repairRequest->remarks_by_technician) }}</textarea>
                                    <x-input-error :messages="$errors->get('remarks_by_technician')" class="mt-2" />
                                </div>
                            @endif
                        @endif


                        <div class="flex items-center justify-end mt-8 space-x-4">
                            @php
                                $previousUrlEffective = url()->previous();
                                $currentUrlFull = url()->full();

                                $defaultFallbackRouteEdit = route('repair_requests.show', $repairRequest->id); // Default คือกลับไปหน้า show ของ item นี้

                                if (Auth::check()) {
                                    if (Auth::user()->is_admin) {
                                        // สำหรับ Admin, ถ้าไม่ได้มาจากหน้าอื่นที่ชัดเจน อาจจะให้กลับไปหน้า manage หรือ show
                                        // $defaultFallbackRouteEdit = route('admin.manage');
                                    } elseif (Auth::user()->is_technician) {
                                        $defaultFallbackRouteEdit = route('repair_requests.index'); // Technician กลับไปหน้ารายการของตัวเอง (Controller index จัดการ view)
                                    } else { // User ทั่วไป
                                        $defaultFallbackRouteEdit = route('repair_requests.index'); // User ทั่วไป กลับไปหน้ารายการของตัวเอง (Controller index จัดการ view)
                                    }
                                }

                                $usePreviousUrl = ($previousUrlEffective &&
                                                   $previousUrlEffective !== $currentUrlFull &&
                                                   $previousUrlEffective !== route('repair_requests.edit', $repairRequest->id, false) &&
                                                   $previousUrlEffective !== route('login', [], false) &&
                                                   $previousUrlEffective !== route('register', [], false));

                                $cancelUrl = $usePreviousUrl ? $previousUrlEffective : $defaultFallbackRouteEdit;
                            @endphp
                            <a href="{{ $cancelUrl }}" class="text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 dark:focus:ring-offset-slate-800">
                                {{ __('ยกเลิก') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-sky-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-700 active:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                                <i class="fas fa-save mr-2"></i>{{ __('บันทึกการเปลี่ยนแปลง') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>