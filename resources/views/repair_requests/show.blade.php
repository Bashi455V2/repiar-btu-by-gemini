<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight mb-3 sm:mb-0 truncate max-w-md xl:max-w-lg">
                <span class="text-slate-500 dark:text-slate-400">ID #{{ $repairRequest->id }}:</span>
                <span class="ml-1">{{ Str::limit($repairRequest->title, 45) }}</span>
            </h2>
            <div class="flex items-center space-x-2">
                {{-- ปุ่มย้อนกลับ --}}
                @php
                    $backRouteName = 'repair_requests.index'; // Default route name for regular users
                    $routeParams = []; // Initialize route parameters

                    // ดึง query parameters ทั้งหมดจาก URL ปัจจุบันมาใช้ในการย้อนกลับ
                    $currentQueryParams = request()->query();

                    // ตรวจสอบว่ามีการส่ง $backUrlParams มาจาก Controller หรือไม่
                    if (isset($backUrlParams) && is_array($backUrlParams)) {
                        // ใช้ $backUrlParams เป็นหลัก หาก Controller ระบุมา
                        $routeParams = $backUrlParams;

                        if (Auth::check() && Auth::user()->is_technician) {
                            if (isset($backUrlParams['filter'])) {
                                $backRouteName = 'technician.queue.index'; // มาจาก Technician Queue
                            } else {
                                $backRouteName = 'technician.tasks.index'; // มาจาก Technician Tasks (งานของฉัน)
                            }
                        } elseif (Auth::check() && Auth::user()->is_admin) {
                            $backRouteName = 'admin.manage'; // มาจาก Admin Manage
                        } else {
                            $backRouteName = 'repair_requests.index'; // ผู้ใช้ทั่วไป
                        }
                    } elseif (Auth::check()) {
                        // กรณีที่ไม่มี $backUrlParams ถูกส่งมา (อาจจะเข้าหน้านี้โดยตรง)
                        // กำหนด Route Default ตาม Role และส่ง query parameters ปัจจุบันไปด้วย
                        if (Auth::user()->is_technician) {
                            $backRouteName = 'technician.tasks.index';
                        } elseif (Auth::user()->is_admin) {
                            $backRouteName = 'admin.manage';
                        }
                        // สำหรับผู้ใช้ทั่วไป default ยังคงเป็น repair_requests.index
                        $routeParams = $currentQueryParams; // ส่ง query params ทั้งหมดกลับไปด้วย
                    }
                @endphp
                {{-- ใช้ $backRouteName (ชื่อ Route) ในฟังก์ชัน route() --}}
                <a href="{{ route($backRouteName, $routeParams) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-md font-semibold text-xs text-slate-700 dark:text-slate-200 uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('ย้อนกลับ') }}
                </a>

                {{-- ปุ่มแก้ไข --}}
                @can('update', $repairRequest)
                    @php
                        $editRouteName = (Auth::user() && Auth::user()->is_admin) ? 'admin.repair_requests.edit' : 'repair_requests.edit';
                    @endphp
                    <a href="{{ route($editRouteName, $repairRequest) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition ease-in-out duration-150">
                        <i class="fas fa-pencil-alt mr-2"></i>{{ __('แก้ไข') }}
                    </a>
                @endcan

                {{-- ปุ่มลบ --}}
                @can('delete', $repairRequest)
                <form action="{{ route('repair_requests.destroy', $repairRequest->id) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบรายการแจ้งซ่อมนี้? การกระทำนี้ไม่สามารถย้อนกลับได้');" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition ease-in-out duration-150">
                        <i class="fas fa-trash-alt mr-2"></i>{{ __('ลบ') }}
                    </button>
                </form>
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
                            {{-- ส่วนแสดงข้อมูลหลัก --}}
                            <div>
                                <h1 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-slate-100 leading-tight break-words">{{ $repairRequest->title }}</h1>
                                <div class="mt-3 flex flex-col sm:flex-row sm:flex-wrap sm:items-center text-xs sm:text-sm text-slate-500 dark:text-slate-400 gap-x-4 gap-y-1">
                                    <span class="inline-flex items-center"><i class="far fa-clock fa-fw mr-1.5 text-slate-400"></i>แจ้งเมื่อ: <span class="ml-1 font-medium text-slate-700 dark:text-slate-300">{{ $repairRequest->created_at->translatedFormat('j F Y เวลา H:i น.') }}</span></span>
                                    <span class="inline-flex items-center"><i class="far fa-user fa-fw mr-1.5 text-slate-400"></i>โดย: <span class="ml-1 font-medium text-slate-700 dark:text-slate-300">{{ $repairRequest->user->name ?? ($repairRequest->requester_name ?? 'N/A') }}</span></span>
                                    @if($repairRequest->requester_phone)<span class="inline-flex items-center"><i class="fas fa-phone fa-fw mr-1.5 text-slate-400"></i>โทร: <span class="ml-1 font-medium text-slate-700 dark:text-slate-300">{{ $repairRequest->requester_phone }}</span></span>@endif
                                </div>
                            </div>
                            <hr class="dark:border-slate-700 my-6">
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-lg font-semibold text-sky-700 dark:text-sky-500 mb-2 flex items-center"><i class="fas fa-align-left fa-fw mr-2"></i>รายละเอียดปัญหา</h4>
                                    <div class="prose prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-300 text-base leading-relaxed ml-1 sm:ml-0 pl-0 sm:pl-[calc(1rem+0.5rem)]">{!! nl2br(e($repairRequest->description)) !!}</div>
                                </div>
                                @if ($repairRequest->remarks_by_technician)
                                    <div class="pt-2">
                                        <h4 class="text-lg font-semibold text-sky-700 dark:text-sky-500 mb-2 flex items-center"><i class="fas fa-comment-dots fa-fw mr-2"></i>หมายเหตุจากช่าง</h4>
                                        <div class="prose prose-sm prose-slate dark:prose-invert max-w-none text-slate-500 dark:text-slate-400 italic bg-slate-50 dark:bg-slate-700/40 p-4 rounded-md ml-1 sm:ml-0 pl-4 sm:pl-[calc(1rem+0.5rem)] border-l-4 border-amber-400 dark:border-amber-500">{!! nl2br(e($repairRequest->remarks_by_technician)) !!}</div>
                                    </div>
                                @endif
                            </div>
                            {{-- รูปภาพก่อน-หลังซ่อม --}}
                            @if ($repairRequest->image_path || $repairRequest->after_image_path)
                            <div class="mt-8">
                                <h4 class="text-lg font-semibold text-sky-700 dark:text-sky-500 mb-4 flex items-center"><i class="far fa-images fa-fw mr-2"></i>รูปภาพประกอบ</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    @if ($repairRequest->image_path)
                                    <div>
                                        <h5 class="text-sm font-semibold text-slate-600 dark:text-slate-300 mb-2">ภาพประกอบปัญหา (ก่อนซ่อม)</h5>
                                        <a href="{{ Storage::url($repairRequest->image_path) }}" data-fancybox="gallery" data-caption="ภาพประกอบปัญหา (ก่อนซ่อม)" class="block group"><img src="{{ Storage::url($repairRequest->image_path) }}" alt="Repair Image" class="rounded-lg shadow-lg w-full h-auto object-cover transition-transform duration-300 group-hover:scale-105 border dark:border-slate-700"></a>
                                    </div>
                                    @endif
                                    @if ($repairRequest->after_image_path)
                                    <div>
                                        <h5 class="text-sm font-semibold text-slate-600 dark:text-slate-300 mb-2">ภาพหลังการซ่อม</h5>
                                        <a href="{{ Storage::url($repairRequest->after_image_path) }}" data-fancybox="gallery" data-caption="ภาพหลังการซ่อม" class="block group"><img src="{{ Storage::url($repairRequest->after_image_path) }}" alt="After Repair Image" class="rounded-lg shadow-lg w-full h-auto object-cover transition-transform duration-300 group-hover:scale-105 border dark:border-slate-700"></a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- Sidebar Information --}}
                        <div class="lg:col-span-2 space-y-6">
                            {{-- ข้อมูลสรุป --}}
                            <div class="bg-slate-100 dark:bg-slate-700/70 p-6 rounded-xl shadow-lg border border-slate-200 dark:border-slate-600">
                                <h4 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4 pb-3 border-b border-slate-300 dark:border-slate-600 flex items-center"><i class="fas fa-thumbtack fa-fw mr-2 text-sky-600 dark:text-sky-500"></i>ข้อมูลสรุป</h4>
                                <dl class="space-y-5">
                                    <div><dt class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center"><i class="fas fa-tag fa-fw mr-2"></i>สถานะ:</dt><dd class="mt-1"><span class="px-3 py-1.5 inline-flex text-base leading-5 font-bold rounded-md {{ $repairRequest->status->color_class ?? 'bg-slate-200 text-slate-800 dark:bg-slate-600 dark:text-slate-100' }}">{{ $repairRequest->status->name ?? 'N/A' }}</span></dd></div>
                                    <div><dt class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center"><i class="fas fa-folder-open fa-fw mr-2"></i>หมวดหมู่:</dt><dd class="mt-1 text-md text-slate-700 dark:text-slate-200 font-medium">{{ $repairRequest->category->name ?? 'N/A' }}</dd></div>
                                    <div><dt class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center"><i class="fas fa-map-marker-alt fa-fw mr-2"></i>สถานที่:</dt><dd class="mt-1 text-md text-slate-700 dark:text-slate-200 font-medium">{{ $repairRequest->location->name ?? 'N/A' }}</dd></div>
                                    <div><dt class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center"><i class="fas fa-user-shield fa-fw mr-2"></i>ช่างผู้รับผิดชอบ:</dt><dd class="mt-1 text-md text-slate-700 dark:text-slate-200 font-medium">{{ $repairRequest->assignedTo->name ?? 'ยังไม่ได้มอบหมาย' }}</dd></div>
                                    @if($repairRequest->completed_at)<div><dt class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center"><i class="far fa-calendar-check fa-fw mr-2"></i>วันที่ซ่อมเสร็จ:</dt><dd class="mt-1 text-md text-slate-700 dark:text-slate-200 font-medium">{{ $repairRequest->completed_at->translatedFormat('j F Y เวลา H:i น.') }}</dd></div>@endif
                                </dl>
                            </div>

                            {{-- ฟอร์มอัปเดตด่วน --}}
                            @can('update', $repairRequest)
                                @if(Auth::user()->is_admin || (Auth::user()->is_technician && $repairRequest->assigned_to_user_id === Auth::id()))
                                <div class="bg-slate-100 dark:bg-slate-700/70 p-6 rounded-xl shadow-lg border border-slate-200 dark:border-slate-600">
                                    <h4 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4 pb-3 border-b border-slate-300 dark:border-slate-600 flex items-center"><i class="fas fa-edit fa-fw mr-2 text-sky-600 dark:text-sky-500"></i>อัปเดต (ด่วน)</h4>
                                    <form method="POST" action="{{ route('repair_requests.update_status_assign', $repairRequest->id) }}">
                                        @csrf @method('PUT')
                                        <div class="space-y-4">
                                            <div><label for="status_id_quick_sidebar" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">สถานะ:</label><select name="status_id" id="status_id_quick_sidebar" class="block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm dark:bg-slate-700 dark:text-slate-200 dark:focus:bg-slate-600 py-2 px-3">@foreach($statuses as $status)<option value="{{ $status->id }}" {{ (old('status_id', $repairRequest->status_id) == $status->id) ? 'selected' : '' }}>{{ $status->name }}</option>@endforeach</select></div>
                                            @if(Auth::user()->is_admin)<div><label for="assigned_to_user_id_quick_sidebar" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">มอบหมายให้ช่าง:</label><select name="assigned_to_user_id" id="assigned_to_user_id_quick_sidebar" class="block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm dark:bg-slate-700 dark:text-slate-200 dark:focus:bg-slate-600 py-2 px-3"><option value="">-- ยังไม่มอบหมาย --</option>@foreach($technicians as $technician)<option value="{{ $technician->id }}" {{ (old('assigned_to_user_id', $repairRequest->assigned_to_user_id) == $technician->id) ? 'selected' : '' }}>{{ $technician->name }}</option>@endforeach</select></div>@endif
                                            <div><label for="remarks_by_technician_quick_sidebar" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">{{ Auth::user()->is_technician ? 'หมายเหตุจากช่าง:' : 'หมายเหตุ (Admin):' }}</label><textarea name="remarks_by_technician" id="remarks_by_technician_quick_sidebar" class="block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm dark:bg-slate-700 dark:text-slate-200 dark:focus:bg-slate-600 py-2 px-3" rows="3">{{ old('remarks_by_technician', $repairRequest->remarks_by_technician) }}</textarea></div>
                                            <button type="submit" class="w-full whitespace-nowrap inline-flex items-center justify-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 dark:focus:ring-offset-slate-800"><i class="fas fa-save mr-2"></i>บันทึก</button>
                                        </div>
                                    </form>
                                </div>
                                @endif
                            @endcan
                        </div>
                    </div>

                    {{-- Activity Log Section (ใช้ @include) --}}
                    @include('repair_requests.partials.activity-log')

                </div>
            </div>
        </div>
    </div>
</x-app-layout>