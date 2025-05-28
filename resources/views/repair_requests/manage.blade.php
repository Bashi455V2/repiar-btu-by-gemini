<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('หน้าจัดการแจ้งซ่อม') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('รายการแจ้งซ่อมทั้งหมด') }}</h3>

                    @if ($repairRequests->isEmpty())
                        <p>{{ __('ยังไม่มีรายการแจ้งซ่อมให้จัดการในขณะนี้') }}</p>
                    @else
                        <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="py-3 px-6">
                                            {{ __('ID') }}
                                        </th>
                                        <th scope="col" class="py-3 px-6">
                                            {{ __('เรื่อง') }}
                                        </th>
                                        <th scope="col" class="py-3 px-6">
                                            {{ __('สถานะ') }}
                                        </th>
                                        <th scope="col" class="py-3 px-6">
                                            {{ __('ความสำคัญ') }}
                                        </th>
                                        <th scope="col" class="py-3 px-6">
                                            {{ __('ผู้แจ้ง') }}
                                        </th>
                                        <th scope="col" class="py-3 px-6">
                                            {{ __('มอบหมายให้') }}
                                        </th>
                                        <th scope="col" class="py-3 px-6">
                                            {{ __('วันที่แจ้ง') }}
                                        </th>
                                        <th scope="col" class="py-3 px-6">
                                            {{ __('ดำเนินการ') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($repairRequests as $request)
                                        <tr class="bg-white border-b hover:bg-gray-50">
                                            <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap">
                                                {{ $request->id }}
                                            </td>
                                            <td class="py-4 px-6">
                                                <a href="{{ route('repair_requests.show', $request) }}" class="text-blue-600 hover:underline">
                                                    {{ Str::limit($request->subject, 50) }}
                                                </a>
                                            </td>
                                            <td class="py-4 px-6">
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                    @if($request->status === 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($request->status === 'in_progress') bg-blue-100 text-blue-800
                                                    @elseif($request->status === 'completed') bg-green-100 text-green-800
                                                    @elseif($request->status === 'cancelled') bg-red-100 text-red-800
                                                    @endif">
                                                    {{ __($request->status) }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-6">
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                                    @if($request->priority === 'low') bg-gray-100 text-gray-800
                                                    @elseif($request->priority === 'normal') bg-green-100 text-green-800
                                                    @elseif($request->priority === 'high') bg-orange-100 text-orange-800
                                                    @elseif($request->priority === 'urgent') bg-red-100 text-red-800
                                                    @endif">
                                                    {{ __($request->priority) }}
                                                </span>
                                            </td>
                                            <td class="py-4 px-6">
                                                {{ $request->user->name ?? 'N/A' }}
                                            </td>
                                            <td class="py-4 px-6">
                                                {{ $request->assignedTo->name ?? 'ยังไม่ได้มอบหมาย' }}
                                            </td>
                                            <td class="py-4 px-6">
                                                {{ $request->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('repair_requests.edit', $request) }}" class="font-medium text-blue-600 hover:underline">
                                                        {{ __('แก้ไข') }}
                                                    </a>
                                                    {{-- ส่วนนี้จะใช้ฟอร์มเพื่อลบ ถ้าต้องการ --}}
                                                    <form action="{{ route('repair_requests.destroy', $request) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบรายการแจ้งซ่อมนี้?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="font-medium text-red-600 hover:underline">
                                                            {{ __('ลบ') }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination Links --}}
                        <div class="mt-4">
                            {{ $repairRequests->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>