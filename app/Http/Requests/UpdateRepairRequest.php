<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
class UpdateRepairRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
public function rules(): array
    {
        $rules = [ // Rules พื้นฐานที่ทุกคนที่มีสิทธิ์ update ต้องผ่าน
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location_id' => 'required|exists:locations,id',
            'category_id' => 'required|exists:categories,id',
            'requester_phone' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'clear_image' => 'nullable|boolean', // สำหรับ checkbox ลบรูป
        ];

        $user = $this->user(); // ดึง User ที่กำลัง Login อยู่

        
        if ($user && ($user->is_admin || $user->is_technician)) {
            $rules['status_id'] = ['required', 'exists:statuses,id'];
            
            if ($user->is_admin) {
$rules['assigned_to_user_id'] = ['nullable', 'exists:users,id'];            }
            $rules['remarks_by_technician'] = ['nullable', 'string'];
            $rules['completed_at'] = ['nullable', 'date'];
        }
       

        return $rules;
    }

}
