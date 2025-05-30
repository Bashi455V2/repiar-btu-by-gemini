<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight mb-2 sm:mb-0 truncate max-w-md">
                <span class="text-slate-500 dark:text-slate-400">ID #{{ $repairRequest->id }}:</span> {{ Str::limit($repairRequest->title, 50) }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ url()->previous(route('repair_requests.index')) }}"
                   class="inline-flex items-center px-4 py-2 bg-slate-100 dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg font-semibold text-xs text-slate-700 dark:text-slate-200 uppercase tracking-widest hover:bg-slate-200 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left w-4 h-4 mr-2 -ml-1"></i> {{-- Font Awesome Icon --}}
                    {{ __('ย้อนกลับ') }}
                </a>
                @can('update', $repairRequest)
                <a href="{{ route('repair_requests.edit', $repairRequest) }}"
                   class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                    <i class="fas fa-pencil-alt w-4 h-4 mr-2 -ml-1"></i> {{-- Font Awesome Icon --}}
                    {{ __('แก้ไข') }}
                </a>
                @endcan

                @can('delete', $repairRequest)
                <form action="{{ route('repair_requests.destroy', $repairRequest->id) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบรายการแจ้งซ่อมนี้? การกระทำนี้ไม่สามารถย้อนกลับได้');" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                        <i class="fas fa-trash-alt w-4 h-4 mr-2 -ml-1"></i> {{-- Font Awesome Icon --}}
                        {{ __('ลบรายการ') }}
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
                            <div>
                                <h1 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-slate-100 leading-tight">
                                    {{ $repairRequest->title }}
                                </h1>
                                <div class="mt-2 text-sm text-slate-500 dark:text-slate-400 space-x-3">
                                    <span><i class="fa-regular fa-clock mr-1"></i>แจ้งเมื่อ: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $repairRequest->created_at->isoFormat('D MMMM YYYY, HH:mm น.') }}</span></span>
                                    <span><i class="fa-regular fa-user mr-1"></i>โดย: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $repairRequest->user->name ?? ($repairRequest->requester_name ?? 'N/A') }}</span></span>
                                    @if($repairRequest->requester_phone)
                                        <span><i class="fa-solid fa-phone mr-1"></i>โทร: <span class="font-medium text-slate-700 dark:text-slate-300">{{ $repairRequest->requester_phone }}</span></span>
                                    @endif
                                </div>
                            </div>

                            <hr class="dark:border-slate-700 my-6">

                            <div class="space-y-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-sky-700 dark:text-sky-500 mb-1 flex items-center">
                                        <i class="fa-solid fa-circle-info mr-2"></i>รายละเอียดปัญหา:
                                    </h4>
                                    <div class="prose prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-300 text-base leading-relaxed pl-6">
                                        {!! nl2br(e($repairRequest->description)) !!}
                                    </div>
                                </div>

                                @if ($repairRequest->remarks_by_technician)
                                    <div class="pt-4">
                                        <h4 class="text-lg font-semibold text-sky-700 dark:text-sky-500 mb-1 flex items-center">
                                            <i class="fa-solid fa-user-gear mr-2"></i>หมายเหตุจากช่าง:
                                        </h4>
                                        <div class="prose prose-sm prose-slate dark:prose-invert max-w-none text-slate-500 dark:text-slate-400 italic bg-slate-50 dark:bg-slate-700/30 p-3 rounded-md pl-6">
                                            {!! nl2br(e($repairRequest->remarks_by_technician)) !!}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if ($repairRequest->image_path)
                                <div class="mt-8">
                                    <h4 class="text-lg font-semibold text-sky-700 dark:text-sky-500 mb-2 flex items-center">
                                        <i class="fa-regular fa-image mr-2"></i>รูปภาพประกอบ:
                                    </h4>
                                    <a href="{{ Storage::url($repairRequest->image_path) }}" data-fancybox="gallery" class="block group ml-6">
                                        <img src="{{ Storage::url($repairRequest->image_path) }}" alt="Repair Image" class="rounded-lg shadow-lg w-full max-w-md object-cover transition-transform duration-300 group-hover:scale-105">
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Sidebar Information --}}
                        <div class="lg:col-span-2 space-y-6">
                            <div class="bg-slate-50 dark:bg-slate-700/60 p-6 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700">
                                <h4 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4 pb-3 border-b border-slate-300 dark:border-slate-600 flex items-center">
                                    <i class="fa-solid fa-clipboard-list mr-2 text-sky-600 dark:text-sky-500"></i>ข้อมูลสรุป
                                </h4>
                                <dl class="space-y-4">
                                    <div>
                                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center"><i class="fa-solid fa-circle-exclamation mr-1.5"></i>สถานะปัจจุบัน:</dt>
                                        <dd class="mt-1">
                                            <span class="px-3 py-1.5 inline-flex text-sm leading-5 font-bold rounded-full {{ $repairRequest->status->color_class ?? 'bg-slate-200 text-slate-800 dark:bg-slate-600 dark:text-slate-100' }}">
                                                {{ $repairRequest->status->name ?? 'N/A' }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center"><i class="fa-solid fa-tags mr-1.5"></i>หมวดหมู่:</dt>
                                        <dd class="mt-1 text-base text-slate-700 dark:text-slate-200 font-medium">{{ $repairRequest->category->name ?? 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center"><i class="fa-solid fa-location-dot mr-1.5"></i>สถานที่:</dt>
                                        <dd class="mt-1 text-base text-slate-700 dark:text-slate-200 font-medium">{{ $repairRequest->location->name ?? 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center"><i class="fa-solid fa-user-cog mr-1.5"></i>ช่างผู้รับผิดชอบ:</dt>
                                        <dd class="mt-1 text-base text-slate-700 dark:text-slate-200 font-medium">{{ $repairRequest->assignedTo->name ?? 'ยังไม่ได้มอบหมาย' }}</dd>
                                    </div>
                                    @if($repairRequest->completed_at)
                                    <div>
                                        <dt class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center"><i class="fa-solid fa-check-circle mr-1.5"></i>วันที่ซ่อมเสร็จ:</dt>
                                        <dd class="mt-1 text-base text-slate-700 dark:text-slate-200 font-medium">{{ $repairRequest->completed_at->isoFormat('D MMMM YYYY, HH:mm น.') }}</dd>
                                    </div>
                                    @endif
                                </dl>
                            </div>

                            @can('manageRequests', App\Models\RepairRequest::class)
                                {{-- ... (ส่วนฟอร์มอัปเดตด่วนใน Sidebar เหมือนเดิม) ... --}}
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>