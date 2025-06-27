<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity; // <--- ตรวจสอบว่ามีการ use Trait นี้
use Spatie\Activitylog\LogOptions;          // <--- Import LogOptions

class RepairRequest extends Model
{
    use HasFactory, LogsActivity; // <--- และมีการใช้ Trait นี้

    protected $fillable = [
    'user_id',
    'title',
    'description',
    'location_id',
    'category_id',
    'status_id',
    'assigned_to_user_id',
    'remarks_by_technician',
    'requester_phone',
    'image_path',
    'after_image_path', // <--- ตรวจสอบว่ามีฟิลด์ใหม่นี้
    'completed_at',
];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    // Relationships (user, location, category, status, assignedTo)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function scopeWithDetails($query)
    {
        return $query->with(['user', 'location', 'category', 'status', 'assignedTo']);
    }

    public function loadDetails()
    {
        return $this->load(['user', 'location', 'category', 'status', 'assignedTo']);
    }

    // VVVVVV เพิ่ม Method นี้เข้าไป VVVVVV
    /**
     * Configure the options for activity logging.
     */
public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->logOnly([
            'title',
            'description',
            'location_id',
            'category_id',
            'status_id', // <--- **สำคัญ: ตรวจสอบว่ามี field นี้**
            'assigned_to_user_id', // หรือ assigned_to
            'remarks_by_technician',
            'completed_at',
              'image_path',         // <--- เพิ่มฟิลด์นี้
            'after_image_path'
        ])
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs()
        ->setDescriptionForEvent(fn(string $eventName) => "รายการแจ้งซ่อม #{$this->id} ได้ถูก {$this->translateEventName($eventName)}")
        ->useLogName('RepairRequest');
}

    // Helper function สำหรับแปลชื่อ event เป็นภาษาไทย (ทางเลือก)
    public function translateEventName(string $eventName): string
    {
        if ($eventName === 'created') {
            return 'สร้างใหม่';
        } elseif ($eventName === 'updated') {
            return 'อัปเดตข้อมูล';
        } elseif ($eventName === 'deleted') {
            return 'ลบ';
        }
        return $eventName;
    }
    // ^^^^^^ สิ้นสุดการเพิ่ม Method ^^^^^^
}