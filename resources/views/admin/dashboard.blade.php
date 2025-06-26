<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                <i class="fas fa-tachometer-alt mr-2 text-sky-600 dark:text-sky-500"></i>{{ __('Dashboard') }}
            </h2>
            <form method="GET" action="{{ route('admin.dashboard') }}" class="mt-3 sm:mt-0 w-full sm:w-auto">
                <div class="flex items-center space-x-2">
                    <label for="date_range" class="text-sm font-medium text-slate-700 dark:text-slate-300 whitespace-nowrap">ช่วงข้อมูล:</label>
                    <select name="date_range" id="date_range" onchange="this.form.submit()"
                            class="block w-full sm:w-auto rounded-md border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-sky-500 dark:focus:border-sky-600 focus:ring-sky-500 dark:focus:ring-sky-600 text-sm shadow-sm">
                        @foreach ($dateRanges as $key => $range)
                            <option value="{{ $key }}" {{ $selectedRange == $key ? 'selected' : '' }}>
                                {{ $range['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="text-sm text-slate-600 dark:text-slate-400 px-1 sm:px-0">
                <i class="far fa-calendar-alt mr-1.5"></i>ข้อมูลสำหรับ: <span class="font-semibold">{{ $currentRangeLabel }} ({{ $startDate->translatedFormat('j M Y') }} - {{ $endDate->translatedFormat('j M Y') }})</span>
            </div>

            {{-- 1. การ์ดสรุปข้อมูล --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- ทั้งหมดในช่วงนี้ --}}
                <div class="bg-white dark:bg-slate-800 shadow-xl rounded-xl p-6 transform hover:scale-105 transition-transform duration-300 border-l-4 border-sky-500">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-slate-500 dark:text-slate-400 uppercase">ทั้งหมด</h3>
                        <i class="fas fa-clipboard-list text-3xl text-sky-500 opacity-80"></i>
                    </div>
                    <p class="text-4xl font-bold text-slate-700 dark:text-slate-100 mt-2">{{ $totalRequestsInPeriod }}</p>
                </div>
                {{-- รอดำเนินการ --}}
                <div class="bg-white dark:bg-slate-800 shadow-xl rounded-xl p-6 transform hover:scale-105 transition-transform duration-300 border-l-4 border-yellow-500">
                     <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-yellow-600 dark:text-yellow-400 uppercase">รอดำเนินการ</h3>
                        <i class="fas fa-hourglass-half text-3xl text-yellow-500 opacity-80"></i>
                    </div>
                    <p class="text-4xl font-bold text-yellow-700 dark:text-yellow-300 mt-2">{{ $pendingRequestsInPeriod }}</p>
                </div>
                {{-- กำลังดำเนินการ --}}
                <div class="bg-white dark:bg-slate-800 shadow-xl rounded-xl p-6 transform hover:scale-105 transition-transform duration-300 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-blue-600 dark:text-blue-400 uppercase">กำลังดำเนินการ</h3>
                        <i class="fas fa-tasks-alt text-3xl text-blue-500 opacity-80"></i>
                    </div>
                    <p class="text-4xl font-bold text-blue-700 dark:text-blue-300 mt-2">{{ $inProgressRequestsInPeriod }}</p>
                </div>
                {{-- ซ่อมเสร็จแล้ว --}}
                <div class="bg-white dark:bg-slate-800 shadow-xl rounded-xl p-6 transform hover:scale-105 transition-transform duration-300 border-l-4 border-green-500">
                     <div class="flex items-center justify-between">
                        <h3 class="text-sm font-medium text-green-600 dark:text-green-400 uppercase">ซ่อมเสร็จแล้ว</h3>
                        <i class="fas fa-check-double text-3xl text-green-500 opacity-80"></i>
                    </div>
                    <p class="text-4xl font-bold text-green-700 dark:text-green-300 mt-2">{{ $completedRequestsInPeriod }}</p>
                </div>
            </div>

            {{-- 2. กราฟเส้นแนวโน้ม และ Pie Chart --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white dark:bg-slate-800 shadow-xl rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-1">แนวโน้มการแจ้งซ่อมรายวัน</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">(ในช่วง: {{ $currentRangeLabel }})</p>
                    <div class="h-80 sm:h-96 relative">
                        <canvas id="trendLineChart"></canvas>
                    </div>
                </div>
                <div class="lg:col-span-1 bg-white dark:bg-slate-800 shadow-xl rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-1">สัดส่วนตามสถานะ</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">(ในช่วง: {{ $currentRangeLabel }})</p>
                    <div class="h-80 sm:h-96 flex items-center justify-center relative">
                         <canvas id="statusPieChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- 3. กราฟแท่งหมวดหมู่ --}}
            <div class="bg-white dark:bg-slate-800 shadow-xl rounded-xl p-6">
                <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100 mb-1">จำนวนตามหมวดหมู่</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">(ในช่วง: {{ $currentRangeLabel }})</p>
                 <div class="h-80 sm:h-96 relative">
                    <canvas id="categoryBarChart"></canvas>
                </div>
            </div>

            {{-- 4. ตารางรายการแจ้งซ่อมล่าสุด (ถ้ามีข้อมูล) --}}
            @if($recentRequests && $recentRequests->count() > 0)
            <div class="mt-8">
                <h3 class="text-xl font-semibold text-slate-900 dark:text-slate-100 mb-4">5 รายการแจ้งซ่อมล่าสุด (ใน <span class="text-sky-600 dark:text-sky-400">{{$currentRangeLabel}}</span>)</h3>
                <div class="bg-white dark:bg-slate-800 shadow-xl sm:rounded-lg overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                            <thead class="bg-slate-50 dark:bg-slate-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">เรื่อง</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">ผู้แจ้ง</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">สถานะ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">วันที่แจ้ง</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                                @foreach ($recentRequests as $requestItem) {{-- เปลี่ยน $request เป็น $requestItem --}}
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-slate-200">{{ $requestItem->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-sky-600 dark:text-sky-400 hover:underline">
                                            <a href="{{ route('repair_requests.show', $requestItem->id) }}">{{ Str::limit($requestItem->title, 35) }}</a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">{{ $requestItem->user->name ?? $requestItem->requester_name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $requestItem->status->color_class ?? 'bg-slate-200 text-slate-800' }}">
                                                {{ $requestItem->status->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">{{ $requestItem->created_at->translatedFormat('j M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- ลบส่วนตารางจัดการผู้ใช้งานออกจาก Dashboard นี้ --}}
            {{-- การจัดการผู้ใช้งานสามารถเข้าถึงได้จากเมนูหลักของ Admin (users.index) --}}

        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script> {{-- อัปเดตเวอร์ชัน Chart.js --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const isDarkMode = document.documentElement.classList.contains('dark');
                const textColor = isDarkMode ? '#cbd5e1' : '#475569'; // slate-300 and slate-600
                const gridColor = isDarkMode ? 'rgba(71, 85, 105, 0.6)' : 'rgba(226, 232, 240, 0.6)'; // slate-600/50 and slate-200/60
                const skyColor = '#0ea5e9'; // sky-500
                const skyColorFill = 'rgba(14, 165, 233, 0.1)';
                const amberColor = '#f59e0b'; // amber-500
                const greenColor = '#10b981'; // green-500
                const redColor = '#ef4444';   // red-500

                Chart.defaults.font.family = 'Figtree, sans-serif';
                Chart.defaults.color = textColor;

                // Pie Chart - Status
                const statusCtx = document.getElementById('statusPieChart');
                const statusData = @json($statusData);
                const statusLabels = @json($statusLabels);
                const statusColors = @json($statusColors); // ควรจะส่ง array ของ hex colors มาจาก controller

                if (statusCtx && statusData && statusData.length > 0 && statusLabels && statusLabels.length === statusData.length) {
                    new Chart(statusCtx, {
                        type: 'doughnut', // เปลี่ยนเป็น Doughnut Chart เพื่อความสวยงาม
                        data: {
                            labels: statusLabels,
                            datasets: [{
                                data: statusData,
                                backgroundColor: statusColors,
                                hoverBackgroundColor: statusColors, // เพิ่ม hover effect
                                hoverOffset: 8,
                                borderWidth: 2, // เพิ่ม border ให้แต่ละชิ้น
                                borderColor: isDarkMode ? '#1e293b' : '#ffffff' // slate-800 or white
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom', labels: { color: textColor, boxWidth: 12, padding: 20, font: { size: 12 } } },
                                title: { display: false },
                                tooltip: {
                                    backgroundColor: isDarkMode ? 'rgba(30, 41, 59, 0.9)' : 'rgba(255,255,255,0.9)',
                                    titleColor: isDarkMode ? '#f1f5f9' : '#1e293b',
                                    bodyColor: isDarkMode ? '#e2e8f0' : '#334155',
                                    borderColor: isDarkMode ? '#475569' : '#cbd5e1',
                                    borderWidth: 1,
                                    boxPadding: 4,
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed !== null) {
                                                label += context.formattedValue + ' รายการ';
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            cutout: '60%' // สำหรับ Doughnut chart
                        }
                    });
                } else if (statusCtx) {
                    const ctx = statusCtx.getContext('2d'); ctx.textAlign = 'center'; ctx.textBaseline = 'middle'; ctx.fillStyle = textColor; ctx.font = '14px Figtree, sans-serif';
                    ctx.fillText("ไม่มีข้อมูลสถานะในช่วงวันที่เลือก", statusCtx.width / 2, statusCtx.height / 2);
                }

                // Bar Chart - Category
                const categoryCtx = document.getElementById('categoryBarChart');
                const categoryData = @json($categoryData);
                const categoryLabels = @json($categoryLabels);
                if (categoryCtx && categoryData && categoryData.length > 0 && categoryLabels && categoryLabels.length === categoryData.length) {
                    new Chart(categoryCtx, {
                        type: 'bar',
                        data: {
                            labels: categoryLabels,
                            datasets: [{
                                label: 'จำนวนรายการ', data: categoryData,
                                backgroundColor: skyColorFill, borderColor: skyColor,
                                borderWidth: 1, borderRadius: 6, barPercentage: 0.5, categoryPercentage: 0.7
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true, ticks: { color: textColor, stepSize: Math.max(1, Math.ceil(Math.max(...categoryData) / 5)) }, grid: { color: gridColor, drawBorder: false } },
                                x: { ticks: { color: textColor, font: {size: 11} }, grid: { display: false } }
                            },
                            plugins: { legend: { display: false }, title: { display: false },
                                tooltip: { backgroundColor: isDarkMode ? 'rgba(30, 41, 59, 0.9)' : 'rgba(255,255,255,0.9)', titleColor: isDarkMode ? '#f1f5f9' : '#1e293b', bodyColor: isDarkMode ? '#e2e8f0' : '#334155', borderColor: isDarkMode ? '#475569' : '#cbd5e1', borderWidth: 1, boxPadding: 4, }
                            }
                        }
                    });
                } else if (categoryCtx) {
                    const ctx = categoryCtx.getContext('2d'); ctx.textAlign = 'center'; ctx.textBaseline = 'middle'; ctx.fillStyle = textColor; ctx.font = '14px Figtree, sans-serif';
                    ctx.fillText("ไม่มีข้อมูลหมวดหมู่ในช่วงวันที่เลือก", categoryCtx.width / 2, categoryCtx.height / 2);
                }

                // Line Chart - Daily Trends
                const trendCtx = document.getElementById('trendLineChart');
                const trendData = @json($trendData);
                const trendLabels = @json($trendLabels);
                if (trendCtx && trendData && trendData.length > 0 && trendLabels && trendLabels.length === trendData.length) {
                    new Chart(trendCtx, {
                        type: 'line',
                        data: {
                            labels: trendLabels,
                            datasets: [{
                                label: 'จำนวนการแจ้งซ่อม', data: trendData,
                                borderColor: skyColor, backgroundColor: skyColorFill,
                                tension: 0.4, fill: true,
                                pointBackgroundColor: skyColor, pointBorderColor: isDarkMode ? '#1e293b' : '#ffffff', pointRadius: 4, pointHoverRadius: 6, pointBorderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true, maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true, ticks: { color: textColor, stepSize: Math.max(1, Math.ceil(Math.max(...trendData) / 5)) }, grid: { color: gridColor, drawBorder: false } },
                                x: { ticks: { color: textColor, font: {size: 11} }, grid: { display: false } }
                            },
                            plugins: { legend: { display: true, labels: { color: textColor, boxWidth:12, font: {size:11} } }, title: { display: false },
                                tooltip: { mode: 'index', intersect: false, backgroundColor: isDarkMode ? 'rgba(30, 41, 59, 0.9)' : 'rgba(255,255,255,0.9)', titleColor: isDarkMode ? '#f1f5f9' : '#1e293b', bodyColor: isDarkMode ? '#e2e8f0' : '#334155', borderColor: isDarkMode ? '#475569' : '#cbd5e1', borderWidth: 1, boxPadding: 4, }
                            },
                            interaction: { intersect: false, mode: 'index' },
                            elements: { line: { borderWidth: 2.5 }}
                        }
                    });
                } else if (trendCtx) {
                    const ctx = trendCtx.getContext('2d'); ctx.textAlign = 'center'; ctx.textBaseline = 'middle'; ctx.fillStyle = textColor; ctx.font = '14px Figtree, sans-serif';
                    ctx.fillText("ไม่มีข้อมูลแนวโน้มในช่วงวันที่เลือก", trendCtx.width / 2, trendCtx.height / 2);
                }
            });
        </script>
    @endpush
</x-app-layout>