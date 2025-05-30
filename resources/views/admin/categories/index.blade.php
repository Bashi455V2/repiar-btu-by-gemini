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
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                                    @foreach ($categories as $category)
                                        <tr>
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-900 dark:text-slate-100 sm:pl-6">{{ $category->id }}</td>
                                            <td class="whitespace-nowrap py-4 px-3 text-sm text-slate-600 dark:text-slate-300">{{ $category->name }}</td>
                                            <td class="hidden sm:table-cell py-4 px-3 text-sm text-slate-500 dark:text-slate-400">{{ Str::limit($category->description, 70) ?? '-' }}</td>
                                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                                <a href="{{ route('admin.categories.edit', $category) }}" class="text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300">
                                                    <i class="fas fa-edit mr-1 text-xs"></i>แก้ไข
                                                </a>
                                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-block ml-3" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบหมวดหมู่นี้?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">
                                                        <i class="fas fa-trash-alt mr-1 text-xs"></i>ลบ
                                                    </button>
                                                </form>
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