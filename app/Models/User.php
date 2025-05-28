<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany; // <--- เพิ่มบรรทัดนี้

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // คุณอาจจะต้องเพิ่ม 'is_admin', 'is_technician' ตรงนี้ด้วย หากคุณเพิ่มคอลัมน์เหล่านี้ในตาราง users
        'is_admin',
        'is_technician',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            // คุณอาจจะต้องเพิ่มการ cast สำหรับ 'is_admin', 'is_technician' ตรงนี้ด้วย
            'is_admin' => 'boolean',
            'is_technician' => 'boolean',
        ];
    }

    /**
     * Get the repair requests for the user.
     */
    public function repairRequests(): HasMany
    {
        return $this->hasMany(RepairRequest::class, 'user_id');
    }

    /**
     * Get the repair requests assigned to the user (as a technician).
     */
    public function assignedRepairRequests(): HasMany
    {
        return $this->hasMany(RepairRequest::class, 'assigned_to');
    }
}