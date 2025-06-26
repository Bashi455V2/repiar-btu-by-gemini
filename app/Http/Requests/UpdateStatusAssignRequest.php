<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth; // ตรวจสอบว่ามีการ use Auth

class UpdateStatusAssignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // การ authorize หลักว่าใครสามารถเห็น "ฟอร์ม" นี้ได้ ถูกจัดการใน Blade view ด้วย @can แล้ว
        // และใน Controller method updateStatusAndAssign ก็ควรจะมีการ authorize('update', $repairRequest) อีกครั้ง (ถ้าจำเป็น)
        // ส่วนนี้เป็นการเช็คเบื้องต้นว่าผู้ใช้ login อยู่หรือไม่
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'status_id' => 'required|exists:statuses,id',
            'remarks_by_technician' => 'nullable|string',
            // 'redirect_to_manage' ไม่ใช่ข้อมูลที่จะบันทึกลง DB จึงไม่จำเป็นต้อง Validate ที่นี่
        ];

        // User ที่ Login อยู่ (ซึ่งควรจะเป็น Admin ถ้าจะส่ง field นี้มา)
        $user = $this->user(); // หรือ Auth::user();

        // เฉพาะ Admin เท่านั้นที่ควรจะส่ง assigned_to_user_id มาได้ และควรจะ Validate
        if ($user && $user->is_admin) { // ตรวจสอบว่า User Model มี is_admin() method/property
            $rules['assigned_to_user_id'] = 'nullable|exists:users,id';
        }

        return $rules;
    }
}