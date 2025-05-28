<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('แก้ไขรายการแจ้งซ่อม') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('repair_requests.update', $repairRequest) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="subject" :value="__('หัวข้อ')" />
                            <x-text-input id="subject" class="block mt-1 w-full" type="text" name="subject" :value="old('subject', $repairRequest->subject)" required autofocus />
                            <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="description" :value="__('รายละเอียด')" />
                            <x-textarea-input id="description" class="block mt-1 w-full" name="description" required>{{ old('description', $repairRequest->description) }}</x-textarea-input>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="location" :value="__('สถานที่')" />
                            <x-text-input id="location" class="block mt-1 w-full" type="text" name="location" :value="old('location', $repairRequest->location)" required />
                            <x-input-error :messages="$errors->get('location')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="contact_info" :value="__('ข้อมูลติดต่อ (เบอร์โทร, Line ID ฯลฯ)')" />
                            <x-text-input id="contact_info" class="block mt-1 w-full" type="text" name="contact_info" :value="old('contact_info', $repairRequest->contact_info)" />
                            <x-input-error :messages="$errors->get('contact_info')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="attachment" :value="__('ไฟล์แนบ (รูปภาพ/PDF)')" />
                            <input id="attachment" type="file" name="attachment" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            <x-input-error :messages="$errors->get('attachment')" class="mt-2" />
                            @if ($repairRequest->attachment)
                                <div class="mt-2 text-sm text-gray-600">
                                    ไฟล์แนบปัจจุบัน: <a href="{{ Storage::url($repairRequest->attachment) }}" target="_blank" class="text-blue-500 hover:underline">ดูไฟล์</a>
                                    {{-- เพิ่ม checkbox สำหรับลบไฟล์แนบ --}}
                                    <label for="clear_attachment" class="ml-4 inline-flex items-center">
                                        <input type="checkbox" name="clear_attachment" id="clear_attachment" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                        <span class="ml-1 text-sm text-gray-600">ลบไฟล์แนบเดิม</span>
                                    </label>
                                </div>
                            @endif
                        </div>

                        @if (Auth::user()->is_admin || Auth::user()->is_technician)
                            <div class="mt-4">
                                <x-input-label for="status" :value="__('สถานะ')" />
                                <select id="status" name="status" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="pending" {{ old('status', $repairRequest->status) == 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                                    <option value="in_progress" {{ old('status', $repairRequest->status) == 'in_progress' ? 'selected' : '' }}>กำลังดำเนินการ</option>
                                    <option value="completed" {{ old('status', $repairRequest->status) == 'completed' ? 'selected' : '' }}>เสร็จสิ้น</option>
                                    <option value="cancelled" {{ old('status', $repairRequest->status) == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="priority" :value="__('ความสำคัญ')" />
                                <select id="priority" name="priority" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="low" {{ old('priority', $repairRequest->priority) == 'low' ? 'selected' : '' }}>ต่ำ</option>
                                    <option value="normal" {{ old('priority', $repairRequest->priority) == 'normal' ? 'selected' : '' }}>ปกติ</option>
                                    <option value="high" {{ old('priority', $repairRequest->priority) == 'high' ? 'selected' : '' }}>สูง</option>
                                    <option value="urgent" {{ old('priority', $repairRequest->priority) == 'urgent' ? 'selected' : '' }}>ด่วน</option>
                                </select>
                                <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="assigned_to" :value="__('มอบหมายให้ช่าง')" />
                                <select id="assigned_to" name="assigned_to" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">-- ไม่มอบหมาย --</option>
                                    @foreach ($technicians as $technician)
                                        <option value="{{ $technician->id }}" {{ old('assigned_to', $repairRequest->assigned_to) == $technician->id ? 'selected' : '' }}>
                                            {{ $technician->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('assigned_to')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="completed_at" :value="__('วันที่ซ่อมเสร็จ')" />
                                {{-- หาก completed_at มีค่า ให้ format เป็น YYYY-MM-DD เพื่อแสดงใน input type="date" --}}
                                <x-text-input id="completed_at" class="block mt-1 w-full" type="date" name="completed_at" :value="old('completed_at', $repairRequest->completed_at ? $repairRequest->completed_at->format('Y-m-d') : '')" />
                                <x-input-error :messages="$errors->get('completed_at')" class="mt-2" />
                            </div>
                        @endif
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('บันทึกการแก้ไข') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>