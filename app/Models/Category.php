<?php

namespace App\Models; // <--- ตรวจสอบ Namespace

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model // <--- ตรวจสอบชื่อ Class
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description', // ถ้ามี
    ];

    // ถ้า Category มี relationship กับ RepairRequest
    public function repairRequests()
    {
        return $this->hasMany(RepairRequest::class, 'category_id');
    }
}