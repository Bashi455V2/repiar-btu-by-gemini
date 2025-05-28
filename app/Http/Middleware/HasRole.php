<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  บทบาทที่จำเป็นในการเข้าถึง Route (เช่น 'admin', 'technician')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
        if (!Auth::check()) {
            return redirect()->route('login'); // ถ้าไม่ได้ล็อกอิน ให้ Redirect ไปหน้า Login
        }

        $user = Auth::user();

        // ตรวจสอบว่าผู้ใช้มีบทบาทใดบทบาทหนึ่งในรายการที่กำหนดหรือไม่
        foreach ($roles as $role) {
            if ($role === 'admin' && $user->is_admin) {
                return $next($request); // ถ้าเป็น Admin และมี is_admin = true
            }
            if ($role === 'technician' && $user->is_technician) {
                return $next($request); // ถ้าเป็น Technician และมี is_technician = true
            }
            // สามารถเพิ่มบทบาทอื่นๆ ที่นี่ได้ในอนาคต
        }

        // ถ้าผู้ใช้ไม่มีบทบาทที่ได้รับอนุญาต ให้แสดงหน้า 403 Forbidden หรือ Redirect
        abort(403, 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
    }
}