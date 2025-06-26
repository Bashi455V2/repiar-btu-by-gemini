<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight mb-3 sm:mb-0 truncate max-w-md xl:max-w-lg">
                <span class="text-slate-500 dark:text-slate-400">ID #{{ $repairRequest->id }}:</span>
                <span class="ml-1">{{ Str::limit($repairRequest->title, 45) }}</span>
            </h2>
            <div class="flex items-center space-x-2">
                {{-- ปุ่มย้อนกลับ --}}
                @php
                    $backToListRoute = route('repair_requests.index');
                    if (Auth::check()) {
                        if (Auth::user()->is_admin) {
                            $backToListRoute = route('admin.manage');
                        } elseif (Auth::user()->is_technician) {
                            $backToListRoute = route('repair_requests.index');
                        }
                    }
                @endphp
                <a href="{{ $backToListRoute }}"
                   class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-md font-semibold text-xs text-slate-700 dark:text-slate-200 uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('ย้อนกลับ') }}
                </a>

                @can('update', $repairRequest)
                <a href="{{ route('repair_requests.edit', $repairRequest) }}"
                   class="inline-flex items-center px-4 py-2 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600 active:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition ease-in-out duration-150">
                    <i class="fas fa-pencil-alt mr-2"></i>{{ __('แก้ไข') }}
                </a>
                @endcan

                @can('delete', $repairRequest)
                <form action="{{ route('repair_requests.destroy', $repairRequest->id) }}" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบรายการแจ้งซ่อมนี้? การกระทำนี้ไม่สามารถย้อนกลับได้');" class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition ease-in-out duration-150">
                        <i class="fas fa-trash-alt mr-2"></i>{{ __('ลบ') }}
                    </button>
                </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-800 shadow-2xl sm:rounded-xl overflow-hidden">
                <div class="p-6 sm:p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-7 gap-6 lg:gap-8">
                        {{-- Main Content Area --}}
                        <div class="lg:col-span-5 space-y-6">
                            <div>
                                <h1 class="text-3xl sm:text-4xl font-bold text-slate-900 dark:text-slate-100 leading-tight break-words">
                                    {{ $repairRequest->title }}
                                </h1>
                                <div class="mt-3 flex flex-col sm:flex-row sm:flex-wrap sm:items-center text-xs sm:text-sm text-slate-500 dark:text-slate-400 gap-x-4 gap-y-1">
                                    <span class="inline-flex items-center"><i class="far fa-clock fa-fw mr-1.5 text-slate-400"></i>แจ้งเมื่อ: <span class="ml-1 font-medium text-slate-700 dark:text-slate-300">{{ $repairRequest->created_at->translatedFormat('j F Y เวลา H:i น.') }}</span></span>
                                    <span class="inline-flex items-center"><i class="far fa-user fa-fw mr-1.5 text-slate-400"></i>โดย: <span class="ml-1 font-medium text-slate-700 dark:text-slate-300">{{ $repairRequest->user->name ?? ($repairRequest->requester_name ?? 'N/A') }}</span></span>
                                    @if($repairRequest->requester_phone)
                                        <span class="inline-flex items-center"><i class="fas fa-phone fa-fw mr-1.5 text-slate-400"></i>โทร: <span class="ml-1 font-medium text-slate-700 dark:text-slate-300">{{ $repairRequest->requester_phone }}</span></span>
                                    @endif
                                </div>
                            </div>

                            <hr class="dark:border-slate-700 my-6">

                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-lg font-semibold text-sky-700 dark:text-sky-500 mb-2 flex items-center">
                                        <i class="fas fa-align-left fa-fw mr-2"></i>รายละเอียดปัญหา
                                    </h4>
                                    <div class="prose prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-300 text-base leading-relaxed ml-1 sm:ml-0 pl-0 sm:pl-[calc(1rem+0.5rem)]">
                                        {!! nl2br(e($repairRequest->description)) !!}
                                    </div>
                                </div>

                                @if ($repairRequest->remarks_by_technician)
                                    <div class="pt-2">
                                        <h4 class="text-lg font-semibold text-sky-700 dark:text-sky-500 mb-2 flex items-center">
                                            <i class="fas fa-comment-dots fa-fw mr-2"></i>หมายเหตุจากช่าง
                                        </h4>
                                        <div class="prose prose-sm prose-slate dark:prose-invert max-w-none text-slate-500 dark:text-slate-400 italic bg-slate-50 dark:bg-slate-700/40 p-4 rounded-md ml-1 sm:ml-0 pl-4 sm:pl-[calc(1rem+0.5rem)] border-l-4 border-amber-400 dark:border-amber-500">
                                            {!! nl2br(e($repairRequest->remarks_by_technician)) !!}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if ($repairRequest->image_path)
                                <div class="mt-8">
                                    <h4 class="text-lg font-semibold text-sky-700 dark:text-sky-500 mb-2 flex items-center">
                                        <i class="far fa-image fa-fw mr-2"></i>รูปภาพประกอบ
                                    </h4>
                                    <a href="{{ Storage::url($repairRequest->image_path) }}" data-fancybox="gallery" class="block group ml-1 sm:ml-0 pl-0 sm:pl-[calc(1rem+0.5rem)]">
                                        <img src="{{ Storage::url($repairRequest->image_path) }}" alt="Repair Image" class="rounded-lg shadow-lg w-full max-w-md lg:max-w-lg object-cover transition-transform duration-300 group-hover:scale-105 border dark:border-slate-700">
                                    </a>
                                </div>
                            @endif
                        </div>

                        {{-- Sidebar Information --}}
                        <div class="lg:col-span-2 space-y-6">
                            <div class="bg-slate-100 dark:bg-slate-700/70 p-6 rounded-xl shadow-lg border border-slate-200 dark:border-slate-600">
                                <h4 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4 pb-3 border-b border-slate-300 dark:border-slate-600 flex items-center">
                                    <i class="fas fa-thumbtack fa-fw mr-2 text-sky-600 dark:text-sky-500"></i>ข้อมูลสรุป
                                </h4>
                                <dl class="space-y-5">
                                    {{-- Status, Category, Location, Assigned Technician, Completed Date --}}
                                    {{-- ... (เหมือนเดิม) ... --}}
                                     <div>
                                        <dt class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center"><i class="fas fa-tag fa-fw mr-2"></i>สถานะ:</dt>
                                        <dd class="mt-1">
                                            <span class="px-3 py-1.5 inline-flex text-base leading-5 font-bold rounded-md {{ $repairRequest->status->color_class ?? 'bg-slate-200 text-slate-800 dark:bg-slate-600 dark:text-slate-100' }}">
                                                {{ $repairRequest->status->name ?? 'N/A' }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center"><i class="fas fa-folder-open fa-fw mr-2"></i>หมวดหมู่:</dt>
                                        <dd class="mt-1 text-md text-slate-700 dark:text-slate-200 font-medium">{{ $repairRequest->category->name ?? 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center"><i class="fas fa-map-marker-alt fa-fw mr-2"></i>สถานที่:</dt>
                                        <dd class="mt-1 text-md text-slate-700 dark:text-slate-200 font-medium">{{ $repairRequest->location->name ?? 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center"><i class="fas fa-user-shield fa-fw mr-2"></i>ช่างผู้รับผิดชอบ:</dt>
                                        <dd class="mt-1 text-md text-slate-700 dark:text-slate-200 font-medium">{{ $repairRequest->assignedTo->name ?? 'ยังไม่ได้มอบหมาย' }}</dd>
                                    </div>
                                    @if($repairRequest->completed_at)
                                    <div>
                                        <dt class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center"><i class="far fa-calendar-check fa-fw mr-2"></i>วันที่ซ่อมเสร็จ:</dt>
                                        <dd class="mt-1 text-md text-slate-700 dark:text-slate-200 font-medium">{{ $repairRequest->completed_at->translatedFormat('j F Y เวลา H:i น.') }}</dd>
                                    </div>
                                    @endif
                                </dl>
                            </div>

                            @can('update', $repairRequest)
                                @if(Auth::user()->is_admin || (Auth::user()->is_technician && $repairRequest->assigned_to_user_id === Auth::id()))
                                <div class="bg-slate-100 dark:bg-slate-700/70 p-6 rounded-xl shadow-lg border border-slate-200 dark:border-slate-600">
                                    <h4 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-4 pb-3 border-b border-slate-300 dark:border-slate-600 flex items-center">
                                        <i class="fas fa-edit fa-fw mr-2 text-sky-600 dark:text-sky-500"></i>อัปเดต (ด่วน)
                                    </h4>
                                    <form method="POST" action="{{ route('repair_requests.update_status_assign', $repairRequest->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="redirect_to_manage" value="0">
                                        <div class="space-y-4">
                                            <div>
                                                <label for="status_id_quick_sidebar" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">สถานะ:</label>
                                                <select name="status_id" id="status_id_quick_sidebar" class="block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm dark:bg-slate-700 dark:text-slate-200 dark:focus:bg-slate-600 py-2 px-3">
                                                    @foreach($statuses as $status)
                                                        <option value="{{ $status->id }}" {{ (old('status_id', $repairRequest->status_id) == $status->id) ? 'selected' : '' }}>
                                                            {{ $status->name }}
                                                        </option>
                                                    @endforeach {{-- ปิด foreach ของ statuses --}}
                                                </select>
                                            </div>

                                            @if(Auth::user()->is_admin)
                                            <div>
                                                <label for="assigned_to_user_id_quick_sidebar" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">มอบหมายให้ช่าง:</label>
                                                <select name="assigned_to_user_id" id="assigned_to_user_id_quick_sidebar" class="block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm dark:bg-slate-700 dark:text-slate-200 dark:focus:bg-slate-600 py-2 px-3">
                                                    <option value="">-- ยังไม่มอบหมาย --</option>
                                                    @foreach($technicians as $technician)
                                                        <option value="{{ $technician->id }}" {{ (old('assigned_to_user_id', $repairRequest->assigned_to_user_id) == $technician->id) ? 'selected' : '' }}>
                                                            {{ $technician->name }}
                                                        </option>
                                                    @endforeach {{-- ปิด foreach ของ technicians --}}
                                                </select>
                                            </div>
                                            @endif {{-- ปิด if ของ Admin สำหรับ assigned_to_user_id --}}

                                            <div>
                                                <label for="remarks_by_technician_quick_sidebar" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                                                    {{ Auth::user()->is_technician ? 'หมายเหตุจากช่าง:' : 'หมายเหตุ (Admin):' }}
                                                </label>
                                                <textarea name="remarks_by_technician" id="remarks_by_technician_quick_sidebar" class="block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm dark:bg-slate-700 dark:text-slate-200 dark:focus:bg-slate-600 py-2 px-3" rows="3">{{ old('remarks_by_technician', $repairRequest->remarks_by_technician) }}</textarea>
                                            </div>

                                            <button type="submit" class="w-full whitespace-nowrap inline-flex items-center justify-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 dark:focus:ring-offset-slate-800">
                                                <i class="fas fa-save mr-2"></i>บันทึก
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                @endif {{-- ปิด if ของ Admin หรือ Technician ที่ assigned --}}
                            @endcan {{-- ปิด can update --}}
                        </div>
                    </div>

                    {{-- Activity Log Section --}}
                    <div class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
                        <h4 class="text-xl font-semibold text-sky-700 dark:text-sky-500 mb-6 flex items-center">
                            <i class="fas fa-history fa-fw mr-3"></i>ประวัติการดำเนินการ
                        </h4>
                        @if ($activities->isEmpty())
                            <div class="text-center py-8 px-4">
                                {{-- ... No activities message ... --}}
                            </div>
                        @else
                            <div class="flow-root">
                                <ul role="list" class="-mb-8">
                                    @foreach ($activities as $activity)
                                        <li>
                                            <div class="relative pb-10">
                                                @if (!$loop->last)
                                                    <span class="absolute left-5 top-5 -ml-px h-full w-0.5 bg-slate-300 dark:bg-slate-700" aria-hidden="true"></span>
                                                @endif
                                                <div class="relative flex items-start space-x-4">
                                                    <div class="relative mt-0.5">
                                                        <div class="flex h-10 w-10 items-center justify-center rounded-full {{
                                                            match ($activity->event) {
                                                                'created' => 'bg-green-500',
                                                                'updated' => 'bg-sky-500',
                                                                'deleted' => 'bg-red-500',
                                                                default => 'bg-slate-500',
                                                            }
                                                        }} ring-4 ring-white dark:ring-slate-800/50 shadow">
                                                            @if ($activity->event === 'created') <i class="fas fa-plus text-white text-sm"></i>
                                                            @elseif ($activity->event === 'updated') <i class="fas fa-pencil-alt text-white text-sm"></i>
                                                            @elseif ($activity->event === 'deleted') <i class="fas fa-trash-alt text-white text-sm"></i>
                                                            @else <i class="fas fa-info-circle text-white text-sm"></i>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="min-w-0 flex-1 pt-0.5">
                                                        <p class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                                            {{ $activity->description }}
                                                            @if ($activity->causer)
                                                                <span class="text-slate-500 dark:text-slate-400">โดย</span> {{ $activity->causer->name }}
                                                            @endif
                                                        </p>
                                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                                            {{ $activity->created_at->translatedFormat('j F Y เวลา H:i น.') }} ({{ $activity->created_at->diffForHumans() }})
                                                        </p>
                                                        @php
                                                            $properties = $activity->properties;
                                                            $changes = $properties->get('attributes');
                                                            $old = $properties->get('old');
                                                        @endphp
                                                        {{-- ส่วนแสดงรายละเอียดการเปลี่ยนแปลง --}}
                                                        @if ($activity->event === 'updated' && !empty($changes) && !empty($old) && count(array_diff_assoc((array)$changes, (array)$old)) > 0)
                                                            <div class="mt-2 text-xs text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700/60 p-3 rounded-md border dark:border-slate-600">
                                                                <p class="font-semibold mb-1 text-slate-700 dark:text-slate-200">รายละเอียดการเปลี่ยนแปลง:</p>
                                                                <ul class="list-none pl-0 space-y-1">
                                                                    @foreach ($changes as $key => $newValueRaw)
                                                                        @if (array_key_exists($key, $old) && ($old[$key] ?? null) != ($newValueRaw ?? null) && !is_array($old[$key] ?? null) && !is_array($newValueRaw ?? null))
                                                                            @php
                                                                                $fieldTranslations = ['title' => 'เรื่อง', 'description' => 'รายละเอียด', 'location_id' => 'สถานที่', 'category_id' => 'หมวดหมู่', 'status_id' => 'สถานะ', 'assigned_to_user_id' => 'ผู้รับผิดชอบ', 'remarks_by_technician' => 'หมายเหตุจากช่าง', 'completed_at' => 'วันที่เสร็จ', 'requester_phone' => 'เบอร์โทรผู้แจ้ง', 'image_path' => 'รูปภาพ'];
                                                                                $translatedKey = $fieldTranslations[$key] ?? Illuminate\Support\Str::headline(str_replace('_id', '', $key));

                                                                                $oldValueRaw = $old[$key] ?? null;

                                                                                $oldValueDisplay = 'N/A';
                                                                                $newValueDisplay = 'N/A';

                                                                                // Logic การแปลงค่าเพื่อแสดงผล (เหมือนเดิมที่ปรับปรุงแล้ว)
                                                                                if ($key === 'completed_at') {
                                                                                    if ($oldValueRaw && strtolower((string)$oldValueRaw) !== 'n/a' && (string)$oldValueRaw !== '') { try { $oldValueDisplay = \Illuminate\Support\Carbon::parse((string)$oldValueRaw)->locale(config('app.locale', 'th'))->translatedFormat('j M Y, H:i'); } catch (\Exception $e) { $oldValueDisplay = (string)$oldValueRaw; }}
                                                                                    if ($newValueRaw && strtolower((string)$newValueRaw) !== 'n/a' && (string)$newValueRaw !== '') { try { $newValueDisplay = \Illuminate\Support\Carbon::parse((string)$newValueRaw)->locale(config('app.locale', 'th'))->translatedFormat('j M Y, H:i'); } catch (\Exception $e) { $newValueDisplay = (string)$newValueRaw; }}
                                                                                } elseif (in_array($key, ['status_id', 'assigned_to_user_id', 'location_id', 'category_id'])) {
                                                                                    switch ($key) {
                                                                                        case 'status_id': $oldValueDisplay = optional(\App\Models\Status::find($oldValueRaw))->name ?? ($oldValueRaw ?? 'N/A'); $newValueDisplay = optional(\App\Models\Status::find($newValueRaw))->name ?? ($newValueRaw ?? 'N/A'); break;
                                                                                        case 'assigned_to_user_id': $oldValueDisplay = optional(\App\Models\User::find($oldValueRaw))->name ?? (is_null($oldValueRaw) ? 'ไม่มีผู้รับผิดชอบ' : ($oldValueRaw ?? 'N/A')); $newValueDisplay = optional(\App\Models\User::find($newValueRaw))->name ?? (is_null($newValueRaw) ? 'ไม่มีผู้รับผิดชอบ' : ($newValueRaw ?? 'N/A')); break;
                                                                                        case 'location_id': $oldValueDisplay = optional(\App\Models\Location::find($oldValueRaw))->name ?? ($oldValueRaw ?? 'N/A'); $newValueDisplay = optional(\App\Models\Location::find($newValueRaw))->name ?? ($newValueRaw ?? 'N/A'); break;
                                                                                        case 'category_id': $oldValueDisplay = optional(\App\Models\Category::find($oldValueRaw))->name ?? ($oldValueRaw ?? 'N/A'); $newValueDisplay = optional(\App\Models\Category::find($newValueRaw))->name ?? ($newValueRaw ?? 'N/A'); break;
                                                                                    }
                                                                                } elseif ($key === 'image_path') {
                                                                                    $oldValueDisplay = $oldValueRaw ? Illuminate\Support\Str::afterLast($oldValueRaw, '/') : 'ไม่มีรูป';
                                                                                    $newValueDisplay = $newValueRaw ? Illuminate\Support\Str::afterLast($newValueRaw, '/') : 'ไม่มีรูป';
                                                                                } else {
                                                                                    $oldValueDisplay = (string)($oldValueRaw ?? 'N/A');
                                                                                    $newValueDisplay = (string)($newValueRaw ?? 'N/A');
                                                                                }
                                                                            @endphp
                                                                            <li><span class="font-medium">{{ $translatedKey }}:</span> <del class="text-red-500 dark:text-red-400 px-1">{{ $oldValueDisplay }}</del> &rarr; <ins class="text-green-600 dark:text-green-400 px-1 no-underline">{{ $newValueDisplay }}</ins></li>
                                                                        @elseif (!array_key_exists($key, $old ?? []) && !is_null($newValueRaw) && !is_array($newValueRaw)) {{-- New field added --}}
                                                                            @php /* ... (Logic แสดงค่าใหม่ เหมือนเดิม) ... */ @endphp
                                                                            {{-- ส่วนแสดงค่าใหม่ (เหมือนเดิมที่ปรับปรุงแล้ว) --}}
                                                                            @php
                                                                                $translatedKey = $fieldTranslations[$key] ?? Illuminate\Support\Str::headline(str_replace('_id', '', $key));
                                                                                $currentValueDisplay = 'N/A';
                                                                                if ($key === 'completed_at' && $newValueRaw && strtolower((string)$newValueRaw) !== 'n/a' && (string)$newValueRaw !== '') { try { $currentValueDisplay = \Illuminate\Support\Carbon::parse((string)$newValueRaw)->locale(config('app.locale', 'th'))->translatedFormat('j M Y, H:i'); } catch (\Exception $e) { $currentValueDisplay = (string)$newValueRaw; }}
                                                                                elseif ($key === 'status_id') { $currentValueDisplay = optional(\App\Models\Status::find($newValueRaw))->name ?? ($newValueRaw ?? 'N/A'); }
                                                                                elseif ($key === 'assigned_to_user_id') { $currentValueDisplay = optional(\App\Models\User::find($newValueRaw))->name ?? (is_null($newValueRaw) ? 'ไม่มีผู้รับผิดชอบ' : ($newValueRaw ?? 'N/A'));}
                                                                                elseif ($key === 'location_id') { $currentValueDisplay = optional(\App\Models\Location::find($newValueRaw))->name ?? ($newValueRaw ?? 'N/A'); }
                                                                                elseif ($key === 'category_id') { $currentValueDisplay = optional(\App\Models\Category::find($newValueRaw))->name ?? ($newValueRaw ?? 'N/A'); }
                                                                                elseif ($key === 'image_path') { $currentValueDisplay = $newValueRaw ? Illuminate\Support\Str::afterLast($newValueRaw, '/') : 'ไม่มีรูป'; }
                                                                                else { $currentValueDisplay = (string)($newValueRaw ?? 'N/A'); }
                                                                            @endphp
                                                                            <li><span class="font-medium">{{ $translatedKey }}:</span> ตั้งค่าเป็น "<span class="italic text-green-600 dark:text-green-400">{{ $currentValueDisplay }}</span>"</li>
                                                                        @endif {{-- ปิด if/elseif ของการแสดง changes --}}
                                                                    @endforeach {{-- ปิด foreach ของ $changes --}}
                                                                </ul>
                                                            </div>
                                                        @endif {{-- ปิด if ของ $activity->event === 'updated' --}}
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach {{-- ปิด foreach ของ $activities --}}
                                </ul>
                            </div>
                            @if($activities instanceof \Illuminate\Pagination\LengthAwarePaginator && $activities->hasPages())
                                <div class="mt-6">
                                    {{ $activities->appends(request()->query())->links() }}
                                </div>
                            @endif {{-- ปิด if ของ $activities->hasPages() --}}
                        @endif {{-- ปิด if ของ $activities->isEmpty() --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>