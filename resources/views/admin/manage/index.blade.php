<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                <i class="fas fa-tasks fa-fw mr-2 text-sky-600 dark:text-sky-500"></i>{{ __('หน้าจัดการแจ้งซ่อม') }}
            </h2>
            <a href="{{ route('repair_requests.create') }}"
                class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-700 active:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150 shadow-md hover:shadow-lg w-full sm:w-auto justify-center">
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
                    {{-- START: Filter Section --}}
                    {{-- ================================================================ --}}
                    <form method="GET" action="{{ route('admin.manage') }}" class="mb-6 bg-slate-50 dark:bg-slate-700/50 p-4 rounded-lg shadow-inner">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
                            {{-- Filter โดยสถานะการมอบหมาย --}}
                            <div>
                                <label for="assignment_status_filter" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">สถานะการมอบหมาย:</label>
                                <select name="assignment_status" id="assignment_status_filter"
                                        class="block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm dark:bg-slate-700 dark:text-slate-200 dark:focus:bg-slate-600 py-2 px-3">
                                    <option value="all" {{ (request('assignment_status', 'all') == 'all') ? 'selected' : '' }}>งานทั้งหมด</option>
                                    <option value="unassigned" {{ (request('assignment_status') == 'unassigned') ? 'selected' : '' }}>งานที่ยังไม่ได้มอบหมาย</option>
                                    <option value="assigned" {{ (request('assignment_status') == 'assigned') ? 'selected' : '' }}>งานที่มอบหมายแล้ว</option>
                                </select>
                            </div>

                            {{-- Filter โดยสถานะการแจ้งซ่อม --}}
                            <div>
                                <label for="status_filter" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">สถานะการแจ้งซ่อม:</label>
                                <select name="status_id" id="status_filter"
                                        class="block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm dark:bg-slate-700 dark:text-slate-200 dark:focus:bg-slate-600 py-2 px-3">
                                    <option value="">ทั้งหมด</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->id }}" {{ (string)request('status_id') === (string)$status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter โดยช่างที่รับผิดชอบ --}}
                            <div>
                                <label for="technician_filter" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">ช่างที่รับผิดชอบ:</label>
                                <select name="assigned_to_user_id" id="technician_filter"
                                        class="block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm dark:bg-slate-700 dark:text-slate-200 dark:focus:bg-slate-600 py-2 px-3">
                                    <option value="">ทั้งหมด</option>
                                    <option value="unassigned_tech" {{ (string)request('assigned_to_user_id') === 'unassigned_tech' ? 'selected' : '' }}>ยังไม่ได้มอบหมายช่าง</option>
                                    @foreach($technicians as $tech)
                                        <option value="{{ $tech->id }}" {{ (string)request('assigned_to_user_id') === (string)$tech->id ? 'selected' : '' }}>{{ $tech->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Search input --}}
                            <div class="md:col-span-1 lg:col-span-1">
                                <label for="search_query" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">ค้นหา (ชื่อเรื่อง/รหัส):</label>
                                <input type="text" name="search_query" id="search_query" value="{{ request('search_query') }}"
                                       placeholder="รหัส หรือ ชื่อเรื่อง..."
                                       class="block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm dark:bg-slate-700 dark:text-slate-200 dark:focus:bg-slate-600 py-2 px-3">
                            </div>

                            {{-- Action Buttons --}}
                            <div class="sm:col-span-2 md:col-span-3 lg:col-span-4 flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 mt-2 sm:mt-0">
                                <button type="submit"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-sky-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-700 active:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150 shadow-md flex-grow sm:flex-grow-0">
                                    <i class="fas fa-filter fa-fw mr-2"></i>{{ __('กรองข้อมูล') }}
                                </button>
                                <a href="{{ route('admin.manage') }}"
                                   class="inline-flex items-center justify-center px-4 py-2 bg-slate-200 border border-slate-300 rounded-lg font-semibold text-xs text-slate-700 uppercase tracking-widest hover:bg-slate-300 active:bg-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:bg-slate-700 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-600 dark:active:bg-slate-500 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150 shadow-md flex-grow sm:flex-grow-0">
                                    <i class="fas fa-redo fa-fw mr-2"></i>{{ __('รีเซ็ต') }}
                                </a>
                            </div>
                        </div>
                    </form>
                    {{-- ================================================================ --}}
                    {{-- END: Filter Section --}}
                    {{-- ================================================================ --}}

                    @if ($repairRequests->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-slate-900 dark:text-slate-200">ไม่พบรายการแจ้งซ่อม</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                @if(request()->hasAny(['assignment_status', 'status_id', 'assigned_to_user_id', 'search_query']) &&
                                    (request('assignment_status', 'all') !== 'all' ||
                                     request('status_id') !== null ||
                                     request('assigned_to_user_id') !== null ||
                                     request('search_query') !== null)
                                )
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
                            <table class="min-w-full table-auto divide-y divide-slate-200 dark:divide-slate-700">
                                <thead class="bg-slate-100 dark:bg-slate-700/50">
                                    <tr>
                                        <th class="py-3.5 pl-4 pr-3 text-left text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider sm:pl-6 w-[80px]">ID</th>
                                        <th class="py-3.5 px-3 text-left text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider min-w-[150px] lg:min-w-[200px]">เรื่อง</th>
                                        <th class="hidden lg:table-cell py-3.5 px-3 text-left text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider min-w-[120px]">ผู้แจ้ง</th>
                                        <th class="py-3.5 px-3 text-left text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider w-[120px]">สถานะ</th>
                                        <th class="hidden xl:table-cell py-3.5 px-3 text-left text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider w-[120px]">มอบหมายให้</th>
                                        <th class="hidden sm:table-cell py-3.5 px-3 text-left text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider w-[120px]">วันที่แจ้ง</th>
                                        <th class="py-3.5 px-3 text-left text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 min-w-[240px] lg:min-w-[280px]">อัปเดตด่วน</th>
                                        <th class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-center text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider min-w-[180px]">ดำเนินการ</th>
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
                                            <td class="py-3 px-3 text-sm align-top"> {{-- align-top เพื่อให้ฟอร์มในคอลัมน์ไม่ถูกบีบ --}}
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
                                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-3 space-y-2 sm:space-y-0">
                                        <a href="{{ route('repair_requests.show', $item) }}" class="block flex-1 sm:mr-2">
                                            <h4 class="text-base font-semibold text-sky-600 dark:text-sky-400 hover:underline leading-tight">{{ $item->title }}</h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">ID: {{ $item->id }} | โดย: {{ $item->user->name ?? ($item->requester_name ?? 'N/A') }}</p>
                                        </a>
                                        <span class="flex-shrink-0 px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $item->status->color_class ?? 'bg-slate-100 text-slate-800 dark:bg-slate-600 dark:text-slate-200' }}">
                                            {{ $item->status->name ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-1 text-sm text-slate-600 dark:text-slate-300 mb-4">
                                        <p><i class="fas fa-map-marker-alt fa-fw mr-2 text-slate-400"></i><strong class="font-medium text-slate-700 dark:text-slate-200">สถานที่:</strong> {{ Str::limit(optional($item->location)->name, 35) ?? 'N/A' }}</p>
                                        <p><i class="fas fa-user-shield fa-fw mr-2 text-slate-400"></i><strong class="font-medium text-slate-700 dark:text-slate-200">มอบหมายให้:</strong> {{ optional($item->assignedTo)->name ?? 'ยังไม่ได้มอบหมาย' }}</p>
                                        <p class="col-span-1 sm:col-span-2"><i class="far fa-calendar-alt fa-fw mr-2 text-slate-400"></i><strong class="font-medium text-slate-700 dark:text-slate-200">วันที่แจ้ง:</strong> {{ $item->created_at->translatedFormat('j M Y, H:i') }}</p>
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

<style>
    /* Custom style for truncate with title on hover */
    .truncate-with-title {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.querySelector('form[action="{{ route('admin.manage') }}"]');
        const selectElements = filterForm.querySelectorAll('select');
        const searchInput = filterForm.querySelector('input[name="search_query"]');

        // ฟังก์ชัน submit ฟอร์มเมื่อมีการเปลี่ยนแปลง
        selectElements.forEach(select => {
            select.addEventListener('change', function() {
                filterForm.submit();
            });
        });

        // ทำให้กด Enter ในช่องค้นหาแล้ว submit ได้
        if (searchInput) {
            searchInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault(); // ป้องกันการ submit ซ้ำ
                    filterForm.submit();
                }
            });
        }
    });
</script>