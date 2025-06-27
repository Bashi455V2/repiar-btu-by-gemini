<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // เพิ่มการ use Rule สำหรับ Validation ที่ซับซ้อน

class UpdateRepairRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // การ authorize หลักทำใน controller ด้วย Policy ('update', $repairRequest) อยู่แล้ว
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
            // Rules เดิมสำหรับข้อมูลทั่วไป
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location_id' => 'required|exists:locations,id',
            'category_id' => 'required|exists:categories,id',
            'requester_phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'clear_image' => 'nullable|boolean',
        ];

        $user = $this->user(); // ดึง User ที่กำลัง Login อยู่

        // VVVVVV  ส่วนที่แก้ไข: เพิ่ม Rules สำหรับรูปภาพหลังการซ่อม  VVVVVV
        // Rules เหล่านี้จะถูกใช้กับฟอร์มของช่าง
        $rules['after_repair_image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';
        $rules['clear_after_image'] = 'nullable|boolean';
        // ^^^^^^  จบส่วนที่แก้ไข ^^^^^^


        // เพิ่ม Rules เฉพาะสำหรับ Admin หรือ Technician
        if ($user && ($user->is_admin || $user->is_technician)) {
            $rules['status_id'] = ['required', 'exists:statuses,id'];
            $rules['remarks_by_technician'] = ['nullable', 'string'];
            $rules['completed_at'] = ['nullable', 'date'];

            // เฉพาะ Admin เท่านั้นที่สามารถส่ง assigned_to_user_id ได้
            if ($user->is_admin) {
                $rules['assigned_to_user_id'] = [
                    'nullable',
                    Rule::exists('users', 'id')->whereNull('deleted_at') // ตรวจสอบเฉพาะ User ที่ยังไม่ถูกลบ
                ];
            }
        }

        return $rules;
    }
}
