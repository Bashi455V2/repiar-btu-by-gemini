<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RepairRequest;
use App\Models\Status;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    
    public function index(Request $request)
    {
        // ... (ส่วน Filter วันที่, การ์ดสรุป - เหมือนเดิม) ...
        $selectedRange = $request->input('date_range', 'this_month');
        $dateRanges = [
            'today' => ['start' => now()->startOfDay(), 'end' => now()->endOfDay(), 'label' => 'วันนี้'],
            'this_week' => ['start' => now()->startOfWeek(), 'end' => now()->endOfWeek(), 'label' => 'สัปดาห์นี้'],
            'last_7_days' => ['start' => now()->subDays(6)->startOfDay(), 'end' => now()->endOfDay(), 'label' => '7 วันล่าสุด'],
            'this_month' => ['start' => now()->startOfMonth(), 'end' => now()->endOfMonth(), 'label' => 'เดือนนี้'],
            'last_30_days' => ['start' => now()->subDays(29)->startOfDay(), 'end' => now()->endOfDay(), 'label' => '30 วันล่าสุด'],
            'last_month' => ['start' => now()->subMonthNoOverflow()->startOfMonth(), 'end' => now()->subMonthNoOverflow()->endOfMonth(), 'label' => 'เดือนที่แล้ว'],
            'this_year' => ['start' => now()->startOfYear(), 'end' => now()->endOfYear(), 'label' => 'ปีนี้'],
        ];
        $currentDateRange = $dateRanges[$selectedRange] ?? $dateRanges['this_month'];
        $startDate = $currentDateRange['start'];
        $endDate = $currentDateRange['end'];
        $currentRangeLabel = $currentDateRange['label'];

        $totalRequestsInPeriod = RepairRequest::whereBetween('created_at', [$startDate, $endDate])->count();
        $statusCountsForCards = Status::withCount(['repairRequests' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])->get()->pluck('repair_requests_count', 'name');
        $pendingRequestsInPeriod = $statusCountsForCards->get('รอดำเนินการ', 0);
        $inProgressRequestsInPeriod = $statusCountsForCards->get('กำลังดำเนินการ', 0);
        $completedRequestsInPeriod = $statusCountsForCards->get('ซ่อมเสร็จสิ้น', 0);


        // --- ข้อมูลสำหรับกราฟสถานะ (Pie Chart) ---
        $statusCountsForChart = Status::withCount(['repairRequests' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->having('repair_requests_count', '>', 0)
            ->orderBy('repair_requests_count', 'desc')
            ->get();

        $statusLabels = $statusCountsForChart->pluck('name')->map(fn($name) => __($name));
        $statusData = $statusCountsForChart->pluck('repair_requests_count');

        // VVVVVV ปรับปรุงการสร้างสีสำหรับ Pie Chart VVVVVV
        $predefinedNiceColors = [
            '#3498db', // Peter River (Blue)
            '#2ecc71', // Emerald (Green)
            '#f1c40f', // Sunflower (Yellow)
            '#e74c3c', // Alizarin (Red)
            '#9b59b6', // Amethyst (Purple)
            '#34495e', // Wet Asphalt (Dark Blue/Grey)
            '#1abc9c', // Turquoise
            '#e67e22', // Carrots (Orange)
            '#95a5a6', // Concrete (Light Grey)
            '#d35400', // Pumpkin
            '#27ae60', // Nephritis
            '#2980b9', // Belize Hole
            '#c0392b', // Pomegranate
            '#8e44ad', // Wisteria
        ];
        shuffle($predefinedNiceColors); // สลับลำดับสีเพื่อให้ดูไม่ซ้ำเดิมทุกครั้ง (ถ้าต้องการ)

        $statusColors = $statusCountsForChart->map(function ($status, $index) use ($predefinedNiceColors) {
            // พยายามใช้ color_hex จาก Model ก่อน (ถ้าคุณยังต้องการให้สถานะบางตัวมีสีประจำ)
            if (!empty($status->color_hex)) {
                return $status->color_hex;
            }
            // ถ้าไม่มี color_hex ให้ใช้สีจาก predefined list
            // ถ้าจำนวนสถานะมากกว่าสีที่เตรียมไว้ จะเริ่มวนใช้สีใน list ซ้ำ
            return $predefinedNiceColors[$index % count($predefinedNiceColors)];
        })->toArray();

        $categoryCountsForChart = Category::withCount(['repairRequests' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->having('repair_requests_count', '>', 0)
        ->orderBy('repair_requests_count', 'desc')
        ->get();
        $categoryLabels = $categoryCountsForChart->pluck('name')->map(fn($name) => __($name));
        $categoryData = $categoryCountsForChart->pluck('repair_requests_count');

        $dailyRequests = RepairRequest::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count')
        )
        ->whereBetween('created_at', [$startDate, $endDate])
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

        $trendLabels = [];
        $trendData = [];
        $period = Carbon::parse($startDate);
        while ($period->lte($endDate)) {
            $formattedDate = $period->translatedFormat('j M');
            $trendLabels[] = $formattedDate;
            $requestForDate = $dailyRequests->firstWhere('date', $period->toDateString());
            $trendData[] = $requestForDate ? $requestForDate->count : 0;
            $period->addDay();
        }

        // ดึงข้อมูลการแจ้งซ่อมล่าสุดในช่วงวันที่ที่เลือก
        $recentRequests = RepairRequest::with(['user', 'status', 'location']) // Eager load ที่จำเป็น
                            ->whereBetween('created_at', [$startDate, $endDate]) // Filter ตามช่วงวันที่
                            ->latest()
                            ->take(5) // เอามา 5 รายการล่าสุดในช่วงวันที่ที่เลือก
                            ->get();

        $users = User::latest()->paginate(10, ['*'], 'users_page');

        return view('admin.dashboard', compact(
            'totalRequestsInPeriod', 'pendingRequestsInPeriod', 'inProgressRequestsInPeriod', 'completedRequestsInPeriod',
            'users',
            'statusLabels', 'statusData', 'statusColors',
            'categoryLabels', 'categoryData',
            'trendLabels', 'trendData',
            'selectedRange',    // <--- ตรวจสอบว่ามี
            'dateRanges',       // <--- ตรวจสอบว่ามี
            'currentRangeLabel',// <--- ตรวจสอบว่ามี
            'startDate',        // <--- **ตรวจสอบว่ามี 'startDate' ใน compact()**
            'endDate',          // <--- **และ 'endDate'**
            'recentRequests'
        ));
    }
}