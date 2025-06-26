<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight mb-2 sm:mb-0">
                <i class="fas fa-users-cog fa-fw mr-2 text-sky-600 dark:text-sky-500"></i>{{ __('การจัดการผู้ใช้งาน') }}
            </h2>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-700 active:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                <i class="fas fa-user-plus fa-fw mr-2"></i>{{ __('เพิ่มผู้ใช้ใหม่') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-xl sm:rounded-xl">
                <div class="p-6 sm:p-8">
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

                    @if ($users->isEmpty())
                        <div class="text-center py-12">
                            <i class="fas fa-users-slash fa-4x text-slate-400 dark:text-slate-500 mb-4"></i>
                            <h3 class="mt-2 text-lg font-semibold text-slate-900 dark:text-slate-100">{{ __('ยังไม่มีผู้ใช้งานในระบบ') }}</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                คุณสามารถเพิ่มผู้ใช้งานใหม่ได้โดยคลิกที่ปุ่มด้านบน
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('admin.users.create') }}"
                                   class="inline-flex items-center px-4 py-2 bg-sky-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-sky-700 active:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800 transition ease-in-out duration-150 shadow-md hover:shadow-lg">
                                    <i class="fas fa-user-plus fa-fw mr-2"></i>
                                    {{ __('เพิ่มผู้ใช้คนแรก') }}
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto align-middle inline-block min-w-full mt-2">
                            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700 rounded-t-lg overflow-hidden">
                                <thead class="bg-slate-100 dark:bg-slate-700/50">
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider sm:pl-6">ID</th>
                                        <th scope="col" class="py-3.5 px-3 text-left text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">ชื่อ</th>
                                        <th scope="col" class="py-3.5 px-3 text-left text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">อีเมล</th>
                                        <th scope="col" class="py-3.5 px-3 text-center text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">บทบาท</th>
                                        <th scope="col" class="hidden sm:table-cell py-3.5 px-3 text-left text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">เข้าร่วมเมื่อ</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-center text-xs sm:text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">ดำเนินการ</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-700 bg-white dark:bg-slate-800">
                                    @foreach ($users as $user)
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors duration-150">
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-slate-900 dark:text-slate-100 sm:pl-6">{{ $user->id }}</td>
                                            <td class="py-4 px-3 text-sm text-slate-700 dark:text-slate-300 font-medium">{{ $user->name }}</td>
                                            <td class="py-4 px-3 text-sm text-slate-500 dark:text-slate-400">{{ $user->email }}</td>
                                            <td class="whitespace-nowrap py-4 px-3 text-sm text-center">
                                                <div class="flex items-center justify-center space-x-1">
                                                    @if($user->is_admin)
                                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-sky-100 text-sky-800 dark:bg-sky-700/50 dark:text-sky-300" title="Admin">
                                                            <i class="fas fa-user-shield mr-1.5"></i>Admin
                                                        </span>
                                                    @endif
                                                    @if($user->is_technician)
                                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-teal-100 text-teal-800 dark:bg-teal-700/50 dark:text-teal-300" title="Technician">
                                                             <i class="fas fa-tools mr-1.5"></i>ช่าง
                                                        </span>
                                                    @endif
                                                    @if(!$user->is_admin && !$user->is_technician)
                                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-700 dark:bg-slate-600 dark:text-slate-300">
                                                            ผู้ใช้ทั่วไป
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="hidden sm:table-cell whitespace-nowrap py-4 px-3 text-sm text-slate-500 dark:text-slate-400">{{ $user->created_at->translatedFormat('j M Y') }}</td>
                                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-center text-sm font-medium sm:pr-6">
                                                <div class="flex items-center justify-center space-x-2">
                                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="text-amber-500 hover:text-amber-600 dark:text-amber-400 dark:hover:text-amber-300 p-1.5 rounded-full hover:bg-amber-100 dark:hover:bg-slate-700 transition-colors duration-150" title="แก้ไขผู้ใช้">
                                                        <i class="fas fa-user-edit fa-fw"></i>
                                                    </a>
                                                    @if(Auth::id() !== $user->id && !$user->is_admin) {{-- ป้องกันการลบตัวเอง และป้องกันการลบ Admin อื่นๆ --}}
                                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบผู้ใช้นี้? การกระทำนี้ไม่สามารถย้อนกลับได้');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 p-1.5 rounded-full hover:bg-red-100 dark:hover:bg-slate-700 transition-colors duration-150" title="ลบผู้ใช้">
                                                            <i class="fas fa-user-times fa-fw"></i>
                                                        </button>
                                                    </form>
                                                    @else
                                                        <button type="button" class="text-slate-300 dark:text-slate-600 p-1.5 rounded-full cursor-not-allowed" title="{{ Auth::id() === $user->id ? 'ไม่สามารถลบตัวเองได้' : 'ไม่สามารถลบ Admin ได้' }}" disabled>
                                                            <i class="fas fa-user-times fa-fw"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- ส่วนแสดงผลสำหรับหน้าจอมือถือ (Card View) --}}
                        <div class="block md:hidden mt-4 space-y-4">
                            @foreach ($users as $user)
                                <div class="bg-white dark:bg-slate-700/50 shadow-lg rounded-lg p-4 border border-slate-200 dark:border-slate-600">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <h4 class="text-md font-semibold text-slate-800 dark:text-slate-100">{{ $user->name }}</h4>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                                        </div>
                                        <div class="flex flex-col items-end space-y-1">
                                            @if($user->is_admin)
                                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-sky-100 text-sky-800 dark:bg-sky-700/50 dark:text-sky-300" title="Admin">
                                                    <i class="fas fa-user-shield mr-1"></i>Admin
                                                </span>
                                            @endif
                                            @if($user->is_technician)
                                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-teal-100 text-teal-800 dark:bg-teal-700/50 dark:text-teal-300" title="Technician">
                                                     <i class="fas fa-tools mr-1"></i>ช่าง
                                                </span>
                                            @endif
                                            @if(!$user->is_admin && !$user->is_technician)
                                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-700 dark:bg-slate-600 dark:text-slate-300">
                                                    ผู้ใช้ทั่วไป
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">เข้าร่วมเมื่อ: {{ $user->created_at->translatedFormat('j M Y') }}</p>
                                    <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-600 flex justify-end space-x-3">
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="text-xs font-medium text-amber-600 dark:text-amber-400 hover:underline inline-flex items-center">
                                            <i class="fas fa-user-edit mr-1"></i>แก้ไข
                                        </a>
                                        @if(Auth::id() !== $user->id && !$user->is_admin)
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบผู้ใช้นี้?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-medium text-red-600 dark:text-red-400 hover:underline inline-flex items-center">
                                                <i class="fas fa-user-times mr-1"></i>ลบ
                                            </button>
                                        </form>
                                        @else
                                            <button type="button" class="text-xs font-medium text-slate-400 dark:text-slate-500 cursor-not-allowed inline-flex items-center" disabled>
                                                <i class="fas fa-user-times mr-1"></i>ลบ
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $users->links('pagination::tailwind') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>