<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight mb-2 sm:mb-0">
                {{ __('หน้าจัดการแจ้งซ่อม') }}
            </h2>
            <a href="{{ route('repair_requests.create') }}"
               class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-700 active:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                <i class="fas fa-plus w-3 h-3 mr-2 -ml-1"></i> {{-- Font Awesome Icon --}}
                {{ __('แจ้งซ่อมใหม่') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-4 sm:p-6 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">
                    <h3 class="text-xl sm:text-2xl font-semibold text-slate-900 dark:text-slate-100 mb-6">
                        {{ __('รายการแจ้งซ่อมทั้งหมด') }}
                    </h3>

                    @if (session('status'))
                        <div class="mb-6 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-700/30 dark:text-green-300"
                            role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($repairRequests->isEmpty())
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-slate-900 dark:text-slate-100">{{ __('ยังไม่มีรายการแจ้งซ่อม') }}</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                {{ __('ยังไม่มีรายการแจ้งซ่อมให้จัดการในขณะนี้') }}
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('repair_requests.create') }}"
                                   class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-700 active:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                                    <i class="fas fa-plus w-3 h-3 mr-2 -ml-1"></i> {{-- Font Awesome Icon --}}
                                    {{ __('เริ่มแจ้งซ่อมรายการแรก') }}
                                </a>
                            </div>
                        </div>
                    @else
                        {{-- ส่วนแสดงผลสำหรับหน้าจอขนาดกลางขึ้นไป (ตาราง) --}}
                        <div class="hidden md:block overflow-x-auto align-middle min-w-full">
                            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                                <thead class="bg-slate-50 dark:bg-slate-700/50">
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 dark:text-slate-200 sm:pl-6">ID</th>
                                        <th scope="col" class="py-3.5 px-3 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">เรื่อง</th>
                                        <th scope="col" class="py-3.5 px-3 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">ผู้แจ้ง</th>
                                        <th scope="col" class="py-3.5 px-3 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">สถานะ</th>
                                        <th scope="col" class="hidden lg:table-cell py-3.5 px-3 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">มอบหมายให้</th>
                                        <th scope="col" class="hidden sm:table-cell py-3.5 px-3 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">วันที่แจ้ง</th>
                                        <th scope="col" class="py-3.5 px-3 text-left text-sm font-semibold text-slate-900 dark:text-slate-200 min-w-[350px]">อัปเดตสถานะ/มอบหมาย</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                                    @foreach ($repairRequests as $item)
                                        <tr>
                                            {{-- ... (td อื่นๆ เหมือนเดิม) ... --}}
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-900 dark:text-slate-100 sm:pl-6">{{ $item->id }}</td>
                                            <td class="py-4 px-3 text-sm text-slate-600 dark:text-slate-300">
                                                <a href="{{ route('repair_requests.show', $item) }}" class="text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300 font-medium group">
                                                    {{ Str::limit($item->title, 25) }}
                                                    <span class="invisible group-hover:visible text-xs text-slate-400 dark:text-slate-500"> ({{ $item->title }})</span>
                                                </a>
                                            </td>
                                            <td class="whitespace-nowrap py-4 px-3 text-sm text-slate-500 dark:text-slate-400">{{ $item->user->name ?? ($item->requester_name ?? 'N/A') }}</td>
                                            <td class="whitespace-nowrap py-4 px-3 text-sm">
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->status->color_class ?? 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-200' }}">
                                                    {{ $item->status->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="hidden lg:table-cell whitespace-nowrap py-4 px-3 text-sm text-slate-500 dark:text-slate-400">{{ $item->assignedTo->name ?? '-' }}</td>
                                            <td class="hidden sm:table-cell whitespace-nowrap py-4 px-3 text-sm text-slate-500 dark:text-slate-400">{{ $item->created_at->isoFormat('D MMM YY') }}</td>
                                            <td class="py-3 px-3 text-sm">
                                                {{-- ฟอร์มอัปเดตสถานะ/มอบหมายงาน เหมือนเดิม --}}
                                                <form method="POST" action="{{ route('repair_requests.update_status_assign', $item->id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="flex flex-col space-y-2 lg:flex-row lg:space-y-0 lg:space-x-2 lg:items-end">
                                                        {{-- ... (select status_id, select assigned_to_user_id, button บันทึก) ... --}}
                                                         <div class="flex-grow">
                                                            <label for="status_id_desktop_{{ $item->id }}" class="sr-only">สถานะ</label>
                                                            <select name="status_id" id="status_id_desktop_{{ $item->id }}" class="block w-full rounded-md border-0 py-1.5 text-slate-900 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-sky-600 sm:text-xs sm:leading-6 bg-white dark:bg-slate-700">
                                                                @foreach ($statuses as $status)
                                                                    <option value="{{ $status->id }}" {{ $item->status_id == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('status_id', "updateFormDesktop_{$item->id}") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                                        </div>
                                                        <div class="flex-grow">
                                                            <label for="assigned_to_user_id_desktop_{{ $item->id }}" class="sr-only">มอบหมายให้</label>
                                                            <select name="assigned_to_user_id" id="assigned_to_user_id_desktop_{{ $item->id }}" class="block w-full rounded-md border-0 py-1.5 text-slate-900 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-sky-600 sm:text-xs sm:leading-6 bg-white dark:bg-slate-700">
                                                                <option value="">-- ไม่มอบหมาย --</option>
                                                                @foreach ($technicians as $technician)
                                                                    <option value="{{ $technician->id }}" {{ $item->assigned_to_user_id == $technician->id ? 'selected' : '' }}>{{ $technician->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('assigned_to_user_id', "updateFormDesktop_{$item->id}") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                                        </div>
                                                        <button type="submit" class="whitespace-nowrap inline-flex items-center justify-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-xs font-medium text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 dark:focus:ring-offset-slate-800">บันทึก</button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                                <a href="{{ route('repair_requests.show', $item) }}" class="text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300 inline-flex items-center">
                                                    <i class="fas fa-eye mr-1 text-xs"></i>ดู
                                                </a>
                                                @can('update', $item)
                                                <a href="{{ route('repair_requests.edit', $item) }}" class="text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 ml-2 sm:ml-3 inline-flex items-center">
                                                    <i class="fas fa-pencil-alt mr-1 text-xs"></i>แก้ไข
                                                </a>
                                                @endcan
                                                @can('delete', $item)
                                                <form action="{{ route('repair_requests.destroy', $item) }}" method="POST" class="inline-block ml-2 sm:ml-3" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบรายการแจ้งซ่อมนี้?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 font-medium inline-flex items-center">
                                                        <i class="fas fa-trash-alt mr-1 text-xs"></i>ลบ
                                                    </button>
                                                </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- ส่วนแสดงผลสำหรับหน้าจอมือถือ (Card View) --}}
                        <div class="block md:hidden mt-4 space-y-4">
                            @foreach ($repairRequests as $item)
                                <div class="bg-white dark:bg-slate-700/50 shadow-md rounded-lg p-4 border border-slate-200 dark:border-slate-600">
                                    {{-- ... (ส่วนแสดงข้อมูลใน Card เหมือนเดิม) ... --}}
                                     <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <a href="{{ route('repair_requests.show', $item) }}" class="text-lg font-semibold text-sky-600 dark:text-sky-400 hover:underline">{{ Str::limit($item->title, 40) }}</a>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">ID: {{ $item->id }} | แจ้งโดย: {{ $item->user->name ?? ($item->requester_name ?? 'N/A') }}</p>
                                        </div>
                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->status->color_class ?? 'bg-slate-100 text-slate-800 dark:bg-slate-600 dark:text-slate-200' }}">
                                            {{ $item->status->name ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-600 dark:text-slate-300 mb-1"><span class="font-medium">สถานที่:</span> {{ Str::limit(optional($item->location)->name, 30) ?? 'N/A' }}</p>
                                    <p class="text-sm text-slate-600 dark:text-slate-300 mb-3"><span class="font-medium">มอบหมายให้:</span> {{ optional($item->assignedTo)->name ?? 'ยังไม่ได้มอบหมาย' }}</p>

                                    <form method="POST" action="{{ route('repair_requests.update_status_assign', $item->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="space-y-3">
                                             {{-- ... (ฟอร์มอัปเดตสถานะ/มอบหมายงานใน Card เหมือนเดิม) ... --}}
                                            <div>
                                                <label for="status_id_mobile_{{ $item->id }}" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">เปลี่ยนสถานะ:</label>
                                                <select name="status_id" id="status_id_mobile_{{ $item->id }}" class="block w-full rounded-md border-0 py-1.5 text-slate-900 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-sky-600 sm:text-xs sm:leading-6 bg-white dark:bg-slate-700">
                                                    @foreach ($statuses as $status)
                                                        <option value="{{ $status->id }}" {{ $item->status_id == $status->id ? 'selected' : '' }}>{{ $status->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('status_id', "updateFormMobile_{$item->id}") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label for="assigned_to_user_id_mobile_{{ $item->id }}" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">มอบหมายช่าง:</label>
                                                <select name="assigned_to_user_id" id="assigned_to_user_id_mobile_{{ $item->id }}" class="block w-full rounded-md border-0 py-1.5 text-slate-900 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 focus:ring-2 focus:ring-inset focus:ring-sky-600 sm:text-xs sm:leading-6 bg-white dark:bg-slate-700">
                                                    <option value="">-- ไม่มอบหมาย --</option>
                                                    @foreach ($technicians as $technician)
                                                        <option value="{{ $technician->id }}" {{ $item->assigned_to_user_id == $technician->id ? 'selected' : '' }}>{{ $technician->name }}</option>
                                                    @endforeach
                                                </select>
                                                 @error('assigned_to_user_id', "updateFormMobile_{$item->id}") <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                            </div>
                                            <button type="submit" class="w-full whitespace-nowrap inline-flex items-center justify-center px-3 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 dark:focus:ring-offset-slate-800">
                                                บันทึกการเปลี่ยนแปลง
                                            </button>
                                        </div>
                                    </form>
                                    <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-600 flex justify-end space-x-3">
                                        <a href="{{ route('repair_requests.show', $item) }}" class="text-xs font-medium text-sky-600 dark:text-sky-400 hover:underline inline-flex items-center"><i class="fas fa-eye mr-1"></i>ดู</a>
                                        @can('update', $item)
                                        <a href="{{ route('repair_requests.edit', $item) }}" class="text-xs font-medium text-amber-600 dark:text-amber-400 hover:underline inline-flex items-center"><i class="fas fa-pencil-alt mr-1"></i>แก้ไข</a>
                                        @endcan
                                        {{-- VVVVVV เพิ่มปุ่มลบใน Card View VVVVVV --}}
                                        @can('delete', $item)
                                        <form action="{{ route('repair_requests.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบรายการแจ้งซ่อมนี้?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-medium text-red-600 dark:text-red-400 hover:underline inline-flex items-center">
                                                <i class="fas fa-trash-alt mr-1"></i>ลบ
                                            </button>
                                        </form>
                                        @endcan
                                        {{-- ^^^^^^ สิ้นสุดปุ่มลบใน Card View ^^^^^^ --}}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $repairRequests->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>