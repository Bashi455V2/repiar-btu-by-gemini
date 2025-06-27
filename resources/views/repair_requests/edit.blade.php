<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
            {{ __('แก้ไขรายการแจ้งซ่อม #') }}{{ $repairRequest->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                    @php
                        // กำหนด Action ของฟอร์มตาม Role ให้ถูกต้อง
                        $updateActionRoute = (Auth::user() && Auth::user()->is_admin)
                            ? route('admin.repair_requests.update', $repairRequest->id)
                            : route('repair_requests.update', $repairRequest->id);
                    @endphp

                    {{-- **สำคัญ:** เพิ่ม enctype="multipart/form-data" เพื่อให้สามารถอัปโหลดไฟล์ได้ --}}
                    <form method="POST" action="{{ $updateActionRoute }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- ส่วนข้อมูลทั่วไป --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Column 1 --}}
                            <div class="space-y-6">
                                <div>
                                    <x-input-label for="title" :value="__('เรื่อง/อาการเบื้องต้น')" />
                                    <x-text-input id="title" class="block mt-1 w-full" type="text" name="title" :value="old('title', $repairRequest->title)" required />
                                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="location_id" :value="__('สถานที่/อาคาร')" />
                                    <select id="location_id" name="location_id" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" required>
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}" {{ old('location_id', $repairRequest->location_id) == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('location_id')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="requester_phone" :value="__('เบอร์โทรศัพท์ติดต่อ')" />
                                    <x-text-input id="requester_phone" class="block mt-1 w-full" type="text" name="requester_phone" :value="old('requester_phone', $repairRequest->requester_phone)" />
                                    <x-input-error :messages="$errors->get('requester_phone')" class="mt-2" />
                                </div>
                            </div>
                            {{-- Column 2 --}}
                            <div class="space-y-6">
                                <div>
                                    <x-input-label for="description" :value="__('รายละเอียดปัญหา')" />
                                    <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" required>{{ old('description', $repairRequest->description) }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="category_id" :value="__('หมวดหมู่ปัญหา')" />
                                    <select id="category_id" name="category_id" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200" required>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $repairRequest->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <hr class="my-8 border-slate-300 dark:border-slate-600">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- รูปภาพประกอบปัญหา (รูปแรก) --}}
                            <div>
                                <x-input-label for="image" :value="__('รูปภาพประกอบปัญหา (เลือกใหม่ถ้าต้องการเปลี่ยน)')" />
                                @if ($repairRequest->image_path)
                                        <div class="mb-2">
                                            <a href="{{ Storage::url($repairRequest->image_path) }}" data-fancybox><img src="{{ Storage::url($repairRequest->image_path) }}" alt="Current Image" class="max-h-48 rounded-md shadow"></a>
                                            <label for="clear_image" class="inline-flex items-center mt-2">
                                                <input type="checkbox" id="clear_image" name="clear_image" value="1" class="rounded border-slate-300 text-sky-600 shadow-sm focus:ring-sky-500">
                                                <span class="ms-2 text-sm text-slate-600 dark:text-slate-400">{{ __('ลบรูปภาพประกอบปัญหา') }}</span>
                                            </label>
                                        </div>
                                @endif
                                <input id="image" name="image" type="file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 mt-1"/>
                                <x-input-error :messages="$errors->get('image')" class="mt-2" />
                            </div>

                            {{-- รูปภาพหลังการซ่อม (สำหรับช่าง) --}}
                            @if (Auth::user()->is_technician)
                            <div>
                                <x-input-label for="after_repair_image" :value="__('รูปภาพหลังการซ่อม (เลือกใหม่ถ้าต้องการเปลี่ยน)')" />
                                @if ($repairRequest->after_image_path)
                                        <div class="mb-2">
                                            <a href="{{ Storage::url($repairRequest->after_image_path) }}" data-fancybox><img src="{{ Storage::url($repairRequest->after_image_path) }}" alt="After Repair Image" class="max-h-48 rounded-md shadow"></a>
                                            <label for="clear_after_image" class="inline-flex items-center mt-2">
                                                <input type="checkbox" id="clear_after_image" name="clear_after_image" value="1" class="rounded border-slate-300 text-sky-600 shadow-sm focus:ring-sky-500">
                                                <span class="ms-2 text-sm text-slate-600 dark:text-slate-400">{{ __('ลบรูปภาพหลังการซ่อม') }}</span>
                                            </label>
                                        </div>
                                @endif
                                <input id="after_repair_image" name="after_repair_image" type="file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100 mt-1"/>
                                <x-input-error :messages="$errors->get('after_repair_image')" class="mt-2" />
                            </div>
                            @endif
                        </div>

                        {{-- ส่วนสำหรับ Admin และ Technician --}}
                        @if (Auth::user()->is_admin || Auth::user()->is_technician)
                            <hr class="my-8 border-slate-300 dark:border-slate-600">
                            <h4 class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-4">ส่วนของผู้ดูแล/ช่าง</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="status_id" :value="__('สถานะปัจจุบัน')" />
                                    <select id="status_id" name="status_id" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200">
                                        @foreach ($statuses as $status)
                                            <option value="{{ $status->id }}" {{ old('status_id', $repairRequest->status_id) == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('status_id')" class="mt-2" />
                                </div>

                                @if (Auth::user()->is_admin)
                                <div>
                                    <x-input-label for="assigned_to_user_id" :value="__('มอบหมายให้ช่าง')" />
                                    <select id="assigned_to_user_id" name="assigned_to_user_id" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200">
                                        <option value="">-- ยังไม่มอบหมาย --</option>
                                        @foreach ($technicians as $technician)
                                            <option value="{{ $technician->id }}" {{ old('assigned_to_user_id', $repairRequest->assigned_to_user_id) == $technician->id ? 'selected' : '' }}>{{ $technician->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('assigned_to_user_id')" class="mt-2" />
                                </div>
                                @endif
                                
                                <div class="md:col-span-2">
                                    <x-input-label for="remarks_by_technician" :value="Auth::user()->is_technician ? __('ผลการดำเนินการ / หมายเหตุการซ่อม') : __('หมายเหตุ (Admin)')" />
                                    <textarea id="remarks_by_technician" name="remarks_by_technician" rows="4" class="block mt-1 w-full border-slate-300 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 rounded-md shadow-sm bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200">{{ old('remarks_by_technician', $repairRequest->remarks_by_technician) }}</textarea>
                                    <x-input-error :messages="$errors->get('remarks_by_technician')" class="mt-2" />
                                </div>
                            </div>
                        @endif

                        {{-- ปุ่ม Submit และ Cancel (ปรับปรุงใหม่) --}}
                        <div class="flex flex-col sm:flex-row items-center justify-end mt-8 gap-4"> {{-- ใช้ flex-col sm:flex-row และ gap-4 สำหรับ responsive spacing --}}
                            @php
                                $cancelRoute = route('repair_requests.show', $repairRequest->id); // Default route for user

                                if (Auth::check()) {
                                    if (Auth::user()->is_admin) {
                                        $cancelRoute = route('admin.manage');
                                    } elseif (Auth::user()->is_technician) {
                                        $cancelRoute = route('repair_requests.index'); // หรืออาจจะเป็น route('technician.dashboard') ถ้ามี
                                    }
                                    // สำหรับผู้ใช้ทั่วไป (is_user = true หรือไม่มี is_admin, is_technician) จะใช้ repair_requests.index
                                    // หากต้องการกลับไปหน้า show เดิมให้ใช้ repair_requests.show
                                    else {
                                        $cancelRoute = route('repair_requests.show', $repairRequest->id); // กลับไปหน้าแสดงรายละเอียดเดิม
                                    }
                                }
                            @endphp
                            <a href="{{ $cancelRoute }}" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 border border-slate-300 dark:border-slate-600 rounded-lg font-semibold text-base text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-700 hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150 shadow-sm">
                                {{ __('ยกเลิก') }}
                            </a>
                            <x-primary-button class="w-full sm:w-auto inline-flex justify-center"> {{-- เพิ่ม w-full sm:w-auto และ inline-flex justify-center --}}
                                {{ __('บันทึกการเปลี่ยนแปลง') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>