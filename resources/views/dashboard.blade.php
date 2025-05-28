<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("คุณเข้าสู่ระบบแล้ว!") }}

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="bg-blue-100 p-4 rounded-lg shadow-md">
                            <h3 class="font-bold text-lg text-blue-800">รายการแจ้งซ่อมทั้งหมด</h3>
                            <p class="text-3xl font-bold text-blue-900">{{ $totalRequests }}</p>
                        </div>
                        <div class="bg-yellow-100 p-4 rounded-lg shadow-md">
                            <h3 class="font-bold text-lg text-yellow-800">รอดำเนินการ</h3>
                            <p class="text-3xl font-bold text-yellow-900">{{ $pendingRequests }}</p>
                        </div>
                        <div class="bg-indigo-100 p-4 rounded-lg shadow-md">
                            <h3 class="font-bold text-lg text-indigo-800">กำลังดำเนินการ</h3>
                            <p class="text-3xl font-bold text-indigo-900">{{ $inProgressRequests }}</p>
                        </div>
                        <div class="bg-green-100 p-4 rounded-lg shadow-md">
                            <h3 class="font-bold text-lg text-green-800">ซ่อมเสร็จแล้ว</h3>
                            <p class="text-3xl font-bold text-green-900">{{ $completedRequests }}</p>
                        </div>
                    </div>

                    {{-- แสดงรายชื่อผู้ใช้งานทั้งหมดสำหรับ Admin/Technician --}}
                    @if (Auth::user()->is_admin || Auth::user()->is_technician)
                        <div class="mt-8">
                            <h3 class="font-semibold text-xl text-gray-800 leading-tight mb-4">{{ __('จัดการผู้ใช้งาน') }}</h3>
                            @if ($users->isEmpty())
                                <p class="text-center text-gray-600">ยังไม่มีผู้ใช้งานในระบบ</p>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    ชื่อ
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    อีเมล
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    แอดมิน
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    ช่าง
                                                </th>
                                                <th scope="col" class="relative px-6 py-3">
                                                    <span class="sr-only">Actions</span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($users as $user)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $user->name }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $user->email }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        @if ($user->is_admin)
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                                ใช่
                                                            </span>
                                                        @else
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                                ไม่ใช่
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        @if ($user->is_technician)
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                                ใช่
                                                            </span>
                                                        @else
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                                ไม่ใช่
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        {{-- คุณอาจต้องการเพิ่มลิงก์แก้ไขผู้ใช้ที่นี่ในอนาคต --}}
                                                        <a href="#" class="text-indigo-600 hover:text-indigo-900">แก้ไขบทบาท</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-4">
                                    {{ $users->links() }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>