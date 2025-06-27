{{-- Activity Log Section (ฉบับปรับปรุง UX) --}}
{{-- ไฟล์นี้ถูกเรียกโดย @include จาก show.blade.php และสามารถเข้าถึงตัวแปร $activities ได้ --}}

<div x-data="{ showAllLogs: false }" class="mt-8 pt-6 border-t border-slate-200 dark:border-slate-700">
    <div class="flex justify-between items-center mb-6">
         <h4 class="text-xl font-semibold text-sky-700 dark:text-sky-500 flex items-center">
            <i class="fas fa-history fa-fw mr-3"></i>ประวัติการดำเนินการ
        </h4>
         @if ($activities->count() > 1)
            <button @click="showAllLogs = !showAllLogs; $dispatch('toggle-all-logs', { show: showAllLogs })" class="text-xs font-semibold text-sky-600 dark:text-sky-400 hover:underline focus:outline-none">
                <span x-show="!showAllLogs">ดูทั้งหมด ({{ $activities->count() }})</span>
                <span x-show="showAllLogs" style="display: none;">ซ่อนประวัติ</span>
            </button>
        @endif
    </div>

    @if ($activities->isEmpty())
        <div class="text-center py-8 px-4">
            <div class="inline-flex items-center justify-center p-4 bg-slate-100 dark:bg-slate-700 rounded-full mb-3"><i class="fas fa-folder-open fa-2x text-slate-400 dark:text-slate-500"></i></div>
            <p class="text-base text-slate-500 dark:text-slate-400">ยังไม่มีประวัติการดำเนินการสำหรับรายการนี้</p>
        </div>
    @else
        <div class="flow-root">
            <ul role="list" class="-mb-8">
                @foreach ($activities as $activity)
                    {{-- Alpine.js: แสดงรายการแรกเสมอ, รายการอื่นแสดงเมื่อกด showAllLogs --}}
                    <li x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }"
                        x-show="open"
                        @toggle-all-logs.window="open = $event.detail.show ? true : {{ $loop->first ? 'true' : 'false' }}"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">

                        <div class="relative pb-10">
                            @if (!$loop->last)
                                <span class="absolute left-5 top-5 -ml-px h-full w-0.5 bg-slate-300 dark:bg-slate-700" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex items-start space-x-4">
                                {{-- Icon --}}
                                <div class="relative mt-0.5">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full {{ match ($activity->event) {'created' => 'bg-green-500','updated' => 'bg-sky-500','deleted' => 'bg-red-500',default => 'bg-slate-500'} }} ring-4 ring-white dark:ring-slate-800/50 shadow">
                                        @if ($activity->event === 'created') <i class="fas fa-plus text-white text-sm"></i>
                                        @elseif ($activity->event === 'updated') <i class="fas fa-pencil-alt text-white text-sm"></i>
                                        @elseif ($activity->event === 'deleted') <i class="fas fa-trash-alt text-white text-sm"></i>
                                        @else <i class="fas fa-info-circle text-white text-sm"></i>
                                        @endif
                                    </div>
                                </div>
                                {{-- Log Content --}}
                                <div class="min-w-0 flex-1 pt-0.5" x-data="{ detailsOpen: false }">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-medium text-slate-700 dark:text-slate-200">
                                                {{ $activity->description }}
                                                @if ($activity->causer) <span class="text-slate-500 dark:text-slate-400">โดย</span> {{ $activity->causer->name }} @endif
                                            </p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $activity->created_at->translatedFormat('j F Y เวลา H:i น.') }}</p>
                                        </div>
                                        @if ($activity->event === 'updated' && !empty($activity->properties['attributes']) && !empty($activity->properties['old']) && count(array_diff_assoc($activity->properties->get('attributes', []), $activity->properties->get('old', []))) > 0)
                                            <button @click="detailsOpen = !detailsOpen" class="text-xs font-semibold text-sky-600 dark:text-sky-400 hover:underline focus:outline-none ml-4 flex-shrink-0">
                                                <span x-show="!detailsOpen">รายละเอียด</span>
                                                <span x-show="detailsOpen" style="display: none;">ซ่อน</span>
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Collapsible Details --}}
                                    <div x-show="detailsOpen" x-collapse style="display: none;">
                                        @if ($activity->event === 'updated' && $activity->properties->has(['old', 'attributes']))
                                            @php
                                                $changes = $activity->properties->get('attributes');
                                                $old = $activity->properties->get('old');
                                            @endphp
                                            @if (is_array($changes) && is_array($old) && count(array_diff_assoc($changes, $old)) > 0)
                                                <div class="mt-3 text-xs text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700/60 p-3 rounded-md border dark:border-slate-600">
                                                    <p class="font-semibold mb-1 text-slate-700 dark:text-slate-200">รายละเอียดการเปลี่ยนแปลง:</p>
                                                    <ul class="list-none pl-0 space-y-1">
                                                        @foreach ($changes as $key => $newValueRaw)
                                                            @if (array_key_exists($key, $old) && ($old[$key] ?? null) != ($newValueRaw ?? null))
                                                                @php
                                                                    $fieldTranslations = ['title' => 'เรื่อง', 'description' => 'รายละเอียด', 'location_id' => 'สถานที่', 'category_id' => 'หมวดหมู่', 'status_id' => 'สถานะ', 'assigned_to_user_id' => 'ผู้รับผิดชอบ', 'remarks_by_technician' => 'หมายเหตุจากช่าง', 'completed_at' => 'วันที่เสร็จ', 'requester_phone' => 'เบอร์โทรผู้แจ้ง', 'image_path' => 'รูปภาพ', 'after_image_path' => 'รูปภาพหลังซ่อม'];
                                                                    $translatedKey = $fieldTranslations[$key] ?? Illuminate\Support\Str::headline(str_replace('_id', '', $key));
                                                                    $oldValueRaw = $old[$key] ?? null;
                                                                    $oldValueDisplay = 'N/A'; $newValueDisplay = 'N/A';
                                                                    if ($key === 'completed_at') { if ($oldValueRaw && strtolower((string)$oldValueRaw) !== 'n/a') { try { $oldValueDisplay = \Illuminate\Support\Carbon::parse((string)$oldValueRaw)->translatedFormat('j M Y, H:i'); } catch (\Exception $e) { $oldValueDisplay = (string)$oldValueRaw; }} if ($newValueRaw && strtolower((string)$newValueRaw) !== 'n/a') { try { $newValueDisplay = \Illuminate\Support\Carbon::parse((string)$newValueRaw)->translatedFormat('j M Y, H:i'); } catch (\Exception $e) { $newValueDisplay = (string)$newValueRaw; }} }
                                                                    elseif (in_array($key, ['status_id', 'assigned_to_user_id', 'location_id', 'category_id'])) { switch ($key) { case 'status_id': $oldValueDisplay = optional(\App\Models\Status::find($oldValueRaw))->name ?? ($oldValueRaw ?? 'N/A'); $newValueDisplay = optional(\App\Models\Status::find($newValueRaw))->name ?? ($newValueRaw ?? 'N/A'); break; case 'assigned_to_user_id': $oldValueDisplay = optional(\App\Models\User::find($oldValueRaw))->name ?? (is_null($oldValueRaw) ? 'ไม่มีผู้รับผิดชอบ' : ($oldValueRaw ?? 'N/A')); $newValueDisplay = optional(\App\Models\User::find($newValueRaw))->name ?? (is_null($newValueRaw) ? 'ไม่มีผู้รับผิดชอบ' : ($newValueRaw ?? 'N/A')); break; case 'location_id': $oldValueDisplay = optional(\App\Models\Location::find($oldValueRaw))->name ?? ($oldValueRaw ?? 'N/A'); $newValueDisplay = optional(\App\Models\Location::find($newValueRaw))->name ?? ($newValueRaw ?? 'N/A'); break; case 'category_id': $oldValueDisplay = optional(\App\Models\Category::find($oldValueRaw))->name ?? ($oldValueRaw ?? 'N/A'); $newValueDisplay = optional(\App\Models\Category::find($newValueRaw))->name ?? ($newValueRaw ?? 'N/A'); break; } }
                                                                    elseif (in_array($key, ['image_path', 'after_image_path'])) { $oldValueDisplay = $oldValueRaw ? Illuminate\Support\Str::afterLast($oldValueRaw, '/') : 'ไม่มีรูป'; $newValueDisplay = $newValueRaw ? Illuminate\Support\Str::afterLast($newValueRaw, '/') : 'ไม่มีรูป'; }
                                                                    else { $oldValueDisplay = (string)($oldValueRaw ?? 'N/A'); $newValueDisplay = (string)($newValueRaw ?? 'N/A'); }
                                                                @endphp
                                                                <li><span class="font-medium">{{ $translatedKey }}:</span> <del class="text-red-500 dark:text-red-400 px-1">{{ $oldValueDisplay }}</del> &rarr; <ins class="text-green-600 dark:text-green-400 px-1 no-underline">{{ $newValueDisplay }}</ins></li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>
