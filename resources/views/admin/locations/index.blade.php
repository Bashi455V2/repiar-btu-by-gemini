<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                {{ __('จัดการสถานที่') }}
            </h2>
            <a href="{{ route('admin.locations.create') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 ...">
                <i class="fas fa-plus mr-2"></i>{{ __('เพิ่มสถานที่ใหม่') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 shadow-xl sm:rounded-lg">
                <div class="p-6 sm:p-8">
                    @if (session('status'))
                        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-700/30 dark:text-green-300" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-700/30 dark:text-red-300" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($locations->isEmpty())
                        <p class="text-slate-600 dark:text-slate-400">{{ __('ยังไม่มีข้อมูลสถานที่') }}</p>
                    @else
                        <div class="overflow-x-auto align-middle inline-block min-w-full">
                            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                                <thead class="bg-slate-50 dark:bg-slate-700/50">
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 dark:text-slate-200 sm:pl-6">ID</th>
                                        <th scope="col" class="py-3.5 px-3 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">ชื่อสถานที่</th>
                                        <th scope="col" class="hidden sm:table-cell py-3.5 px-3 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">อาคาร</th>
                                        <th scope="col" class="hidden sm:table-cell py-3.5 px-3 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">ชั้น</th>
                                        <th scope="col" class="hidden md:table-cell py-3.5 px-3 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">ห้อง</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                                    @foreach ($locations as $location)
                                        <tr>
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-900 dark:text-slate-100 sm:pl-6">{{ $location->id }}</td>
                                            <td class="whitespace-nowrap py-4 px-3 text-sm text-slate-600 dark:text-slate-300">{{ $location->name }}</td>
                                            <td class="hidden sm:table-cell whitespace-nowrap py-4 px-3 text-sm text-slate-500 dark:text-slate-400">{{ $location->building ?? '-' }}</td>
                                            <td class="hidden sm:table-cell whitespace-nowrap py-4 px-3 text-sm text-slate-500 dark:text-slate-400">{{ $location->floor ?? '-' }}</td>
                                            <td class="hidden md:table-cell whitespace-nowrap py-4 px-3 text-sm text-slate-500 dark:text-slate-400">{{ $location->room_number ?? '-' }}</td>
                                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                                <a href="{{ route('admin.locations.edit', $location) }}" class="text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300">แก้ไข</a>
                                                <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" class="inline-block ml-3" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบสถานที่นี้?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">ลบ</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-6">
                            {{ $locations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>