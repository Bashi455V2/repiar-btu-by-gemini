<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('รายละเอียดแจ้งซ่อม: ') . $repairRequest->subject }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">หัวข้อ:</h3>
                            <p class="mt-1 text-gray-600">{{ $repairRequest->subject }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">ผู้แจ้ง:</h3>
                            <p class="mt-1 text-gray-600">{{ $repairRequest->user->name }} ({{ $repairRequest->user->email }})</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">สถานที่:</h3>
                            <p class="mt-1 text-gray-600">{{ $repairRequest->location }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">ข้อมูลติดต่อ:</h3>
                            <p class="mt-1 text-gray-600">{{ $repairRequest->contact_info ?? Auth::user()->email }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">สถานะ:</h3>
                            <p class="mt-1 text-gray-600">
                                <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full
                                    @if($repairRequest->status == 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($repairRequest->status == 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($repairRequest->status == 'completed') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($repairRequest->status) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">ความสำคัญ:</h3>
                            <p class="mt-1 text-gray-600">
                                <span class="px-2 inline-flex text-sm leading-5 font-semibold rounded-full
                                    @if($repairRequest->priority == 'urgent' || $repairRequest->priority == 'high') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($repairRequest->priority) }}
                                </span>
                            </p>
                        </div>
                        @if ($repairRequest->assigned_to)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">มอบหมายให้:</h3>
                            <p class="mt-1 text-gray-600">{{ $repairRequest->technician->name }}</p>
                        </div>
                        @endif
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">วันที่แจ้ง:</h3>
                            <p class="mt-1 text-gray-600">{{ $repairRequest->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @if ($repairRequest->completed_at)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">วันที่ซ่อมเสร็จ:</h3>
                            <p class="mt-1 text-gray-600">{{ $repairRequest->completed_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900">รายละเอียดปัญหา:</h3>
                        <p class="mt-1 text-gray-600">{{ $repairRequest->description }}</p>
                    </div>

                    @if ($repairRequest->attachment)
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900">ไฟล์แนบ:</h3>
                            <a href="{{ Storage::url($repairRequest->attachment) }}" target="_blank" class="text-blue-600 hover:text-blue-800 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                ดูไฟล์แนบ
                            </a>
                        </div>
                    @endif

                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('repair_requests.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2">
                            กลับ
                        </a>
                        @if (Auth::user()->id === $repairRequest->user_id || Auth::user()->is_admin)
                            <a href="{{ route('repair_requests.edit', $repairRequest) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded mr-2">
                                แก้ไข
                            </a>
                            <form action="{{ route('repair_requests.destroy', $repairRequest) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('คุณแน่ใจหรือไม่ที่ต้องการลบรายการนี้?')">
                                    ลบ
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>