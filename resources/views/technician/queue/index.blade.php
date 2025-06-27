<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            {{-- Title สำหรับหน้า "คิวงานซ่อมทั้งหมด" --}}
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                <i class="fas fa-inbox fa-fw mr-2 text-blue-500"></i>{{ __('คิวงานซ่อมทั้งหมด') }}
            </h2>

        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8">
                    {{-- Flash Messages --}}
                    @if (session('status'))
                        <div class="mb-6 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-700/30 dark:text-green-300" role="alert">{{ session('status') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="mb-6 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-700/30 dark:text-red-300" role="alert">{{ session('error') }}</div>
                    @endif

                    {{-- ฟิลเตอร์สำหรับ "คิวงานซ่อม" (งานที่รับได้ / งานที่ช่างคนอื่นรับไป) --}}
                    <div class="mb-6 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-lg shadow-inner flex flex-wrap gap-3 items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ __('มุมมองงาน:') }}</span>
                            <a href="{{ route('technician.queue.index', ['filter' => 'available', 'status_group' => $currentStatusGroup]) }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md
                               {{ ($currentQueueFilter ?? 'available') == 'available' ? 'bg-sky-600 text-white shadow-md hover:bg-sky-700' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-900 border-slate-300 dark:border-slate-600' }}
                               focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                                 <i class="fas fa-hand-paper mr-2"></i>
                                 {{ __('งานที่รับได้') }}
                            </a>
                            <a href="{{ route('technician.queue.index', ['filter' => 'others_assigned', 'status_group' => $currentStatusGroup]) }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md
                               {{ ($currentQueueFilter ?? 'available') == 'others_assigned' ? 'bg-sky-600 text-white shadow-md hover:bg-sky-700' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-900 border-slate-300 dark:border-slate-600' }}
                               focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                                 <i class="fas fa-user-friends mr-2"></i>
                                 {{ __('งานที่ช่างคนอื่นรับไป') }}
                            </a>
                        </div>
                    </div>


                    {{-- Tabs กรองสถานะสำหรับ "คิวงานซ่อม" --}}
                    <div class="mb-6">
                        <nav class="flex space-x-2 lg:space-x-4 overflow-x-auto p-1 bg-slate-50 dark:bg-slate-700/50 rounded-lg shadow-inner" aria-label="Tabs">
                            @php
                                $tabs = [
                                    'new' => ['name' => 'งานใหม่', 'icon' => 'fa-inbox'],
                                    'in_progress' => ['name' => 'กำลังดำเนินการ', 'icon' => 'fa-spinner'],
                                    'completed' => ['name' => 'งานที่เสร็จสิ้น', 'icon' => 'fa-check-circle'],
                                ];
                            @endphp
                            @foreach ($tabs as $key => $tab)
                                {{-- ลิงก์สำหรับเปลี่ยนสถานะใน 'คิวงานซ่อม' --}}
                                <a href="{{ route('technician.queue.index', ['status_group' => $key, 'filter' => $currentQueueFilter]) }}"
                                   class="flex-shrink-0 flex items-center justify-center px-4 py-2 text-sm font-medium rounded-md whitespace-nowrap transition-colors duration-200
                                   {{ ($currentStatusGroup ?? 'new') == $key ? 'bg-sky-600 text-white shadow-md' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600' }}">
                                     <i class="fas {{ $tab['icon'] }} mr-2 {{ ($currentStatusGroup ?? 'new') == $key ? '' : (($tab['icon'] == 'fa-spinner') ? 'fa-spin' : '') }}"></i>
                                     {{ $tab['name'] }}
                                     @if(isset($taskCounts[$key]) && $taskCounts[$key] > 0)
                                     <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                     {{ ($currentStatusGroup ?? 'new') == $key ? 'bg-sky-100 text-sky-800 dark:bg-sky-500/20 dark:text-sky-300' : 'bg-slate-200 text-slate-800 dark:bg-slate-600 dark:text-slate-200' }}">
                                         {{ $taskCounts[$key] }}
                                     </span>
                                     @endif
                                </a>
                            @endforeach
                        </nav>
                    </div>

                    @if ($repairRequests->isEmpty())
                        <div class="text-center py-12">
                            <i class="fas fa-folder-open fa-4x text-slate-300 dark:text-slate-600"></i>
                            <h3 class="mt-4 text-sm font-medium text-slate-900 dark:text-slate-200">ไม่พบรายการในหมวดหมู่นี้</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">ลองเลือกดูในหมวดหมู่อื่น หรือเปลี่ยนมุมมองในเมนูด้านบน</p>
                        </div>
                    @else
                        {{-- Card View for Mobile --}}
                        <div class="grid grid-cols-1 gap-4 md:hidden">
                            @foreach ($repairRequests as $item)
                                <div class="bg-white dark:bg-slate-800/50 rounded-lg shadow-md border dark:border-slate-700 p-4 space-y-3">
                                    <div class="flex justify-between items-start">
                                        {{-- แก้ไขลิงก์ title สำหรับ Card View --}}
                                        <a href="{{ route('repair_requests.show', ['repair_request' => $item->id, 'status_group' => $currentStatusGroup, 'filter' => $currentQueueFilter]) }}" class="font-bold text-slate-800 dark:text-slate-100 hover:text-sky-600 dark:hover:text-sky-400 break-words pr-2">
                                            {{ $item->title }}
                                        </a>
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full flex-shrink-0 {{ $item->status->color_class ?? 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-200' }}">{{ $item->status->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400 space-y-1">
                                        <p><i class="fas fa-map-marker-alt fa-fw mr-1"></i>{{ $item->location->name ?? 'N/A' }}</p>
                                        <p><i class="fas fa-user fa-fw mr-1"></i>ผู้แจ้ง: {{ $item->user->name ?? 'N/A' }}</p>
                                        <p><i class="fas fa-user-shield fa-fw mr-1"></i>มอบหมายให้: {{ $item->assignedTo->name ?? '-' }}</p>
                                        <p><i class="far fa-clock fa-fw mr-1"></i>วันที่แจ้ง: {{ $item->created_at->isoFormat('D MMM YY') }}</p>
                                    </div>
                                    <div class="border-t dark:border-slate-700 pt-3 flex justify-end space-x-4">
                                        {{-- เงื่อนไขการแสดงปุ่ม "รับงานนี้" ในหน้าคิวงานซ่อม --}}
                                        @if(is_null($item->assigned_to_user_id) && ($currentQueueFilter ?? 'available') === 'available')
                                            @can('claim', $item)
                                            <form method="POST" action="{{ route('repair_requests.claim', $item) }}" onsubmit="return confirm('คุณต้องการรับงานซ่อมนี้ใช่หรือไม่?')">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="text-sm font-semibold text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">รับงานนี้</button>
                                            </form>
                                            @endcan
                                        @else
                                            {{-- แก้ไขปุ่ม "ดูรายละเอียด" สำหรับ Card View --}}
                                            <a href="{{ route('repair_requests.show', ['repair_request' => $item->id, 'status_group' => $currentStatusGroup, 'filter' => $currentQueueFilter]) }}" class="text-sm font-semibold text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">ดูรายละเอียด</a>
                                            {{-- ปุ่มอัปเดตอาจมีหรือไม่มีก็ได้ขึ้นอยู่กับ Policy ของคุณ --}}
                                            @can('update', $item)
                                                <a href="{{ route('repair_requests.edit', $item) }}" class="text-sm font-semibold text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300">อัปเดต</a>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Table View for Desktop --}}
                        <div class="hidden md:block overflow-x-auto align-middle min-w-full">
                            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                                <thead class="bg-slate-50 dark:bg-slate-700/50">
                                    <tr>
                                        <th scope="col" class="py-3.5 px-4 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">ID</th>
                                        <th scope="col" class="py-3.5 px-4 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">เรื่อง</th>
                                        <th scope="col" class="hidden sm:table-cell py-3.5 px-4 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">หมวดหมู่</th>
                                        <th scope="col" class="py-3.5 px-4 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">สถานะ</th>
                                        <th scope="col" class="hidden lg:table-cell py-3.5 px-4 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">ผู้แจ้ง</th>
                                        <th scope="col" class="hidden lg:table-cell py-3.5 px-4 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">มอบหมายให้</th>
                                        <th scope="col" class="hidden md:table-cell py-3.5 px-4 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">วันที่แจ้ง</th>
                                        <th scope="col" class="relative py-3.5 px-4"><span class="sr-only">ดำเนินการ</span></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                                    @foreach ($repairRequests as $item)
                                        <tr>
                                            <td class="whitespace-nowrap py-4 px-4 text-sm font-medium text-slate-900 dark:text-slate-100">{{ $item->id }}</td>
                                            <td class="py-4 px-4 text-sm text-slate-600 dark:text-slate-300 max-w-xs truncate" title="{{ $item->title }}">
                                                {{-- แก้ไขลิงก์ title สำหรับ Table View --}}
                                                <a href="{{ route('repair_requests.show', ['repair_request' => $item->id, 'status_group' => $currentStatusGroup, 'filter' => $currentQueueFilter]) }}" class="text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300 font-medium">{{ $item->title }}</a>
                                            </td>
                                            <td class="hidden sm:table-cell whitespace-nowrap py-4 px-4 text-sm text-slate-500 dark:text-slate-400">{{ $item->category->name ?? 'N/A' }}</td>
                                            <td class="whitespace-nowrap py-4 px-4 text-sm"><span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->status->color_class ?? 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-200' }}">{{ $item->status->name ?? 'N/A' }}</span></td>
                                            <td class="hidden lg:table-cell whitespace-nowrap py-4 px-4 text-sm text-slate-500 dark:text-slate-400">{{ $item->user->name ?? 'N/A' }}</td>
                                            <td class="hidden lg:table-cell whitespace-nowrap py-4 px-4 text-sm text-slate-500 dark:text-slate-400">{{ $item->assignedTo->name ?? '-' }}</td>
                                            <td class="hidden md:table-cell whitespace-nowrap py-4 px-4 text-sm text-slate-500 dark:text-slate-400">{{ $item->created_at->isoFormat('D MMM YY') }}</td>
                                            <td class="relative whitespace-nowrap py-4 px-4 text-right text-sm font-medium">
                                                {{-- เงื่อนไขการแสดงปุ่ม "รับงานนี้" ในหน้าคิวงานซ่อม (table view) --}}
                                                @if(is_null($item->assigned_to_user_id) && ($currentQueueFilter ?? 'available') === 'available')
                                                    @can('claim', $item)
                                                    <form method="POST" action="{{ route('repair_requests.claim', $item) }}" onsubmit="return confirm('คุณต้องการรับงานซ่อมนี้ใช่หรือไม่?')">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="font-semibold text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">รับงานนี้ <i class="fas fa-hand-paper ml-1"></i></button>
                                                    </form>
                                                    @endcan
                                                @else
                                                    {{-- แก้ไขปุ่ม "ดู" สำหรับ Table View --}}
                                                    <a href="{{ route('repair_requests.show', ['repair_request' => $item->id, 'status_group' => $currentStatusGroup, 'filter' => $currentQueueFilter]) }}" class="text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">ดู</a>
                                                    @can('update', $item)
                                                        <a href="{{ route('repair_requests.edit', $item) }}" class="text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 ml-3">อัปเดต</a>
                                                    @endcan
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-6">
                            {{ $repairRequests->appends(request()->query())->links('vendor.pagination.custom') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>