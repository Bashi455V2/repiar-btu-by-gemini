<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                <i class="fas fa-tasks fa-fw mr-2 text-sky-600 dark:text-sky-500"></i>{{ __('หน้าจัดการแจ้งซ่อม') }}
            </h2>
            <a href="{{ route('repair_requests.create') }}"
                class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-700 active:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                <i class="fas fa-plus fa-fw mr-2"></i>{{ __('แจ้งซ่อมใหม่') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-xl sm:rounded-xl">
                <div class="p-4 sm:p-6">
                    {{-- Flash Messages --}}
                    @if (session('status'))
                        <div class="mb-6 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-700/30 dark:text-green-300" role="alert">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-6 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-700/30 dark:text-red-300" role="alert">
                            <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    {{-- ================================================================ --}}
                    {{-- START: เพิ่มส่วน Filter ตรงนี้ --}}
                    {{-- ================================================================ --}}
                    <form method="GET" action="{{ route('admin.manage') }}" class="mb-6"> {{-- หรือ route ที่ถูกต้องสำหรับหน้านี้ --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
                            <div>
                                <label for="assignment_status_filter" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">กรองตามการมอบหมาย:</label>
                                <select name="assignment_status" id="assignment_status_filter" onchange="this.form.submit()"
                                        class="block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm dark:bg-slate-700 dark:text-slate-200 dark:focus:bg-slate-600 py-2 px-3">
                                    @if(isset($assignmentStatuses)) {{-- ตรวจสอบว่าตัวแปรถูกส่งมา --}}
                                        @foreach($assignmentStatuses as $key => $value)
                                            <option value="{{ $key }}" {{ (isset($currentAssignmentFilter) && $currentAssignmentFilter == $key) ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    @else
                                        {{-- Fallback เผื่อตัวแปรไม่ได้ถูกส่งมา (ไม่ควรเกิดขึ้นถ้า Controller ถูกต้อง) --}}
                                        <option value="all" {{ (request('assignment_status', 'all') == 'all') ? 'selected' : '' }}>งานทั้งหมด</option>
                                        <option value="unassigned" {{ (request('assignment_status') == 'unassigned') ? 'selected' : '' }}>งานที่ยังไม่ได้มอบหมาย</option>
                                        <option value="assigned" {{ (request('assignment_status') == 'assigned') ? 'selected' : '' }}>งานที่มอบหมายแล้ว</option>
                                    @endif
                                </select>
                            </div>
                            {{-- หากมี Filter อื่นๆ สามารถเพิ่ม col ใหม่ใน grid นี้ได้ --}}
                            {{-- ตัวอย่าง:
                            <div>
                                <label for="status_filter" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">กรองตามสถานะ:</label>
                                <select name="status_id" id="status_filter" onchange="this.form.submit()" class="block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm dark:bg-slate-700 dark:text-slate-200 dark:focus:bg-slate-600 py-2 px-3">
                                    <option value="">สถานะทั้งหมด</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}" {{ request('status_id') == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            --}}
                        </div>
                    </form>
                    {{-- ================================================================ --}}
                    {{-- END: ส่วน Filter --}}
                    {{-- ================================================================ --}}

                    @if ($repairRequests->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-slate-900 dark:text-slate-200">ไม่พบรายการแจ้งซ่อม</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                @if(request()->has('assignment_status') && request('assignment_status') !== 'all')
                                    ลองเปลี่ยนตัวเลือกการกรอง หรือ
                                @endif
                                เริ่มต้นด้วยการแจ้งซ่อมใหม่
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('repair_requests.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 dark:focus:ring-offset-slate-800">
                                    <i class="fas fa-plus fa-fw mr-2"></i>
                                    แจ้งซ่อมใหม่
                                </a>
                            </div>
                        </div>
                    @else
                        {{-- ส่วนแสดงผลสำหรับหน้าจอขนาดกลางขึ้นไป (ตาราง) --}}
                        <div class="hidden md:block overflow-x-auto align-middle min-w-full mt-2">
                            {{-- โค้ดตารางของคุณ --}}
                            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                                <thead class="bg-slate-100 dark:bg-slate-700/50">
                                    <tr>
                                        <th class="py-3.5 pl-4 pr-3 text-left text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider sm:pl-6">ID</th>
                                        <th class="py-3.5 px-3 text-left text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">เรื่อง</th>
                                        <th class="hidden lg:table-cell py-3.5 px-3 text-left text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">ผู้แจ้ง</th>
                                        <th class="py-3.5 px-3 text-left text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">สถานะ</th>
                                        <th class="hidden xl:table-cell py-3.5 px-3 text-left text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">มอบหมายให้</th>
                                        <th class="hidden sm:table-cell py-3.5 px-3 text-left text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">วันที่แจ้ง</th>
                                        <th class="py-3.5 px-3 text-left text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 min-w-[280px] lg:min-w-[320px]">อัปเดตด่วน</th>
                                        <th class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-center text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">ดำเนินการ</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                                    @foreach ($repairRequests as $item)
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors duration-150 group">
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-900 dark:text-slate-100 sm:pl-6">{{ $item->id }}</td>
                                            <td class="py-4 px-3 text-sm text-slate-700 dark:text-slate-300 max-w-[200px] truncate" title="{{$item->title}}">
                                                <a href="{{ route('repair_requests.show', $item) }}" class="text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300 font-semibold">
                                                    {{ $item->title }}
                                                </a>
                                            </td>
                                            <td class="hidden lg:table-cell whitespace-nowrap py-4 px-3 text-sm text-slate-500 dark:text-slate-400">{{ $item->user->name ?? ($item->requester_name ?? 'N/A') }}</td>
                                            <td class="whitespace-nowrap py-4 px-3 text-sm">
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $item->status->color_class ?? 'bg-slate-200 text-slate-800 dark:bg-slate-600 dark:text-slate-100' }}">
                                                    {{ $item->status->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="hidden xl:table-cell whitespace-nowrap py-4 px-3 text-sm text-slate-500 dark:text-slate-400">{{ $item->assignedTo->name ?? '-' }}</td>
                                            <td class="hidden sm:table-cell whitespace-nowrap py-4 px-3 text-sm text-slate-500 dark:text-slate-400">{{ $item->created_at->translatedFormat('j M Y') }}</td>
                                            <td class="py-3 px-3 text-sm align-middle">
                                                @include('repair_requests.partials.manage-update-form', ['item' => $item, 'statuses' => $statuses, 'technicians' => $technicians, 'formIdSuffix' => '_desktop'])
                                            </td>
                                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-center text-sm font-medium sm:pr-6 align-middle">
                                                 @include('repair_requests.partials.manage-action-buttons', ['item' => $item])
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- ส่วนแสดงผลสำหรับหน้าจอมือถือ (Card View) --}}
                        <div class="block md:hidden mt-4 space-y-4">
                            @foreach ($repairRequests as $item)
                                <div class="bg-white dark:bg-slate-700/50 shadow-lg rounded-lg p-4 border border-slate-200 dark:border-slate-600">
                                    <div class="flex justify-between items-start mb-3">
                                        <a href="{{ route('repair_requests.show', $item) }}" class="block flex-1 mr-2">
                                            <h4 class="text-base font-semibold text-sky-600 dark:text-sky-400 hover:underline leading-tight">{{ $item->title }}</h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">ID: {{ $item->id }} | โดย: {{ $item->user->name ?? ($item->requester_name ?? 'N/A') }}</p>
                                        </a>
                                        <span class="flex-shrink-0 px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $item->status->color_class ?? 'bg-slate-100 text-slate-800 dark:bg-slate-600 dark:text-slate-200' }}">
                                            {{ $item->status->name ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="space-y-1 text-xs text-slate-600 dark:text-slate-300 mb-4">
                                        <p><i class="fas fa-map-marker-alt fa-fw mr-1 text-slate-400"></i><strong class="font-medium text-slate-700 dark:text-slate-200">สถานที่:</strong> {{ Str::limit(optional($item->location)->name, 35) ?? 'N/A' }}</p>
                                        <p><i class="fas fa-user-shield fa-fw mr-1 text-slate-400"></i><strong class="font-medium text-slate-700 dark:text-slate-200">มอบหมายให้:</strong> {{ optional($item->assignedTo)->name ?? 'ยังไม่ได้มอบหมาย' }}</p>
                                        <p><i class="far fa-calendar-alt fa-fw mr-1 text-slate-400"></i><strong class="font-medium text-slate-700 dark:text-slate-200">วันที่แจ้ง:</strong> {{ $item->created_at->translatedFormat('j M Y') }}</p>
                                    </div>

                                    @include('repair_requests.partials.manage-update-form', ['item' => $item, 'statuses' => $statuses, 'technicians' => $technicians, 'formIdSuffix' => '_mobile'])

                                    <div class="mt-4 pt-3 border-t border-slate-200 dark:border-slate-600 flex justify-end space-x-3">
                                        @include('repair_requests.partials.manage-action-buttons', ['item' => $item, 'isMobile' => true])
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{-- อย่าลืม appends(request()->query()) เพื่อให้ filter ยังคงอยู่เมื่อเปลี่ยนหน้า --}}
                            {{ $repairRequests->appends(request()->query())->links('pagination::tailwind') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>