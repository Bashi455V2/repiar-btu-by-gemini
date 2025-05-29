<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight mb-2 sm:mb-0">
                {{ Auth::user()->is_admin ? __('รายการแจ้งซ่อมทั้งหมด') : (Auth::user()->is_technician ? __('งานที่เกี่ยวข้อง') : __('รายการแจ้งซ่อมของฉัน')) }}
            </h2>
            <a href="{{ route('repair_requests.create') }}"
               class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-700 active:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                {{ __('แจ้งซ่อมใหม่') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700">

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
                                {{ Auth::user()->is_admin || Auth::user()->is_technician ? __('ยังไม่มีรายการแจ้งซ่อมให้จัดการในขณะนี้') : __('คุณยังไม่มีรายการแจ้งซ่อมที่สร้างไว้') }}
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('repair_requests.create') }}"
                                   class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-700 active:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                                    <svg class="w-4 h-4 mr-2 -ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                    {{ __('เริ่มแจ้งซ่อมรายการแรก') }}
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto align-middle inline-block min-w-full">
                            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                                <thead class="bg-slate-50 dark:bg-slate-700/50">
                                    <tr>
                                        <th scope="col" class="py-3.5 px-4 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">ID</th>
                                        <th scope="col" class="py-3.5 px-4 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">เรื่อง</th>
                                        <th scope="col" class="hidden sm:table-cell py-3.5 px-4 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">หมวดหมู่</th>
                                        <th scope="col" class="hidden md:table-cell py-3.5 px-4 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">สถานที่</th>
                                        <th scope="col" class="py-3.5 px-4 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">สถานะ</th>
                                        @if(Auth::user()->is_admin || Auth::user()->is_technician)
                                            <th scope="col" class="hidden lg:table-cell py-3.5 px-4 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">ผู้แจ้ง</th>
                                            <th scope="col" class="hidden lg:table-cell py-3.5 px-4 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">มอบหมายให้</th>
                                        @endif
                                        <th scope="col" class="hidden md:table-cell py-3.5 px-4 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">วันที่แจ้ง</th>
                                        <th scope="col" class="relative py-3.5 px-4">
                                            <span class="sr-only">ดำเนินการ</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                                    @foreach ($repairRequests as $item)
                                        <tr>
                                            <td class="whitespace-nowrap py-4 px-4 text-sm font-medium text-slate-900 dark:text-slate-100">{{ $item->id }}</td>
                                            <td class="whitespace-nowrap py-4 px-4 text-sm text-slate-600 dark:text-slate-300">
                                                <a href="{{ route('repair_requests.show', $item) }}" class="text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300 font-medium">
                                                    {{ Str::limit($item->title, 30) }}
                                                </a>
                                            </td>
                                            <td class="hidden sm:table-cell whitespace-nowrap py-4 px-4 text-sm text-slate-500 dark:text-slate-400">{{ $item->category->name ?? 'N/A' }}</td>
                                            <td class="hidden md:table-cell whitespace-nowrap py-4 px-4 text-sm text-slate-500 dark:text-slate-400">{{ Str::limit($item->location->name ?? 'N/A', 25) }}</td>
                                            <td class="whitespace-nowrap py-4 px-4 text-sm">
                                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->status->color_class ?? 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-200' }}">
                                                    {{ $item->status->name ?? 'N/A' }}
                                                </span>
                                            </td>
                                            @if(Auth::user()->is_admin || Auth::user()->is_technician)
                                                <td class="hidden lg:table-cell whitespace-nowrap py-4 px-4 text-sm text-slate-500 dark:text-slate-400">{{ $item->user->name ?? ($item->requester_name ?? 'N/A') }}</td>
                                                <td class="hidden lg:table-cell whitespace-nowrap py-4 px-4 text-sm text-slate-500 dark:text-slate-400">{{ $item->assignedTo->name ?? '-' }}</td>
                                            @endif
                                            <td class="hidden md:table-cell whitespace-nowrap py-4 px-4 text-sm text-slate-500 dark:text-slate-400">{{ $item->created_at->isoFormat('D MMM YYYY') }}</td>
                                            <td class="relative whitespace-nowrap py-4 px-4 text-right text-sm font-medium">
                                                <a href="{{ route('repair_requests.show', $item) }}" class="text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300">ดู</a>
                                                @can('update', $item)
                                                <a href="{{ route('repair_requests.edit', $item) }}" class="text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300 ml-3">แก้ไข</a>
                                                @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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