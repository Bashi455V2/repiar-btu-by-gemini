<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                {{ __('จัดการหมวดหมู่ปัญหา') }}
            </h2>
            <a href="{{ route('admin.categories.create') }}"
               class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-700 active:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i>{{ __('เพิ่มหมวดหมู่ใหม่') }}
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

                    @if ($categories->isEmpty())
                        <p class="text-slate-600 dark:text-slate-400">{{ __('ยังไม่มีข้อมูลหมวดหมู่') }}</p>
                    @else
                        <div class="overflow-x-auto align-middle inline-block min-w-full">
                            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                                <thead class="bg-slate-50 dark:bg-slate-700/50">
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-slate-900 dark:text-slate-200 sm:pl-6">ID</th>
                                        <th scope="col" class="py-3.5 px-3 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">ชื่อหมวดหมู่</th>
                                        <th scope="col" class="hidden sm:table-cell py-3.5 px-3 text-left text-sm font-semibold text-slate-900 dark:text-slate-200">คำอธิบาย</th>
                                                <th scope="col" class="py-3.5 px-3 text-center text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">จำนวนที่ใช้</th>
        {{-- ^^^^^^ เพิ่ม Header ใหม่ ^^^^^^ --}}
        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-center text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">ดำเนินการ</th>

                                    </tr>
                                </thead>
                                {{-- ใน resources/views/admin/categories/index.blade.php --}}
<tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
    @foreach ($categories as $category)
        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors duration-150">
            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-900 dark:text-slate-100 sm:pl-6">{{ $category->id }}</td>
            <td class="whitespace-nowrap py-4 px-3 text-sm text-slate-600 dark:text-slate-300">{{ $category->name }}</td>
            <td class="hidden sm:table-cell py-4 px-3 text-sm text-slate-500 dark:text-slate-400 max-w-xs truncate" title="{{ $category->description }}">{{ Str::limit($category->description, 70) ?? '-' }}</td>
            {{-- VVVVVV เพิ่ม Cell ใหม่สำหรับแสดงจำนวน VVVVVV --}}
            <td class="whitespace-nowrap py-4 px-3 text-sm text-center text-slate-500 dark:text-slate-400">
                @if($category->repair_requests_count > 0)
                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-sky-100 text-sky-800 dark:bg-sky-700/50 dark:text-sky-300">
                        {{ $category->repair_requests_count }} รายการ
                    </span>
                @else
                    <span class="text-slate-400 dark:text-slate-500">-</span>
                @endif
            </td>
            {{-- ^^^^^^ เพิ่ม Cell ใหม่สำหรับแสดงจำนวน ^^^^^^ --}}
            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-center text-sm font-medium sm:pr-6">
                <div class="flex items-center justify-center space-x-2">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-amber-500 hover:text-amber-600 dark:text-amber-400 dark:hover:text-amber-300 p-1.5 rounded-full hover:bg-amber-100 dark:hover:bg-slate-700 transition-colors duration-150" title="แก้ไข">
                        <i class="fas fa-edit fa-fw"></i>
                    </a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบหมวดหมู่นี้? (ถ้ามีการใช้งานอยู่จะไม่สามารถลบได้)');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 p-1.5 rounded-full hover:bg-red-100 dark:hover:bg-slate-700 transition-colors duration-150 {{ $category->repair_requests_count > 0 ? 'opacity-50 cursor-not-allowed' : '' }}" title="ลบ" {{ $category->repair_requests_count > 0 ? 'disabled' : '' }}>
                            <i class="fas fa-trash-alt fa-fw"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    @endforeach
</tbody>
                            </table>
                        </div>
                        <div class="mt-6">
                            {{ $categories->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>