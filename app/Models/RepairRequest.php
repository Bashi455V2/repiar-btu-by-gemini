<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'description',
        'location',
        'contact_info',
        'status',
        'priority',
        'attachment',
        'assigned_to',
        'completed_at',
    ];

    // เพิ่มส่วนนี้เข้าไปใน Model ของคุณ
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'completed_at' => 'datetime', // เพิ่มคอลัมน์ completed_at ด้วยถ้าคุณต้องการ format มัน
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}