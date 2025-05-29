<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepairRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'location_id',
        'category_id',
        'status_id',
        'requester_phone',
        'image_path',
        'assigned_to_user_id',
        'remarks_by_technician',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    // Relationships
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

    /**
     * Scope a query to eager load common details.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithDetails($query) // <--- มี scopeWithDetails() อันเดียวที่ถูกต้อง
    {
        return $query->with(['user', 'location', 'category', 'status', 'assignedTo']);
    }

    /**
     * Eager load common details for an existing model instance.
     * (ถ้าคุณต้องการใช้งาน ให้ uncomment ส่วนนี้)
     */
    public function loadDetails()
    {
        return $this->load(['user', 'location', 'category', 'status', 'assignedTo']);
    }
}