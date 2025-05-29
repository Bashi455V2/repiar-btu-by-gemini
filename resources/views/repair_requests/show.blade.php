<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight mb-2 sm:mb-0 truncate max-w-md">
                <span class="text-slate-500 dark:text-slate-400">ID #{{ $repairRequest->id }}:</span> {{ Str::limit($repairRequest->title, 50) }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ url()->previous(route('repair_requests.index')) }}"
                   class="inline-flex items-center px-4 py-2 bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg font-semibold text-xs text-slate-700 dark:text-slate-200 uppercase tracking-widest hover:bg-slate-200 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2 -ml-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    {{ __('ย้อนกลับ') }}
                </a>
                @can('update', $repairRequest)
                <a href="{{ route('repair_requests.edit', $repairRequest) }}"
                   class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    {{ __('แก้ไข') }}
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 shadow-2xl sm:rounded-xl overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-7 gap-6 lg:gap-8">
                        {{-- Main Content Area --}}
                        <div class="lg:col-span-5 space-y-6">
                            <div>
                                <h1 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-slate-100 leading-tight">
                                    {{ $repairRequest->title }}
                                </h1>
                                <div class="mt-2 text-sm text-slate-500 dark:text-slate-400 space-x-3">
                                    <span>แจ้งเมื่อ: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $repairRequest->created_at->isoFormat('D MMMM YYYY, HH:mm น.') }}</span></span>
                                    <span>โดย: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $repairRequest->user->name ?? ($repairRequest->requester_name ?? 'N/A') }}</span></span>
                                    @if($repairRequest->requester_phone)
                                        <span>โทร: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $repairRequest->requester_phone }}</span></span>
                                    @endif
                                </div>
                            </div>

                            <hr class="dark:border-slate-700 my-6">

                            <div class="space-y-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-sky-700 dark:text-sky-500 mb-1">รายละเอียดปัญหา:</h4>
                                    <div class="prose prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-300 text-base leading-relaxed">
                                        {!! nl2br(e($repairRequest->description)) !!}
                                    </div>
                                </div>

                                @if ($repairRequest->remarks_by_technician)
                                    <div class="pt-4">
                                        <h4 class="text-lg font-semibold text-sky-700 dark:text-sky-500 mb-1">หมายเหตุจากช่าง:</h4>
                                        <div class="prose prose-sm prose-slate dark:prose-invert max-w-none text-slate-500 dark:text-slate-400 italic bg-slate-50 dark:bg-slate-700/30 p-3 rounded-md">
                                            {!! nl2br(e($repairRequest->remarks_by_technician)) !!}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if ($repairRequest->image_path)
                                <div class="mt-8">
                                    <h4 class="text-lg font-semibold text-sky-700 dark:text-sky-500 mb-2">รูปภาพประกอบ:</h4>
                                    <a href="{{ Storage::url($repairRequest->image_path) }}" data-fancybox="gallery" class="block group">
                                        <img src="{{ Storage::url($repairRequest->image_path) }}" alt="Repair Image" class="rounded-lg shadow-lg w-full max-w-lg object-cover transition-transform duration-300 group-hover:scale-105">
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Sidebar Information --}}
                        <div class="lg:col-span-2 space-y-6">
                            <div class="bg-slate-50 dark:bg-slate-700/60 p-6 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700">
                                <h4 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4 pb-3 border-b border-slate-300 dark:border-slate-600">ข้อมูลสรุป</h4>
                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">สถานะปัจจุบัน:</dt>
                                        <dd class="mt-1">
                                            <span class="px-3 py-1.5 inline-flex text-sm leading-5 font-bold rounded-full {{ $repairRequest->status->color_class ?? 'bg-slate-200 text-slate-800 dark:bg-slate-600 dark:text-slate-100' }}">
                                                {{ $repairRequest->status->name ?? 'N/A' }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">หมวดหมู่:</dt>
                                        <dd class="mt-1 text-base text-slate-700 dark:text-slate-200 font-medium">{{ $repairRequest->category->name ?? 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">สถานที่:</dt>
                                        <dd class="mt-1 text-base text-slate-700 dark:text-slate-200 font-medium">{{ $repairRequest->location->name ?? 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">ช่างผู้รับผิดชอบ:</dt>
                                        <dd class="mt-1 text-base text-slate-700 dark:text-slate-200 font-medium">{{ $repairRequest->assignedTo->name ?? 'ยังไม่ได้มอบหมาย' }}</dd>
                                    </div>
                                    @if($repairRequest->completed_at)
                                    <div>
                                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">วันที่ซ่อมเสร็จ:</dt>
                                        <dd class="mt-1 text-base text-slate-700 dark:text-slate-200 font-medium">{{ $repairRequest->completed_at->isoFormat('D MMMM YYYY, HH:mm น.') }}</dd>
                                    </div>
                                    @endif
                                </dl>
                            </div>

                            @can('manageRequests', App\Models\RepairRequest::class)
                                <div class="bg-slate-50 dark:bg-slate-700/60 p-6 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700">
                                    <h4 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4 pb-3 border-b border-slate-300 dark:border-slate-600">อัปเดต (ด่วน)</h4>
                                    <form method="POST" action="{{ route('repair_requests.update_status_assign', $repairRequest->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="redirect_to_manage" value="0"> {{-- เพื่อให้ redirect กลับมาหน้านี้ --}}
                                        <div class="space-y-4">
                                            <div>
                                                <label for="status_id_show" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">เปลี่ยนสถานะ:</label>
                                                <select name="status_id" id="status_id_show" class="block w-full rounded-md border-0 py-2 text-slate-900 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-sky-600 sm:text-sm sm:leading-6 bg-white dark:bg-slate-700">
                                                    @foreach ($statuses as $status)
                                                        <option value="{{ $status->id }}" {{ $repairRequest->status_id == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label for="assigned_to_user_id_show" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">มอบหมายช่าง:</label>
                                                <select name="assigned_to_user_id" id="assigned_to_user_id_show" class="block w-full rounded-md border-0 py-2 text-slate-900 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-sky-600 sm:text-sm sm:leading-6 bg-white dark:bg-slate-700">
                                                    <option value="">-- ไม่มอบหมาย --</option>
                                                    @foreach ($technicians as $technician)
                                                        <option value="{{ $technician->id }}" {{ $repairRequest->assigned_to_user_id == $technician->id ? 'selected' : '' }}>{{ $technician->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label for="remarks_by_technician_show" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">หมายเหตุเพิ่มเติม:</label>
                                                <textarea name="remarks_by_technician" id="remarks_by_technician_show" rows="3" class="block w-full rounded-md border-0 py-1.5 text-slate-900 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-sky-600 sm:text-sm sm:leading-6 bg-white dark:bg-slate-700">{{ old('remarks_by_technician', $repairRequest->remarks_by_technician) }}</textarea>
                                            </div>
                                            <button type="submit" class="w-full whitespace-nowrap inline-flex items-center justify-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 dark:focus:ring-offset-slate-800">
                                                บันทึกการเปลี่ยนแปลง
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>